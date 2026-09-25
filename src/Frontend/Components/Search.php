<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Content\PostType;
use CB\Dictionary\Frontend\Assets;
use CB\Dictionary\Frontend\RestSearch;

defined( 'ABSPATH' ) || exit;

final class Search {
	private static int $instance = 0;

	/**
	 * @param array{
	 *     placeholder?:mixed,
	 *     source?:mixed,
	 *     results?:mixed,
	 *     limit?:mixed,
	 *     excerpt?:mixed,
	 *     show_count?:mixed,
	 *     live?:mixed,
	 *     min_chars?:mixed,
	 *     button_mode?:mixed,
	 *     button_text?:mixed,
	 *     button_icon_position?:mixed,
	 *     button_placement?:mixed,
	 *     button_overlay_side?:mixed
	 * } $args
	 */
	public static function render( array $args = [] ): string {
		$args = wp_parse_args(
			$args,
			[
				'placeholder'          => __( 'Search dictionary…', 'core-blueprint-dictionary' ),
				'source'               => 'default',
				'results'              => 'inline',
				'limit'                => 30,
				'excerpt'              => false,
				'show_count'           => false,
				'live'                 => true,
				'min_chars'            => 2,
				'button_mode'          => 'text',
				'button_text'          => __( 'Search', 'core-blueprint-dictionary' ),
				'button_icon_position' => 'before',
				'button_placement'     => 'inline',
				'button_overlay_side'  => 'right',
			]
		);

		$placeholder          = sanitize_text_field( self::scalar_string( $args['placeholder'] ) );
		$source               = SearchResults::source_key( $args['source'] );
		$results_mode         = self::choice( $args['results'], [ 'inline', 'external' ], 'inline' );
		$live                 = self::boolean( $args['live'], true );
		$min_chars            = max( 1, min( 10, absint( $args['min_chars'] ) ?: 2 ) );
		$button_mode          = self::choice( $args['button_mode'], [ 'text', 'icon', 'text-icon', 'hidden' ], 'text' );
		$button_text          = sanitize_text_field( self::scalar_string( $args['button_text'] ) );
		$button_text          = '' !== $button_text ? $button_text : __( 'Search', 'core-blueprint-dictionary' );
		$button_icon_position = self::choice( $args['button_icon_position'], [ 'before', 'after' ], 'before' );
		$button_placement     = self::choice( $args['button_placement'], [ 'inline', 'overlay' ], 'inline' );
		$button_overlay_side  = self::choice( $args['button_overlay_side'], [ 'left', 'right' ], 'right' );
		$query_string         = SearchResults::request_source() === '' || SearchResults::request_source() === $source
			? SearchResults::request_query()
			: '';

		Assets::enqueue_search( $live );

		self::$instance++;
		$input_id   = 'cb-dictionary-q-' . self::$instance;
		$results_id = 'cb-dictionary-results-' . $source;
		$list_id    = $results_id . '-list';
		$action     = get_post_type_archive_link( PostType::TYPE ) ?: home_url( '/' );

		$attrs = [
			'class'                     => 'cb-dictionary-search',
			'data-cb-dictionary-search' => $source,
			'data-results-mode'         => $results_mode,
			'data-live-search'          => $live ? '1' : '0',
			'data-endpoint'             => $live ? rest_url( RestSearch::NAMESPACE . RestSearch::ROUTE ) : '',
			'data-min-chars'            => (string) $min_chars,
			'data-button-mode'          => $button_mode,
			'data-button-placement'     => $button_placement,
			'data-button-side'          => $button_overlay_side,
			'data-loading-label'        => __( 'Searching…', 'core-blueprint-dictionary' ),
			'data-no-results-label'     => __( 'No matching dictionary entries found.', 'core-blueprint-dictionary' ),
			'data-error-label'          => __( 'Live search is temporarily unavailable. Submit the form to search.', 'core-blueprint-dictionary' ),
		];

		$html = '<div' . self::attributes( $attrs ) . '>';
		$html .= '<form class="cb-dictionary-search__form" method="get" role="search" action="' . esc_url( $action ) . '">';
		$html .= '<label class="screen-reader-text" for="' . esc_attr( $input_id ) . '">' . esc_html__( 'Search dictionary', 'core-blueprint-dictionary' ) . '</label>';
		$html .= '<input id="' . esc_attr( $input_id ) . '" class="cb-dictionary-search__input" type="search" name="cb_dictionary_q" value="' . esc_attr( $query_string ) . '" placeholder="' . esc_attr( $placeholder ) . '" autocomplete="off" aria-controls="' . esc_attr( $list_id ) . '"';
		if ( $live ) {
			$html .= ' role="combobox" aria-autocomplete="list" aria-expanded="' . ( '' !== $query_string ? 'true' : 'false' ) . '"';
		}
		$html .= '>';
		$html .= '<input type="hidden" name="cb_dictionary_source" value="' . esc_attr( $source ) . '">';
		$html .= self::submit_button( $button_mode, $button_text, $button_icon_position, $button_placement );
		$html .= '</form>';

		if ( 'inline' === $results_mode ) {
			$html .= SearchResults::render(
				[
					'source'     => $source,
					'limit'      => $args['limit'],
					'excerpt'    => $args['excerpt'],
					'show_count' => $args['show_count'],
				]
			);
		}

		return $html . '</div>';
	}

	private static function submit_button( string $mode, string $text, string $icon_position, string $placement ): string {
		$classes = [
			'cb-dictionary-search__submit',
			'cb-dictionary-search__submit--' . $mode,
			'cb-dictionary-search__submit--' . $placement,
		];

		$button = '<button class="' . esc_attr( implode( ' ', $classes ) ) . '" type="submit"';
		if ( 'icon' === $mode ) {
			$button .= ' aria-label="' . esc_attr( $text ) . '"';
		}
		$button .= '>';

		$text_html = '<span class="cb-dictionary-search__submit-text">' . esc_html( $text ) . '</span>';
		$icon_html = '<span class="cb-dictionary-search__submit-icon" aria-hidden="true"></span>';

		if ( 'icon' === $mode ) {
			$button .= $icon_html;
		} elseif ( 'text-icon' === $mode ) {
			$button .= 'after' === $icon_position ? $text_html . $icon_html : $icon_html . $text_html;
		} else {
			$button .= $text_html;
		}

		return $button . '</button>';
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

	/** @param string[] $allowed */
	private static function choice( mixed $value, array $allowed, string $default ): string {
		if ( ! is_scalar( $value ) ) {
			return $default;
		}
		$value = sanitize_key( (string) $value );
		return in_array( $value, $allowed, true ) ? $value : $default;
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
