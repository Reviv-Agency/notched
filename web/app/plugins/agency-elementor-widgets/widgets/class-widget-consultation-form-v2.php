<?php
/**
 * Consultation Form V2 — Notched brand.
 *
 * Self-contained lead-capture banner: a large rounded photo on the left and an
 * overlapping white card on the right holding a heading, subtext and a WORKING
 * contact form (Name, Email, intl-tel Phone, Zip, Project message, consent
 * checkbox, brown CTA button).
 *
 * Submission is handled by this plugin (no Elementor Pro Form needed): the form
 * posts to admin-ajax (`aew_consultation_submit`), which validates, emails the
 * lead to a configurable address, and stores it in the `notched_leads` table.
 * Review leads under wp-admin → Notched Leads. See includes/class-lead-store.php.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Widget_Base;

class Widget_Consultation_Form_V2 extends Widget_Base {

	private const ASSET_SLUG = 'consultation-form-v2';

	public function get_name(): string      { return 'agency-consultation-form-v2'; }
	public function get_title(): string     { return esc_html__( 'Consultation Form V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-form-horizontal'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'form', 'consultation', 'contact', 'lead', 'notched' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's built-in _padding control to OUR inner wrapper so the
	 * outer block keeps its full-bleed background. Defaults left EMPTY — a
	 * non-empty default emits one non-responsive rule that clobbers the
	 * stylesheet's responsive X padding at every breakpoint (build guide §5,
	 * gotcha #16).
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-cfv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_photo();
		$this->controls_card();
		$this->controls_form();

		$this->style_body();
		$this->style_card();
		$this->style_heading();
		$this->style_subtext();
		$this->style_form();
	}

	private function controls_photo(): void {
		$this->start_controls_section( 's_photo', [ 'label' => 'Photo' ] );

		$this->add_control( 'show_photo', [
			'label'   => 'Show photo',
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$this->add_control( 'photo', [
			'label'     => 'Image',
			'type'      => Controls_Manager::MEDIA,
			'default'   => [ 'url' => Widget_Assets::url( self::ASSET_SLUG, 'images/consultation-hero.jpg' ) ],
			'condition' => [ 'show_photo' => 'yes' ],
		] );

		$this->add_control( 'photo_alt', [
			'label'     => 'Alt text',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'A custom Notched backyard pergola and seating area',
			'condition' => [ 'show_photo' => 'yes' ],
		] );

		$this->add_control( 'photo_focus', [
			'label'   => 'Image focus',
			'type'    => Controls_Manager::SELECT,
			'default' => 'center',
			'options' => [ 'left' => 'Left', 'center' => 'Center', 'right' => 'Right' ],
			'selectors' => [ '{{WRAPPER}} .aew-cfv2__photo-img' => 'object-position: {{VALUE}} center;' ],
			'condition' => [ 'show_photo' => 'yes' ],
		] );

		$this->end_controls_section();
	}

	private function controls_card(): void {
		$this->start_controls_section( 's_card', [ 'label' => 'Card content' ] );

		$this->add_control( 'heading', [
			'label'   => 'Heading',
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => "Need help getting started? Let's talk!",
		] );

		$this->add_control( 'subtext', [
			'label'   => 'Subtext',
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => "Fill out your information and we'll get in touch ASAP!",
		] );

		$this->end_controls_section();
	}

	private function controls_form(): void {
		$this->start_controls_section( 's_form', [ 'label' => 'Form' ] );

		$this->add_control( 'notify_email', [
			'label'       => 'Send leads to (email)',
			'type'        => Controls_Manager::TEXT,
			'input_type'  => 'email',
			'default'     => '',
			'placeholder' => 'sales@notched.com',
			'description' => 'Leave blank to only store leads in wp-admin → Notched Leads (no email sent).',
		] );

		// Field labels (editable).
		$this->add_control( 'label_name',    [ 'label' => 'Name label',    'type' => Controls_Manager::TEXT, 'default' => 'Your Name' ] );
		$this->add_control( 'label_email',   [ 'label' => 'Email label',   'type' => Controls_Manager::TEXT, 'default' => 'Your Email' ] );
		$this->add_control( 'label_phone',   [ 'label' => 'Phone label',   'type' => Controls_Manager::TEXT, 'default' => 'Your Phone' ] );
		$this->add_control( 'label_zip',     [ 'label' => 'Zip label',     'type' => Controls_Manager::TEXT, 'default' => 'Your Zip Code' ] );
		$this->add_control( 'label_message', [ 'label' => 'Message label', 'type' => Controls_Manager::TEXT, 'default' => 'Tell us a bit about your project!' ] );

		$this->add_control( 'consent_text', [
			'label'   => 'Consent text',
			'type'    => Controls_Manager::TEXT,
			'default' => 'I agree to be contacted by a notched expert!',
		] );

		$this->add_control( 'button_text', [
			'label'   => 'Button text',
			'type'    => Controls_Manager::TEXT,
			'default' => 'Get Your Free Consultation',
		] );

		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ────────────────────────────────────────────────────────

	private function style_body(): void {
		$this->start_controls_section( 'ss_body', [ 'label' => 'Section', 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'body_bg', [
			'label'     => 'Section background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-body-bg: {{VALUE}};' ],
		] );

		$this->add_responsive_control( 'body_max_w', [
			'label'      => 'Inner max width',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 800, 'max' => 1920 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 1600 ],
			'selectors'  => [ '{{WRAPPER}} .aew-cfv2__inner' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		$this->add_control( 'photo_radius', [
			'label'      => 'Photo corner radius',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-cfv2__photo' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_card(): void {
		$this->start_controls_section( 'ss_card', [ 'label' => 'Card', 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'card_bg', [
			'label'     => 'Card background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-card-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'card_radius', [
			'label'      => 'Card corner radius',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 24 ],
			'selectors'  => [ '{{WRAPPER}} .aew-cfv2__card' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_heading(): void {
		$this->start_controls_section( 'ss_heading', [ 'label' => 'Heading', 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'heading_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-heading: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'heading_typo',
			'selector'       => '{{WRAPPER}} .aew-cfv2__heading',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 40 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_subtext(): void {
		$this->start_controls_section( 'ss_subtext', [ 'label' => 'Subtext', 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'subtext_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-subtext: {{VALUE}};' ],
		] );

		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'           => 'subtext_typo',
			'selector'       => '{{WRAPPER}} .aew-cfv2__subtext',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'line_height' => [ 'default' => [ 'unit' => '%', 'size' => 140 ] ],
			],
		] );

		$this->end_controls_section();
	}

	private function style_form(): void {
		$this->start_controls_section( 'ss_form', [ 'label' => 'Form', 'tab' => Controls_Manager::TAB_STYLE ] );

		$this->add_control( 'form_label_color', [
			'label'     => 'Field label color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-form-label: {{VALUE}};' ],
		] );

		$this->add_control( 'form_field_bg', [
			'label'     => 'Field background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-form-field-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'form_field_border', [
			'label'     => 'Field border color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#BFC0BF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-form-field-border: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg', [
			'label'     => 'Button background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-btn-bg: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_bg_hover', [
			'label'     => 'Button background (hover)',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#876137',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-btn-bg-hover: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text', [
			'label'     => 'Button text color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-btn-text: {{VALUE}};' ],
		] );

		$this->add_control( 'btn_text_hover', [
			'label'     => 'Button text color (hover)',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#FFFFFF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-cfv2-btn-text-hover: {{VALUE}};' ],
		] );

		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s = $this->get_settings_for_display();

		$photo     = $s['photo'] ?? [];
		$photo_url = is_array( $photo ) ? ( $photo['url'] ?? '' ) : '';
		// Computed MEDIA defaults don't backfill onto legacy saved instances
		// (gotcha #17): fall back to the bundled asset when empty.
		if ( '' === $photo_url ) {
			$photo_url = Widget_Assets::url( self::ASSET_SLUG, 'images/consultation-hero.jpg' );
		}
		$photo_alt  = (string) ( $s['photo_alt'] ?? '' );
		$show_photo = 'yes' === ( $s['show_photo'] ?? 'yes' );

		$heading = (string) ( $s['heading'] ?? '' );
		$subtext = (string) ( $s['subtext'] ?? '' );

		$notify_email = sanitize_email( (string) ( $s['notify_email'] ?? '' ) );
		$button_text  = (string) ( $s['button_text'] ?? 'Get Your Free Consultation' );
		$consent_text = (string) ( $s['consent_text'] ?? 'I agree to be contacted by a notched expert!' );

		$uid = 'aew-cfv2-' . $this->get_id();

		/*
		 * Resolved colours → inline CSS vars on the wrapper (build guide §6.8).
		 */
		$color_vars = Color_Vars::build(
			$this,
			$s,
			[
				'body_bg'           => '--aew-cfv2-body-bg',
				'card_bg'           => '--aew-cfv2-card-bg',
				'heading_color'     => '--aew-cfv2-heading',
				'subtext_color'     => '--aew-cfv2-subtext',
				'form_label_color'  => '--aew-cfv2-form-label',
				'form_field_bg'     => '--aew-cfv2-form-field-bg',
				'form_field_border' => '--aew-cfv2-form-field-border',
				'btn_bg'            => '--aew-cfv2-btn-bg',
				'btn_bg_hover'      => '--aew-cfv2-btn-bg-hover',
				'btn_text'          => '--aew-cfv2-btn-text',
				'btn_text_hover'    => '--aew-cfv2-btn-text-hover',
			]
		);
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		$ajax_url = admin_url( 'admin-ajax.php' );
		$nonce    = wp_create_nonce( Lead_Store::nonce_action() );
		?>
		<section class="aew-cfv2" data-aew-consultation-form-v2<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_attr above ?>>
			<div class="aew-cfv2__inner">

				<?php if ( $show_photo && $photo_url ) : ?>
					<div class="aew-cfv2__photo">
						<img class="aew-cfv2__photo-img"
							src="<?php echo esc_url( $photo_url ); ?>"
							alt="<?php echo esc_attr( $photo_alt ); ?>"
							loading="lazy"
							decoding="async" />
					</div>
				<?php endif; ?>

				<div class="aew-cfv2__card">
					<?php if ( '' !== trim( $heading ) ) : ?>
						<h2 class="aew-cfv2__heading" id="<?php echo esc_attr( $uid ); ?>-h"><?php echo esc_html( $heading ); ?></h2>
					<?php endif; ?>

					<?php if ( '' !== trim( $subtext ) ) : ?>
						<p class="aew-cfv2__subtext"><?php echo esc_html( $subtext ); ?></p>
					<?php endif; ?>

					<form class="aew-cfv2__form"
						action="<?php echo esc_url( $ajax_url ); ?>"
						method="post"
						novalidate
						aria-labelledby="<?php echo esc_attr( $uid ); ?>-h"
						data-aew-cfv2-form>

						<input type="hidden" name="action" value="<?php echo esc_attr( Lead_Store::ajax_action() ); ?>" />
						<input type="hidden" name="_aew_nonce" value="<?php echo esc_attr( $nonce ); ?>" />
						<input type="hidden" name="notify_email" value="<?php echo esc_attr( $notify_email ); ?>" />
						<input type="hidden" name="source_url" value="<?php echo esc_url( home_url( add_query_arg( null, null ) ) ); ?>" />
						<?php // Honeypot — hidden from humans, tempting to bots. ?>
						<div class="aew-cfv2__hp" aria-hidden="true">
							<label>Website<input type="text" name="website" tabindex="-1" autocomplete="off" /></label>
						</div>

						<div class="aew-cfv2__fields">
							<div class="aew-cfv2__field aew-cfv2__field--half">
								<label class="aew-cfv2__label" for="<?php echo esc_attr( $uid ); ?>-name"><?php echo esc_html( $s['label_name'] ?? 'Your Name' ); ?> <span class="aew-cfv2__req">*</span></label>
								<input class="aew-cfv2__input" id="<?php echo esc_attr( $uid ); ?>-name" type="text" name="name" required autocomplete="name" />
							</div>

							<div class="aew-cfv2__field aew-cfv2__field--half">
								<label class="aew-cfv2__label" for="<?php echo esc_attr( $uid ); ?>-email"><?php echo esc_html( $s['label_email'] ?? 'Your Email' ); ?> <span class="aew-cfv2__req">*</span></label>
								<input class="aew-cfv2__input" id="<?php echo esc_attr( $uid ); ?>-email" type="email" name="email" required autocomplete="email" />
							</div>

							<div class="aew-cfv2__field aew-cfv2__field--half">
								<label class="aew-cfv2__label" for="<?php echo esc_attr( $uid ); ?>-phone"><?php echo esc_html( $s['label_phone'] ?? 'Your Phone' ); ?> <span class="aew-cfv2__req">*</span></label>
								<input class="aew-cfv2__input" id="<?php echo esc_attr( $uid ); ?>-phone" type="tel" name="phone" required autocomplete="tel" />
							</div>

							<div class="aew-cfv2__field aew-cfv2__field--half">
								<label class="aew-cfv2__label" for="<?php echo esc_attr( $uid ); ?>-zip"><?php echo esc_html( $s['label_zip'] ?? 'Your Zip Code' ); ?> <span class="aew-cfv2__req">*</span></label>
								<input class="aew-cfv2__input" id="<?php echo esc_attr( $uid ); ?>-zip" type="text" name="zip" required autocomplete="postal-code" inputmode="numeric" />
							</div>

							<div class="aew-cfv2__field aew-cfv2__field--full">
								<label class="aew-cfv2__label" for="<?php echo esc_attr( $uid ); ?>-message"><?php echo esc_html( $s['label_message'] ?? 'Tell us a bit about your project!' ); ?> <span class="aew-cfv2__req">*</span></label>
								<textarea class="aew-cfv2__input aew-cfv2__textarea" id="<?php echo esc_attr( $uid ); ?>-message" name="message" rows="4" required></textarea>
							</div>

							<div class="aew-cfv2__field aew-cfv2__field--full aew-cfv2__field--submit">
								<button class="aew-cfv2__btn" type="submit"><?php echo esc_html( $button_text ); ?></button>
							</div>

							<div class="aew-cfv2__field aew-cfv2__field--full aew-cfv2__consent">
								<label class="aew-cfv2__consent-label">
									<input class="aew-cfv2__checkbox" type="checkbox" name="consent" value="1" required />
									<span><?php echo esc_html( $consent_text ); ?> <span class="aew-cfv2__req">*</span></span>
								</label>
							</div>
						</div>

						<p class="aew-cfv2__status" role="status" aria-live="polite" hidden></p>
					</form>
				</div>

			</div>
		</section>
		<?php
	}
}
