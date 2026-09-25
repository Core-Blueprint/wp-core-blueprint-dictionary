<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Content\Alphabet as AlphabetModel;
use CB\Dictionary\Content\Taxonomies;

defined( 'ABSPATH' ) || exit;

final class Alphabet {
	/** @param array<string,mixed> $args */
	public static function render( array $args = [] ): string {
		$args = wp_parse_args(
			$args,
			[
				'show_empty' => false,
			]
		);

		$show_empty = self::boolean( $args['show_empty'], false );
		$terms = get_terms(
			[
				'taxonomy'   => Taxonomies::LETTER,
				'hide_empty' => ! $show_empty,
				'slug'       => array_keys( AlphabetModel::terms() ),
			]
		);
		if ( is_wp_error( $terms ) ) {
			return '';
		}

		$by_slug = [];
		foreach ( $terms as $term ) {
			if ( $term instanceof \WP_Term ) {
				$by_slug[ $term->slug ] = $term;
			}
		}

		$current = get_queried_object();
		$current_slug = $current instanceof \WP_Term && Taxonomies::LETTER === $current->taxonomy
			? (string) $current->slug
			: '';

		$html = '<nav class="cb-dictionary-alphabet" aria-label="' . esc_attr__( 'Dictionary alphabet', 'core-blueprint-dictionary' ) . '"><ul class="cb-dictionary-alphabet__items">';
		foreach ( AlphabetModel::terms() as $slug => $label ) {
			$is_current = $slug === $current_slug;
			$html .= '<li class="cb-dictionary-alphabet__item' . ( $is_current ? ' cb-dictionary-alphabet__item--current' : '' ) . '">';

			if ( isset( $by_slug[ $slug ] ) ) {
				$link = get_term_link( $by_slug[ $slug ] );
				if ( ! is_wp_error( $link ) ) {
					$html .= '<a class="cb-dictionary-alphabet__link" href="' . esc_url( $link ) . '"' . ( $is_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $label ) . '</a>';
				}
			} elseif ( $show_empty ) {
				$html .= '<span class="cb-dictionary-alphabet__label cb-dictionary-alphabet__label--empty" aria-disabled="true">' . esc_html( $label ) . '</span>';
			}

			$html .= '</li>';
		}

		return $html . '</ul></nav>';
	}

	private static function boolean( mixed $value, bool $default ): bool {
		if ( is_bool( $value ) ) {
			return $value;
		}
		if ( is_scalar( $value ) ) {
			$parsed = filter_var( (string) $value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE );
			return null === $parsed ? $default : $parsed;
		}
		return $default;
	}
}
