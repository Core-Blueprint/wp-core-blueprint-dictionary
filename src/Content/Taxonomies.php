<?php
declare(strict_types=1);

namespace CB\Dictionary\Content;

use CB\Dictionary\Settings;

defined( 'ABSPATH' ) || exit;

final class Taxonomies {
	public const CATEGORY = 'cb_dictionary_category';
	public const TAG      = 'cb_dictionary_tag';
	public const LETTER   = 'cb_dictionary_letter';

	public static function register(): void {
		$base = Settings::rewrite_base();

		register_taxonomy(
			self::CATEGORY,
			[ PostType::TYPE ],
			[
				'labels' => [
					'name'              => __( 'Dictionary Categories', 'core-blueprint-dictionary' ),
					'singular_name'     => __( 'Dictionary Category', 'core-blueprint-dictionary' ),
					'search_items'      => __( 'Search Dictionary Categories', 'core-blueprint-dictionary' ),
					'all_items'         => __( 'All Dictionary Categories', 'core-blueprint-dictionary' ),
					'parent_item'       => __( 'Parent Dictionary Category', 'core-blueprint-dictionary' ),
					'parent_item_colon' => __( 'Parent Dictionary Category:', 'core-blueprint-dictionary' ),
					'edit_item'         => __( 'Edit Dictionary Category', 'core-blueprint-dictionary' ),
					'update_item'       => __( 'Update Dictionary Category', 'core-blueprint-dictionary' ),
					'add_new_item'      => __( 'Add New Dictionary Category', 'core-blueprint-dictionary' ),
					'new_item_name'     => __( 'New Dictionary Category Name', 'core-blueprint-dictionary' ),
					'menu_name'         => __( 'Categories', 'core-blueprint-dictionary' ),
				],
				'public'            => true,
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rest_base'         => 'dictionary-categories',
				'rewrite'           => [ 'slug' => $base . '/category', 'with_front' => false ],
			]
		);

		register_taxonomy(
			self::TAG,
			[ PostType::TYPE ],
			[
				'labels' => [
					'name'                       => __( 'Dictionary Tags', 'core-blueprint-dictionary' ),
					'singular_name'              => __( 'Dictionary Tag', 'core-blueprint-dictionary' ),
					'search_items'               => __( 'Search Dictionary Tags', 'core-blueprint-dictionary' ),
					'popular_items'              => __( 'Popular Dictionary Tags', 'core-blueprint-dictionary' ),
					'all_items'                  => __( 'All Dictionary Tags', 'core-blueprint-dictionary' ),
					'edit_item'                  => __( 'Edit Dictionary Tag', 'core-blueprint-dictionary' ),
					'update_item'                => __( 'Update Dictionary Tag', 'core-blueprint-dictionary' ),
					'add_new_item'               => __( 'Add New Dictionary Tag', 'core-blueprint-dictionary' ),
					'new_item_name'              => __( 'New Dictionary Tag Name', 'core-blueprint-dictionary' ),
					'separate_items_with_commas' => __( 'Separate tags with commas', 'core-blueprint-dictionary' ),
					'add_or_remove_items'        => __( 'Add or remove tags', 'core-blueprint-dictionary' ),
					'choose_from_most_used'      => __( 'Choose from the most used tags', 'core-blueprint-dictionary' ),
					'menu_name'                  => __( 'Tags', 'core-blueprint-dictionary' ),
				],
				'public'            => true,
				'hierarchical'      => false,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'rest_base'         => 'dictionary-tags',
				'rewrite'           => [ 'slug' => $base . '/tag', 'with_front' => false ],
			]
		);

		register_taxonomy(
			self::LETTER,
			[ PostType::TYPE ],
			[
				'labels' => [
					'name'          => __( 'Dictionary Alphabet', 'core-blueprint-dictionary' ),
					'singular_name' => __( 'Dictionary Letter', 'core-blueprint-dictionary' ),
				],
				'public'             => true,
				'publicly_queryable' => true,
				'hierarchical'       => false,
				'show_ui'            => false,
				'show_admin_column'  => true,
				'show_in_rest'       => true,
				'rest_base'          => 'dictionary-letters',
				'query_var'          => true,
				'capabilities'       => [
					'manage_terms' => 'do_not_allow',
					'edit_terms'   => 'do_not_allow',
					'delete_terms' => 'do_not_allow',
					'assign_terms' => 'do_not_allow',
				],
				'rewrite' => [ 'slug' => $base . '/letter', 'with_front' => false ],
			]
		);
	}
}
