<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend;

use CB\Dictionary\Content\Taxonomies;

defined( 'ABSPATH' ) || exit;

final class ArchiveContext {
	/**
	 * Explicit filters win as a set. Archive context is only used when all
	 * explicit Dictionary taxonomy filters are empty.
	 *
	 * @return array{category:string,tag:string,letter:string}
	 */
	public static function taxonomy_filters( string $category = '', string $tag = '', string $letter = '' ): array {
		$filters = [
			'category' => sanitize_title( $category ),
			'tag'      => sanitize_title( $tag ),
			'letter'   => sanitize_title( $letter ),
		];

		if ( '' !== $filters['category'] || '' !== $filters['tag'] || '' !== $filters['letter'] ) {
			return $filters;
		}

		$queried = get_queried_object();
		if ( ! $queried instanceof \WP_Term ) {
			return $filters;
		}

		$slug = sanitize_title( (string) $queried->slug );
		if ( '' === $slug ) {
			return $filters;
		}

		if ( Taxonomies::CATEGORY === $queried->taxonomy ) {
			$filters['category'] = $slug;
		} elseif ( Taxonomies::TAG === $queried->taxonomy ) {
			$filters['tag'] = $slug;
		} elseif ( Taxonomies::LETTER === $queried->taxonomy ) {
			$filters['letter'] = $slug;
		}

		return $filters;
	}
}
