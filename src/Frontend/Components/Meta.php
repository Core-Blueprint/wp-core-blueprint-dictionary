<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Frontend\Data;

defined( 'ABSPATH' ) || exit;

final class Meta {
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

		$fields = [
			'pronunciation' => __( 'Pronunciation', 'core-blueprint-dictionary' ),
			'abbreviation'  => __( 'Abbreviation', 'core-blueprint-dictionary' ),
			'synonyms'      => __( 'Synonyms', 'core-blueprint-dictionary' ),
			'source'        => __( 'Source', 'core-blueprint-dictionary' ),
			'letters'       => __( 'Alphabet', 'core-blueprint-dictionary' ),
		];

		$items = [];
		foreach ( $fields as $key => $label ) {
			$value = trim( (string) ( $record[ $key ] ?? '' ) );
			if ( '' === $value ) {
				continue;
			}
			$items[] = '<div class="cb-dictionary-meta__item"><dt class="cb-dictionary-meta__label">' . esc_html( $label ) . '</dt><dd class="cb-dictionary-meta__value">' . esc_html( $value ) . '</dd></div>';
		}

		return [] === $items ? '' : '<dl class="cb-dictionary-meta">' . implode( '', $items ) . '</dl>';
	}
}
