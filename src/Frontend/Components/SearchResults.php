<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Frontend\Queries;

defined( 'ABSPATH' ) || exit;

final class SearchResults {
	/**
	 * @param array{
	 *     source?:mixed,
	 *     query?:mixed,
	 *     limit?:mixed,
	 *     excerpt?:mixed,
	 *     show_count?:mixed
	 * } $args
	 */
	public static function render( array $args = [] ): string {
		$args = wp_parse_args(
			$args,
			[
				'source'     => 'default',
				'query'      => null,
				'limit'      => 30,
				'excerpt'    => false,
				'show_count' => false,
			]
		);

		$source = self::source_key( $args['source'] );
		$query_string = null === $args['query']
			? self::request_query_for( $source )
			: sanitize_text_field( trim( (string) $args['query'] ) );
		$limit = max( 1, min( 100, absint( $args['limit'] ) ?: 30 ) );
		$show_excerpt = self::boolean( $args['excerpt'], false );
		$show_count = self::boolean( $args['show_count'], false );
		$id = 'cb-dictionary-results-' . $source;

		$html = '<div class="cb-dictionary-search-results" id="' . esc_attr( $id ) . '" data-cb-dictionary-results="' . esc_attr( $source ) . '" aria-live="polite"';
		if ( '' === $query_string ) {
			return $html . ' hidden></div>';
		}
		$html .= '>';

		$query = Queries::entries(
			[
				's'              => $query_string,
				'posts_per_page' => $limit,
			]
		);

		if ( ! $query->have_posts() ) {
			return $html . '<p class="cb-dictionary-search-results__status">' . esc_html__( 'No matching dictionary entries found.', 'core-blueprint-dictionary' ) . '</p></div>';
		}

		if ( $show_count ) {
			$html .= '<p class="cb-dictionary-search-results__count">' . esc_html(
				sprintf(
					/* translators: %d: number of dictionary search results. */
					_n( '%d result', '%d results', (int) $query->post_count, 'core-blueprint-dictionary' ),
					(int) $query->post_count
				)
			) . '</p>';
		}

		$html .= '<ul class="cb-dictionary-search-results__items">';
		foreach ( $query->posts as $post ) {
			if ( ! $post instanceof \WP_Post ) {
				continue;
			}

			$html .= '<li class="cb-dictionary-search-results__item">';
			$html .= '<a class="cb-dictionary-search-results__link" href="' . esc_url( get_permalink( $post ) ) . '">';
			$html .= '<span class="cb-dictionary-search-results__title">' . esc_html( get_the_title( $post ) ) . '</span>';

			if ( $show_excerpt ) {
				$excerpt = trim( wp_strip_all_tags( get_the_excerpt( $post ), true ) );
				if ( '' !== $excerpt ) {
					$html .= '<span class="cb-dictionary-search-results__excerpt">' . esc_html( $excerpt ) . '</span>';
				}
			}

			$html .= '</a></li>';
		}

		return $html . '</ul></div>';
	}

	public static function request_query(): string {
		return isset( $_GET['cb_dictionary_q'] )
			? sanitize_text_field( trim( (string) wp_unslash( $_GET['cb_dictionary_q'] ) ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only search input.
			: '';
	}

	public static function request_source(): string {
		return isset( $_GET['cb_dictionary_source'] )
			? self::source_key( wp_unslash( $_GET['cb_dictionary_source'] ) ) // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only search input.
			: '';
	}

	public static function source_key( mixed $source ): string {
		if ( ! is_scalar( $source ) ) {
			return 'default';
		}
		$key = sanitize_key( (string) $source );
		return '' === $key ? 'default' : $key;
	}

	private static function request_query_for( string $source ): string {
		$request_source = self::request_source();
		if ( '' !== $request_source && $request_source !== $source ) {
			return '';
		}
		return self::request_query();
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
