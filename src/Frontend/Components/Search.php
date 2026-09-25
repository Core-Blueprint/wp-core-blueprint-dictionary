<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend\Components;

use CB\Dictionary\Content\PostType;

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
	 *     show_count?:mixed
	 * } $args
	 */
	public static function render( array $args = [] ): string {
		$args = wp_parse_args(
			$args,
			[
				'placeholder' => __( 'Search dictionary…', 'core-blueprint-dictionary' ),
				'source'      => 'default',
				'results'     => 'inline',
				'limit'       => 30,
				'excerpt'     => false,
				'show_count'  => false,
			]
		);

		$placeholder = sanitize_text_field( (string) $args['placeholder'] );
		$source = SearchResults::source_key( $args['source'] );
		$results_mode = 'external' === sanitize_key( (string) $args['results'] ) ? 'external' : 'inline';
		$query_string = SearchResults::request_source() === '' || SearchResults::request_source() === $source
			? SearchResults::request_query()
			: '';

		self::$instance++;
		$input_id = 'cb-dictionary-q-' . self::$instance;
		$results_id = 'cb-dictionary-results-' . $source;
		$action = get_post_type_archive_link( PostType::TYPE ) ?: home_url( '/' );

		$html = '<div class="cb-dictionary-search" data-cb-dictionary-search="' . esc_attr( $source ) . '" data-results-mode="' . esc_attr( $results_mode ) . '">';
		$html .= '<form class="cb-dictionary-search__form" method="get" role="search" action="' . esc_url( $action ) . '">';
		$html .= '<label class="screen-reader-text" for="' . esc_attr( $input_id ) . '">' . esc_html__( 'Search dictionary', 'core-blueprint-dictionary' ) . '</label>';
		$html .= '<input id="' . esc_attr( $input_id ) . '" class="cb-dictionary-search__input" type="search" name="cb_dictionary_q" value="' . esc_attr( $query_string ) . '" placeholder="' . esc_attr( $placeholder ) . '" autocomplete="off" aria-controls="' . esc_attr( $results_id ) . '">';
		$html .= '<input type="hidden" name="cb_dictionary_source" value="' . esc_attr( $source ) . '">';
		$html .= '<button class="cb-dictionary-search__submit" type="submit">' . esc_html__( 'Search', 'core-blueprint-dictionary' ) . '</button>';
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
}
