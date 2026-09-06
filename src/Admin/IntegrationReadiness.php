<?php
declare(strict_types=1);

namespace CB\Dictionary\Admin;

use CB\Core\UI\IntegrationGrid;
use CB\Dictionary\Integration\Builders\Readiness as BuilderReadiness;

defined( 'ABSPATH' ) || exit;

/**
 * Dictionary-owned integration meaning and customer-facing readiness copy.
 *
 * Base owns IntegrationGrid presentation; builder detection remains inside the
 * builder integration boundary.
 */
final class IntegrationReadiness {
	/** @return array<int,array<string,mixed>> */
	public static function items(): array {
		return [ self::bricks_item() ];
	}

	/** @return array<string,mixed> */
	private static function bricks_item(): array {
		$active = BuilderReadiness::bricks_active();

		return [
			'name'         => __( 'Bricks Builder', 'core-blueprint-dictionary' ),
			'description'  => $active
				? __( 'Bricks is active. Dictionary dynamic data, queries and conditions are available through the optional adapter while Dictionary data and domain logic remain builder-neutral.', 'core-blueprint-dictionary' )
				: __( 'Bricks is optional. Dictionary works without a builder through native WordPress content, shortcodes and builder-neutral frontend contracts.', 'core-blueprint-dictionary' ),
			'status'       => $active ? IntegrationGrid::READY : IntegrationGrid::OPTIONAL,
			'status_label' => $active
				? __( 'Ready', 'core-blueprint-dictionary' )
				: __( 'Not active', 'core-blueprint-dictionary' ),
		];
	}
}
