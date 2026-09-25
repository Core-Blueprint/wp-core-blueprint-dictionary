<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend;

use CB\Dictionary\Content\Meta;

defined( 'ABSPATH' ) || exit;

final class Search {
	private const DEFAULT_LIMIT   = 30;
	private const MAX_LIMIT       = 100;
	private const MAX_CANDIDATES  = 200;

	/**
	 * Search published Dictionary entries using canonical text and Dictionary aliases.
	 *
	 * Title matches rank above abbreviation and synonym matches. WordPress content/excerpt
	 * search remains available as the lowest-priority fallback.
	 *
	 * @return array{items:array<int,array<string,mixed>>,total:int}
	 */
	public static function entries( string $search, int $limit = self::DEFAULT_LIMIT ): array {
		$search = sanitize_text_field( trim( $search ) );
		$limit  = max( 1, min( self::MAX_LIMIT, $limit ) );

		if ( '' === $search ) {
			return [
				'items' => [],
				'total' => 0,
			];
		}

		$candidate_limit = min( self::MAX_CANDIDATES, max( 50, $limit * 4 ) );
		$ids             = [];

		$content_query = Queries::entries(
			[
				's'              => $search,
				'posts_per_page' => $candidate_limit,
				'orderby'        => 'relevance',
				'order'          => 'DESC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
			]
		);
		foreach ( (array) $content_query->posts as $post_id ) {
			$post_id = absint( $post_id );
			if ( $post_id > 0 ) {
				$ids[ $post_id ] = true;
			}
		}

		$meta_query = Queries::entries(
			[
				'posts_per_page' => $candidate_limit,
				'orderby'        => 'title',
				'order'          => 'ASC',
				'fields'         => 'ids',
				'no_found_rows'  => true,
				'meta_query'     => [
					'relation' => 'OR',
					[
						'key'     => Meta::ABBREVIATION,
						'value'   => $search,
						'compare' => 'LIKE',
					],
					[
						'key'     => Meta::SYNONYMS,
						'value'   => $search,
						'compare' => 'LIKE',
					],
				],
			]
		);
		foreach ( (array) $meta_query->posts as $post_id ) {
			$post_id = absint( $post_id );
			if ( $post_id > 0 ) {
				$ids[ $post_id ] = true;
			}
		}

		$ranked = [];
		foreach ( array_keys( $ids ) as $post_id ) {
			$post = get_post( $post_id );
			if ( ! $post instanceof \WP_Post || 'publish' !== $post->post_status ) {
				continue;
			}

			$title        = (string) get_the_title( $post );
			$abbreviation = (string) get_post_meta( $post->ID, Meta::ABBREVIATION, true );
			$synonyms     = (string) get_post_meta( $post->ID, Meta::SYNONYMS, true );
			$ranked[]     = [
				'score' => self::score( $search, $title, $abbreviation, $synonyms ),
				'item'  => [
					'id'           => (int) $post->ID,
					'title'        => $title,
					'permalink'    => (string) get_permalink( $post ),
					'excerpt'      => trim( wp_strip_all_tags( get_the_excerpt( $post ), true ) ),
					'abbreviation' => $abbreviation,
					'synonyms'     => $synonyms,
				],
			];
		}

		usort(
			$ranked,
			static function ( array $left, array $right ): int {
				$score = (int) ( $right['score'] ?? 0 ) <=> (int) ( $left['score'] ?? 0 );
				if ( 0 !== $score ) {
					return $score;
				}

				$left_title  = (string) ( $left['item']['title'] ?? '' );
				$right_title = (string) ( $right['item']['title'] ?? '' );
				return strcasecmp( $left_title, $right_title );
			}
		);

		$items = array_map(
			static fn( array $ranked_item ): array => (array) ( $ranked_item['item'] ?? [] ),
			array_slice( $ranked, 0, $limit )
		);

		return [
			'items' => $items,
			'total' => count( $items ),
		];
	}

	private static function score( string $search, string $title, string $abbreviation, string $synonyms ): int {
		$needle       = self::normalize( $search );
		$title_value  = self::normalize( $title );
		$abbreviation = self::normalize( $abbreviation );
		$synonym_list = self::synonyms( $synonyms );

		if ( $needle === $title_value ) {
			return 1000;
		}
		if ( '' !== $abbreviation && $needle === $abbreviation ) {
			return 950;
		}
		if ( in_array( $needle, $synonym_list, true ) ) {
			return 900;
		}
		if ( str_starts_with( $title_value, $needle ) ) {
			return 800;
		}
		if ( '' !== $abbreviation && str_starts_with( $abbreviation, $needle ) ) {
			return 750;
		}
		foreach ( $synonym_list as $synonym ) {
			if ( str_starts_with( $synonym, $needle ) ) {
				return 700;
			}
		}
		if ( str_contains( $title_value, $needle ) ) {
			return 600;
		}
		if ( '' !== $abbreviation && str_contains( $abbreviation, $needle ) ) {
			return 550;
		}
		foreach ( $synonym_list as $synonym ) {
			if ( str_contains( $synonym, $needle ) ) {
				return 500;
			}
		}

		return 100;
	}

	/** @return string[] */
	private static function synonyms( string $value ): array {
		$parts = preg_split( '/[\\r\\n,;|]+/u', $value ) ?: [];
		$items = [];
		foreach ( $parts as $part ) {
			$part = self::normalize( $part );
			if ( '' !== $part ) {
				$items[] = $part;
			}
		}
		return array_values( array_unique( $items ) );
	}

	private static function normalize( string $value ): string {
		$value = trim( remove_accents( $value ) );
		return function_exists( 'mb_strtolower' ) ? mb_strtolower( $value, 'UTF-8' ) : strtolower( $value );
	}
}
