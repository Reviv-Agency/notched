<?php
/**
 * Process Steps Elementor widget (Frame 45).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Three stacked process cards on a gray band.
 */
class Widget_Process_Steps extends Widget_Base {

	private const ASSET_SLUG = 'process-steps';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-process-steps';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Process Steps', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-number-field';
	}

	/**
	 * @return array<int, string>
	 */
	public function get_categories(): array {
		return [ 'agency-widgets' ];
	}

	/**
	 * @return array<int, string>
	 */
	public function get_style_depends(): array {
		return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ];
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_keywords(): array {
		return [ 'process', 'steps', 'how it works', 'frame' ];
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private function default_steps(): array {
		$items = [
			[
				'step_label'       => esc_html__( 'Step 1', 'agency-elementor-widgets' ),
				'step_title'       => esc_html__( 'A Conversation, Not a Pitch', 'agency-elementor-widgets' ),
				'step_description' => esc_html__(
					"Your first meeting with Jon is free. No pressure, no obligation. Just an honest conversation about your home, what isn't working, what you're hoping to create, and what a realistic investment looks like for the scope you have in mind.\n\nJon will tell you directly if something doesn't add up, if there's a smarter approach, or if Bright Homes isn't the right fit for your project. You leave with a clear picture of what's possible and complete freedom to decide what happens next, on your timeline, when you're ready.",
					'agency-elementor-widgets'
				),
			],
			[
				'step_label'       => esc_html__( 'Step 2', 'agency-elementor-widgets' ),
				'step_title'       => esc_html__( 'Everything Resolved Before a Wall Is Touched', 'agency-elementor-widgets' ),
				'step_description' => esc_html__(
					"The Project Development phase is where uncertainty ends. Your existing home is documented to the inch. Up to three concept plans are developed and refined. Every finish and fitting is selected with Elena before construction begins. The full scope is detailed and priced, specifically, not approximately. And you walk through the finished result in a photo realistic 3D model before anything is built.\n\nBy the time you sign the construction contract, nothing is left to chance. You know exactly what you're getting, exactly what it costs, and exactly when you'll have it.",
					'agency-elementor-widgets'
				),
			],
			[
				'step_label'       => esc_html__( 'Step 3', 'agency-elementor-widgets' ),
				'step_title'       => esc_html__( 'A Build That Runs Exactly as Planned', 'agency-elementor-widgets' ),
				'step_description' => esc_html__(
					"Construction begins with every decision already made and every material already ordered. Your dedicated client portal, weekly construction meetings, and direct access to every member of the team mean you're never left wondering what's happening on site or what's coming next.\n\nAt the end, a handover walkthrough, a full operational manual for your new home, and a 2 year workmanship warranty. And a finished result that looks exactly the way it did in the 3D model, because every decision was made before the building began.",
					'agency-elementor-widgets'
				),
			],
		];

		$defaults = [];
		foreach ( $items as $index => $item ) {
			$n = $index + 1;
			$defaults[] = [
				'step_image'       => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/step-' . $n . '.webp' ),
				],
				'step_label'       => $item['step_label'],
				'step_title'       => $item['step_title'],
				'step_description' => $item['step_description'],
			];
		}

		return $defaults;
	}

	/**
	 * @return void
	 */
	protected function register_controls(): void {
		$this->register_content_controls();
		$this->register_style_controls();
	}

	/**
	 * @return void
	 */
	private function register_content_controls(): void {
		$this->start_controls_section(
			'section_content',
			[
				'label' => esc_html__( 'Steps', 'agency-elementor-widgets' ),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'step_image',
			[
				'label'   => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/step-1.webp' ),
				],
			]
		);

		$repeater->add_control(
			'step_label',
			[
				'label'   => esc_html__( 'Step label', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Step 1', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'step_title',
			[
				'label'   => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Step title', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'step_description',
			[
				'label' => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'type'  => Controls_Manager::WYSIWYG,
				'default'     => '',
				'rows'        => 6,
			]
		);

		$this->add_control(
			'steps',
			[
				'label'       => esc_html__( 'Steps', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_steps(),
				'title_field' => '{{{ step_label }}} — {{{ step_title }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens       = Design_Tokens::get();
		$gray_default = $tokens['color_gray'] ?? '#E1DEDA';
		$cream_default = $tokens['color_cream'] ?? '#F8F5F1';
		$dark_default = $tokens['color_blue_dark'] ?? '#252F37';

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Section', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $gray_default,
				'selectors' => [
					'{{WRAPPER}} .aew-process-steps' => '--aew-process-steps-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_max_width',
			[
				'label'      => esc_html__( 'Content max width', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 320,
						'max' => 1920,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 1440,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-process-steps__inner' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_padding',
			[
				'label'       => esc_html__( 'Wrapper padding', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Padding on all sides of the gray band inner box.',
					'agency-elementor-widgets'
				),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '40',
					'right'    => '120',
					'bottom'   => '40',
					'left'     => '120',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'tablet_default' => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-process-steps__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'cards_gap',
			[
				'label'      => esc_html__( 'Gap between cards', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-process-steps__list' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_card',
			[
				'label' => esc_html__( 'Card', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'card_background',
			[
				'label'     => esc_html__( 'Background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $cream_default,
				'selectors' => [
					'{{WRAPPER}} .aew-process-steps__card' => '--aew-process-steps-card-bg: {{VALUE}}; background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default'    => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '20',
					'right'    => '20',
					'bottom'   => '20',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-process-steps__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_radius',
			[
				'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 80,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 40,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-process-steps__card' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_inner_gap',
			[
				'label'      => esc_html__( 'Gap inside card', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 48,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 16,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-process-steps__card' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label' => esc_html__( 'Image', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'image_height',
			[
				'label'      => esc_html__( 'Height', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 120,
						'max' => 800,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 400,
				],
				'tablet_default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 240,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-process-steps' => '--aew-process-steps-image-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_radius',
			[
				'label'      => esc_html__( 'Border radius', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 48,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'mobile_default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-process-steps' => '--aew-process-steps-image-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_typography',
			[
				'label' => esc_html__( 'Typography', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-process-steps' => '--aew-process-steps-text: {{VALUE}};',
					'{{WRAPPER}} .aew-process-steps__step' => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-process-steps__title' => 'color: {{VALUE}};',
					'{{WRAPPER}} .aew-process-steps__text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'step_label_typography',
				'label'          => esc_html__( 'Step label', 'agency-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .aew-process-steps__step',
				'fields_options' => [
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '700' ],
					'font_size'   => [
						'default' => [ 'unit' => 'px', 'size' => 48 ],
						'tablet_default' => [ 'unit' => 'px', 'size' => 24 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'step_title_typography',
				'label'          => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .aew-process-steps__title',
				'fields_options' => [
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [ 'unit' => 'px', 'size' => 48 ],
						'tablet_default' => [ 'unit' => 'px', 'size' => 24 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 24 ],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'step_description_typography',
				'label'          => esc_html__( 'Description', 'agency-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .aew-process-steps__text',
				'fields_options' => [
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [ 'unit' => 'px', 'size' => 18 ],
						'tablet_default' => [ 'unit' => 'px', 'size' => 18 ],
						'mobile_default' => [ 'unit' => 'px', 'size' => 14 ],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param array<string, mixed> $image_setting Media control value.
	 * @param int                  $index         Step index.
	 * @return string
	 */
	private function resolve_image_url( array $image_setting, int $index ): string {
		$url = trim( (string) ( $image_setting['url'] ?? '' ) );

		if ( '' !== $url && ! empty( $image_setting['id'] ) ) {
			$attachment_url = wp_get_attachment_image_url( (int) $image_setting['id'], 'full' );
			if ( is_string( $attachment_url ) && '' !== $attachment_url ) {
				$url = $attachment_url;
			}
		}

		$n = ( $index % 3 ) + 1;
		$fallback = Widget_Assets::url( self::ASSET_SLUG, 'images/step-' . $n . '.webp' );

		if ( '' === $url ) {
			return $fallback;
		}

		$url = str_replace(
			'/agency-elementor-widgets/agency-elementor-widgets/',
			'/agency-elementor-widgets/',
			$url
		);

		if ( preg_match( '#/process-steps/images/step-(\d+)\.webp$#', $url, $matches ) ) {
			$file_path = Widget_Assets::path( self::ASSET_SLUG, 'images/step-' . $matches[1] . '.webp' );
			if ( is_readable( $file_path ) ) {
				return Widget_Assets::url( self::ASSET_SLUG, 'images/step-' . $matches[1] . '.webp' );
			}
		}

		return $url;
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$steps    = $settings['steps'] ?? [];

		if ( ! is_array( $steps ) || empty( $steps ) ) {
			return;
		}

		$this->add_render_attribute( 'inner', 'class', 'aew-process-steps__inner' );
		$this->add_render_attribute( 'list', 'class', 'aew-process-steps__list' );

		?>
		<section class="aew-process-steps">
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div <?php $this->print_render_attribute_string( 'list' ); ?>>
					<?php foreach ( $steps as $index => $step ) : ?>
						<?php
						if ( ! is_array( $step ) ) {
							continue;
						}

						$label = trim( (string) ( $step['step_label'] ?? '' ) );
						$title = trim( (string) ( $step['step_title'] ?? '' ) );
						$desc  = (string) ( $step['step_description'] ?? '' );
						$image = $this->resolve_image_url( (array) ( $step['step_image'] ?? [] ), (int) $index );

						if ( '' === $label && '' === $title && Rich_Text::is_empty( $desc ) && '' === $image ) {
							continue;
						}

						$card_key = 'card_' . $index;
						$this->add_render_attribute( $card_key, 'class', 'aew-process-steps__card' );
						?>
						<article <?php $this->print_render_attribute_string( $card_key ); ?>>
							<?php if ( '' !== $image ) : ?>
								<div class="aew-process-steps__media">
									<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy" decoding="async" />
								</div>
							<?php endif; ?>

							<?php if ( '' !== $label || '' !== $title || ! empty( $desc ) ) : ?>
								<div class="aew-process-steps__content">
									<?php if ( '' !== $label ) : ?>
										<div class="aew-process-steps__step-col">
											<?php
											$label_key = $this->get_repeater_setting_key( 'step_label', 'steps', $index );
											$this->add_render_attribute( $label_key, 'class', 'aew-process-steps__step' );
											$this->add_inline_editing_attributes( $label_key, 'none' );
											?>
											<span <?php $this->print_render_attribute_string( $label_key ); ?>>
												<?php echo esc_html( $label ); ?>
											</span>
										</div>
									<?php endif; ?>
									<?php if ( '' !== $title || ! Rich_Text::is_empty( $desc ) ) : ?>
										<div class="aew-process-steps__copy">
											<?php if ( '' !== $title ) : ?>
												<?php
												$title_key = $this->get_repeater_setting_key( 'step_title', 'steps', $index );
												$this->add_render_attribute( $title_key, 'class', 'aew-process-steps__title' );
												$this->add_inline_editing_attributes( $title_key, 'none' );
												?>
												<h3 <?php $this->print_render_attribute_string( $title_key ); ?>>
													<?php echo esc_html( $title ); ?>
												</h3>
											<?php endif; ?>
											<?php if ( ! Rich_Text::is_empty( $desc ) ) : ?>
												<?php
												$desc_key = $this->get_repeater_setting_key( 'step_description', 'steps', $index );
												$this->add_render_attribute( $desc_key, 'class', 'aew-process-steps__body aew-process-steps__text aew-rich-text' );
												$this->add_inline_editing_attributes( $desc_key, 'advanced' );
												?>
												<div <?php $this->print_render_attribute_string( $desc_key ); ?>>
													<?php Rich_Text::echo_html( $desc ); ?>
												</div>
											<?php endif; ?>
										</div>
									<?php endif; ?>
								</div>
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}
}
