<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders;

defined( 'ABSPATH' ) || exit;

/**
 * Builder availability/readiness detection stays inside the builder boundary.
 */
final class Readiness {
	public static function bricks_active(): bool {
		return defined( 'BRICKS_VERSION' );
	}
}
