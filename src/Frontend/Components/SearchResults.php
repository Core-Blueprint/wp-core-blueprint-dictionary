<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Frontend\Search as SearchProvider;

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

		$source       = self::source_key( $args['source'] );
		$query_string = null === $args['query']
			? self::request_query_for( $source )
			: sanitize_text_field( trim( self::scalar_string( $args['query'] ) ) );
		$limit        = max( 1, min( 100, absint( $args['limit'] ) ?: 30 ) );
		$show_excerpt = self::boolean( $args['excerpt'], false );
		$show_count   = self::boolean( $args['show_count'], false );
		$id           = 'cb-dictionary-results-' . $source;
		$list_id      = $id . '-list';

		$result = '' === $query_string
			? [ 'items' => [], 'total' => 0 ]
			: SearchProvider::entries( $query_string, $limit );
		$items = (array) ( $result['items'] ?? [] );
		$total = count( $items );

		$attrs = [
			'class'                      => 'cb-dictionary-search-results',
			'id'                         => $id,
			'data-cb-dictionary-results' => $source,
			'data-limit'                 => (string) $limit,
			'data-show-excerpt'          => $show_excerpt ? '1' : '0',
			'data-show-count'            => $show_count ? '1' : '0',
			'aria-live'                  => 'polite',
		];

		$html = '<div' . self::attributes( $attrs ) . ( '' === $query_string ? ' hidden' : '' ) . '>';
		$html .= '<p class="cb-dictionary-search-results__count" data-cb-dictionary-search-count';
		if ( ! $show_count || 0 === $total ) {
			$html .= ' hidden';
		}
		$html .= '>';
		if ( $show_count && $total > 0 ) {
			$html .= esc_html( self::count_label( $total ) );
		}
		$html .= '</p>';

		$html .= '<p class="cb-dictionary-search-results__status" data-cb-dictionary-search-status>';
		if ( '' !== $query_string && 0 === $total ) {
			$html .= esc_html__( 'No matching dictionary entries found.', 'core-blueprint-dictionary' );
		}
		$html .= '</p>';

		$html .= '<ul class="cb-dictionary-search-results__items" id="' . esc_attr( $list_id ) . '" role="listbox">';
		$html .= self::result_items( $items, $show_excerpt, $list_id );
		$html .= '</ul></div>';

		return $html;
	}

	public static function request_query(): string {
		if ( ! isset( $_GET['cb_dictionary_q'] ) ) {
			return '';
		}

		$value = wp_unslash( $_GET['cb_dictionary_q'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only search input.
		return is_scalar( $value ) ? sanitize_text_field( trim( (string) $value ) ) : '';
	}

	public static function request_source(): string {
		if ( ! isset( $_GET['cb_dictionary_source'] ) ) {
			return '';
		}

		return self::source_key( wp_unslash( $_GET['cb_dictionary_source'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public read-only search input.
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

	/** @param array<int,array<string,mixed>> $items */
	private static function result_items( array $items, bool $show_excerpt, string $list_id ): string {
		$html = '';
		foreach ( $items as $index => $item ) {
			$title     = sanitize_text_field( (string) ( $item['title'] ?? '' ) );
			$permalink = esc_url( (string) ( $item['permalink'] ?? '' ) );
			if ( '' === $title || '' === $permalink ) {
				continue;
			}

			$option_id = $list_id . '-option-' . ( $index + 1 );
			$html .= '<li class="cb-dictionary-search-results__item" id="' . esc_attr( $option_id ) . '" role="option" aria-selected="false">';
			$html .= '<a class="cb-dictionary-search-results__link" href="' . $permalink . '">';
			$html .= '<span class="cb-dictionary-search-results__title">' . esc_html( $title ) . '</span>';

			if ( $show_excerpt ) {
				$excerpt = trim( wp_strip_all_tags( (string) ( $item['excerpt'] ?? '' ), true ) );
				if ( '' !== $excerpt ) {
					$html .= '<span class="cb-dictionary-search-results__excerpt">' . esc_html( $excerpt ) . '</span>';
				}
			}

			$html .= '</a></li>';
		}
		return $html;
	}

	private static function count_label( int $total ): string {
		return sprintf(
			/* translators: %d: number of dictionary search results. */
			_n( '%d result', '%d results', $total, 'core-blueprint-dictionary' ),
			$total
		);
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

	private static function scalar_string( mixed $value ): string {
		return is_scalar( $value ) ? (string) $value : '';
	}

	/** @param array<string,string> $attributes */
	private static function attributes( array $attributes ): string {
		$html = '';
		foreach ( $attributes as $name => $value ) {
			$html .= ' ' . esc_attr( $name ) . '="' . esc_attr( $value ) . '"';
		}
		return $html;
	}
}
