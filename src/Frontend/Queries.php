<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend;

use CB\Dictionary\Content\PostType;
use CB\Dictionary\Content\Taxonomies;

defined( 'ABSPATH' ) || exit;

final class Queries {
	/** @param array<string,mixed> $args */
	public static function entries( array $args = [] ): \WP_Query {
		$defaults = [
			'post_type'           => PostType::TYPE,
			'post_status'         => 'publish',
			'posts_per_page'      => 20,
			'orderby'             => 'title',
			'order'               => 'ASC',
			'ignore_sticky_posts' => true,
			'suppress_filters'    => false,
		];
		return new \WP_Query( array_merge( $defaults, $args ) );
	}

	/** @return array<string,mixed> */
	public static function taxonomy_filter( string $category = '', string $tag = '', string $letter = '' ): array {
		$tax_query = [];
		foreach ( [ Taxonomies::CATEGORY => $category, Taxonomies::TAG => $tag, Taxonomies::LETTER => $letter ] as $taxonomy => $slug ) {
			$slug = sanitize_title( $slug );
			if ( '' !== $slug ) {
				$tax_query[] = [ 'taxonomy' => $taxonomy, 'field' => 'slug', 'terms' => [ $slug ] ];
			}
		}
		if ( count( $tax_query ) > 1 ) {
			$tax_query['relation'] = 'AND';
		}
		return $tax_query;
	}
}
