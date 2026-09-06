<?php
/**
 * Plugin Name:       Core Blueprint Dictionary
 * Plugin URI:        https://coreblueprint.io
 * Description:       Lightweight builder-agnostic digital dictionary with native WordPress content and taxonomies.
 * Version:           0.1.0-rc1
 * Author:            Core Blueprint
 * Author URI:        https://coreblueprint.io
 * License:           GPL-2.0+
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       core-blueprint-dictionary
 * Domain Path:       /languages
 * Requires at least: 7.0
 * Requires PHP:      8.4
 *
 * @package CB_Dictionary
 */

declare(strict_types=1);

defined( 'ABSPATH' ) || exit;

define( 'CB_DICTIONARY_VERSION',      '0.1.0-rc1' );
define( 'CB_DICTIONARY_REQUIRED_API', '1.0' );
define( 'CB_DICTIONARY_FILE',         __FILE__ );
define( 'CB_DICTIONARY_DIR',          plugin_dir_path( __FILE__ ) );
define( 'CB_DICTIONARY_URL',          plugin_dir_url( __FILE__ ) );
define( 'CB_DICTIONARY_BASENAME',     plugin_basename( __FILE__ ) );

spl_autoload_register( static function ( string $class ): void {
	$prefix = 'CB\\Dictionary\\';
	$length = strlen( $prefix );

	if ( 0 !== strncmp( $class, $prefix, $length ) ) {
		return;
	}

	$relative = substr( $class, $length );
	$file     = CB_DICTIONARY_DIR . 'src/' . str_replace( '\\', '/', $relative ) . '.php';

	if ( is_file( $file ) ) {
		require_once $file;
	}
} );

add_action( 'init', static function (): void {
	load_plugin_textdomain(
		'core-blueprint-dictionary',
		false,
		dirname( CB_DICTIONARY_BASENAME ) . '/languages'
	);
}, 1 );

function cb_dictionary_api_compatible( string $available, string $required ): bool {
	if ( 1 !== preg_match( '/^(\\d+)\\.(\\d+)$/', $available, $available_match ) ) {
		return false;
	}
	if ( 1 !== preg_match( '/^(\\d+)\\.(\\d+)$/', $required, $required_match ) ) {
		return false;
	}

	return (int) $available_match[1] === (int) $required_match[1]
		&& (int) $available_match[2] >= (int) $required_match[2];
}

function cb_dictionary_base_ready(): bool {
	if ( ! defined( 'CB_CORE_API_VERSION' ) ) {
		return false;
	}
	if ( ! cb_dictionary_api_compatible( (string) CB_CORE_API_VERSION, CB_DICTIONARY_REQUIRED_API ) ) {
		return false;
	}

	return class_exists( '\\CB\\Core\\ExtensionRegistry' )
		&& class_exists( '\\CB\\Core\\Admin\\PageRegistry' )
		&& interface_exists( '\\CB\\Core\\Admin\\Page' )
		&& class_exists( '\\CB\\Core\\UI\\Notice' )
		&& class_exists( '\\CB\\Core\\UI\\IntegrationGrid' )
		&& class_exists( '\\CB\\Core\\Governance\\EventRegistry' )
		&& class_exists( '\\CB\\Core\\Governance\\Audit' );
}

function cb_dictionary_dependency_message(): string {
	if ( ! defined( 'CB_CORE_API_VERSION' ) ) {
		return __( 'Core Blueprint Dictionary requires an active Core Blueprint Base plugin.', 'core-blueprint-dictionary' );
	}

	if ( ! cb_dictionary_api_compatible( (string) CB_CORE_API_VERSION, CB_DICTIONARY_REQUIRED_API ) ) {
		return sprintf(
			/* translators: 1: required Core API version, 2: available Core API version. */
			__( 'Core Blueprint Dictionary requires Core API %1$s or a newer compatible minor version. This site provides %2$s.', 'core-blueprint-dictionary' ),
			CB_DICTIONARY_REQUIRED_API,
			(string) CB_CORE_API_VERSION
		);
	}

	return __( 'Core Blueprint Dictionary cannot access the required public Base contracts.', 'core-blueprint-dictionary' );
}

function cb_dictionary_activate(): void {
	if ( ! cb_dictionary_base_ready() ) {
		if ( ! function_exists( 'deactivate_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		deactivate_plugins( CB_DICTIONARY_BASENAME );
		wp_die(
			esc_html( 'Core Blueprint Dictionary requires an active, Core API 1.x compatible Core Blueprint Base installation.' ),
			esc_html( 'Core Blueprint dependency required' ),
			[ 'back_link' => true ]
		);
	}

	\CB\Dictionary\Install::activate();
}
register_activation_hook( __FILE__, 'cb_dictionary_activate' );
register_deactivation_hook( __FILE__, [ '\\CB\\Dictionary\\Install', 'deactivate' ] );

add_action( 'plugins_loaded', static function (): void {
	if ( ! cb_dictionary_base_ready() ) {
		if ( is_admin() ) {
			add_action( 'admin_notices', static function (): void {
				if ( ! current_user_can( 'activate_plugins' ) ) {
					return;
				}

				printf(
					'<div class="notice notice-error"><p><strong>%s</strong> %s</p></div>',
					esc_html__( 'Core Blueprint Dictionary:', 'core-blueprint-dictionary' ),
					esc_html( cb_dictionary_dependency_message() )
				);
			} );
		}
		return;
	}

	\CB\Dictionary\Plugin::boot();
}, 30 );
