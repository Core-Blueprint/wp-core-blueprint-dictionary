<?php
declare(strict_types=1);

namespace CB\Dictionary\Support;

defined( 'ABSPATH' ) || exit;

final class Requirements {
	public static function api_compatible( string $available, string $required ): bool {
		if ( 1 !== preg_match( '/^(\d+)\.(\d+)$/', $available, $available_match ) ) {
			return false;
		}
		if ( 1 !== preg_match( '/^(\d+)\.(\d+)$/', $required, $required_match ) ) {
			return false;
		}

		return (int) $available_match[1] === (int) $required_match[1]
			&& (int) $available_match[2] >= (int) $required_match[2];
	}

	/** @return string[] */
	public static function bootstrap_issues(): array {
		$issues = [];

		if ( version_compare( PHP_VERSION, '8.4', '<' ) ) {
			$issues[] = 'php-version';
		}
		if ( ! defined( 'CB_CORE_API_VERSION' ) ) {
			$issues[] = 'base-missing';
			return $issues;
		}
		if ( ! self::api_compatible( (string) CB_CORE_API_VERSION, CB_DICTIONARY_REQUIRED_API ) ) {
			$issues[] = 'base-api-incompatible';
		}

		return $issues;
	}

	/** @return string[] */
	public static function product_issues(): array {
		if ( [] !== self::bootstrap_issues() ) {
			return [];
		}

		$required = [
			'\\CB\\Core\\ExtensionRegistry',
			'\\CB\\Core\\Admin\\SettingsRegistry',
			'\\CB\\Core\\Governance\\EventRegistry',
			'\\CB\\Core\\Governance\\Audit',
		];
		foreach ( $required as $class ) {
			if ( ! class_exists( $class ) ) {
				return [ 'base-contract-unavailable' ];
			}
		}

		return [];
	}

	public static function bootstrap_ready(): bool {
		return [] === self::bootstrap_issues();
	}

	public static function runtime_ready(): bool {
		return self::bootstrap_ready() && [] === self::product_issues();
	}

	public static function operator_message(): string {
		$issues = array_merge( self::bootstrap_issues(), self::product_issues() );
		$issue  = (string) ( $issues[0] ?? '' );

		switch ( $issue ) {
			case 'php-version':
				return sprintf( 'PHP 8.4 or newer is required. This server runs PHP %s.', PHP_VERSION );
			case 'base-missing':
				return 'An active Core Blueprint Base installation is required.';
			case 'base-api-incompatible':
				return sprintf(
					'Core API %1$s or a newer compatible minor version is required. This site provides %2$s.',
					CB_DICTIONARY_REQUIRED_API,
					defined( 'CB_CORE_API_VERSION' ) ? (string) CB_CORE_API_VERSION : 'none'
				);
			case 'base-contract-unavailable':
				return 'Required public Core Blueprint Base services are unavailable.';
			default:
				return 'Ready';
		}
	}
}
