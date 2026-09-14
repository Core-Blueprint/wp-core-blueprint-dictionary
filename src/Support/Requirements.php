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

	/** @return string[] Stable Bootstrap v1 issue IDs. */
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

	/** @return string[] Product blockers intentionally outside Bootstrap v1. */
	public static function product_issues(): array {
		if ( ! self::runtime_ready() ) {
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

	/** Generic Bootstrap v1 readiness only: PHP, Base presence and Core API. */
	public static function runtime_ready(): bool {
		return self::bootstrap_ready();
	}

	public static function product_ready(): bool {
		return self::runtime_ready() && [] === self::product_issues();
	}

	/** Canonical untranslated activation explanation. */
	public static function activation_message(): string {
		$issue = self::primary_issue( self::bootstrap_issues() );

		switch ( $issue ) {
			case 'php-version':
				return sprintf( 'PHP %1$s or newer is required. This server runs PHP %2$s.', '8.4', PHP_VERSION );
			case 'base-missing':
				return 'Core Blueprint must be installed and active.';
			case 'base-api-incompatible':
				return sprintf(
					'Core API %1$s or a newer compatible minor version is required. Available Core API: %2$s.',
					CB_DICTIONARY_REQUIRED_API,
					defined( 'CB_CORE_API_VERSION' ) ? (string) CB_CORE_API_VERSION : 'none'
				);
			default:
				return 'Ready';
		}
	}

	/** Operator-facing Bootstrap v1 explanation. */
	public static function operator_message(): string {
		$issue = self::primary_issue( self::bootstrap_issues() );

		switch ( $issue ) {
			case 'php-version':
				return sprintf(
					__( 'PHP %1$s or newer is required. This server runs PHP %2$s.', 'core-blueprint-dictionary' ),
					'8.4',
					PHP_VERSION
				);
			case 'base-missing':
				return __( 'Core Blueprint must be installed and active.', 'core-blueprint-dictionary' );
			case 'base-api-incompatible':
				return sprintf(
					__( 'Core API %1$s or a newer compatible minor version is required. Available Core API: %2$s.', 'core-blueprint-dictionary' ),
					CB_DICTIONARY_REQUIRED_API,
					defined( 'CB_CORE_API_VERSION' ) ? (string) CB_CORE_API_VERSION : __( 'none', 'core-blueprint-dictionary' )
				);
			default:
				return __( 'Ready', 'core-blueprint-dictionary' );
		}
	}

	public static function product_activation_message(): string {
		return [] === self::product_issues()
			? 'Ready'
			: 'Required Core Blueprint Base contracts are unavailable.';
	}

	public static function product_operator_message(): string {
		return [] === self::product_issues()
			? __( 'Ready', 'core-blueprint-dictionary' )
			: __( 'Required Core Blueprint Base contracts are unavailable.', 'core-blueprint-dictionary' );
	}

	/** @param string[] $issues */
	private static function primary_issue( array $issues ): string {
		return (string) ( $issues[0] ?? '' );
	}
}
