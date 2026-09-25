<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend;

use CB\Dictionary\Content\Taxonomies;
use CB\Dictionary\Frontend\Components\Alphabet as AlphabetComponent;
use CB\Dictionary\Frontend\Components\Entries as EntriesComponent;
use CB\Dictionary\Frontend\Components\Meta as MetaComponent;
use CB\Dictionary\Frontend\Components\Search as SearchComponent;
use CB\Dictionary\Frontend\Components\SearchResults as SearchResultsComponent;

defined( 'ABSPATH' ) || exit;

final class Shortcodes {
	public static function init(): void {
		add_shortcode( 'cb_dictionary_list', [ __CLASS__, 'list_entries' ] );
		add_shortcode( 'cb_dictionary_search', [ __CLASS__, 'search' ] );
		add_shortcode( 'cb_dictionary_search_results', [ __CLASS__, 'search_results' ] );
		add_shortcode( 'cb_dictionary_alphabet', [ __CLASS__, 'alphabet' ] );
		add_shortcode( 'cb_dictionary_categories', [ __CLASS__, 'categories' ] );
		add_shortcode( 'cb_dictionary_meta', [ __CLASS__, 'meta' ] );
	}

	/** @param array<string,mixed>|string $atts */
	public static function list_entries( array|string $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'category' => '',
				'tag'      => '',
				'letter'   => '',
				'limit'    => 50,
				'excerpt'  => 'true',
			],
			is_array( $atts ) ? $atts : [],
			'cb_dictionary_list'
		);
		return EntriesComponent::render( $atts );
	}

	/** @param array<string,mixed>|string $atts */
	public static function search( array|string $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'placeholder' => __( 'Search dictionary…', 'core-blueprint-dictionary' ),
				'limit'       => 30,
				'source'      => 'default',
				'results'     => 'inline',
				'excerpt'     => 'false',
				'show_count'  => 'false',
				'live'        => 'true',
				'min_chars'   => 2,
			],
			is_array( $atts ) ? $atts : [],
			'cb_dictionary_search'
		);
		return SearchComponent::render( $atts );
	}

	/** @param array<string,mixed>|string $atts */
	public static function search_results( array|string $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'limit'      => 30,
				'source'     => 'default',
				'excerpt'    => 'false',
				'show_count' => 'false',
			],
			is_array( $atts ) ? $atts : [],
			'cb_dictionary_search_results'
		);
		return SearchResultsComponent::render( $atts );
	}

	/** @param array<string,mixed>|string $atts */
	public static function alphabet( array|string $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'show_empty' => 'false',
			],
			is_array( $atts ) ? $atts : [],
			'cb_dictionary_alphabet'
		);
		return AlphabetComponent::render( $atts );
	}

	/** @param array<string,mixed>|string $atts */
	public static function categories( array|string $atts = [] ): string {
		$atts = shortcode_atts( [ 'hide_empty' => 'true' ], is_array( $atts ) ? $atts : [], 'cb_dictionary_categories' );
		$terms = get_terms(
			[
				'taxonomy'   => Taxonomies::CATEGORY,
				'hide_empty' => filter_var( $atts['hide_empty'], FILTER_VALIDATE_BOOLEAN ),
				'parent'     => 0,
			]
		);
		if ( is_wp_error( $terms ) || [] === $terms ) {
			return '';
		}

		$html = '<ul class="cb-dictionary-categories">';
		foreach ( $terms as $term ) {
			if ( ! $term instanceof \WP_Term ) {
				continue;
			}
			$link = get_term_link( $term );
			if ( ! is_wp_error( $link ) ) {
				$html .= '<li class="cb-dictionary-categories__item"><a href="' . esc_url( $link ) . '">' . esc_html( $term->name ) . '</a></li>';
			}
		}
		return $html . '</ul>';
	}

	/** @param array<string,mixed>|string $atts */
	public static function meta( array|string $atts = [] ): string {
		$atts = shortcode_atts(
			[
				'id'     => 0,
				'fields' => implode( ',', MetaComponent::DEFAULT_FIELDS ),
			],
			is_array( $atts ) ? $atts : [],
			'cb_dictionary_meta'
		);
		return MetaComponent::render( $atts );
	}
}
