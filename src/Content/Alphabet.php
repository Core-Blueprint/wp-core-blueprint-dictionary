<?php
declare(strict_types=1);

namespace CB\Dictionary\Content;

defined( 'ABSPATH' ) || exit;

final class Alphabet {
	private static bool $assigning = false;

	public static function init(): void {
		add_action( 'wp_after_insert_post', [ __CLASS__, 'assign_for_post' ], 90, 4 );
	}

	/** @return array<string,string> slug => label */
	public static function terms(): array {
		$terms = [ '0-9' => '0-9' ];
		foreach ( range( 'A', 'Z' ) as $letter ) {
			$terms[ strtolower( $letter ) ] = $letter;
		}
		return $terms;
	}

	public static function seed_terms(): void {
		foreach ( self::terms() as $slug => $label ) {
			if ( term_exists( $slug, Taxonomies::LETTER ) ) {
				continue;
			}
			wp_insert_term( $label, Taxonomies::LETTER, [ 'slug' => $slug ] );
		}
	}

	public static function assign_for_post( int $post_id, \WP_Post $post, bool $update, ?\WP_Post $post_before ): void {
		unset( $update, $post_before );
		if ( self::$assigning || PostType::TYPE !== $post->post_type ) {
			return;
		}
		if ( 'auto-draft' === $post->post_status || wp_is_post_autosave( $post_id ) || wp_is_post_revision( $post_id ) ) {
			return;
		}

		$slug = self::bucket_for_title( (string) $post->post_title );
		if ( '' === $slug ) {
			return;
		}

		$term = get_term_by( 'slug', $slug, Taxonomies::LETTER );
		if ( ! $term instanceof \WP_Term ) {
			self::seed_terms();
			$term = get_term_by( 'slug', $slug, Taxonomies::LETTER );
		}
		if ( ! $term instanceof \WP_Term ) {
			return;
		}

		$current = wp_get_object_terms( $post_id, Taxonomies::LETTER, [ 'fields' => 'ids' ] );
		if ( is_wp_error( $current ) ) {
			return;
		}
		$current = array_map( 'intval', $current );
		if ( [ (int) $term->term_id ] === $current ) {
			return;
		}

		self::$assigning = true;
		wp_set_object_terms( $post_id, [ (int) $term->term_id ], Taxonomies::LETTER, false );
		self::$assigning = false;
	}

	public static function bucket_for_title( string $title ): string {
		$title = trim( wp_strip_all_tags( $title ) );
		if ( '' === $title ) {
			return '';
		}

		$ascii = trim( remove_accents( $title ) );
		if ( '' === $ascii ) {
			return '0-9';
		}

		$first = strtoupper( substr( $ascii, 0, 1 ) );
		if ( $first >= 'A' && $first <= 'Z' ) {
			return strtolower( $first );
		}
		if ( $first >= '0' && $first <= '9' ) {
			return '0-9';
		}

		return '0-9';
	}
}
