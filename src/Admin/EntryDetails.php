<?php
declare(strict_types=1);

namespace CB\Dictionary\Admin;

use CB\Dictionary\Content\Meta;
use CB\Dictionary\Content\PostType;

defined( 'ABSPATH' ) || exit;

final class EntryDetails {
	private const NONCE_ACTION = 'cb_dictionary_save_details';
	private const NONCE_FIELD  = 'cb_dictionary_details_nonce';

	public static function init(): void {
		add_action( 'add_meta_boxes_' . PostType::TYPE, [ __CLASS__, 'add_meta_box' ] );
		add_action( 'save_post_' . PostType::TYPE, [ __CLASS__, 'save' ], 10, 2 );
	}

	public static function add_meta_box(): void {
		add_meta_box(
			'cb-dictionary-details',
			__( 'Dictionary Details', 'core-blueprint-dictionary' ),
			[ __CLASS__, 'render' ],
			PostType::TYPE,
			'side',
			'default'
		);
	}

	public static function render( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_FIELD );
		$pronunciation = (string) get_post_meta( $post->ID, Meta::PRONUNCIATION, true );
		$abbreviation  = (string) get_post_meta( $post->ID, Meta::ABBREVIATION, true );
		$synonyms      = (string) get_post_meta( $post->ID, Meta::SYNONYMS, true );
		$source        = (string) get_post_meta( $post->ID, Meta::SOURCE, true );
		$featured      = (bool) get_post_meta( $post->ID, Meta::FEATURED, true );
		?>
		<p><label for="cb_dictionary_pronunciation"><strong><?php esc_html_e( 'Pronunciation', 'core-blueprint-dictionary' ); ?></strong></label><input class="widefat" type="text" id="cb_dictionary_pronunciation" name="cb_dictionary_pronunciation" value="<?php echo esc_attr( $pronunciation ); ?>"><span class="description"><?php esc_html_e( 'Optional pronunciation or phonetic hint.', 'core-blueprint-dictionary' ); ?></span></p>
		<p><label for="cb_dictionary_abbreviation"><strong><?php esc_html_e( 'Abbreviation / acronym', 'core-blueprint-dictionary' ); ?></strong></label><input class="widefat" type="text" id="cb_dictionary_abbreviation" name="cb_dictionary_abbreviation" value="<?php echo esc_attr( $abbreviation ); ?>"></p>
		<p><label for="cb_dictionary_synonyms"><strong><?php esc_html_e( 'Alternative terms / synonyms', 'core-blueprint-dictionary' ); ?></strong></label><input class="widefat" type="text" id="cb_dictionary_synonyms" name="cb_dictionary_synonyms" value="<?php echo esc_attr( $synonyms ); ?>"><span class="description"><?php esc_html_e( 'Optional comma-separated alternative names.', 'core-blueprint-dictionary' ); ?></span></p>
		<p><label for="cb_dictionary_source"><strong><?php esc_html_e( 'Source / reference', 'core-blueprint-dictionary' ); ?></strong></label><input class="widefat" type="text" id="cb_dictionary_source" name="cb_dictionary_source" value="<?php echo esc_attr( $source ); ?>"></p>
		<p><label><input type="checkbox" name="cb_dictionary_featured" value="1" <?php checked( $featured ); ?>> <?php esc_html_e( 'Featured entry', 'core-blueprint-dictionary' ); ?></label></p>
		<p class="description"><?php esc_html_e( 'The alphabet letter is assigned automatically from the entry title and cannot be selected manually.', 'core-blueprint-dictionary' ); ?></p>
		<?php
	}

	public static function save( int $post_id, \WP_Post $post ): void {
		if ( PostType::TYPE !== $post->post_type || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}
		if ( ! isset( $_POST[ self::NONCE_FIELD ] ) ) {
			return;
		}
		$nonce = sanitize_text_field( wp_unslash( $_POST[ self::NONCE_FIELD ] ) );
		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$fields = [
			Meta::PRONUNCIATION => 'cb_dictionary_pronunciation',
			Meta::ABBREVIATION  => 'cb_dictionary_abbreviation',
			Meta::SYNONYMS      => 'cb_dictionary_synonyms',
			Meta::SOURCE        => 'cb_dictionary_source',
		];
		foreach ( $fields as $meta_key => $request_key ) {
			$value = isset( $_POST[ $request_key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $request_key ] ) ) : '';
			if ( '' === $value ) {
				delete_post_meta( $post_id, $meta_key );
			} else {
				update_post_meta( $post_id, $meta_key, $value );
			}
		}
		update_post_meta( $post_id, Meta::FEATURED, isset( $_POST['cb_dictionary_featured'] ) ? 1 : 0 );
	}
}
