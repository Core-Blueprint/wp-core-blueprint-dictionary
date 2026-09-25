<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks\Elements;

use CB\Dictionary\Frontend\Components\SearchResults as SearchResultsComponent;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class SearchResults extends \Bricks\Element {
	public $category = ElementRegistry::CATEGORY;
	public $name     = 'cb-dictionary-search-results';
	public $icon     = 'ti-list';

	public function get_label(): string {
		return esc_html__( 'Dictionary Search Results', 'core-blueprint-dictionary' );
	}

	/** @return string[] */
	public function get_keywords(): array {
		return [ 'core blueprint', 'dictionary', 'glossary', 'search', 'results' ];
	}

	public function set_control_groups(): void {
		$this->control_groups['results'] = [
			'title' => esc_html__( 'Results', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['items'] = [
			'title' => esc_html__( 'Result items', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
	}

	public function set_controls(): void {
		$this->controls['usage'] = [
			'tab'     => 'content',
			'group'   => 'results',
			'type'    => 'info',
			'content' => esc_html__( 'Pair this element with Dictionary Search by using the same source key. It renders server-side GET results now and is ready for live search enhancement.', 'core-blueprint-dictionary' ),
		];
		$this->controls['sourceKey'] = [
			'tab'         => 'content',
			'group'       => 'results',
			'label'       => esc_html__( 'Source key', 'core-blueprint-dictionary' ),
			'type'        => 'text',
			'default'     => 'default',
			'description' => esc_html__( 'Must match the Search element source key.', 'core-blueprint-dictionary' ),
		];
		$this->controls['limit'] = [
			'tab'     => 'content',
			'group'   => 'results',
			'label'   => esc_html__( 'Result limit', 'core-blueprint-dictionary' ),
			'type'    => 'number',
			'default' => 30,
			'min'     => 1,
			'max'     => 100,
			'step'    => 1,
		];
		$this->controls['showExcerpt'] = [
			'tab'     => 'content',
			'group'   => 'results',
			'label'   => esc_html__( 'Show excerpts', 'core-blueprint-dictionary' ),
			'type'    => 'checkbox',
			'default' => false,
		];
		$this->controls['showCount'] = [
			'tab'     => 'content',
			'group'   => 'results',
			'label'   => esc_html__( 'Show result count', 'core-blueprint-dictionary' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['titleTypography'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Title typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search-results__title' ] ],
		];
		$this->controls['excerptTypography'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Excerpt typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-search-results__excerpt' ] ],
		];
		$this->controls['itemBackground'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-search-results__item' ] ],
		];
		$this->controls['itemBorder'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-search-results__item' ] ],
		];
		$this->controls['itemPadding'] = [
			'tab'   => 'style',
			'group' => 'items',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-search-results__item' ] ],
		];
	}

	public function render(): void {
		$settings = $this->settings;
		$args = [
			'source'     => (string) ( $settings['sourceKey'] ?? 'default' ),
			'limit'      => $settings['limit'] ?? 30,
			'excerpt'    => self::checkbox( $settings, 'showExcerpt', false ),
			'show_count' => self::checkbox( $settings, 'showCount', false ),
		];

		$this->set_attribute( '_root', 'class', 'cb-dictionary-bricks-search-results' );
		echo '<div ' . $this->render_attributes( '_root' ) . '>' . SearchResultsComponent::render( $args ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted builder-neutral Dictionary renderer output.
	}

	/** @param array<string,mixed> $settings */
	private static function checkbox( array $settings, string $key, bool $default ): bool {
		if ( ! array_key_exists( $key, $settings ) ) {
			return $default;
		}
		return (bool) $settings[ $key ];
	}
}
