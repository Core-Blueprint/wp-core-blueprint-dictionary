<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend;

use CB\Dictionary\Content\Alphabet;
use CB\Dictionary\Content\Meta;
use CB\Dictionary\Content\PostType;
use CB\Dictionary\Content\Taxonomies;

defined( 'ABSPATH' ) || exit;

final class Shortcodes {
	public static function init(): void {
		add_shortcode( 'cb_dictionary_list', [ __CLASS__, 'list_entries' ] );
		add_shortcode( 'cb_dictionary_search', [ __CLASS__, 'search' ] );
		add_shortcode( 'cb_dictionary_alphabet', [ __CLASS__, 'alphabet' ] );
		add_shortcode( 'cb_dictionary_categories', [ __CLASS__, 'categories' ] );
		add_shortcode( 'cb_dictionary_meta', [ __CLASS__, 'meta' ] );
	}

	/** @param array<string,mixed>|string $atts */
	public static function list_entries( array|string $atts = [] ): string {
		$atts = shortcode_atts( [ 'category' => '', 'tag' => '', 'letter' => '', 'limit' => 50, 'excerpt' => 'true' ], is_array( $atts ) ? $atts : [], 'cb_dictionary_list' );
		$limit = max( 1, min( 100, absint( $atts['limit'] ) ) );
		$args  = [ 'posts_per_page' => $limit ];
		$tax   = Queries::taxonomy_filter( (string) $atts['category'], (string) $atts['tag'], (string) $atts['letter'] );
		if ( ! empty( $tax ) ) {
			$args['tax_query'] = $tax;
		}

		$query = Queries::entries( $args );
		if ( ! $query->have_posts() ) {
			return '<div class="cb-dictionary-list cb-dictionary-list--empty">' . esc_html__( 'No dictionary entries found.', 'core-blueprint-dictionary' ) . '</div>';
		}

		$show_excerpt = filter_var( $atts['excerpt'], FILTER_VALIDATE_BOOLEAN );
		$html = '<div class="cb-dictionary-list"><ul class="cb-dictionary-list__items">';
		foreach ( $query->posts as $post ) {
			if ( ! $post instanceof \WP_Post ) {
				continue;
			}
			$html .= '<li class="cb-dictionary-list__item"><a class="cb-dictionary-list__link" href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a>';
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

	/** @param array<string,mixed>|string $atts */
	public static function search( array|string $atts = [] ): string {
		$atts = shortcode_atts( [ 'placeholder' => __( 'Search dictionary…', 'core-blueprint-dictionary' ), 'limit' => 30 ], is_array( $atts ) ? $atts : [], 'cb_dictionary_search' );
		$query_string = isset( $_GET['cb_dictionary_q'] ) ? sanitize_text_field( wp_unslash( $_GET['cb_dictionary_q'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$html  = '<div class="cb-dictionary-search">';
		$html .= '<form class="cb-dictionary-search__form" method="get" action="' . esc_url( get_post_type_archive_link( PostType::TYPE ) ?: home_url( '/' ) ) . '">';
		$html .= '<label class="screen-reader-text" for="cb-dictionary-q">' . esc_html__( 'Search dictionary', 'core-blueprint-dictionary' ) . '</label>';
		$html .= '<input id="cb-dictionary-q" class="cb-dictionary-search__input" type="search" name="cb_dictionary_q" value="' . esc_attr( $query_string ) . '" placeholder="' . esc_attr( (string) $atts['placeholder'] ) . '">';
		$html .= '<button class="cb-dictionary-search__submit" type="submit">' . esc_html__( 'Search', 'core-blueprint-dictionary' ) . '</button></form>';

		if ( '' !== $query_string ) {
			$query = Queries::entries( [ 's' => $query_string, 'posts_per_page' => max( 1, min( 100, absint( $atts['limit'] ) ) ) ] );
			$html .= '<div class="cb-dictionary-search__results">';
			if ( ! $query->have_posts() ) {
				$html .= '<p>' . esc_html__( 'No matching dictionary entries found.', 'core-blueprint-dictionary' ) . '</p>';
			} else {
				$html .= '<ul>';
				foreach ( $query->posts as $post ) {
					if ( $post instanceof \WP_Post ) {
						$html .= '<li><a href="' . esc_url( get_permalink( $post ) ) . '">' . esc_html( get_the_title( $post ) ) . '</a></li>';
					}
				}
				$html .= '</ul>';
			}
			$html .= '</div>';
		}
		return $html . '</div>';
	}

	/** @param array<string,mixed>|string $atts */
	public static function alphabet( array|string $atts = [] ): string {
		$atts = shortcode_atts( [ 'show_empty' => 'false' ], is_array( $atts ) ? $atts : [], 'cb_dictionary_alphabet' );
		$show_empty = filter_var( $atts['show_empty'], FILTER_VALIDATE_BOOLEAN );
		$terms = get_terms( [ 'taxonomy' => Taxonomies::LETTER, 'hide_empty' => ! $show_empty, 'slug' => array_keys( Alphabet::terms() ) ] );
		if ( is_wp_error( $terms ) ) {
			return '';
		}
		$by_slug = [];
		foreach ( $terms as $term ) {
			if ( $term instanceof \WP_Term ) {
				$by_slug[ $term->slug ] = $term;
			}
		}

		$html = '<nav class="cb-dictionary-alphabet" aria-label="' . esc_attr__( 'Dictionary alphabet', 'core-blueprint-dictionary' ) . '"><ul class="cb-dictionary-alphabet__items">';
		foreach ( Alphabet::terms() as $slug => $label ) {
			$html .= '<li class="cb-dictionary-alphabet__item">';
			if ( isset( $by_slug[ $slug ] ) ) {
				$link = get_term_link( $by_slug[ $slug ] );
				if ( ! is_wp_error( $link ) ) {
					$html .= '<a class="cb-dictionary-alphabet__link" href="' . esc_url( $link ) . '">' . esc_html( $label ) . '</a>';
				}
			} elseif ( $show_empty ) {
				$html .= '<span class="cb-dictionary-alphabet__label cb-dictionary-alphabet__label--empty">' . esc_html( $label ) . '</span>';
			}
			$html .= '</li>';
		}
		return $html . '</ul></nav>';
	}

	/** @param array<string,mixed>|string $atts */
	public static function categories( array|string $atts = [] ): string {
		$atts = shortcode_atts( [ 'hide_empty' => 'true' ], is_array( $atts ) ? $atts : [], 'cb_dictionary_categories' );
		$terms = get_terms( [ 'taxonomy' => Taxonomies::CATEGORY, 'hide_empty' => filter_var( $atts['hide_empty'], FILTER_VALIDATE_BOOLEAN ), 'parent' => 0 ] );
		if ( is_wp_error( $terms ) || empty( $terms ) ) {
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
		$atts = shortcode_atts( [ 'id' => 0 ], is_array( $atts ) ? $atts : [], 'cb_dictionary_meta' );
		$post_id = absint( $atts['id'] );
		if ( 0 === $post_id ) {
			$post_id = get_the_ID();
		}
		if ( $post_id <= 0 || PostType::TYPE !== get_post_type( $post_id ) ) {
			return '';
		}

		$items = [];
		$fields = [ Meta::PRONUNCIATION => __( 'Pronunciation', 'core-blueprint-dictionary' ), Meta::ABBREVIATION => __( 'Abbreviation', 'core-blueprint-dictionary' ), Meta::SYNONYMS => __( 'Synonyms', 'core-blueprint-dictionary' ), Meta::SOURCE => __( 'Source', 'core-blueprint-dictionary' ) ];
		foreach ( $fields as $key => $label ) {
			$value = trim( (string) get_post_meta( $post_id, $key, true ) );
			if ( '' !== $value ) {
				$items[] = '<div class="cb-dictionary-meta__item"><dt>' . esc_html( $label ) . '</dt><dd>' . esc_html( $value ) . '</dd></div>';
			}
		}
		$letter_terms = wp_get_post_terms( $post_id, Taxonomies::LETTER );
		if ( ! is_wp_error( $letter_terms ) && isset( $letter_terms[0] ) && $letter_terms[0] instanceof \WP_Term ) {
			$items[] = '<div class="cb-dictionary-meta__item"><dt>' . esc_html__( 'Alphabet', 'core-blueprint-dictionary' ) . '</dt><dd>' . esc_html( $letter_terms[0]->name ) . '</dd></div>';
		}
		return empty( $items ) ? '' : '<dl class="cb-dictionary-meta">' . implode( '', $items ) . '</dl>';
	}
}
