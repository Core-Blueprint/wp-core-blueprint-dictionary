<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Content\Taxonomies;

defined( 'ABSPATH' ) || exit;

final class Categories {
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
				'taxonomy'   => Taxonomies::CATEGORY,
				'hide_empty' => ! $show_empty,
				'parent'     => 0,
				'orderby'    => 'name',
				'order'      => 'ASC',
			]
		);
		if ( is_wp_error( $terms ) || [] === $terms ) {
			return '';
		}

		$current = get_queried_object();
		$current_slug = $current instanceof \WP_Term && Taxonomies::CATEGORY === $current->taxonomy
			? (string) $current->slug
			: '';

		$html = '<nav class="cb-dictionary-categories" aria-label="' . esc_attr__( 'Dictionary categories', 'core-blueprint-dictionary' ) . '"><ul class="cb-dictionary-categories__items">';
		foreach ( $terms as $term ) {
			if ( ! $term instanceof \WP_Term ) {
				continue;
			}

			$link = get_term_link( $term );
			if ( is_wp_error( $link ) ) {
				continue;
			}

			$is_current = $term->slug === $current_slug;
			$is_empty   = 0 === (int) $term->count;
			$classes    = [ 'cb-dictionary-categories__item' ];
			if ( $is_current ) {
				$classes[] = 'cb-dictionary-categories__item--current';
			}
			if ( $is_empty ) {
				$classes[] = 'cb-dictionary-categories__item--empty';
			}

			$html .= '<li class="' . esc_attr( implode( ' ', $classes ) ) . '">';
			$html .= '<a class="cb-dictionary-categories__link" href="' . esc_url( $link ) . '"' . ( $is_current ? ' aria-current="page"' : '' ) . '>' . esc_html( $term->name ) . '</a>';
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
