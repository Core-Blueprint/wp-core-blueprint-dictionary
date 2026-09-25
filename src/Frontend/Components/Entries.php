<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Frontend\ArchiveContext;
use CB\Dictionary\Frontend\Queries;

defined( 'ABSPATH' ) || exit;

final class Entries {
	/** @param array<string,mixed> $args */
	public static function render( array $args = [] ): string {
		$args = wp_parse_args(
			$args,
			[
				'category' => '',
				'tag'      => '',
				'letter'   => '',
				'limit'    => 50,
				'excerpt'  => true,
			]
		);

		$limit = max( 1, min( 100, absint( $args['limit'] ) ?: 50 ) );
		$query_args = [
			'posts_per_page' => $limit,
		];
		$filters = ArchiveContext::taxonomy_filters(
			(string) $args['category'],
			(string) $args['tag'],
			(string) $args['letter']
		);
		$tax_query = Queries::taxonomy_filter(
			$filters['category'],
			$filters['tag'],
			$filters['letter']
		);
		if ( [] !== $tax_query ) {
			$query_args['tax_query'] = $tax_query;
		}

		$query = Queries::entries( $query_args );
		if ( ! $query->have_posts() ) {
			return '<div class="cb-dictionary-list cb-dictionary-list--empty">' . esc_html__( 'No dictionary entries found.', 'core-blueprint-dictionary' ) . '</div>';
		}

		$show_excerpt = self::boolean( $args['excerpt'], true );
		$html = '<div class="cb-dictionary-list"><ul class="cb-dictionary-list__items">';
		foreach ( $query->posts as $post ) {
			if ( ! $post instanceof \WP_Post ) {
				continue;
			}

			$html .= '<li class="cb-dictionary-list__item">';
			$html .= '<a class="cb-dictionary-list__link" href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a>';

			if ( $show_excerpt ) {
				$excerpt = get_the_excerpt( $post );
				if ( '' !== trim( $excerpt ) ) {
					$html .= '<div class="cb-dictionary-list__excerpt">' . wp_kses_post( wpautop( $excerpt ) ) . '</div>';
				}
			}

			$html .= '</li>';
		}

		return $html . '</ul></div>';
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
