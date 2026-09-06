<?php
declare(strict_types=1);

namespace CB\Dictionary\Admin;

use CB\Core\Admin\SettingsRegistry;
use CB\Core\UI\IntegrationGrid;
use CB\Core\UI\Notice;
use CB\Dictionary\Content\PostType;
use CB\Dictionary\Content\Taxonomies;
use CB\Dictionary\Integration\Suite;
use CB\Dictionary\Settings;

defined( 'ABSPATH' ) || exit;

final class SettingsPage {
	private const TAB_OVERVIEW     = 'overview';
	private const TAB_GENERAL      = 'general';
	private const TAB_INTEGRATIONS = 'integrations';

	public static function init(): void {
		add_action( 'cb_core_register_settings', [ __CLASS__, 'register' ] );
	}

	public static function register(): void {
		$page = new self();

		SettingsRegistry::register(
			Suite::ID,
			[
				'label'        => $page->menu_title(),
				'description'  => __( 'Manage Dictionary health, URL behavior, native content structure and optional integrations. Entries remain normal WordPress content and presentation stays builder-neutral.', 'core-blueprint-dictionary' ),
				'group'        => SettingsRegistry::GROUP_CONTENT_PUBLISHING,
				'capability'   => $page->capability(),
				'renderer'     => [ $page, 'render' ],
				'requirements' => [
					'components' => [
						'panels',
						'notices',
						'form-controls',
						'integration-grid',
						'metric-tiles',
						'nav-tabs',
						'status',
					],
				],
			]
		);
	}

	public function title(): string {
		return __( 'Dictionary', 'core-blueprint-dictionary' );
	}

	public function menu_title(): string {
		return __( 'Dictionary', 'core-blueprint-dictionary' );
	}

	public function capability(): string {
		return 'manage_options';
	}

	public function render(): void {
		if ( ! current_user_can( $this->capability() ) ) {
			wp_die( esc_html__( 'You do not have permission to access this page.', 'core-blueprint-dictionary' ) );
		}

		$tab = self::current_tab();
		?>
		<div class="wrap cb-core-wrap cb-core-page cb-dictionary-settings-wrap">
			<p class="cb-core-eyebrow"><?php esc_html_e( 'Core Blueprint', 'core-blueprint-dictionary' ); ?></p>
			<h1 class="cb-core-title"><?php esc_html_e( 'Dictionary', 'core-blueprint-dictionary' ); ?></h1>
			<p class="cb-core-intro"><?php esc_html_e( 'Manage Dictionary health, URL behavior, native content structure and optional integrations. Entries remain normal WordPress content and presentation stays builder-neutral.', 'core-blueprint-dictionary' ); ?></p>

			<?php self::render_tabs( $tab ); ?>

			<?php
			switch ( $tab ) {
				case self::TAB_GENERAL:
					self::render_general();
					break;
				case self::TAB_INTEGRATIONS:
					self::render_integrations();
					break;
				case self::TAB_OVERVIEW:
				default:
					self::render_overview();
					break;
			}
			?>
		</div>
		<?php
	}

	/** @param array<string,scalar> $query */
	public static function url( string $tab = self::TAB_OVERVIEW, array $query = [] ): string {
		if ( ! array_key_exists( $tab, self::tabs() ) ) {
			$tab = self::TAB_OVERVIEW;
		}

		$query['tab'] = $tab;
		return SettingsRegistry::url( Suite::ID, $query );
	}

	/** @return array<string,string> */
	private static function tabs(): array {
		return [
			self::TAB_OVERVIEW     => __( 'Overview', 'core-blueprint-dictionary' ),
			self::TAB_GENERAL      => __( 'General', 'core-blueprint-dictionary' ),
			self::TAB_INTEGRATIONS => __( 'Integrations', 'core-blueprint-dictionary' ),
		];
	}

	private static function current_tab(): string {
		$tab = isset( $_GET['tab'] )
			? sanitize_key( (string) wp_unslash( $_GET['tab'] ) )
			: self::TAB_OVERVIEW; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- navigation-only state.

		return array_key_exists( $tab, self::tabs() ) ? $tab : self::TAB_OVERVIEW;
	}

	private static function tab_url( string $tab ): string {
		return self::url( $tab );
	}

	private static function render_tabs( string $active_tab ): void {
		?>
		<nav class="nav-tab-wrapper cb-core-tab-wrapper" aria-label="<?php echo esc_attr__( 'Dictionary sections', 'core-blueprint-dictionary' ); ?>">
			<?php foreach ( self::tabs() as $key => $label ) : ?>
				<a class="nav-tab <?php echo $active_tab === $key ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( self::tab_url( $key ) ); ?>"<?php echo $active_tab === $key ? ' aria-current="page"' : ''; ?>><?php echo esc_html( $label ); ?></a>
			<?php endforeach; ?>
		</nav>
		<?php
	}

	private static function render_overview(): void {
		$counts     = wp_count_posts( PostType::TYPE );
		$published  = (int) ( $counts->publish ?? 0 );
		$drafts     = (int) ( $counts->draft ?? 0 );
		$categories = wp_count_terms( [
			'taxonomy'   => Taxonomies::CATEGORY,
			'hide_empty' => false,
		] );
		$tags = wp_count_terms( [
			'taxonomy'   => Taxonomies::TAG,
			'hide_empty' => false,
		] );
		$categories = is_wp_error( $categories ) ? 0 : (int) $categories;
		$tags       = is_wp_error( $tags ) ? 0 : (int) $tags;
		?>
		<div class="cb-core-tiles" aria-label="<?php echo esc_attr__( 'Dictionary at a glance', 'core-blueprint-dictionary' ); ?>">
			<a class="cb-core-tile cb-core-tile--metric cb-core-tile--navigation cb-core-tile--neutral" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . PostType::TYPE ) ); ?>">
				<span class="cb-core-tile__label"><?php esc_html_e( 'Published entries', 'core-blueprint-dictionary' ); ?></span>
				<strong class="cb-core-tile__value"><?php echo esc_html( number_format_i18n( $published ) ); ?></strong>
			</a>
			<a class="cb-core-tile cb-core-tile--metric cb-core-tile--navigation cb-core-tile--neutral" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . PostType::TYPE . '&post_status=draft' ) ); ?>">
				<span class="cb-core-tile__label"><?php esc_html_e( 'Drafts', 'core-blueprint-dictionary' ); ?></span>
				<strong class="cb-core-tile__value"><?php echo esc_html( number_format_i18n( $drafts ) ); ?></strong>
			</a>
			<a class="cb-core-tile cb-core-tile--metric cb-core-tile--navigation cb-core-tile--neutral" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=' . Taxonomies::CATEGORY . '&post_type=' . PostType::TYPE ) ); ?>">
				<span class="cb-core-tile__label"><?php esc_html_e( 'Categories', 'core-blueprint-dictionary' ); ?></span>
				<strong class="cb-core-tile__value"><?php echo esc_html( number_format_i18n( $categories ) ); ?></strong>
			</a>
			<a class="cb-core-tile cb-core-tile--metric cb-core-tile--navigation cb-core-tile--neutral" href="<?php echo esc_url( admin_url( 'edit-tags.php?taxonomy=' . Taxonomies::TAG . '&post_type=' . PostType::TYPE ) ); ?>">
				<span class="cb-core-tile__label"><?php esc_html_e( 'Tags', 'core-blueprint-dictionary' ); ?></span>
				<strong class="cb-core-tile__value"><?php echo esc_html( number_format_i18n( $tags ) ); ?></strong>
			</a>
		</div>

		<section class="cb-core-panel">
			<h2><?php esc_html_e( 'Dictionary content', 'core-blueprint-dictionary' ); ?></h2>
			<p><?php esc_html_e( 'Entries, Categories and Tags remain on their normal WordPress content screens. Use this Core Admin page for extension-wide configuration and integration readiness.', 'core-blueprint-dictionary' ); ?></p>
			<p><a class="button cb-core-button cb-core-button--secondary" href="<?php echo esc_url( admin_url( 'edit.php?post_type=' . PostType::TYPE ) ); ?>"><?php esc_html_e( 'Manage entries', 'core-blueprint-dictionary' ); ?></a></p>
		</section>
		<?php
	}

	private static function render_general(): void {
		$rewrite_base = Settings::rewrite_base();
		$example      = home_url( '/' . $rewrite_base . '/example-term/' );
		$updated      = isset( $_GET['cb_dictionary_updated'] )
			? sanitize_key( (string) wp_unslash( $_GET['cb_dictionary_updated'] ) )
			: ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- display-only redirect state.

		if ( 'changed' === $updated ) {
			self::render_feedback(
				'success',
				__( 'URL base updated', 'core-blueprint-dictionary' ),
				__( 'The new Dictionary URL base is active. WordPress rewrite rules were refreshed once after the new routes were registered.', 'core-blueprint-dictionary' )
			);
		} elseif ( 'unchanged' === $updated ) {
			self::render_feedback(
				'info',
				__( 'No changes needed', 'core-blueprint-dictionary' ),
				__( 'The Dictionary URL base already had this value, so no rewrite refresh was necessary.', 'core-blueprint-dictionary' )
			);
		}
		?>
		<section class="cb-core-panel">
			<h2><?php esc_html_e( 'Permalinks', 'core-blueprint-dictionary' ); ?></h2>
			<p><?php esc_html_e( 'Choose the URL base used by the Dictionary archive, individual entries and Dictionary taxonomy archives. Changing this later changes public URLs, so existing external links may need redirects.', 'core-blueprint-dictionary' ); ?></p>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
				<input type="hidden" name="action" value="cb_dictionary_save_settings">
				<?php wp_nonce_field( 'cb_dictionary_save_settings', 'cb_dictionary_settings_nonce' ); ?>
				<p><label for="cb_dictionary_rewrite_base"><strong><?php esc_html_e( 'Dictionary URL base', 'core-blueprint-dictionary' ); ?></strong></label><br><input class="regular-text" type="text" id="cb_dictionary_rewrite_base" name="rewrite_base" value="<?php echo esc_attr( $rewrite_base ); ?>" placeholder="dictionary" autocomplete="off"></p>
				<p class="description"><?php esc_html_e( 'Use a slug such as dictionary, glossary, woordenboek or begrippen. Nested paths such as academy/dictionary are also supported. Empty or invalid input falls back to dictionary.', 'core-blueprint-dictionary' ); ?></p>
				<p class="description"><strong><?php esc_html_e( 'Example:', 'core-blueprint-dictionary' ); ?></strong> <code><?php echo esc_html( $example ); ?></code></p>
				<?php submit_button( __( 'Save URL base', 'core-blueprint-dictionary' ) ); ?>
			</form>
		</section>

		<section class="cb-core-panel">
			<h2><?php esc_html_e( 'Alphabet', 'core-blueprint-dictionary' ); ?></h2>
			<p><?php esc_html_e( 'Dictionary maintains a fixed 0-9 and A-Z taxonomy automatically. Users manage Categories and Tags themselves; the Alphabet taxonomy is machine-owned and is assigned from each entry title.', 'core-blueprint-dictionary' ); ?></p>
		</section>
		<?php
	}

	private static function render_integrations(): void {
		if ( ! class_exists( IntegrationGrid::class ) ) {
			self::render_feedback(
				'warning',
				__( 'Integration readiness unavailable', 'core-blueprint-dictionary' ),
				__( 'Dictionary is active, but this Core Blueprint Base build does not expose the shared IntegrationGrid presentation contract. Update Base to view integration readiness on this tab.', 'core-blueprint-dictionary' )
			);
			return;
		}

		echo IntegrationGrid::render( IntegrationReadiness::items() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Base IntegrationGrid owns escaping and presentation.
	}

	private static function render_feedback( string $variant, string $title, string $message ): void {
		if ( class_exists( Notice::class ) ) {
			echo Notice::render( [
				'variant' => $variant,
				'title'   => $title,
				'message' => $message,
			] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Base Notice owns escaping and presentation.
			return;
		}

		$wp_variant = match ( $variant ) {
			'success' => 'notice-success',
			'warning' => 'notice-warning',
			'error'   => 'notice-error',
			default   => 'notice-info',
		};

		printf(
			'<div class="notice %1$s"><p><strong>%2$s</strong> %3$s</p></div>',
			esc_attr( $wp_variant ),
			esc_html( $title ),
			esc_html( $message )
		);
	}
}
