<?php
declare(strict_types=1);

namespace CB\Dictionary\Integration\Builders\Bricks\Elements;

use CB\Dictionary\Frontend\Components\Meta as MetaComponent;
use CB\Dictionary\Integration\Builders\Bricks\ElementRegistry;

defined( 'ABSPATH' ) || exit;

final class EntryData extends \Bricks\Element {
	public $category = ElementRegistry::CATEGORY;
	public $name     = 'cb-dictionary-entry-data';
	public $icon     = 'ti-info-alt';

	public function get_label(): string {
		return esc_html__( 'Dictionary Entry Data', 'core-blueprint-dictionary' );
	}

	/** @return string[] */
	public function get_keywords(): array {
		return [ 'core blueprint', 'dictionary', 'meta', 'entry', 'data' ];
	}

	public function set_control_groups(): void {
		$this->control_groups['entry'] = [
			'title' => esc_html__( 'Entry', 'core-blueprint-dictionary' ),
			'tab'   => 'content',
		];
		$this->control_groups['labels'] = [
			'title' => esc_html__( 'Labels', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
		$this->control_groups['values'] = [
			'title' => esc_html__( 'Values', 'core-blueprint-dictionary' ),
			'tab'   => 'style',
		];
	}

	public function set_controls(): void {
		$this->controls['entryId'] = [
			'tab'         => 'content',
			'group'       => 'entry',
			'label'       => esc_html__( 'Entry ID', 'core-blueprint-dictionary' ),
			'type'        => 'number',
			'default'     => 0,
			'min'         => 0,
			'description' => esc_html__( 'Leave at 0 to use the current Dictionary entry context.', 'core-blueprint-dictionary' ),
		];
		$this->controls['labelTypography'] = [
			'tab'   => 'style',
			'group' => 'labels',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-meta__label' ] ],
		];
		$this->controls['valueTypography'] = [
			'tab'   => 'style',
			'group' => 'values',
			'label' => esc_html__( 'Typography', 'core-blueprint-dictionary' ),
			'type'  => 'typography',
			'css'   => [ [ 'property' => 'typography', 'selector' => '.cb-dictionary-meta__value' ] ],
		];
	}

	public function render(): void {
		$settings = $this->settings;
		$this->set_attribute( '_root', 'class', 'cb-dictionary-bricks-entry-data' );
		echo '<div ' . $this->render_attributes( '_root' ) . '>' . MetaComponent::render( [ 'id' => $settings['entryId'] ?? 0 ] ) . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- trusted builder-neutral Dictionary renderer output.
	}
}
