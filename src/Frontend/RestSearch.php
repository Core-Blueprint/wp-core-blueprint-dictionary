<?php
declare(strict_types=1);

namespace CB\Dictionary\Frontend;

defined( 'ABSPATH' ) || exit;

final class RestSearch {
	public const NAMESPACE = 'cb-dictionary/v1';
	public const ROUTE     = '/search';

	public static function init(): void {
		add_action( 'rest_api_init', [ __CLASS__, 'register' ] );
	}

	public static function register(): void {
		register_rest_route(
			self::NAMESPACE,
			self::ROUTE,
			[
				'methods'             => \WP_REST_Server::READABLE,
				'callback'            => [ __CLASS__, 'search' ],
				'permission_callback' => '__return_true',
				'args'                => [
					'q' => [
						'required'          => true,
						'sanitize_callback' => 'sanitize_text_field',
					],
					'limit' => [
						'default'           => 30,
						'sanitize_callback' => 'absint',
					],
				],
			]
		);
	}

	public static function search( \WP_REST_Request $request ): \WP_REST_Response {
		$query = sanitize_text_field( trim( (string) $request->get_param( 'q' ) ) );
		$limit = max( 1, min( 100, absint( $request->get_param( 'limit' ) ) ?: 30 ) );
		$result = Search::entries( $query, $limit );

		$items = [];
		foreach ( $result['items'] as $item ) {
			$items[] = [
				'id'           => absint( $item['id'] ?? 0 ),
				'title'        => sanitize_text_field( (string) ( $item['title'] ?? '' ) ),
				'permalink'    => esc_url_raw( (string) ( $item['permalink'] ?? '' ) ),
				'excerpt'      => wp_strip_all_tags( (string) ( $item['excerpt'] ?? '' ), true ),
				'abbreviation' => sanitize_text_field( (string) ( $item['abbreviation'] ?? '' ) ),
			];
		}

		$total = count( $items );
		$response = new \WP_REST_Response(
			[
				'items'       => $items,
				'total'       => $total,
				'count_label' => sprintf(
					/* translators: %d: number of dictionary search results. */
					_n( '%d result', '%d results', $total, 'core-blueprint-dictionary' ),
					$total
				),
			],
			200
		);
		$response->header( 'Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0' );
		$response->header( 'Vary', 'Cookie' );
		return $response;
	}
}
