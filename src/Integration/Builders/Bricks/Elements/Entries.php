<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks\Elements;

use CB\Dictionary\Frontend\Components\Entries as EntriesComponent;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class Entries extends \Bricks\Element {
	public $category = ElementRegistry::CATEGORY;
	public $name     = 'cb-dictionary-entries';
	public $icon     = 'ti-list';

	public function get_label(): string {
		return esc_html__( 'Dictionary Entries', 'core-blueprint-dictionary' );
	}

	/** @return string[] */
	public function get_keywords(): array {
		return [ 'core blueprint', 'dictionary', 'entries', 'terms', 'glossary' ];
	}

	public function set_control_groups(): void {
		$this->control_groups['query'] = [
			'title' => esc_html__( 'Entries', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['layout'] = [
			'title' => esc_html__( 'List layout', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['items'] = [
			'title' => esc_html__( 'Items', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['empty'] = [
			'title' => esc_html__( 'Empty state', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
	}

	public function set_controls(): void {
		foreach ( [
			'category' => esc_html__( 'Category slug', 'core-blueprint-dictionary' ),
			'tag'      => esc_html__( 'Tag slug', 'core-blueprint-dictionary' ),
			'letter'   => esc_html__( 'Letter slug', 'core-blueprint-dictionary' ),
		] as $key => $label ) {
			$this->controls[ $key ] = [
				'tab'   => 'content',
				'group' => 'query',
				'label' => $label,
				'type'  => 'text',
			];
		}
		$this->controls['limit'] = [
			'tab'     => 'content',
			'group'   => 'query',
			'label'   => esc_html__( 'Entry limit', 'core-blueprint-dictionary' ),
			'type'    => 'number',
			'default' => 50,
			'min'     => 1,
			'max'     => 100,
			'step'    => 1,
		];
		$this->controls['showExcerpt'] = [
			'tab'     => 'content',
			'group'   => 'query',
			'label'   => esc_html__( 'Show excerpts', 'core-blueprint-dictionary' ),
			'type'    => 'checkbox',
			'default' => true,
		];

		$this->controls['listDisplay'] = [
			'tab'     => 'content',
			'group'   => 'layout',
			'label'   => esc_html__( 'Display', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				'grid'  => 'grid',
				'flex'  => 'flex',
				'block' => 'block',
			],
			'css'     => [ [ 'property' => 'display', 'selector' => '.cb-dictionary-list__items' ] ],
		];
		$this->controls['listStyleType'] = [
			'tab'     => 'content',
			'group'   => 'layout',
			'label'   => esc_html__( 'List marker', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [
				''                     => esc_html__( 'Browser default', 'core-blueprint-dictionary' ),
				'none'                 => esc_html__( 'None', 'core-blueprint-dictionary' ),
				'disc'                 => esc_html__( 'Disc', 'core-blueprint-dictionary' ),
				'circle'               => esc_html__( 'Circle', 'core-blueprint-dictionary' ),
				'square'               => esc_html__( 'Square', 'core-blueprint-dictionary' ),
				'decimal'              => esc_html__( 'Decimal', 'core-blueprint-dictionary' ),
				'decimal-leading-zero' => esc_html__( 'Decimal leading zero', 'core-blueprint-dictionary' ),
				'lower-alpha'          => esc_html__( 'Lower alpha', 'core-blueprint-dictionary' ),
				'upper-alpha'          => esc_html__( 'Upper alpha', 'core-blueprint-dictionary' ),
				'lower-roman'          => esc_html__( 'Lower roman', 'core-blueprint-dictionary' ),
				'upper-roman'          => esc_html__( 'Upper roman', 'core-blueprint-dictionary' ),
			],
			'default' => '',
			'css'     => [ [ 'property' => 'list-style-type', 'selector' => '.cb-dictionary-list__items' ] ],
		];
		$this->controls['listMargin'] = [
			'tab'   => 'content',
			'group' => 'layout',
			'label' => esc_html__( 'List margin', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'margin', 'selector' => '.cb-dictionary-list__items' ] ],
		];
		$this->controls['listPadding'] = [
			'tab'   => 'content',
			'group' => 'layout',
			'label' => esc_html__( 'List padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-list__items' ] ],
		];

		$this->controls['listColumns'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Grid columns', 'core-blueprint-dictionary' ),
			'type'     => 'select',
			'options'  => [
				'1fr'                       => '1',
				'repeat(2, minmax(0, 1fr))' => '2',
				'repeat(3, minmax(0, 1fr))' => '3',
				'repeat(4, minmax(0, 1fr))' => '4',
			],
			'css'      => [ [ 'property' => 'grid-template-columns', 'selector' => '.cb-dictionary-list__items' ] ],
			'required' => [ 'listDisplay', '=', 'grid' ],
		];
		$this->controls['listGap'] = [
			'tab'   => 'content',
			'group' => 'layout',
			'label' => esc_html__( 'Gap', 'core-blueprint-dictionary' ),
			'type'  => 'slider',
			'css'   => [ [ 'property' => 'gap', 'selector' => '.cb-dictionary-list__items' ] ],
			'required' => [ 'listDisplay', '=', [ 'flex', 'grid' ] ],
		];

		$this->controls['linkTypography'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Title typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-list__link' ] ],
		];
		$this->controls['excerptTypography'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Excerpt typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-list__excerpt' ] ],
			'required' => [ 'showExcerpt', '=', true ],
		];
		$this->controls['itemBackground'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'exclude' => [ 'videoUrl', 'videoScale' ],
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-list__item' ] ],
		];
		$this->controls['itemBorder'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-list__item' ] ],
		];
		$this->controls['itemPadding'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-list__item' ] ],
		];
		$this->controls['itemShadow'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Box shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-list__item' ] ],
		];
		$this->controls['itemHoverBackground'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Hover background color', 'core-blueprint-dictionary' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'background-color', 'selector' => '.cb-dictionary-list__item:hover' ] ],
		];
		$this->controls['linkHoverColor'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Title hover color', 'core-blueprint-dictionary' ),
			'type'  => 'color',
			'css'   => [ [ 'property' => 'color', 'selector' => '.cb-dictionary-list__link:hover' ] ],
		];
		$this->controls['excerptSpacing'] = [
			'tab'   => 'content',
			'group' => 'items',
			'label' => esc_html__( 'Excerpt margin', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'margin', 'selector' => '.cb-dictionary-list__excerpt' ] ],
			'required' => [ 'showExcerpt', '=', true ],
		];

		$this->controls['emptyTypography'] = [
			'tab'   => 'content',
			'group' => 'empty',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-list--empty' ] ],
		];
		$this->controls['emptyBackground'] = [
			'tab'   => 'content',
			'group' => 'empty',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'exclude' => [ 'videoUrl', 'videoScale' ],
			'css'   => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-list--empty' ] ],
		];
		$this->controls['emptyBorder'] = [
			'tab'   => 'content',
			'group' => 'empty',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-list--empty' ] ],
		];
		$this->controls['emptyPadding'] = [
			'tab'   => 'content',
			'group' => 'empty',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-list--empty' ] ],
		];
	}

	public function render(): void {
		$settings = $this->settings;
		$args = [
			'category' => (string) ( $settings['category'] ?? '' ),
			'tag'      => (string) ( $settings['tag'] ?? '' ),
			'letter'   => (string) ( $settings['letter'] ?? '' ),
			'limit'    => $settings['limit'] ?? 50,
			'excerpt'  => array_key_exists( 'showExcerpt', $settings ) ? (bool) $settings['showExcerpt'] : true,
		];

		$this->set_attribute( '_root', 'class', 'cb-dictionary-bricks-entries' );
		echo '<div ' . $this->render_attributes( '_root' ) . '>' . EntriesComponent::render( $args ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted builder-neutral Dictionary renderer output.
	}
}
