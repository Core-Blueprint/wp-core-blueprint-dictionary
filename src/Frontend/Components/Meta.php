<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Frontend\Data;

defined( 'ABSPATH' ) || exit;

final class Meta {
	/** @var string[] */
	public const DEFAULT_FIELDS = [
		'pronunciation',
		'abbreviation',
		'synonyms',
		'source',
		'categories',
		'tags',
		'letters',
	];

	/** @return array<string,string> */
	public static function field_options(): array {
		return [
			'pronunciation' => __( 'Pronunciation', 'core-blueprint-dictionary' ),
			'abbreviation'  => __( 'Abbreviation', 'core-blueprint-dictionary' ),
			'synonyms'      => __( 'Synonyms', 'core-blueprint-dictionary' ),
			'source'        => __( 'Source', 'core-blueprint-dictionary' ),
			'categories'    => __( 'Categories', 'core-blueprint-dictionary' ),
			'tags'          => __( 'Tags', 'core-blueprint-dictionary' ),
			'letters'       => __( 'Alphabet', 'core-blueprint-dictionary' ),
		];
	}

	/** @param array<string,mixed> $args */
	public static function render( array $args = [] ): string {
		$post_id = absint( $args['id'] ?? 0 );
		if ( 0 === $post_id ) {
			$post_id = get_the_ID();
		}

		$record = $post_id > 0 ? Data::entry( $post_id ) : [];
		if ( [] === $record ) {
			return '';
		}

		$selected_fields = self::normalize_fields( $args['fields'] ?? self::DEFAULT_FIELDS );
		if ( [] === $selected_fields ) {
			return '';
		}

		$options = self::field_options();
		$items   = [];
		foreach ( $selected_fields as $key ) {
			if ( ! isset( $options[ $key ] ) ) {
				continue;
			}

			$value = trim( (string) ( $record[ $key ] ?? '' ) );
			if ( '' === $value ) {
				continue;
			}

			$items[] = '<div class="cb-dictionary-meta__item"><dt class="cb-dictionary-meta__label">' . esc_html( $options[ $key ] ) . '</dt><dd class="cb-dictionary-meta__value">' . esc_html( $value ) . '</dd></div>';
		}

		return [] === $items ? '' : '<dl class="cb-dictionary-meta">' . implode( '', $items ) . '</dl>';
	}

	/** @return string[] */
	private static function normalize_fields( mixed $value ): array {
		if ( is_string( $value ) ) {
			$value = preg_split( '/[\s,;|]+/', $value ) ?: [];
		} elseif ( ! is_array( $value ) ) {
			$value = is_scalar( $value ) ? [ (string) $value ] : [];
		}

		$allowed  = array_keys( self::field_options() );
		$selected = [];
		foreach ( $value as $field ) {
			if ( ! is_scalar( $field ) ) {
				continue;
			}

			$field = sanitize_key( (string) $field );
			if ( in_array( $field, $allowed, true ) && ! in_array( $field, $selected, true ) ) {
				$selected[] = $field;
			}
		}

		return $selected;
	}
}
