<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks\Elements;

use CB\Dictionary\Frontend\Components\Alphabet as AlphabetComponent;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class Alphabet extends \Bricks\Element {
	public $category = ElementRegistry::CATEGORY;
	public $name     = 'cb-dictionary-alphabet';
	public $icon     = 'ti-layout-grid2';

	public function get_label(): string {
		return esc_html__( 'Dictionary Alphabet', 'core-blueprint-dictionary' );
	}

	/** @return string[] */
	public function get_keywords(): array {
		return [ 'core blueprint', 'dictionary', 'alphabet', 'a-z', 'glossary' ];
	}

	public function set_control_groups(): void {
		$this->control_groups['alphabet'] = [
			'title' => esc_html__( 'Alphabet', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['letters'] = [
			'title' => esc_html__( 'Letters', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
	}

	public function set_controls(): void {
		$this->controls['showEmpty'] = [
			'tab'     => 'content',
			'group'   => 'alphabet',
			'label'   => esc_html__( 'Show empty letters', 'core-blueprint-dictionary' ),
			'type'    => 'checkbox',
			'default' => false,
		];
		$this->controls['typography'] = [
			'tab'   => 'style',
			'group' => 'letters',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [
				[ 'property' => 'typography', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'typography', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
		$this->controls['background'] = [
			'tab'   => 'style',
			'group' => 'letters',
			'label' => esc_html__( 'Background', 'core-blueprint-dictionary' ),
			'type'  => 'background',
			'css'   => [
				[ 'property' => 'background', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'background', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
		$this->controls['border'] = [
			'tab'   => 'style',
			'group' => 'letters',
			'label' => esc_html__( 'Border', 'core-blueprint-dictionary' ),
			'type'  => 'border',
			'css'   => [
				[ 'property' => 'border', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'border', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
		$this->controls['padding'] = [
			'tab'   => 'style',
			'group' => 'letters',
			'label' => esc_html__( 'Padding', 'core-blueprint-dictionary' ),
			'type'  => 'dimensions',
			'css'   => [
				[ 'property' => 'padding', 'selector' => '.cb-dictionary-alphabet__link' ],
				[ 'property' => 'padding', 'selector' => '.cb-dictionary-alphabet__label' ],
			],
		];
	}

	public function render(): void {
		$settings = $this->settings;
		$args = [
			'show_empty' => array_key_exists( 'showEmpty', $settings ) ? (bool) $settings['showEmpty'] : false,
		];

		$this->set_attribute( '_root', 'class', 'cb-dictionary-bricks-alphabet' );
		echo '<div ' . $this->render_attributes( '_root' ) . '>' . AlphabetComponent::render( $args ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted builder-neutral Dictionary renderer output.
	}
}
