<?php
/**
 * Footer V2 — Notched brand.
 *
 * Matches Wix original: forest hero band, dark green body with
 * logo + MENU + QUICK LINKS + ADDRESS/CONTACT columns, bottom bar
 * with copyright + legal links.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Widget_Footer_V2 extends Widget_Base {

	private const ASSET_SLUG = 'footer-v2';

	public function get_name(): string      { return 'agency-footer-v2'; }
	public function get_title(): string     { return esc_html__( 'Footer V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-footer'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'footer', 'notched', 'menu', 'contact' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-fov2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
			// Leave the override empty so the stylesheet owns the responsive X
			// padding (40px mobile/tablet → 80px desktop). A non-empty default
			// here is emitted by Elementor WITHOUT media queries and would
			// clobber the stylesheet at every breakpoint.
			$stack['controls']['_padding']['default']        = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['tablet_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
			$stack['controls']['_padding']['mobile_default'] = [ 'top' => '', 'right' => '', 'bottom' => '', 'left' => '', 'unit' => 'px', 'isLinked' => false ];
		}
		return $stack;
	}

	// ─────────────────────────────────────────────────────────────────────────
	// CONTROLS
	// ─────────────────────────────────────────────────────────────────────────

	protected function register_controls(): void {
		$this->controls_hero();
		$this->controls_brand();
		$this->controls_menu();
		$this->controls_quick_links();
		$this->controls_address();
		$this->controls_contact();
		$this->controls_bottom_bar();

		$this->style_body();
		$this->style_hero();
		$this->style_headings();
		$this->style_links();
		$this->style_address();
		$this->style_bottom_bar();
	}

	private function controls_hero(): void {
		$this->start_controls_section( 's_hero', [ 'label' => 'Top image (forest)' ] );
		$this->add_control( 'show_hero', [ 'label' => 'Show', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes' ] );
		$this->add_control( 'hero_image', [
			'label'   => 'Image',
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/hero-forest.avif' ) ],
			'condition' => [ 'show_hero' => 'yes' ],
		] );
		$this->add_control( 'hero_alt', [
			'label'     => 'Alt text',
			'type'      => Controls_Manager::TEXT,
			'default'   => '',
			'condition' => [ 'show_hero' => 'yes' ],
		] );
		$this->add_responsive_control( 'hero_height', [
			'label'      => 'Height',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 80, 'max' => 1400 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 1200 ],
			'tablet_default' => [ 'unit' => 'px', 'size' => 455 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 200 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fov2__hero' => 'height: {{SIZE}}{{UNIT}};' ],
			'condition'  => [ 'show_hero' => 'yes' ],
		] );
		$this->end_controls_section();
	}

	private function controls_brand(): void {
		$this->start_controls_section( 's_brand', [ 'label' => 'Logo' ] );
		$this->add_control( 'logo', [
			'label'   => 'Logo image',
			'type'    => Controls_Manager::MEDIA,
			'default' => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/footer-logo.png' ) ],
		] );
		$this->add_control( 'logo_link', [
			'label'   => 'Logo link',
			'type'    => Controls_Manager::URL,
			'default' => [ 'url' => home_url( '/' ) ],
		] );
		$this->add_responsive_control( 'logo_height', [
			'label'      => 'Height',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 40, 'max' => 200 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 110 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 90 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fov2__logo-img' => 'height: {{SIZE}}{{UNIT}}; width: auto;' ],
		] );
		$this->end_controls_section();
	}

	private function controls_menu(): void {
		$this->start_controls_section( 's_menu', [ 'label' => 'Menu column' ] );
		$this->add_control( 'menu_heading', [
			'label'   => 'Heading',
			'type'    => Controls_Manager::TEXT,
			'default' => 'MENU',
		] );
		$menus = wp_get_nav_menus();
		$opts  = [ '' => '— Select —' ];
		foreach ( $menus as $m ) { $opts[ (string) $m->term_id ] = $m->name; }
		$default_menu = $this->default_menu_id_for_location( 'menu-2' );
		$this->add_control( 'menu_id', [
			'label'   => 'WordPress menu',
			'type'    => Controls_Manager::SELECT,
			'options' => $opts,
			'default' => $default_menu,
			'description' => 'Defaults to whichever menu is assigned to the theme\'s "Footer" location.',
		] );
		$this->end_controls_section();
	}

	private function controls_quick_links(): void {
		$this->start_controls_section( 's_quick', [ 'label' => 'Quick Links column' ] );
		$this->add_control( 'quick_heading', [
			'label'   => 'Heading',
			'type'    => Controls_Manager::TEXT,
			'default' => 'QUICK LINKS',
		] );
		$menus = wp_get_nav_menus();
		$opts  = [ '' => '— Select —' ];
		foreach ( $menus as $m ) { $opts[ (string) $m->term_id ] = $m->name; }
		$this->add_control( 'quick_menu_id', [
			'label'   => 'WordPress menu',
			'type'    => Controls_Manager::SELECT,
			'options' => $opts,
			'default' => '',
		] );
		$this->end_controls_section();
	}

	private function controls_address(): void {
		$this->start_controls_section( 's_address', [ 'label' => 'Address column' ] );
		$this->add_control( 'address_heading', [
			'label'   => 'Heading',
			'type'    => Controls_Manager::TEXT,
			'default' => 'ADDRESS',
		] );
		$this->add_control( 'address_line_1', [
			'label'   => 'Line 1',
			'type'    => Controls_Manager::TEXT,
			'default' => '195 W Malvern Ave,',
		] );
		$this->add_control( 'address_line_2', [
			'label'   => 'Line 2',
			'type'    => Controls_Manager::TEXT,
			'default' => 'South Salt Lake, UT 84115',
		] );
		$this->end_controls_section();
	}

	private function controls_contact(): void {
		$this->start_controls_section( 's_contact', [ 'label' => 'Contact column' ] );
		$this->add_control( 'contact_heading', [
			'label'   => 'Heading',
			'type'    => Controls_Manager::TEXT,
			'default' => 'CONTACT',
		] );
		$this->add_control( 'phone_number', [
			'label'   => 'Phone (display)',
			'type'    => Controls_Manager::TEXT,
			'default' => '801.410.4255',
		] );
		$this->add_control( 'email_address', [
			'label'   => 'Email',
			'type'    => Controls_Manager::TEXT,
			'default' => 'hello@notched.com',
		] );
		$this->end_controls_section();
	}

	private function controls_bottom_bar(): void {
		$this->start_controls_section( 's_bottom', [ 'label' => 'Bottom bar' ] );
		$this->add_control( 'copyright', [
			'label'   => 'Copyright text',
			'type'    => Controls_Manager::TEXT,
			'default' => '© ' . gmdate( 'Y' ) . ' Notched Timber. All rights reserved.',
		] );
		$rep = new Repeater();
		$rep->add_control( 'label', [ 'label' => 'Label', 'type' => Controls_Manager::TEXT, 'default' => 'Link' ] );
		$rep->add_control( 'url',   [ 'label' => 'URL',   'type' => Controls_Manager::URL ] );
		$this->add_control( 'legal_links', [
			'label'       => 'Legal links',
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ label }}}',
			'default'     => [
				[ 'label' => 'Privacy Policy',   'url' => [ 'url' => home_url( '/privacy-policy/' ) ] ],
				[ 'label' => 'Terms of Service', 'url' => [ 'url' => home_url( '/terms-of-service/' ) ] ],
				[ 'label' => 'Cookies Settings', 'url' => [ 'url' => '#cookie-settings' ] ],
			],
		] );
		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ────────────────────────────────────────────────────────

	private function style_body(): void {
		$this->start_controls_section( 'ss_body', [ 'label' => 'Body', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'body_bg', [
			'label'   => 'Background',
			'type'    => Controls_Manager::COLOR,
			'default' => '#093328',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fov2-body-bg: {{VALUE}};' ],
		] );
		$this->add_responsive_control( 'body_max_w', [
			'label'      => 'Inner max width',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 800, 'max' => 1920 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 1440 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fov2__inner' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_responsive_control( 'body_pad_y', [
			'label'      => 'Vertical padding',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 16, 'max' => 200 ] ],
			'default'        => [ 'unit' => 'px', 'size' => 64 ],
			'mobile_default' => [ 'unit' => 'px', 'size' => 100 ],
			'selectors'  => [ '{{WRAPPER}} .aew-fov2__body-inner' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_hero(): void {
		$this->start_controls_section( 'ss_hero', [ 'label' => 'Top image', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'hero_object_position', [
			'label'   => 'Image focus',
			'type'    => Controls_Manager::SELECT,
			'default' => 'center',
			'options' => [
				'top'    => 'Top',
				'center' => 'Center',
				'bottom' => 'Bottom',
			],
			'selectors' => [ '{{WRAPPER}} .aew-fov2__hero-img' => 'object-position: center {{VALUE}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_headings(): void {
		$this->start_controls_section( 'ss_headings', [ 'label' => 'Column headings', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'heading_color', [
			'label'   => 'Color',
			'type'    => Controls_Manager::COLOR,
			'default' => '#CDB797',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fov2-heading: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'heading_typo',
			'selector' => '{{WRAPPER}} .aew-fov2__heading',
			'fields_options' => [
				'font_family' => [ 'default' => 'Playfair Display' ],
				'font_weight' => [ 'default' => '700' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 20 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 1.5 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_links(): void {
		$this->start_controls_section( 'ss_links', [ 'label' => 'Links', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'link_color', [
			'label'   => 'Color',
			'type'    => Controls_Manager::COLOR,
			'default' => '#CDB797',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fov2-link: {{VALUE}};' ],
		] );
		$this->add_control( 'link_color_hover', [
			'label'   => 'Hover color',
			'type'    => Controls_Manager::COLOR,
			'default' => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fov2-link-hover: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'link_typo',
			'selector' => '{{WRAPPER}} .aew-fov2__link',
			'fields_options' => [
				'font_family' => [ 'default' => 'Teko' ],
				'font_weight' => [ 'default' => '600' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 1 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_address(): void {
		$this->start_controls_section( 'ss_address', [ 'label' => 'Address text', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'address_color', [
			'label'   => 'Color',
			'type'    => Controls_Manager::COLOR,
			'default' => '#CDB797',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fov2-address: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'address_typo',
			'selector' => '{{WRAPPER}} .aew-fov2__address',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ],
				'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.6 ] ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_bottom_bar(): void {
		$this->start_controls_section( 'ss_bottom', [ 'label' => 'Bottom bar', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'bottom_text_color', [
			'label'   => 'Text color',
			'type'    => Controls_Manager::COLOR,
			'default' => '#CDB797',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fov2-bottom-text: {{VALUE}};' ],
		] );
		$this->add_control( 'bottom_divider_color', [
			'label'   => 'Divider color',
			'type'    => Controls_Manager::COLOR,
			'default' => 'rgba(191,192,191,.2)',
			'selectors' => [ '{{WRAPPER}}' => '--aew-fov2-divider: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'bottom_typo',
			'selector' => '{{WRAPPER}} .aew-fov2__copyright, {{WRAPPER}} .aew-fov2__legal a',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 14 ] ],
			],
		] );
		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s         = $this->get_settings_for_display();
		$logo      = $s['logo'] ?? [];
		$logo_url  = is_array( $logo ) ? ( $logo['url'] ?? '' ) : '';
		$logo_link = $this->parse_link( $s['logo_link'] ?? [] );
		$hero      = $s['hero_image'] ?? [];
		$hero_url  = is_array( $hero ) ? ( $hero['url'] ?? '' ) : '';
		$hero_alt  = (string) ( $s['hero_alt'] ?? '' );
		$phone_raw = trim( (string) ( $s['phone_number'] ?? '' ) );
		$phone_tel = preg_replace( '/[^\d+]/', '', $phone_raw );
		$email     = trim( (string) ( $s['email_address'] ?? '' ) );

		$menu_tree  = $this->nav_tree_from_menu_id( (int) ( $s['menu_id'] ?? 0 ) );
		$quick_tree = $this->nav_tree_from_menu_id( (int) ( $s['quick_menu_id'] ?? 0 ) );

		/*
		 * Resolved colours as inline CSS vars on the wrapper. get_settings_for_display()
		 * resolves global colours → hex so global-bound picks render on the front end
		 * (Elementor drops globals for direct-property custom-control selectors). The
		 * `selectors` on each control drive the editor preview; this inline value wins
		 * on live. CSS consumes each var with a design-system fallback.
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'body_bg'              => '--aew-fov2-body-bg',
				'heading_color'        => '--aew-fov2-heading',
				'link_color'           => '--aew-fov2-link',
				'link_color_hover'     => '--aew-fov2-link-hover',
				'address_color'        => '--aew-fov2-address',
				'bottom_text_color'    => '--aew-fov2-bottom-text',
				'bottom_divider_color' => '--aew-fov2-divider',
			]
		);
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		/*
		 * Per-page override for the top (forest) image. The footer is a single
		 * global template, so its "Show top image" toggle is site-wide. To let
		 * individual pages drop the image, we check the queried page's
		 * `_aew_footer_hide_hero` meta — when truthy, the hero is suppressed on
		 * that page only. The global toggle remains the default everywhere else.
		 */
		$show_hero = 'yes' === ( $s['show_hero'] ?? 'yes' );
		$page_id   = (int) get_queried_object_id();
		if ( $page_id && get_post_meta( $page_id, '_aew_footer_hide_hero', true ) ) {
			$show_hero = false;
		}
		?>
		<footer class="aew-fov2" data-aew-footer-v2<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- value escaped via esc_attr above ?>>

			<?php if ( $show_hero && $hero_url ) : ?>
				<div class="aew-fov2__hero"
					role="img"
					aria-label="<?php echo esc_attr( $hero_alt ); ?>"
					style="background-image: url('<?php echo esc_url( $hero_url ); ?>');">
				</div>
			<?php endif; ?>

			<div class="aew-fov2__body">
				<div class="aew-fov2__body-inner">
					<div class="aew-fov2__inner">

						<!-- Logo -->
						<div class="aew-fov2__brand">
							<?php if ( $logo_url ) : ?>
								<a class="aew-fov2__logo"
									href="<?php echo esc_url( $logo_link['url'] ?: home_url( '/' ) ); ?>"
									<?php echo $logo_link['target'] ? 'target="' . esc_attr( $logo_link['target'] ) . '"' : ''; ?>>
									<img class="aew-fov2__logo-img"
										src="<?php echo esc_url( $logo_url ); ?>"
										alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
										decoding="async" loading="lazy" />
								</a>
							<?php endif; ?>
						</div>

						<!-- MENU column -->
						<div class="aew-fov2__col aew-fov2__col--menu">
							<h3 class="aew-fov2__heading"><?php echo esc_html( $s['menu_heading'] ?? 'MENU' ); ?></h3>
							<?php $this->render_link_list( $menu_tree ); ?>
						</div>

						<!-- QUICK LINKS column -->
						<div class="aew-fov2__col aew-fov2__col--quick">
							<h3 class="aew-fov2__heading"><?php echo esc_html( $s['quick_heading'] ?? 'QUICK LINKS' ); ?></h3>
							<?php $this->render_link_list( $quick_tree ); ?>
						</div>

						<!-- ADDRESS + CONTACT column -->
						<div class="aew-fov2__col aew-fov2__col--contact">
							<h3 class="aew-fov2__heading"><?php echo esc_html( $s['address_heading'] ?? 'ADDRESS' ); ?></h3>
							<address class="aew-fov2__address">
								<?php
								$line_1 = (string) ( $s['address_line_1'] ?? '' );
								$line_2 = (string) ( $s['address_line_2'] ?? '' );
								if ( $line_1 ) {
									echo esc_html( $line_1 );
									if ( $line_2 ) echo '<br>';
								}
								if ( $line_2 ) echo esc_html( $line_2 );
								?>
							</address>

							<h3 class="aew-fov2__heading aew-fov2__heading--gap"><?php echo esc_html( $s['contact_heading'] ?? 'CONTACT' ); ?></h3>
							<?php if ( $phone_raw ) : ?>
								<p class="aew-fov2__phone"><a href="tel:<?php echo esc_attr( $phone_tel ); ?>"><?php echo esc_html( $phone_raw ); ?></a></p>
							<?php endif; ?>
							<?php if ( $email ) : ?>
								<p class="aew-fov2__email"><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></p>
							<?php endif; ?>
						</div>

					</div><!-- /.aew-fov2__inner -->
				</div><!-- /.aew-fov2__body-inner -->

				<!-- Bottom bar -->
				<div class="aew-fov2__bottom">
					<div class="aew-fov2__inner aew-fov2__inner--bottom">
						<p class="aew-fov2__copyright"><?php echo esc_html( $s['copyright'] ?? '' ); ?></p>
						<?php
						$legal = $s['legal_links'] ?? [];
						if ( ! empty( $legal ) ) :
						?>
						<ul class="aew-fov2__legal">
							<?php foreach ( $legal as $item ) :
								$ud  = $item['url'] ?? [];
								$url = is_array( $ud ) ? ( $ud['url'] ?? '' ) : '';
								$target = ! empty( $ud['is_external'] ) ? '_blank' : '';
								if ( ! $url ) continue;
							?>
								<li>
									<a href="<?php echo esc_url( $url ); ?>"
										<?php echo $target ? 'target="' . esc_attr( $target ) . '"' : ''; ?>>
										<?php echo esc_html( $item['label'] ?? '' ); ?>
									</a>
								</li>
							<?php endforeach; ?>
						</ul>
						<?php endif; ?>
					</div>
				</div>
			</div><!-- /.aew-fov2__body -->
		</footer>
		<?php
	}

	// ─────────────────────────────────────────────────────────────────────────
	// HELPERS
	// ─────────────────────────────────────────────────────────────────────────

	private function render_link_list( array $items ): void {
		if ( empty( $items ) ) return;
		echo '<ul class="aew-fov2__list">';
		foreach ( $items as $item ) {
			$url    = $item['url'] ?? '';
			$label  = $item['label'] ?? '';
			$target = $item['target'] ?? '';
			if ( ! $url ) continue;
			printf(
				'<li class="aew-fov2__item"><a class="aew-fov2__link" href="%s"%s>%s</a></li>',
				esc_url( $url ),
				$target ? ' target="' . esc_attr( $target ) . '"' : '',
				esc_html( $label )
			);
		}
		echo '</ul>';
	}

	private function parse_link( $d ): array {
		if ( ! is_array( $d ) ) return [ 'url' => '', 'target' => '', 'rel' => '' ];
		$t = ! empty( $d['is_external'] ) ? '_blank' : '';
		$r = $t ? 'noopener' : '';
		if ( ! empty( $d['nofollow'] ) ) $r .= ' nofollow';
		return [ 'url' => $d['url'] ?? '', 'target' => $t, 'rel' => trim( $r ) ];
	}

	/**
	 * Returns a flat top-level array from a WP nav menu ID.
	 * Each entry: [ label, url, target ]
	 */
	private function nav_tree_from_menu_id( int $menu_id ): array {
		if ( $menu_id <= 0 ) return [];
		$raw = wp_get_nav_menu_items( $menu_id );
		if ( ! is_array( $raw ) ) return [];
		$out = [];
		foreach ( $raw as $item ) {
			if ( (int) $item->menu_item_parent !== 0 ) continue; // top-level only
			$out[] = [
				'label'  => $item->title,
				'url'    => $item->url ?? '',
				'target' => $item->target ?: '',
			];
		}
		return $out;
	}

	private function default_menu_id_for_location( string $location ): string {
		$locations = get_nav_menu_locations();
		if ( ! empty( $locations[ $location ] ) ) {
			return (string) (int) $locations[ $location ];
		}
		return '';
	}
}
