<?php
declare(strict_types=1);

namespace CB\Dictionary\Content;

use CB\Dictionary\Settings;

defined( 'ABSPATH' ) || exit;

final class PostType {
	public const TYPE = 'cb_dictionary';

	public static function register(): void {
		register_post_type(
			self::TYPE,
			[
				'labels' => [
					'name'                     => __( 'Dictionary', 'core-blueprint-dictionary' ),
					'singular_name'            => __( 'Dictionary Entry', 'core-blueprint-dictionary' ),
					'menu_name'                => __( 'Dictionary', 'core-blueprint-dictionary' ),
					'name_admin_bar'           => __( 'Dictionary Entry', 'core-blueprint-dictionary' ),
					'add_new'                  => __( 'Add New', 'core-blueprint-dictionary' ),
					'add_new_item'             => __( 'Add New Entry', 'core-blueprint-dictionary' ),
					'new_item'                 => __( 'New Entry', 'core-blueprint-dictionary' ),
					'edit_item'                => __( 'Edit Entry', 'core-blueprint-dictionary' ),
					'view_item'                => __( 'View Entry', 'core-blueprint-dictionary' ),
					'view_items'               => __( 'View Dictionary', 'core-blueprint-dictionary' ),
					'all_items'                => __( 'All Entries', 'core-blueprint-dictionary' ),
					'search_items'             => __( 'Search Dictionary', 'core-blueprint-dictionary' ),
					'not_found'                => __( 'No dictionary entries found.', 'core-blueprint-dictionary' ),
					'not_found_in_trash'       => __( 'No dictionary entries found in Trash.', 'core-blueprint-dictionary' ),
					'archives'                 => __( 'Dictionary Archive', 'core-blueprint-dictionary' ),
					'attributes'               => __( 'Entry Attributes', 'core-blueprint-dictionary' ),
					'featured_image'           => __( 'Featured image', 'core-blueprint-dictionary' ),
					'set_featured_image'       => __( 'Set featured image', 'core-blueprint-dictionary' ),
					'remove_featured_image'    => __( 'Remove featured image', 'core-blueprint-dictionary' ),
					'use_featured_image'       => __( 'Use as featured image', 'core-blueprint-dictionary' ),
					'filter_items_list'        => __( 'Filter dictionary entries', 'core-blueprint-dictionary' ),
					'items_list_navigation'    => __( 'Dictionary list navigation', 'core-blueprint-dictionary' ),
					'items_list'               => __( 'Dictionary entries', 'core-blueprint-dictionary' ),
					'item_published'           => __( 'Dictionary entry published.', 'core-blueprint-dictionary' ),
					'item_published_privately' => __( 'Dictionary entry published privately.', 'core-blueprint-dictionary' ),
					'item_reverted_to_draft'   => __( 'Dictionary entry reverted to draft.', 'core-blueprint-dictionary' ),
					'item_scheduled'           => __( 'Dictionary entry scheduled.', 'core-blueprint-dictionary' ),
					'item_updated'             => __( 'Dictionary entry updated.', 'core-blueprint-dictionary' ),
				],
				'description'         => __( 'Digital dictionary entries managed by Core Blueprint Dictionary.', 'core-blueprint-dictionary' ),
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'show_in_rest'        => true,
				'rest_base'           => 'dictionary',
				'has_archive'         => true,
				'hierarchical'        => false,
				'exclude_from_search' => false,
				'menu_position'       => 26.5,
				'menu_icon'           => 'dashicons-book-alt',
				'rewrite'             => [
					'slug'       => Settings::rewrite_base(),
					'with_front' => false,
				],
				'supports' => [
					'title',
					'editor',
					'author',
					'thumbnail',
					'excerpt',
					'revisions',
					'custom-fields',
					'comments',
				],
				'map_meta_cap'     => true,
				'capability_type'  => 'post',
				'delete_with_user' => false,
			]
		);
	}
}
