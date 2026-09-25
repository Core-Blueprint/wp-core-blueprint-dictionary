<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders;

use CB\Dictionary\Integration\Builders\Bricks\Bootstrap as BricksBootstrap;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class Bootstrap {
	private static bool $registered = false;
	private static bool $booted = false;

	public static function init(): void {
		if ( self::$registered ) {
			return;
		}
		self::$registered = true;

		add_action( 'init', [ ElementRegistry::class, 'register' ], 11 );
		add_action( 'init', [ self::class, 'boot' ], 30 );
	}

	public static function boot(): void {
		if ( self::$booted ) {
			return;
		}
		if ( ! defined( 'BRICKS_VERSION' ) && ! class_exists( '\\Bricks\\Query' ) ) {
			return;
		}

		self::$booted = true;
		BricksBootstrap::init();
	}
}
