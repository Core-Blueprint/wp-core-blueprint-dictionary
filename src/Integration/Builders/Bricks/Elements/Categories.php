<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks\Elements;

use CB\Dictionary\Frontend\Components\Categories as CategoriesComponent;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class Categories extends \Bricks\Element {
	public $category = ElementRegistry::CATEGORY;
	public $name     = 'cb-dictionary-categories';
	public $icon     = 'ti-folder';

	public function get_label(): string {
		return esc_html__( 'Dictionary Categories', 'core-blueprint-dictionary' );
	}

	/** @return string[] */
	public function get_keywords(): array {
		return [ 'core blueprint', 'dictionary', 'categories', 'taxonomy', 'glossary' ];
	}

	public function set_control_groups(): void {
		foreach ( [
			'categories' => esc_html__( 'Categories', 'core-blueprint-dictionary' ),
			'layout'     => esc_html__( 'List layout', 'core-blueprint-dictionary' ),
			'links'      => esc_html__( 'Category links', 'core-blueprint-dictionary' ),
			'current'    => esc_html__( 'Current category', 'core-blueprint-dictionary' ),
			'empty'      => esc_html__( 'Empty categories', 'core-blueprint-dictionary' ),
		] as $key => $title ) {
			$this->control_groups[ $key ] = [ 'title' => $title, 'tab' => 'content' ];
		}
	}

	public function set_controls(): void {
		$this->controls['showEmpty'] = [
			'tab'     => 'content',
			'group'   => 'categories',
			'label'   => esc_html__( 'Show empty categories', 'core-blueprint-dictionary' ),
			'type'    => 'checkbox',
			'default' => false,
		];

		$this->controls['listDisplay'] = [
			'tab'     => 'content',
			'group'   => 'layout',
			'label'   => esc_html__( 'Display', 'core-blueprint-dictionary' ),
			'type'    => 'select',
			'options' => [ 'block' => 'block', 'flex' => 'flex', 'grid' => 'grid' ],
			'css'     => [ [ 'property' => 'display', 'selector' => '.cb-dictionary-categories__items' ] ],
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
			'css'     => [ [ 'property' => 'list-style-type', 'selector' => '.cb-dictionary-categories__items' ] ],
		];
		foreach ( [ 'listMargin' => [ 'List margin', 'margin' ], 'listPadding' => [ 'List padding', 'padding' ] ] as $name => [ $label, $property ] ) {
			$this->controls[ $name ] = [
				'tab'   => 'content',
				'group' => 'layout',
				'label' => esc_html__( $label, 'core-blueprint-dictionary' ),
				'type'  => 'dimensions',
				'css'   => [ [ 'property' => $property, 'selector' => '.cb-dictionary-categories__items' ] ],
			];
		}
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
			'css'      => [ [ 'property' => 'grid-template-columns', 'selector' => '.cb-dictionary-categories__items' ] ],
			'required' => [ 'listDisplay', '=', 'grid' ],
		];
		$this->controls['listFlexWrap'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Flex wrap', 'core-blueprint-dictionary' ),
			'type'     => 'select',
			'options'  => [
				'nowrap'       => esc_html__( 'No wrap', 'core-blueprint-dictionary' ),
				'wrap'         => esc_html__( 'Wrap', 'core-blueprint-dictionary' ),
				'wrap-reverse' => esc_html__( 'Wrap reverse', 'core-blueprint-dictionary' ),
			],
			'inline'   => true,
			'css'      => [ [ 'property' => 'flex-wrap', 'selector' => '.cb-dictionary-categories__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		$this->controls['listDirection'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Direction', 'core-blueprint-dictionary' ),
			'type'     => 'direction',
			'inline'   => true,
			'rerender' => true,
			'css'      => [ [ 'property' => 'flex-direction', 'selector' => '.cb-dictionary-categories__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		$this->controls['listJustifyContent'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Align main axis', 'core-blueprint-dictionary' ),
			'type'     => 'justify-content',
			'css'      => [ [ 'property' => 'justify-content', 'selector' => '.cb-dictionary-categories__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		$this->controls['listAlignItems'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Align cross axis', 'core-blueprint-dictionary' ),
			'type'     => 'align-items',
			'css'      => [ [ 'property' => 'align-items', 'selector' => '.cb-dictionary-categories__items' ] ],
			'required' => [ 'listDisplay', '=', 'flex' ],
		];
		foreach ( [ 'listColumnGap' => [ 'Column gap', 'column-gap' ], 'listRowGap' => [ 'Row gap', 'row-gap' ] ] as $name => [ $label, $property ] ) {
			$this->controls[ $name ] = [
				'tab'      => 'content',
				'group'    => 'layout',
				'label'    => esc_html__( $label, 'core-blueprint-dictionary' ),
				'type'     => 'number',
				'units'    => true,
				'css'      => [ [ 'property' => $property, 'selector' => '.cb-dictionary-categories__items' ] ],
				'required' => [ 'listDisplay', '=', 'flex' ],
			];
		}
		$this->controls['listGridGap'] = [
			'tab'      => 'content',
			'group'    => 'layout',
			'label'    => esc_html__( 'Grid gap', 'core-blueprint-dictionary' ),
			'type'     => 'number',
			'units'    => true,
			'css'      => [ [ 'property' => 'gap', 'selector' => '.cb-dictionary-categories__items' ] ],
			'required' => [ 'listDisplay', '=', 'grid' ],
		];

		$this->controls['linkTypography'] = [
			'tab'   => 'content',
			'group' => 'links',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-categories__link' ] ],
		];
		$this->controls['linkBackground'] = [
			'tab'     => 'content',
			'group'   => 'links',
			'label'   => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'    => 'background',
			'exclude' => [ 'videoUrl', 'videoScale' ],
			'css'     => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-categories__link' ] ],
		];
		$this->controls['linkBorder'] = [
			'tab'   => 'content',
			'group' => 'links',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-categories__link' ] ],
		];
		$this->controls['linkPadding'] = [
			'tab'   => 'content',
			'group' => 'links',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [ [ 'property' => 'padding', 'selector' => '.cb-dictionary-categories__link' ] ],
		];
		$this->controls['linkShadow'] = [
			'tab'   => 'content',
			'group' => 'links',
			'label' => esc_html__( 'Box shadow', 'core-blueprint-dictionary' ),
			'type'  => 'box-shadow',
			'css'   => [ [ 'property' => 'box-shadow', 'selector' => '.cb-dictionary-categories__link' ] ],
		];
		foreach ( [
			'linkHoverColor'      => [ 'Hover text color', 'color', '.cb-dictionary-categories__link:hover' ],
			'linkHoverBackground' => [ 'Hover background color', 'background-color', '.cb-dictionary-categories__link:hover' ],
			'linkFocusColor'      => [ 'Focus text color', 'color', '.cb-dictionary-categories__link:focus-visible' ],
			'linkFocusBackground' => [ 'Focus background color', 'background-color', '.cb-dictionary-categories__link:focus-visible' ],
		] as $name => [ $label, $property, $selector ] ) {
			$this->controls[ $name ] = [
				'tab'   => 'content',
				'group' => 'links',
				'label' => esc_html__( $label, 'core-blueprint-dictionary' ),
				'type'  => 'color',
				'css'   => [ [ 'property' => $property, 'selector' => $selector ] ],
			];
		}

		$this->controls['currentTypography'] = [
			'tab'   => 'content',
			'group' => 'current',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-categories__item--current .cb-dictionary-categories__link' ] ],
		];
		$this->controls['currentBackground'] = [
			'tab'     => 'content',
			'group'   => 'current',
			'label'   => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'    => 'background',
			'exclude' => [ 'videoUrl', 'videoScale' ],
			'css'     => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-categories__item--current .cb-dictionary-categories__link' ] ],
		];
		$this->controls['currentBorder'] = [
			'tab'   => 'content',
			'group' => 'current',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-categories__item--current .cb-dictionary-categories__link' ] ],
		];

		$this->controls['emptyTypography'] = [
			'tab'      => 'content',
			'group'    => 'empty',
			'label'    => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'     => 'typography',
			'css'      => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-categories__item--empty .cb-dictionary-categories__link' ] ],
			'required' => [ 'showEmpty', '=', true ],
		];
		$this->controls['emptyBackground'] = [
			'tab'      => 'content',
			'group'    => 'empty',
			'label'    => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'     => 'background',
			'exclude'  => [ 'videoUrl', 'videoScale' ],
			'css'      => [ [ 'property' => 'background', 'selector' => '.cb-dictionary-categories__item--empty .cb-dictionary-categories__link' ] ],
			'required' => [ 'showEmpty', '=', true ],
		];
		$this->controls['emptyBorder'] = [
			'tab'      => 'content',
			'group'    => 'empty',
			'label'    => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'     => 'border',
			'css'      => [ [ 'property' => 'border', 'selector' => '.cb-dictionary-categories__item--empty .cb-dictionary-categories__link' ] ],
			'required' => [ 'showEmpty', '=', true ],
		];
		$this->controls['emptyOpacity'] = [
			'tab'      => 'content',
			'group'    => 'empty',
			'label'    => esc_html__( 'Opacity', 'core-blueprint-dictionary' ),
			'type'     => 'number',
			'min'      => 0,
			'max'      => 1,
			'step'     => 0.1,
			'css'      => [ [ 'property' => 'opacity', 'selector' => '.cb-dictionary-categories__item--empty .cb-dictionary-categories__link' ] ],
			'required' => [ 'showEmpty', '=', true ],
		];
	}

	public function render(): void {
		$settings = $this->settings;
		$args = [
			'show_empty' => array_key_exists( 'showEmpty', $settings ) ? (bool) $settings['showEmpty'] : false,
		];

		$this->set_attribute( '_root', 'class', 'cb-dictionary-bricks-categories' );
		echo '<div ' . $this->render_attributes( '_root' ) . '>' . CategoriesComponent::render( $args ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted builder-neutral Dictionary renderer output.
	}
}
