<?php
/**
 * Testimonial Grid Elementor widget (Frame 40).
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
 * 2×3 testimonial cards — manual repeater or bh_testimonial posts.
 */
class Widget_Testimonial_Grid extends Widget_Base {

	private const ASSET_SLUG = 'testimonial-grid';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-testimonial-grid';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Testimonial Grid', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-testimonial';
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
		return [ 'testimonial', 'review', 'grid', 'stars', 'frame' ];
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private function default_testimonials(): array {
		$items = [
			[
				'reviewer_name' => esc_html__( 'Jason M.', 'agency-elementor-widgets' ),
				'project_meta'  => esc_html__( 'Full Floor Renovation, Salt Lake City', 'agency-elementor-widgets' ),
				'quote_title'   => esc_html__( 'On time. On budget. No surprises.', 'agency-elementor-widgets' ),
				'quote_body'    => esc_html__(
					'Condo floor replacement. They were first to submit and their price was the lowest. We are happy customers.',
					'agency-elementor-widgets'
				),
			],
			[
				'reviewer_name' => esc_html__( 'Charice C.', 'agency-elementor-widgets' ),
				'project_meta'  => esc_html__( 'Major Home Remodel, Salt Lake County', 'agency-elementor-widgets' ),
				'quote_title'   => esc_html__( 'We Actually Had a Lot of Fun', 'agency-elementor-widgets' ),
				'quote_body'    => esc_html__(
					"Working with Bright Homes taught me there is skill level \"other.\" They may be new to Utah but they're not new to construction or helping clients. Thanks for helping us save our marriage one renovation at a time!",
					'agency-elementor-widgets'
				),
			],
			[
				'reviewer_name' => esc_html__( 'Mandi J.', 'agency-elementor-widgets' ),
				'project_meta'  => esc_html__( 'Whole Home Remodel, Draper, Utah County', 'agency-elementor-widgets' ),
				'quote_title'   => esc_html__( 'Everything Felt Thoughtfully Planned', 'agency-elementor-widgets' ),
				'quote_body'    => esc_html__(
					"I had so many contractors tell me they wouldn't do my project because of cost and design and on and on. You guys weren't scared away. You had design ideas, cost control and schedule control.",
					'agency-elementor-widgets'
				),
			],
			[
				'reviewer_name' => esc_html__( 'Maridy J.', 'agency-elementor-widgets' ),
				'project_meta'  => esc_html__( 'Repeat Client, Three Separate Projects', 'agency-elementor-widgets' ),
				'quote_title'   => esc_html__( 'Smooth, Seamless, and So Well Organized', 'agency-elementor-widgets' ),
				'quote_body'    => esc_html__(
					"You actually made that a fun and stress-free process by doing so much preparation for us that we didn't have to do. From the first time we met in person we loved working with you.",
					'agency-elementor-widgets'
				),
			],
			[
				'reviewer_name' => esc_html__( 'Linda P.', 'agency-elementor-widgets' ),
				'project_meta'  => esc_html__( 'Whole Home Remodel, Highland, Utah County', 'agency-elementor-widgets' ),
				'quote_title'   => esc_html__( 'Jon Had Our Best Interest in Mind Throughout', 'agency-elementor-widgets' ),
				'quote_body'    => esc_html__(
					'He took care of every detail along the way, and we felt seen and heard when it came to our unique design choices and budget constraints. He helped us prioritize what we needed to focus on and was incredibly accommodating and easy to work with.',
					'agency-elementor-widgets'
				),
			],
			[
				'reviewer_name' => esc_html__( 'Beth F.', 'agency-elementor-widgets' ),
				'project_meta'  => esc_html__( 'Home Remodel, Salt Lake City', 'agency-elementor-widgets' ),
				'quote_title'   => esc_html__( "It's a good feeling when you can trust your contractor.", 'agency-elementor-widgets' ),
				'quote_body'    => esc_html__(
					'We felt confident every step of the way, and Bright Homes consistently delivered quality craftsmanship while respecting our timeline and budget.',
					'agency-elementor-widgets'
				),
			],
		];

		$avatar = Widget_Assets::url( self::ASSET_SLUG, 'images/avatar-placeholder.webp' );
		$defaults = [];

		foreach ( $items as $index => $item ) {
			$n = $index + 1;
			$defaults[] = [
				'project_image'  => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/testimonial-' . $n . '.webp' ),
				],
				'profile_image'  => [
					'url' => $avatar,
				],
				'reviewer_name'  => $item['reviewer_name'],
				'project_meta'   => $item['project_meta'],
				'quote_title'    => $item['quote_title'],
				'quote_body'     => $item['quote_body'],
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
				'label' => esc_html__( 'Content', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'content_source',
			[
				'label'   => esc_html__( 'Content source', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'manual',
				'options' => [
					'manual' => esc_html__( 'Manual cards', 'agency-elementor-widgets' ),
					'posts'  => esc_html__( 'From Testimonials', 'agency-elementor-widgets' ),
				],
			]
		);

		$this->add_control(
			'selected_posts',
			[
				'label'       => esc_html__( 'Testimonials', 'agency-elementor-widgets' ),
				'description' => esc_html__(
					'Choose published Testimonial posts. Order here controls display order.',
					'agency-elementor-widgets'
				),
				'type'        => Controls_Manager::SELECT2,
				'multiple'    => true,
				'label_block' => true,
				'options'     => Cpt_Testimonial::get_post_options(),
				'condition'   => [
					'content_source' => 'posts',
				],
			]
		);

		$this->add_control(
			'star_count',
			[
				'label'   => esc_html__( 'Star count', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5,
				'min'     => 1,
				'max'     => 5,
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'project_image',
			[
				'label'   => esc_html__( 'Project image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/testimonial-1.webp' ),
				],
			]
		);

		$repeater->add_control(
			'profile_image',
			[
				'label'   => esc_html__( 'Profile image', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/avatar-placeholder.webp' ),
				],
			]
		);

		$repeater->add_control(
			'reviewer_name',
			[
				'label'   => esc_html__( 'Reviewer name', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'project_meta',
			[
				'label'   => esc_html__( 'Project / location', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '',
			]
		);

		$repeater->add_control(
			'quote_title',
			[
				'label'   => esc_html__( 'Quote title', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Quote title', 'agency-elementor-widgets' ),
			]
		);

		$repeater->add_control(
			'quote_body',
			[
				'label'   => esc_html__( 'Quote body', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::WYSIWYG,
				'default' => '',
			]
		);

		$this->add_control(
			'testimonials',
			[
				'label'       => esc_html__( 'Cards', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $repeater->get_controls(),
				'default'     => $this->default_testimonials(),
				'title_field' => '{{{ quote_title }}}',
				'condition'   => [
					'content_source' => 'manual',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens        = Design_Tokens::get();
		$dark_default  = $tokens['color_blue_dark'] ?? '#252F37';
		$card_default  = '#314654';
		$white_default = $tokens['color_white'] ?? '#FFFFFF';
		$yellow_default = $tokens['color_yellow'] ?? '#EBC543';

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
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-testimonial-grid' => '--aew-testimonial-grid-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'section_padding',
			[
				'label'      => esc_html__( 'Inner padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'default'    => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '40',
					'right'    => '16',
					'bottom'   => '40',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-testimonial-grid__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'grid_gap',
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
					'{{WRAPPER}} .aew-testimonial-grid__grid' => 'gap: {{SIZE}}{{UNIT}};',
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
				'default'   => $card_default,
				'selectors' => [
					'{{WRAPPER}} .aew-testimonial-grid__card' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'card_padding',
			[
				'label'      => esc_html__( 'Padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '16',
					'right'    => '16',
					'bottom'   => '16',
					'left'     => '16',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-testimonial-grid__card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'size' => 26,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-testimonial-grid__card' => 'border-radius: {{SIZE}}{{UNIT}};',
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
					'{{WRAPPER}} .aew-testimonial-grid__card' => 'gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_media',
			[
				'label' => esc_html__( 'Images & stars', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'project_image_radius',
			[
				'label'      => esc_html__( 'Project image radius', 'agency-elementor-widgets' ),
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
					'{{WRAPPER}} .aew-testimonial-grid__project-image img' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'avatar_size',
			[
				'label'      => esc_html__( 'Avatar size', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 32,
						'max' => 120,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 64,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-testimonial-grid__avatar' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'star_size',
			[
				'label'      => esc_html__( 'Star size', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range'      => [
					'px' => [
						'min' => 12,
						'max' => 48,
					],
				],
				'default'    => [
					'unit' => 'px',
					'size' => 24,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-testimonial-grid__star' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'star_color',
			[
				'label'     => esc_html__( 'Star color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $yellow_default,
				'selectors' => [
					'{{WRAPPER}} .aew-testimonial-grid__stars' => 'color: {{VALUE}};',
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
			'attribution_color',
			[
				'label'     => esc_html__( 'Attribution color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $white_default,
				'selectors' => [
					'{{WRAPPER}} .aew-testimonial-grid__attribution' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'attribution_typography',
				'label'          => esc_html__( 'Attribution', 'agency-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .aew-testimonial-grid__attribution',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 18,
						],
						'tablet_default' => [
							'unit' => 'px',
							'size' => 14,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 14,
						],
					],
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__( 'Title color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $white_default,
				'selectors' => [
					'{{WRAPPER}} .aew-testimonial-grid__title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'title_typography',
				'label'          => esc_html__( 'Title', 'agency-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .aew-testimonial-grid__title',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Teko' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 48,
						],
						'tablet_default' => [
							'unit' => 'px',
							'size' => 24,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 24,
						],
					],
					'line_height' => [
						'default' => [
							'unit' => 'em',
							'size' => 1.15,
						],
					],
				],
			]
		);

		$this->add_control(
			'body_color',
			[
				'label'     => esc_html__( 'Body color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $white_default,
				'selectors' => [
					'{{WRAPPER}} .aew-testimonial-grid__body' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'           => 'body_typography',
				'label'          => esc_html__( 'Quote body', 'agency-elementor-widgets' ),
				'selector'       => '{{WRAPPER}} .aew-testimonial-grid__body',
				'fields_options' => [
					'typography'  => [ 'default' => 'custom' ],
					'font_family' => [ 'default' => 'Lato' ],
					'font_weight' => [ 'default' => '400' ],
					'font_size'   => [
						'default' => [
							'unit' => 'px',
							'size' => 20,
						],
						'tablet_default' => [
							'unit' => 'px',
							'size' => 14,
						],
						'mobile_default' => [
							'unit' => 'px',
							'size' => 14,
						],
					],
					'line_height' => [
						'default' => [
							'unit' => 'em',
							'size' => 1.5,
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param array<string, mixed> $settings Widget settings.
	 * @return array<int, array<string, mixed>>
	 */
	private function get_cards( array $settings ): array {
		$source = $settings['content_source'] ?? 'manual';

		if ( 'posts' === $source ) {
			$ids = $settings['selected_posts'] ?? [];
			if ( ! is_array( $ids ) ) {
				$ids = [];
			}

			$cards = [];
			foreach ( $ids as $post_id ) {
				$post_id = (int) $post_id;
				if ( $post_id <= 0 ) {
					continue;
				}
				$card = $this->get_card_data_from_post( $post_id );
				if ( null !== $card ) {
					$cards[] = $card;
				}
			}

			return $cards;
		}

		$items = $settings['testimonials'] ?? [];
		if ( ! is_array( $items ) ) {
			return [];
		}

		$cards = [];
		foreach ( $items as $index => $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}
			$cards[] = [
				'project_image' => $this->resolve_image_url( $item['project_image'] ?? [], 'testimonial-' . ( ( $index % 6 ) + 1 ) . '.webp' ),
				'profile_image' => $this->resolve_image_url( $item['profile_image'] ?? [], 'avatar-placeholder.webp' ),
				'reviewer_name' => trim( (string) ( $item['reviewer_name'] ?? '' ) ),
				'project_meta'  => trim( (string) ( $item['project_meta'] ?? '' ) ),
				'quote_title'   => trim( (string) ( $item['quote_title'] ?? '' ) ),
				'quote_body'    => trim( (string) ( $item['quote_body'] ?? '' ) ),
			];
		}

		return $cards;
	}

	/**
	 * @param int $post_id Post ID.
	 * @return array<string, mixed>|null
	 */
	private function get_card_data_from_post( int $post_id ): ?array {
		$post = get_post( $post_id );
		if ( ! $post || Cpt_Testimonial::POST_TYPE !== $post->post_type || 'publish' !== $post->post_status ) {
			return null;
		}

		$project_url = get_the_post_thumbnail_url( $post_id, 'full' ) ?: '';
		$profile_id  = (int) get_post_meta( $post_id, 'bh_profile_image_id', true );
		$profile_url = $profile_id > 0 ? (string) wp_get_attachment_image_url( $profile_id, 'full' ) : Widget_Assets::url( self::ASSET_SLUG, 'images/avatar-placeholder.webp' );

		$quote_title = (string) get_post_meta( $post_id, 'bh_quote_title', true );
		if ( '' === $quote_title ) {
			$quote_title = get_the_title( $post_id );
		}

		return [
			'project_image' => $project_url,
			'profile_image' => $profile_url,
			'reviewer_name' => (string) get_post_meta( $post_id, 'bh_reviewer_name', true ),
			'project_meta'  => (string) get_post_meta( $post_id, 'bh_project_meta', true ),
			'quote_title'   => $quote_title,
			'quote_body'    => (string) get_post_meta( $post_id, 'bh_quote_body', true ),
		];
	}

	/**
	 * @param array<string, mixed> $image  Media control value.
	 * @param string               $fallback_filename Asset filename fallback.
	 * @return string
	 */
	private function resolve_image_url( array $image, string $fallback_filename ): string {
		$attachment_id = (int) ( $image['id'] ?? 0 );
		if ( $attachment_id > 0 ) {
			$url = wp_get_attachment_image_url( $attachment_id, 'full' );
			if ( is_string( $url ) && '' !== $url ) {
				return $this->normalize_asset_url( $url );
			}
		}

		$url = (string) ( $image['url'] ?? '' );
		if ( '' !== $url ) {
			return $this->normalize_asset_url( $url );
		}

		return Widget_Assets::url( self::ASSET_SLUG, 'images/' . $fallback_filename );
	}

	/**
	 * @param string $url Image URL.
	 * @return string
	 */
	private function normalize_asset_url( string $url ): string {
		$url = str_replace(
			'/agency-elementor-widgets/agency-elementor-widgets/',
			'/agency-elementor-widgets/',
			$url
		);

		if ( preg_match( '#/testimonial-grid/images/([^/]+\.webp)$#', $url, $matches ) ) {
			$file_path = Widget_Assets::path( self::ASSET_SLUG, 'images/' . $matches[1] );
			if ( is_readable( $file_path ) ) {
				return Widget_Assets::url( self::ASSET_SLUG, 'images/' . $matches[1] );
			}
		}

		return $url;
	}

	/**
	 * @return void
	 */
	private function render_star_icon(): void {
		?>
		<svg class="aew-testimonial-grid__star" width="24" height="24" viewBox="0 0 18 18" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
			<path d="M9 1.5L11.04 6.63L16.5 7.24L12.45 11.07L13.59 16.44L9 13.77L4.41 16.44L5.55 11.07L1.5 7.24L6.96 6.63L9 1.5Z"/>
		</svg>
		<?php
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();
		$cards    = $this->get_cards( $settings );

		if ( empty( $cards ) ) {
			return;
		}

		$stars = min( 5, max( 1, (int) ( $settings['star_count'] ?? 5 ) ) );

		$this->add_render_attribute( 'inner', 'class', 'aew-testimonial-grid__inner' );
		$this->add_render_attribute( 'grid', 'class', 'aew-testimonial-grid__grid' );

		?>
		<section class="aew-testimonial-grid">
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div <?php $this->print_render_attribute_string( 'grid' ); ?>>
					<?php foreach ( $cards as $index => $card ) : ?>
						<?php $this->render_card( $card, $index, $stars, $settings ); ?>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
		<?php
	}

	/**
	 * @param array<string, mixed> $card     Card data.
	 * @param int                  $index    Card index.
	 * @param int                  $stars    Star count.
	 * @param array<string, mixed> $settings Widget settings.
	 * @return void
	 */
	private function render_card( array $card, int $index, int $stars, array $settings ): void {
		$project_url   = (string) ( $card['project_image'] ?? '' );
		$profile_url   = (string) ( $card['profile_image'] ?? '' );
		$reviewer_name = (string) ( $card['reviewer_name'] ?? '' );
		$project_meta  = (string) ( $card['project_meta'] ?? '' );
		$quote_title   = (string) ( $card['quote_title'] ?? '' );
		$quote_body    = (string) ( $card['quote_body'] ?? '' );

		$card_key = 'card_' . $index;
		$this->add_render_attribute( $card_key, 'class', 'aew-testimonial-grid__card' );

		?>
		<article <?php $this->print_render_attribute_string( $card_key ); ?>>
			<?php if ( '' !== $project_url ) : ?>
				<div class="aew-testimonial-grid__project-image">
					<img src="<?php echo esc_url( $project_url ); ?>" alt="" loading="lazy" decoding="async" />
				</div>
			<?php endif; ?>

			<div class="aew-testimonial-grid__meta">
				<?php if ( '' !== $profile_url ) : ?>
					<div class="aew-testimonial-grid__avatar">
						<img src="<?php echo esc_url( $profile_url ); ?>" alt="<?php echo esc_attr( $reviewer_name ); ?>" loading="lazy" decoding="async" />
					</div>
				<?php endif; ?>

				<div class="aew-testimonial-grid__meta-content">
					<div class="aew-testimonial-grid__stars" aria-hidden="true">
						<?php for ( $s = 0; $s < $stars; $s++ ) : ?>
							<?php $this->render_star_icon(); ?>
						<?php endfor; ?>
					</div>

					<?php if ( '' !== $reviewer_name || '' !== $project_meta ) : ?>
						<p class="aew-testimonial-grid__attribution">
							<?php if ( '' !== $reviewer_name ) : ?>
								<strong class="aew-testimonial-grid__name"><?php echo esc_html( $reviewer_name ); ?></strong>
							<?php endif; ?>
							<?php if ( '' !== $project_meta ) : ?>
								<span class="aew-testimonial-grid__project-meta"><?php echo esc_html( ' | ' . $project_meta ); ?></span>
							<?php endif; ?>
						</p>
					<?php endif; ?>
				</div>
			</div>

			<?php if ( '' !== $quote_title ) : ?>
				<?php
				$title_key = $this->get_repeater_setting_key( 'quote_title', 'testimonials', $index );
				$this->add_render_attribute( $title_key, 'class', 'aew-testimonial-grid__title' );
				$this->add_inline_editing_attributes( $title_key, 'none' );
				?>
				<h3 <?php $this->print_render_attribute_string( $title_key ); ?>>
					<?php echo esc_html( $quote_title ); ?>
				</h3>
			<?php endif; ?>

			<?php if ( ! Rich_Text::is_empty( $quote_body ) ) : ?>
				<?php
				$body_key = $this->get_repeater_setting_key( 'quote_body', 'testimonials', $index );
				$this->add_render_attribute( $body_key, 'class', 'aew-testimonial-grid__body aew-rich-text' );
				$this->add_inline_editing_attributes( $body_key, 'advanced' );
				?>
				<div <?php $this->print_render_attribute_string( $body_key ); ?>>
					<?php Rich_Text::echo_html( $quote_body ); ?>
				</div>
			<?php endif; ?>
		</article>
		<?php
	}
}
