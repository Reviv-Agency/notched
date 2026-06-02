<?php
/**
 * Footer Elementor widget (Frame 69).
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Widget_Base;

/**
 * Dark wrapper + yellow footer box with nav columns and social links.
 */
class Widget_Footer extends Widget_Base {

	private const ASSET_SLUG = 'footer';

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'agency-footer';
	}

	/**
	 * @return string
	 */
	public function get_title(): string {
		return esc_html__( 'Footer', 'agency-elementor-widgets' );
	}

	/**
	 * @return string
	 */
	public function get_icon(): string {
		return 'eicon-footer';
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
		return [ 'footer', 'navigation', 'social', 'frame' ];
	}

	/**
	 * @param string $slug Link group slug.
	 * @return array<int, array<string, string>>
	 */
	private function default_links( string $slug ): array {
		$map = [
			'services' => [
				[ 'label' => 'Whole Home Remodel', 'url' => '#' ],
				[ 'label' => 'Home Additions', 'url' => '#' ],
				[ 'label' => 'Kitchen Remodels', 'url' => '#' ],
				[ 'label' => 'Bathroom Remodels', 'url' => '#' ],
				[ 'label' => 'Basements', 'url' => '#' ],
			],
			'about' => [
				[ 'label' => 'About Us', 'url' => '#' ],
				[ 'label' => 'Areas We Serve', 'url' => '#' ],
				[ 'label' => 'Remodeling Process', 'url' => '#' ],
			],
			'resources' => [
				[ 'label' => 'FAQs', 'url' => '#' ],
				[ 'label' => 'Tips & Advice', 'url' => '#' ],
				[ 'label' => 'Free Remodeling Guide', 'url' => '#' ],
			],
		];

		$items = $map[ $slug ] ?? [];
		$rows  = [];

		foreach ( $items as $item ) {
			$rows[] = [
				'link_label' => $item['label'],
				'link_url'   => [
					'url' => $item['url'],
				],
			];
		}

		return $rows;
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private function default_social(): array {
		return [
			[
				'network' => 'facebook',
				'url'     => [ 'url' => 'https://www.facebook.com/' ],
			],
			[
				'network' => 'instagram',
				'url'     => [ 'url' => 'https://www.instagram.com/' ],
			],
			[
				'network' => 'linkedin',
				'url'     => [ 'url' => 'https://www.linkedin.com/' ],
			],
		];
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
			'section_brand',
			[
				'label' => esc_html__( 'Brand', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'logo',
			[
				'label'   => esc_html__( 'Logo', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::MEDIA,
				'default' => [
					'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/footer-logo.webp' ),
				],
			]
		);

		$this->add_control(
			'logo_alt',
			[
				'label'       => esc_html__( 'Logo alt text', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__( 'Bright Homes', 'agency-elementor-widgets' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'tagline',
			[
				'label'   => esc_html__( 'Tagline', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__(
					"South Salt Lake County & North Utah County's Renovation Specialists Fixed Price. Truly Fixed. Plan Everything. Build it Right.",
					'agency-elementor-widgets'
				),
				'rows'    => 4,
			]
		);

		$this->end_controls_section();

		$this->register_link_section( 'services', esc_html__( 'Services', 'agency-elementor-widgets' ) );
		$this->register_link_section( 'about', esc_html__( 'About', 'agency-elementor-widgets' ) );
		$this->register_link_section( 'resources', esc_html__( 'Resources', 'agency-elementor-widgets' ) );

		$this->start_controls_section(
			'section_contact',
			[
				'label' => esc_html__( 'Contact', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'contact_heading',
			[
				'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Contact', 'agency-elementor-widgets' ),
			]
		);

		$this->add_control(
			'contact_address',
			[
				'label'   => esc_html__( 'Address', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXTAREA,
				'default' => esc_html__(
					"3300 North Triumph Blvd. Suite 100\nLehi, UT 84043",
					'agency-elementor-widgets'
				),
				'rows'    => 2,
			]
		);

		$this->add_control(
			'contact_phone',
			[
				'label'   => esc_html__( 'Phone', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => '801.406.3895',
			]
		);

		$this->add_control(
			'contact_phone_link',
			[
				'label'   => esc_html__( 'Phone link', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url' => 'tel:8014063895',
				],
			]
		);

		$this->add_control(
			'contact_email',
			[
				'label'   => esc_html__( 'Email', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => 'info@brighthomesutah.com',
			]
		);

		$this->add_control(
			'contact_email_link',
			[
				'label'   => esc_html__( 'Email link', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::URL,
				'default' => [
					'url' => 'mailto:info@brighthomesutah.com',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social',
			[
				'label' => esc_html__( 'Social', 'agency-elementor-widgets' ),
			]
		);

		$social_repeater = new Repeater();

		$social_repeater->add_control(
			'network',
			[
				'label'   => esc_html__( 'Network', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'facebook',
				'options' => [
					'facebook'  => esc_html__( 'Facebook', 'agency-elementor-widgets' ),
					'instagram' => esc_html__( 'Instagram', 'agency-elementor-widgets' ),
					'linkedin'  => esc_html__( 'LinkedIn', 'agency-elementor-widgets' ),
				],
			]
		);

		$social_repeater->add_control(
			'url',
			[
				'label'       => esc_html__( 'URL', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://', 'agency-elementor-widgets' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'social_links',
			[
				'label'       => esc_html__( 'Social links', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $social_repeater->get_controls(),
				'default'     => $this->default_social(),
				'title_field' => '{{{ network }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param string $id      Section id prefix.
	 * @param string $heading Default heading label.
	 * @return void
	 */
	private function register_link_section( string $id, string $heading ): void {
		$this->start_controls_section(
			'section_' . $id,
			[
				'label' => $heading,
			]
		);

		$this->add_control(
			$id . '_heading',
			[
				'label'   => esc_html__( 'Heading', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => $heading,
			]
		);

		$link_repeater = new Repeater();

		$link_repeater->add_control(
			'link_label',
			[
				'label'   => esc_html__( 'Label', 'agency-elementor-widgets' ),
				'type'    => Controls_Manager::TEXT,
				'default' => esc_html__( 'Link', 'agency-elementor-widgets' ),
			]
		);

		$link_repeater->add_control(
			'link_url',
			[
				'label'       => esc_html__( 'URL', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://', 'agency-elementor-widgets' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			$id . '_links',
			[
				'label'       => esc_html__( 'Links', 'agency-elementor-widgets' ),
				'type'        => Controls_Manager::REPEATER,
				'fields'      => $link_repeater->get_controls(),
				'default'     => $this->default_links( $id ),
				'title_field' => '{{{ link_label }}}',
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @return void
	 */
	private function register_style_controls(): void {
		$tokens         = Design_Tokens::get();
		$dark_default   = $tokens['color_blue_dark'] ?? '#252F37';
		$yellow_default = $tokens['color_yellow'] ?? '#EBC543';

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__( 'Section', 'agency-elementor-widgets' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'wrapper_background',
			[
				'label'     => esc_html__( 'Wrapper background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-footer' => '--aew-footer-wrapper-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'wrapper_padding',
			[
				'label'      => esc_html__( 'Wrapper padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '120',
					'right'    => '80',
					'bottom'   => '120',
					'left'     => '80',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'mobile_default' => [
					'top'      => '120',
					'right'    => '20',
					'bottom'   => '120',
					'left'     => '20',
					'unit'     => 'px',
					'isLinked' => false,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-footer__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'box_background',
			[
				'label'     => esc_html__( 'Yellow box background', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $yellow_default,
				'selectors' => [
					'{{WRAPPER}} .aew-footer' => '--aew-footer-box-bg: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label'      => esc_html__( 'Yellow box padding', 'agency-elementor-widgets' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em' ],
				'default'    => [
					'top'      => '80',
					'right'    => '80',
					'bottom'   => '80',
					'left'     => '80',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'mobile_default' => [
					'top'      => '40',
					'right'    => '40',
					'bottom'   => '40',
					'left'     => '40',
					'unit'     => 'px',
					'isLinked' => true,
				],
				'selectors'  => [
					'{{WRAPPER}} .aew-footer__box' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__( 'Text color', 'agency-elementor-widgets' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => $dark_default,
				'selectors' => [
					'{{WRAPPER}} .aew-footer' => '--aew-footer-text-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	/**
	 * @param mixed $link_data Link control value.
	 * @return array{url: string, target: string, rel: string}
	 */
	private function parse_link( $link_data ): array {
		if ( ! is_array( $link_data ) ) {
			return [ 'url' => '', 'target' => '', 'rel' => '' ];
		}

		$url    = $link_data['url'] ?? '';
		$target = ! empty( $link_data['is_external'] ) ? '_blank' : '';
		$rel    = [];
		if ( $target ) {
			$rel[] = 'noopener';
		}
		if ( ! empty( $link_data['nofollow'] ) ) {
			$rel[] = 'nofollow';
		}

		return [
			'url'    => (string) $url,
			'target' => $target,
			'rel'    => implode( ' ', $rel ),
		];
	}

	/**
	 * @param string $slug Network slug.
	 * @return string
	 */
	private function social_icon_url( string $slug ): string {
		$allowed = [ 'facebook', 'instagram', 'linkedin' ];
		if ( ! in_array( $slug, $allowed, true ) ) {
			$slug = 'facebook';
		}

		return Widget_Assets::url( self::ASSET_SLUG, 'images/icon-' . $slug . '.svg' );
	}

	/**
	 * @param string               $heading Heading text.
	 * @param array<int, mixed>    $links   Repeater rows.
	 * @return void
	 */
	private function render_link_block( string $heading, array $links ): void {
		if ( '' === trim( $heading ) && empty( $links ) ) {
			return;
		}

		?>
		<div class="aew-footer__block">
			<?php if ( '' !== trim( $heading ) ) : ?>
				<h3 class="aew-footer__heading"><?php echo esc_html( $heading ); ?></h3>
			<?php endif; ?>
			<?php if ( ! empty( $links ) ) : ?>
				<ul class="aew-footer__list">
					<?php foreach ( $links as $row ) : ?>
						<?php
						$label = trim( (string) ( $row['link_label'] ?? '' ) );
						if ( '' === $label ) {
							continue;
						}
						$link = $this->parse_link( $row['link_url'] ?? [] );
						?>
						<li class="aew-footer__item">
							<?php if ( '' !== $link['url'] ) : ?>
								<a class="aew-footer__link" href="<?php echo esc_url( $link['url'] ); ?>"
									<?php
									if ( $link['target'] ) {
										echo ' target="' . esc_attr( $link['target'] ) . '"';
									}
									if ( $link['rel'] ) {
										echo ' rel="' . esc_attr( $link['rel'] ) . '"';
									}
									?>
								><?php echo esc_html( $label ); ?></a>
							<?php else : ?>
								<span class="aew-footer__text"><?php echo esc_html( $label ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * @return void
	 */
	protected function render(): void {
		$settings = $this->get_settings_for_display();

		$logo      = (array) ( $settings['logo'] ?? [] );
		$logo_url  = $logo['url'] ?? Widget_Assets::url( self::ASSET_SLUG, 'images/footer-logo.webp' );
		$logo_alt  = trim( (string) ( $settings['logo_alt'] ?? '' ) );
		$tagline   = trim( (string) ( $settings['tagline'] ?? '' ) );
		$social    = $settings['social_links'] ?? [];
		$address   = trim( (string) ( $settings['contact_address'] ?? '' ) );
		$phone     = trim( (string) ( $settings['contact_phone'] ?? '' ) );
		$email     = trim( (string) ( $settings['contact_email'] ?? '' ) );
		$phone_lnk = $this->parse_link( $settings['contact_phone_link'] ?? [] );
		$email_lnk = $this->parse_link( $settings['contact_email_link'] ?? [] );

		if ( ! is_array( $social ) ) {
			$social = [];
		}

		$this->add_render_attribute( 'inner', 'class', 'aew-footer__inner' );
		$this->add_render_attribute( 'box', 'class', 'aew-footer__box' );

		?>
		<footer class="aew-footer">
			<div <?php $this->print_render_attribute_string( 'inner' ); ?>>
				<div <?php $this->print_render_attribute_string( 'box' ); ?>>
					<div class="aew-footer__grid">
						<div class="aew-footer__brand">
							<?php if ( '' !== $logo_url ) : ?>
								<a class="aew-footer__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>">
									<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( $logo_alt ?: get_bloginfo( 'name' ) ); ?>" width="163" height="112" loading="lazy" decoding="async" />
								</a>
							<?php endif; ?>
							<?php if ( '' !== $tagline ) : ?>
								<?php
								$this->add_render_attribute( 'tagline', 'class', 'aew-footer__tagline' );
								$this->add_inline_editing_attributes( 'tagline', 'none' );
								?>
								<p <?php $this->print_render_attribute_string( 'tagline' ); ?>><?php echo esc_html( $tagline ); ?></p>
							<?php endif; ?>
						</div>

						<div class="aew-footer__column">
							<?php
							$this->render_link_block(
								trim( (string) ( $settings['services_heading'] ?? '' ) ),
								$settings['services_links'] ?? []
							);
							?>
							<div class="aew-footer__block">
								<?php if ( '' !== trim( (string) ( $settings['contact_heading'] ?? '' ) ) ) : ?>
									<h3 class="aew-footer__heading"><?php echo esc_html( $settings['contact_heading'] ); ?></h3>
								<?php endif; ?>
								<ul class="aew-footer__list">
									<?php if ( '' !== $address ) : ?>
										<li class="aew-footer__item">
											<span class="aew-footer__text"><?php echo nl2br( esc_html( $address ) ); ?></span>
										</li>
									<?php endif; ?>
									<?php if ( '' !== $phone ) : ?>
										<li class="aew-footer__item">
											<?php if ( '' !== $phone_lnk['url'] ) : ?>
												<a class="aew-footer__link" href="<?php echo esc_url( $phone_lnk['url'] ); ?>"><?php echo esc_html( $phone ); ?></a>
											<?php else : ?>
												<span class="aew-footer__text"><?php echo esc_html( $phone ); ?></span>
											<?php endif; ?>
										</li>
									<?php endif; ?>
									<?php if ( '' !== $email ) : ?>
										<li class="aew-footer__item">
											<?php if ( '' !== $email_lnk['url'] ) : ?>
												<a class="aew-footer__link" href="<?php echo esc_url( $email_lnk['url'] ); ?>"><?php echo esc_html( $email ); ?></a>
											<?php else : ?>
												<span class="aew-footer__text"><?php echo esc_html( $email ); ?></span>
											<?php endif; ?>
										</li>
									<?php endif; ?>
								</ul>
							</div>
						</div>

						<div class="aew-footer__column">
							<?php
							$this->render_link_block(
								trim( (string) ( $settings['about_heading'] ?? '' ) ),
								$settings['about_links'] ?? []
							);
							$this->render_link_block(
								trim( (string) ( $settings['resources_heading'] ?? '' ) ),
								$settings['resources_links'] ?? []
							);
							?>
							<?php if ( ! empty( $social ) ) : ?>
								<div class="aew-footer__social" aria-label="<?php esc_attr_e( 'Social media', 'agency-elementor-widgets' ); ?>">
									<?php foreach ( $social as $index => $row ) : ?>
										<?php
										$network = (string) ( $row['network'] ?? 'facebook' );
										$link    = $this->parse_link( $row['url'] ?? [] );
										if ( '' === $link['url'] ) {
											continue;
										}
										$icon_url = $this->social_icon_url( $network );
										$label    = ucfirst( $network );
										?>
										<a class="aew-footer__social-link" href="<?php echo esc_url( $link['url'] ); ?>"
											aria-label="<?php echo esc_attr( $label ); ?>"
											<?php
											if ( $link['target'] ) {
												echo ' target="' . esc_attr( $link['target'] ) . '"';
											}
											if ( $link['rel'] ) {
												echo ' rel="' . esc_attr( $link['rel'] ) . '"';
											} else {
												echo ' rel="noopener noreferrer"';
											}
											?>
										>
											<img src="<?php echo esc_url( $icon_url ); ?>" alt="" width="40" height="40" loading="lazy" decoding="async" />
										</a>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</div>
		</footer>
		<?php
	}
}
