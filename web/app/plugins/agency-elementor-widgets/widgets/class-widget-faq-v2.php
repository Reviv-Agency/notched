<?php
/**
 * FAQ V2 — Notched brand.
 *
 * Matches the Wix "Questions Customers Ask" layout:
 *   - Title + search box ("Looking for something?")
 *   - Vertical category rail on the left (tabs filter the question list)
 *   - Searchable accordion of question/answer items (plus / minus icons)
 *   - Per-answer social share bar (Facebook / X / LinkedIn / Copy link)
 *
 * Additive widget — does NOT replace the legacy `agency-faq-accordion`.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Repeater;
use Elementor\Widget_Base;

class Widget_Faq_V2 extends Widget_Base {

	private const ASSET_SLUG = 'faq-v2';

	public function get_name(): string      { return 'agency-faq-v2'; }
	public function get_title(): string     { return esc_html__( 'FAQ V2 (Notched)', 'agency-elementor-widgets' ); }
	public function get_icon(): string      { return 'eicon-accordion'; }
	public function get_categories(): array { return [ 'agency-widgets' ]; }
	public function get_keywords(): array   { return [ 'faq', 'questions', 'accordion', 'search', 'notched' ]; }

	public function get_style_depends(): array  { return [ 'aew-tokens', Widget_Assets::handle( self::ASSET_SLUG ) ]; }
	public function get_script_depends(): array { return [ Widget_Assets::handle( self::ASSET_SLUG ) ]; }

	/**
	 * Re-point Elementor's _padding control to OUR inner wrapper so the outer
	 * block keeps its full-bleed background. Defaults left EMPTY so the
	 * stylesheet owns the responsive X padding (gotcha #16).
	 */
	public function get_stack( $with_common_controls = true ) {
		$stack = parent::get_stack( $with_common_controls );
		if ( $with_common_controls && isset( $stack['controls']['_padding'] ) ) {
			$stack['controls']['_padding']['selectors']      = [ '{{WRAPPER}} .aew-faqv2__inner' => 'padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}};' ];
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
		$this->controls_intro();
		$this->controls_categories();
		$this->controls_questions();
		$this->controls_share();

		$this->style_body();
		$this->style_title();
		$this->style_search();
		$this->style_categories();
		$this->style_cards();
		$this->style_typography();
	}

	private function controls_intro(): void {
		$this->start_controls_section( 's_intro', [ 'label' => 'Intro' ] );
		$this->add_control( 'title', [
			'label'   => 'Title',
			'type'    => Controls_Manager::TEXT,
			'default' => 'QUESTIONS CUSTOMERS ASK',
		] );
		$this->add_control( 'show_search', [
			'label'   => 'Show search box',
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );
		$this->add_control( 'search_placeholder', [
			'label'     => 'Search placeholder',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'Looking for something?',
			'condition' => [ 'show_search' => 'yes' ],
		] );
		$this->add_control( 'no_results_text', [
			'label'     => 'No-results text',
			'type'      => Controls_Manager::TEXT,
			'default'   => 'No questions match your search.',
			'condition' => [ 'show_search' => 'yes' ],
		] );
		$this->end_controls_section();
	}

	private function controls_categories(): void {
		$this->start_controls_section( 's_categories', [ 'label' => 'Categories' ] );
		$this->add_control( 'show_categories', [
			'label'   => 'Show category rail',
			'type'    => Controls_Manager::SWITCHER,
			'default' => 'yes',
		] );

		$rep = new Repeater();
		$rep->add_control( 'cat_label', [
			'label'   => 'Category name',
			'type'    => Controls_Manager::TEXT,
			'default' => 'Category',
		] );

		$this->add_control( 'categories', [
			'label'       => 'Categories',
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ cat_label }}}',
			'default'     => $this->default_categories(),
			'description' => 'The left rail order. Match a question to a category by typing the SAME name in the question\'s Category field.',
			'condition'   => [ 'show_categories' => 'yes' ],
		] );
		$this->end_controls_section();
	}

	private function controls_questions(): void {
		$this->start_controls_section( 's_questions', [ 'label' => 'Questions' ] );

		$rep = new Repeater();
		$rep->add_control( 'question', [
			'label'   => 'Question',
			'type'    => Controls_Manager::TEXTAREA,
			'rows'    => 2,
			'default' => 'Question',
		] );
		$rep->add_control( 'answer', [
			'label'   => 'Answer',
			'type'    => Controls_Manager::WYSIWYG,
			'default' => 'Answer text.',
		] );
		$rep->add_control( 'category', [
			'label'       => 'Category',
			'type'        => Controls_Manager::TEXT,
			'default'     => '',
			'description' => 'Type a category name exactly as it appears in the Categories list to group this question under that tab.',
		] );
		$rep->add_control( 'open_by_default', [
			'label'        => 'Open by default',
			'type'         => Controls_Manager::SWITCHER,
			'return_value' => 'yes',
			'default'      => '',
		] );

		$this->add_control( 'questions', [
			'label'       => 'Questions',
			'type'        => Controls_Manager::REPEATER,
			'fields'      => $rep->get_controls(),
			'title_field' => '{{{ question }}}',
			'default'     => $this->default_questions(),
		] );
		$this->end_controls_section();
	}

	private function controls_share(): void {
		$this->start_controls_section( 's_share', [ 'label' => 'Social share' ] );
		$this->add_control( 'show_share', [
			'label'       => 'Show share bar in answers',
			'type'        => Controls_Manager::SWITCHER,
			'default'     => 'yes',
			'description' => 'Facebook / X / LinkedIn / Copy-link row inside each open answer.',
		] );
		$this->add_control( 'show_share_facebook', [ 'label' => 'Facebook', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'show_share' => 'yes' ] ] );
		$this->add_control( 'show_share_twitter',  [ 'label' => 'X (Twitter)', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'show_share' => 'yes' ] ] );
		$this->add_control( 'show_share_linkedin', [ 'label' => 'LinkedIn', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'show_share' => 'yes' ] ] );
		$this->add_control( 'show_share_copy',     [ 'label' => 'Copy link', 'type' => Controls_Manager::SWITCHER, 'default' => 'yes', 'condition' => [ 'show_share' => 'yes' ] ] );
		$this->end_controls_section();
	}

	// ── STYLE SECTIONS ────────────────────────────────────────────────────────

	private function style_body(): void {
		$this->start_controls_section( 'ss_body', [ 'label' => 'Body', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'body_bg', [
			'label'     => 'Outer background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'description' => 'The full-bleed band behind the FAQ panel.',
			'selectors' => [
				// Belt + suspenders: drive the CSS var (consumed in the stylesheet)
				// AND paint the property directly so it shows even where the var
				// fallback or host section would otherwise win.
				'{{WRAPPER}}'              => '--aew-faqv2-body-bg: {{VALUE}};',
				'{{WRAPPER}} .aew-faqv2'    => 'background-color: {{VALUE}};',
			],
		] );
		$this->add_responsive_control( 'body_max_w', [
			'label'      => 'Inner max width',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 800, 'max' => 1920 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 1440 ],
			'selectors'  => [ '{{WRAPPER}} .aew-faqv2__inner' => 'max-width: {{SIZE}}{{UNIT}};' ],
		] );

		// ── Inner panel: the rounded card holding the FAQ (its own bg + radius). ──
		$this->add_control( 'h_panel', [
			'label'     => 'Inner panel',
			'type'      => Controls_Manager::HEADING,
			'separator' => 'before',
		] );
		$this->add_control( 'panel_bg', [
			'label'     => 'Panel background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '',
			'description' => 'Background of the inner panel that wraps the FAQ. Leave empty for transparent (panel = same as outer).',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-panel-bg: {{VALUE}};' ],
		] );
		$this->add_responsive_control( 'panel_radius', [
			'label'      => 'Panel border radius',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 64 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 0 ],
			'selectors'  => [ '{{WRAPPER}} .aew-faqv2__panel' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_responsive_control( 'panel_padding', [
			'label'      => 'Panel padding',
			'type'       => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px' ],
			'default'        => [ 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'unit' => 'px', 'isLinked' => false ],
			'selectors'  => [ '{{WRAPPER}} .aew-faqv2__panel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ],
		] );

		$this->end_controls_section();
	}

	private function style_title(): void {
		$this->start_controls_section( 'ss_title', [ 'label' => 'Title', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'title_color', [
			'label'     => 'Color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#2A4F41',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-title: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'title_typo',
			'selector' => '{{WRAPPER}} .aew-faqv2__title',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 40 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 24 ] ],
				'line_height'    => [ 'default' => [ 'unit' => '%', 'size' => 85 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_search(): void {
		$this->start_controls_section( 'ss_search', [ 'label' => 'Search box', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_search' => 'yes' ] ] );
		$this->add_control( 'search_text_color', [
			'label'     => 'Text color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-search-text: {{VALUE}};' ],
		] );
		$this->add_control( 'search_border_color', [
			'label'     => 'Underline color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#BFC0BF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-search-border: {{VALUE}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_categories(): void {
		$this->start_controls_section( 'ss_categories', [ 'label' => 'Category rail', 'tab' => Controls_Manager::TAB_STYLE, 'condition' => [ 'show_categories' => 'yes' ] ] );
		$this->add_control( 'cat_active_color', [
			'label'     => 'Active color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#AA7D44',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-cat-active: {{VALUE}};' ],
		] );
		$this->add_control( 'cat_color', [
			'label'     => 'Inactive color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-cat: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'cat_typo',
			'selector' => '{{WRAPPER}} .aew-faqv2__cat',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 24 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 0.5 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->end_controls_section();
	}

	private function style_cards(): void {
		$this->start_controls_section( 'ss_cards', [ 'label' => 'Question cards', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'card_bg', [
			'label'     => 'Card background',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#F6F0EC',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-card-bg: {{VALUE}};' ],
		] );
		$this->add_control( 'card_border_color', [
			'label'     => 'Card border',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#BFC0BF',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-card-border: {{VALUE}};' ],
		] );
		$this->add_control( 'icon_color', [
			'label'     => 'Expand/collapse icon',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-icon: {{VALUE}};' ],
		] );
		$this->add_responsive_control( 'card_gap', [
			'label'      => 'Gap between cards',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 12 ],
			'selectors'  => [ '{{WRAPPER}} .aew-faqv2__list' => 'gap: {{SIZE}}{{UNIT}};' ],
		] );
		$this->add_responsive_control( 'card_radius', [
			'label'      => 'Card radius',
			'type'       => Controls_Manager::SLIDER,
			'size_units' => [ 'px' ],
			'range'      => [ 'px' => [ 'min' => 0, 'max' => 48 ] ],
			'default'    => [ 'unit' => 'px', 'size' => 8 ],
			'selectors'  => [ '{{WRAPPER}} .aew-faqv2__item' => 'border-radius: {{SIZE}}{{UNIT}};' ],
		] );
		$this->end_controls_section();
	}

	private function style_typography(): void {
		$this->start_controls_section( 'ss_typo', [ 'label' => 'Question & answer text', 'tab' => Controls_Manager::TAB_STYLE ] );
		$this->add_control( 'question_color', [
			'label'     => 'Question color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-question: {{VALUE}};' ],
		] );
		$this->add_control( 'answer_color', [
			'label'     => 'Answer color',
			'type'      => Controls_Manager::COLOR,
			'default'   => '#141C19',
			'selectors' => [ '{{WRAPPER}}' => '--aew-faqv2-answer: {{VALUE}};' ],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'question_typo',
			'selector' => '{{WRAPPER}} .aew-faqv2__question',
			'fields_options' => [
				'font_family'    => [ 'default' => 'Teko' ],
				'font_weight'    => [ 'default' => '600' ],
				'font_size'      => [ 'default' => [ 'unit' => 'px', 'size' => 18 ] ],
				'letter_spacing' => [ 'default' => [ 'unit' => 'px', 'size' => 0.5 ] ],
				'text_transform' => [ 'default' => 'uppercase' ],
			],
		] );
		$this->add_group_control( Group_Control_Typography::get_type(), [
			'name'     => 'answer_typo',
			'selector' => '{{WRAPPER}} .aew-faqv2__answer',
			'fields_options' => [
				'font_family' => [ 'default' => 'Lato' ],
				'font_weight' => [ 'default' => '400' ],
				'font_size'   => [ 'default' => [ 'unit' => 'px', 'size' => 16 ], 'mobile_default' => [ 'unit' => 'px', 'size' => 14 ] ],
				'line_height' => [ 'default' => [ 'unit' => 'em', 'size' => 1.5 ] ],
			],
		] );
		$this->end_controls_section();
	}

	// ─────────────────────────────────────────────────────────────────────────
	// RENDER
	// ─────────────────────────────────────────────────────────────────────────

	protected function render(): void {
		$s   = $this->get_settings_for_display();
		$wid = $this->get_id();

		$show_search = 'yes' === ( $s['show_search'] ?? 'yes' );
		$show_cats   = 'yes' === ( $s['show_categories'] ?? 'yes' );
		$show_share  = 'yes' === ( $s['show_share'] ?? 'yes' );

		// Fall back to the bundled defaults when the saved repeaters are empty.
		// Elementor's editor does NOT materialize a REPEATER control's `default`
		// array in the live-preview, and instances saved before content existed
		// store nothing — so without this fallback a freshly-inserted widget can
		// render empty. (Same emission gotcha as media-control defaults, #17.)
		$questions  = ! empty( $s['questions'] ) && is_array( $s['questions'] ) ? $s['questions'] : $this->default_questions();
		$categories = $show_cats
			? ( ! empty( $s['categories'] ) && is_array( $s['categories'] ) ? $s['categories'] : $this->default_categories() )
			: [];

		// Determine the first non-empty question index to open by default.
		$default_open = null;
		foreach ( $questions as $i => $q ) {
			if ( '' === trim( (string) ( $q['question'] ?? '' ) ) ) {
				continue;
			}
			if ( 'yes' === ( $q['open_by_default'] ?? '' ) ) {
				$default_open = $i;
				break;
			}
			if ( null === $default_open ) {
				$default_open = $i; // fall back to first valid item
			}
		}

		// Active category = the first category that actually has a question.
		$active_cat = '';
		if ( $show_cats && $categories ) {
			foreach ( $categories as $c ) {
				$label = trim( (string) ( $c['cat_label'] ?? '' ) );
				if ( '' === $label ) {
					continue;
				}
				foreach ( $questions as $q ) {
					if ( in_array( $this->norm( $label ), $this->cat_list( (string) ( $q['category'] ?? '' ) ), true ) ) {
						$active_cat = $label;
						break 2;
					}
				}
			}
		}

		$color_vars = Color_Vars::build( $this, $s, [
			'body_bg'             => '--aew-faqv2-body-bg',
			'panel_bg'            => '--aew-faqv2-panel-bg',
			'title_color'         => '--aew-faqv2-title',
			'search_text_color'   => '--aew-faqv2-search-text',
			'search_border_color' => '--aew-faqv2-search-border',
			'cat_active_color'    => '--aew-faqv2-cat-active',
			'cat_color'           => '--aew-faqv2-cat',
			'card_bg'             => '--aew-faqv2-card-bg',
			'card_border_color'   => '--aew-faqv2-card-border',
			'icon_color'          => '--aew-faqv2-icon',
			'question_color'      => '--aew-faqv2-question',
			'answer_color'        => '--aew-faqv2-answer',
		] );
		$style_attr = '' !== $color_vars ? ' style="' . esc_attr( $color_vars ) . '"' : '';

		$has_sidebar = $show_cats && ! empty( $categories );
		?>
		<section class="aew-faqv2<?php echo $has_sidebar ? ' aew-faqv2--has-sidebar' : ''; ?>"
			data-aew-faq-v2<?php echo $style_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped via esc_attr above ?>>
			<div class="aew-faqv2__inner">
			<div class="aew-faqv2__panel">

				<!-- Header: title + search -->
				<div class="aew-faqv2__header">
					<?php if ( ! empty( $s['title'] ) ) : ?>
						<h2 class="aew-faqv2__title"><?php echo esc_html( $s['title'] ); ?></h2>
					<?php endif; ?>

					<?php if ( $show_search ) : ?>
						<div class="aew-faqv2__search">
							<input type="text"
								class="aew-faqv2__search-input"
								data-aew-faqv2-search
								placeholder="<?php echo esc_attr( $s['search_placeholder'] ?? 'Looking for something?' ); ?>"
								aria-label="<?php echo esc_attr( $s['search_placeholder'] ?? 'Looking for something?' ); ?>" />
							<span class="aew-faqv2__search-icon" aria-hidden="true">
								<svg viewBox="0 0 24 24" fill="currentColor" width="20" height="20"><path fill-rule="evenodd" d="M6.6108,14.3887 C5.5718,13.3497 4.9998,11.9687 4.9998,10.4997 C4.9998,9.0317 5.5718,7.6507 6.6108,6.6117 C7.6498,5.5727 9.0308,4.9997 10.4998,4.9997 C11.9688,4.9997 13.3498,5.5727 14.3888,6.6117 C15.4278,7.6507 15.9998,9.0317 15.9998,10.4997 C15.9998,11.9687 15.4278,13.3497 14.3888,14.3887 C13.3498,15.4277 11.9688,15.9997 10.4998,15.9997 C9.0308,15.9997 7.6498,15.4277 6.6108,14.3887 Z M20.0028,19.2957 L15.4328,14.7247 C16.4438,13.5477 16.9998,12.0687 16.9998,10.4997 C16.9998,8.7637 16.3238,7.1317 15.0958,5.9047 C13.8688,4.6767 12.2358,3.9997 10.4998,3.9997 C8.7638,3.9997 7.1318,4.6767 5.9038,5.9047 C4.6758,7.1317 3.9998,8.7637 3.9998,10.4997 C3.9998,12.2367 4.6758,13.8677 5.9038,15.0957 C7.1318,16.3237 8.7638,16.9997 10.4998,16.9997 C12.0688,16.9997 13.5478,16.4437 14.7258,15.4317 L19.2958,20.0027 L20.0028,19.2957 Z"></path></svg>
							</span>
						</div>
					<?php endif; ?>
				</div>

				<div class="aew-faqv2__layout">

					<!-- Category rail -->
					<?php if ( $has_sidebar ) : ?>
						<div class="aew-faqv2__rail" role="tablist" aria-orientation="vertical" aria-label="<?php esc_attr_e( 'Categories', 'agency-elementor-widgets' ); ?>">
							<?php foreach ( $categories as $ci => $c ) :
								$label = trim( (string) ( $c['cat_label'] ?? '' ) );
								if ( '' === $label ) {
									continue;
								}
								$is_active = $this->norm( $label ) === $this->norm( $active_cat );
								?>
								<button type="button"
									class="aew-faqv2__cat<?php echo $is_active ? ' is-active' : ''; ?>"
									role="tab"
									data-aew-faqv2-cat="<?php echo esc_attr( $this->norm( $label ) ); ?>"
									aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
									tabindex="<?php echo $is_active ? '0' : '-1'; ?>">
									<?php echo esc_html( $label ); ?>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<!-- Questions -->
					<div class="aew-faqv2__main">
						<?php if ( $show_search ) : ?>
							<p class="aew-faqv2__noresults" data-aew-faqv2-noresults hidden>
								<?php echo esc_html( $s['no_results_text'] ?? 'No questions match your search.' ); ?>
							</p>
						<?php endif; ?>

						<div class="aew-faqv2__list" data-aew-faqv2-list>
							<?php foreach ( $questions as $i => $q ) :
								$question = trim( (string) ( $q['question'] ?? '' ) );
								if ( '' === $question ) {
									continue;
								}
								$answer   = (string) ( $q['answer'] ?? '' );
								$cats_q   = $this->cat_list( (string) ( $q['category'] ?? '' ) );
								$is_open  = ( null !== $default_open && (int) $i === (int) $default_open );

								// Only the active category's items are visible on first paint.
								// A question can list multiple categories, so match if the
								// active category is among them.
								$cat_match  = ! $has_sidebar || '' === $active_cat || in_array( $this->norm( $active_cat ), $cats_q, true );
								// JS reads this pipe-delimited list (with leading/trailing
								// pipes) so a category click can substring-match "|slug|".
								$cat_attr   = $cats_q ? '|' . implode( '|', $cats_q ) . '|' : '';
								$item_id    = 'aew-faqv2-' . esc_attr( $wid ) . '-q' . $i;
								$panel_id   = $item_id . '-panel';
								?>
								<div class="aew-faqv2__item<?php echo $is_open ? ' is-open' : ''; ?>"
									data-aew-faqv2-item
									data-cat="<?php echo esc_attr( $cat_attr ); ?>"
									data-text="<?php echo esc_attr( $this->norm( $question ) ); ?>"
									<?php echo $cat_match ? '' : 'hidden'; ?>>
									<button type="button"
										class="aew-faqv2__trigger"
										id="<?php echo esc_attr( $item_id ); ?>"
										aria-expanded="<?php echo $is_open ? 'true' : 'false'; ?>"
										aria-controls="<?php echo esc_attr( $panel_id ); ?>">
										<span class="aew-faqv2__question"><?php echo esc_html( $question ); ?></span>
										<span class="aew-faqv2__icon" aria-hidden="true">
											<svg class="aew-faqv2__icon-plus" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path fill-rule="evenodd" d="M13,5 L13,12 L20,12 L20,13 L13,13 L13,20 L12,20 L11.999,13 L5,13 L5,12 L12,12 L12,5 L13,5 Z"></path></svg>
											<svg class="aew-faqv2__icon-minus" viewBox="0 0 24 24" fill="currentColor" width="24" height="24"><path fill-rule="evenodd" d="M20,12 L20,13 L5,13 L5,12 L20,12 Z"></path></svg>
										</span>
									</button>
									<div class="aew-faqv2__panel"
										id="<?php echo esc_attr( $panel_id ); ?>"
										role="region"
										aria-labelledby="<?php echo esc_attr( $item_id ); ?>"
										<?php echo $is_open ? '' : 'hidden'; ?>>
										<div class="aew-faqv2__answer aew-rich-text">
											<?php Rich_Text::echo_html( $answer ); ?>
										</div>
										<?php if ( $show_share ) {
											$this->render_share_bar( $s, $wid, $i );
										} ?>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					</div><!-- /.aew-faqv2__main -->
				</div><!-- /.aew-faqv2__layout -->
			</div><!-- /.aew-faqv2__panel -->
			</div><!-- /.aew-faqv2__inner -->
		</section>
		<?php
	}

	// ─────────────────────────────────────────────────────────────────────────
	// HELPERS
	// ─────────────────────────────────────────────────────────────────────────

	/** Normalize a string for case-insensitive matching/search. */
	private function norm( string $v ): string {
		return strtolower( trim( $v ) );
	}

	/**
	 * A question's `category` field may list MULTIPLE categories, comma-separated,
	 * so one question can appear under more than one tab (e.g. the first tab shows
	 * a curated mix). Returns a normalized array of category slugs.
	 *
	 * @return array<int,string>
	 */
	private function cat_list( string $v ): array {
		$out = [];
		foreach ( explode( ',', $v ) as $part ) {
			$n = $this->norm( $part );
			if ( '' !== $n ) {
				$out[] = $n;
			}
		}
		return $out;
	}

	/**
	 * Per-answer social share bar. Links resolve client-side (JS rewrites the
	 * deep-link to the current page URL + ?questionId=); server-side we emit a
	 * sensible href fallback.
	 */
	private function render_share_bar( array $s, $wid, int $i ): void {
		$fb   = 'yes' === ( $s['show_share_facebook'] ?? 'yes' );
		$tw   = 'yes' === ( $s['show_share_twitter'] ?? 'yes' );
		$li   = 'yes' === ( $s['show_share_linkedin'] ?? 'yes' );
		$copy = 'yes' === ( $s['show_share_copy'] ?? 'yes' );
		if ( ! $fb && ! $tw && ! $li && ! $copy ) {
			return;
		}
		$qid = 'q-' . $wid . '-' . $i;
		?>
		<div class="aew-faqv2__share" data-aew-faqv2-share data-qid="<?php echo esc_attr( $qid ); ?>">
			<?php if ( $fb ) : ?>
				<a class="aew-faqv2__share-btn" data-share="facebook" href="#" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on Facebook', 'agency-elementor-widgets' ); ?>">
					<svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24" aria-hidden="true"><path fill-rule="evenodd" d="M20,12 C20,7.58171875 16.4182812,4 12,4 C7.58171875,4 4,7.58171875 4,12 C4,15.9930312 6.92548437,19.3026562 10.75,19.9028125 L10.75,14.3125 L8.71875,14.3125 L8.71875,12 L10.75,12 L10.75,10.2375 C10.75,8.2325 11.9443437,7.125 13.7717187,7.125 C14.6469844,7.125 15.5625,7.28125 15.5625,7.28125 L15.5625,9.25 L14.5537187,9.25 C13.5599219,9.25 13.25,9.86667187 13.25,10.4993281 L13.25,12 L15.46875,12 L15.1140625,14.3125 L13.25,14.3125 L13.25,19.9028125 C17.0745156,19.3026562 20,15.9930312 20,12"></path></svg>
				</a>
			<?php endif; ?>
			<?php if ( $tw ) : ?>
				<a class="aew-faqv2__share-btn" data-share="twitter" href="#" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on X', 'agency-elementor-widgets' ); ?>">
					<svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24" aria-hidden="true"><path d="M13.303 10.771 19.123 4h-1.38L12.69 9.88 8.655 4H4l6.103 8.89L4 19.993h1.38l5.335-6.21 4.262 6.21h4.655l-6.33-9.22h.001Zm-1.889 2.198-.618-.885-4.92-7.045h2.118l3.97 5.686.619.885 5.16 7.39h-2.117l-4.212-6.03Z"></path></svg>
				</a>
			<?php endif; ?>
			<?php if ( $li ) : ?>
				<a class="aew-faqv2__share-btn" data-share="linkedin" href="#" target="_blank" rel="noopener noreferrer" aria-label="<?php esc_attr_e( 'Share on LinkedIn', 'agency-elementor-widgets' ); ?>">
					<svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24" aria-hidden="true"><path fill-rule="evenodd" d="M17.6348754,17.6337778 L15.2626394,17.6337778 L15.2626394,13.9202963 C15.2626394,13.0346667 15.2469351,11.8957037 14.0294085,11.8957037 C12.7943998,11.8957037 12.6059484,12.861037 12.6059484,13.8568889 L12.6059484,17.6337778 L10.2337124,17.6337778 L10.2337124,9.99822222 L12.5099448,9.99822222 L12.5099448,11.0423704 L12.5425386,11.0423704 C12.8592911,10.4417778 13.633542,9.80888889 14.7879551,9.80888889 C17.1913034,9.80888889 17.6348754,11.3899259 17.6348754,13.4459259 L17.6348754,17.6337778 Z M7.55835401,8.95496296 C6.79773325,8.95496296 6.18289566,8.33837037 6.18289566,7.57925926 C6.18289566,6.81925926 6.79773325,6.20266667 7.55835401,6.20266667 C8.31690063,6.20266667 8.93351606,6.81925926 8.93351606,7.57925926 C8.93351606,8.33837037 8.31690063,8.95496296 7.55835401,8.95496296 Z M6.37134709,17.6337778 L8.74536094,17.6337778 L8.74536094,9.99822222 L6.37134709,9.99822222 L6.37134709,17.6337778 Z M18.8165488,4 L5.18078447,4 C4.52950109,4 4,4.51674074 4,5.15377778 L4,18.8456296 C4,19.4826667 4.52950109,20 5.18078447,20 L18.8165488,20 C19.4693137,20 20,19.4826667 20,18.8456296 L20,5.15377778 C20,4.51674074 19.4693137,4 18.8165488,4 Z"></path></svg>
				</a>
			<?php endif; ?>
			<?php if ( $copy ) : ?>
				<button type="button" class="aew-faqv2__share-btn" data-share="copy" aria-label="<?php esc_attr_e( 'Copy link', 'agency-elementor-widgets' ); ?>">
					<svg viewBox="0 0 24 24" fill="currentColor" width="24" height="24" aria-hidden="true"><path fill-rule="evenodd" d="M7.7859,10.3591 C9.3999,8.7461 12.0259,8.7461 13.6399,10.3591 L14.0689,10.7891 L13.3619,11.4961 L12.9329,11.0661 C11.7089,9.8451 9.7179,9.8431 8.4929,11.0661 L5.9199,13.6411 C5.3269,14.2331 4.9999,15.0211 4.9999,15.8601 C4.9999,16.6991 5.3269,17.4871 5.9199,18.0801 C7.1059,19.2661 9.1729,19.2661 10.3599,18.0801 L11.9429,16.4981 L12.6499,17.2051 L11.0669,18.7871 C10.2849,19.5691 9.2459,20.0001 8.1399,20.0001 C7.0339,20.0001 5.9939,19.5691 5.2129,18.7871 C4.4309,18.0061 3.9999,16.9661 3.9999,15.8601 C3.9999,14.7551 4.4309,13.7151 5.2129,12.9341 L7.7859,10.3591 Z M15.8606,3.9999 C16.9666,3.9999 18.0056,4.4309 18.7876,5.2129 C19.5696,5.9939 19.9996,7.0339 19.9996,8.1399 C19.9996,9.2449 19.5696,10.2849 18.7876,11.0669 L16.2136,13.6409 C15.4066,14.4469 14.3466,14.8509 13.2866,14.8509 C12.2266,14.8509 11.1666,14.4469 10.3596,13.6409 L9.9306,13.2109 L10.6376,12.5039 L11.0666,12.9339 C12.2926,14.1569 14.2836,14.1579 15.5066,12.9339 L18.0806,10.3589 C18.6736,9.7669 18.9996,8.9789 18.9996,8.1399 C18.9996,7.3009 18.6736,6.5129 18.0806,5.9199 C16.8936,4.7339 14.8266,4.7339 13.6406,5.9199 L12.0576,7.5019 L11.3506,6.7949 L12.9336,5.2129 C13.7146,4.4309 14.7546,3.9999 15.8606,3.9999 Z"></path></svg>
				</button>
			<?php endif; ?>
		</div>
		<?php
	}

	private function default_categories(): array {
		return [
			[ 'cat_label' => 'Timber Pergola Kits' ],
			[ 'cat_label' => 'Working With Notched' ],
			[ 'cat_label' => 'Cost & Value' ],
			[ 'cat_label' => 'Installation' ],
			[ 'cat_label' => 'Design & Customization' ],
			[ 'cat_label' => 'Permits & Regulations' ],
			[ 'cat_label' => 'Materials & Durability' ],
			[ 'cat_label' => 'Shipping & Delivery' ],
		];
	}

	private function default_questions(): array {
		return [
			// ── Timber Pergola Kits ──────────────────────────────────────────
			[
				'question' => 'How much does a timber pergola kit cost?',
				'answer'   => '<p>Pergola kit pricing depends on the structure style, footprint size, timber package, and customization level. Smaller traditional pergolas typically start around the mid $5,000 range, while larger premium heavy timber structures can exceed $17,000. Contemporary flat roof pergolas, Kingston pergolas, and specialty pergola structures each have different pricing tiers based on engineering and material requirements.</p>',
				'category' => 'Timber Pergola Kits',
				'open_by_default' => 'yes',
			],
			[
				'question' => 'What timber package options are available?',
				'answer'   => '<p>Notched offers multiple timber package options designed to create different architectural styles and structural appearances.</p>'
					. '<h3><strong>Regular Timber Package</strong></h3>'
					. '<p>Features:</p>'
					. '<ul><li>6x6 posts</li><li>3x10 beams</li><li>2x8 rafters</li><li>2x4 lattice</li></ul>'
					. '<p>This package delivers a clean classic pergola appearance while remaining budget friendly and highly functional.</p>'
					. '<h3><strong>Medium Timber Package</strong></h3>'
					. '<p>Features:</p>'
					. '<ul><li>8x8 posts</li><li>4x10 beams</li><li>3x8 rafters</li><li>2x4 lattice</li></ul>'
					. '<p>The Medium package creates a heavier, more substantial architectural look and is one of the most popular upgrades for homeowners wanting a premium timber feel.</p>'
					. '<h3><strong>Enhanced Timber Package</strong></h3>'
					. '<p>Features:</p>'
					. '<ul><li>8x8 posts</li><li>4x12 beams</li><li>3x10 rafters</li><li>2x6 lattice</li></ul>'
					. '<p>The Enhanced package is designed for bold luxury timber structures with oversized beams, deeper roof lines, and a dramatic heavy timber appearance.</p>',
				'category' => 'Timber Pergola Kits',
			],
			[
				'question' => 'What pergola sizes are available?',
				'answer'   => '<p>Notched pergola kits are available in a wide range of standard footprint sizes, including:</p>'
					. '<ul><li>8&rsquo;x8&rsquo;</li><li>8&rsquo;x10&rsquo;</li><li>8&rsquo;x14&rsquo;</li><li>8&rsquo;x18&rsquo;</li><li>10&rsquo;x10&rsquo;</li><li>10&rsquo;x14&rsquo;</li><li>10&rsquo;x18&rsquo;</li><li>14&rsquo;x14&rsquo;</li><li>14&rsquo;x18&rsquo;</li><li>18&rsquo;x18&rsquo;</li></ul>'
					. '<p>Additional sizes may be available depending on the pergola style and timber package selected. Custom sizes and oversized structures are also available for select projects.</p>',
				'category' => 'Timber Pergola Kits',
			],
			[
				'question' => 'What\'s the difference between traditional, Kingston, and contemporary pergolas?',
				'answer'   => '<h3><strong>Traditional Pergolas</strong></h3>'
					. '<p>Traditional pergolas feature timeless timber framing details, classic proportions, and curved brace styling that works beautifully with farmhouse, rustic, mountain, and craftsman homes.</p>'
					. '<h3><strong>Kingston Pergolas</strong></h3>'
					. '<p>Kingston pergolas use larger timber members, upgraded structural detailing, and heavier proportions to create a bold luxury outdoor living structure with a premium architectural presence.</p>'
					. '<h3><strong>Contemporary Pergolas</strong></h3>'
					. '<p>Contemporary pergolas feature simplified lines, modern roof styling, clean beam layouts, and minimalist detailing designed for modern homes and sleek outdoor spaces.</p>',
				'category' => 'Timber Pergola Kits',
			],

			// ── Working With Notched ──────────────────────────────────────────
			[
				'question' => 'Why choose Notched over other companies?',
				'answer'   => '<p>Notched stands out for its authentic timber craftsmanship. Unlike lightweight constructions, we use traditional joinery and premium materials to create durable, heirloom-quality structures. Plus, we collaborate with you to design a structure that perfectly complements your space.</p>',
				'category' => 'Working With Notched',
			],
			[
				'question' => 'Can I talk with someone before deciding?',
				'answer'   => '<p>Absolutely. We encourage it. Peace of mind is a huge priority for us. A quick conversation with our team can help answer questions and give you a better sense of what&rsquo;s possible for your space.</p>',
				'category' => 'Working With Notched',
			],
			[
				'question' => 'How do I get started?',
				'answer'   => '<p>The easiest way is to request a quote or schedule a consultation. From there we can talk through your ideas and help you take the next step.</p>',
				'category' => 'Working With Notched',
			],

			// ── Design & Customization ───────────────────────────────────────
			[
				'question' => 'Can I customize the size or design?',
				'answer'   => '<p>Yes! Our designers are itching to make custom structures to better fit your space. That might include different sizes, timber packages, stains, or roofing options.</p>',
				'category' => 'Design & Customization, Timber Pergola Kits',
			],
			[
				'question' => 'How do I know which structure is right for my space?',
				'answer'   => '<p>That&rsquo;s exactly what our design consultations are for. We&rsquo;ll talk about your yard, how you plan to use the space, and the look you want to achieve. From there we can recommend a structure that fits well.</p>',
				'category' => 'Design & Customization',
			],
			[
				'question' => 'Do you provide design help or renderings?',
				'answer'   => '<p>Yes. This is another specialty of ours. We provide a 3D rendering for every custom project, so you can clearly see how the structure will look before anything is built.</p>',
				'category' => 'Design & Customization',
			],

			// ── Cost & Value ─────────────────────────────────────────────────
			[
				'question' => 'Why are Notched structures more expensive than pergola kits I see online?',
				'answer'   => '<p>Most kits online use thin lumber, metal brackets, and decorative pieces that are not built to last very long. Our structures are built from real structural timber. The posts, beams, and joinery are designed the same way traditional timber frame buildings have been built for generations. It costs more up front, but you end up with something far stronger, more beautiful, and built to last. That&rsquo;s why we back up every structure with a 25 year warranty. Though, they&rsquo;re built to last a lifetime.</p>',
				'category' => 'Cost & Value',
			],
			[
				'question' => 'Is a timber structure really worth the investment?',
				'answer'   => '<p>For many homeowners, it absolutely is. A well-built outdoor structure essentially extends the footprint of your home. It creates a place for gatherings, relaxing evenings, and everyday life outside. It adds lasting character and huge value to your property.</p>',
				'category' => 'Cost & Value',
			],
			[
				'question' => 'How much do your structures cost?',
				'answer'   => '<p>Every project is a little different depending on size, materials, and design. Some of our smaller DIY kits are very approachable, while larger custom structures are a bigger investment. The best way to get an accurate number is to request a quote and talk with our team about what you have in mind.</p>'
					. '<p>Reach out if you want to talk about your structure!</p>'
					. '<p><a class="aew-faqv2__answer-btn" href="' . esc_url( home_url( '/contact-us/' ) ) . '">FREE CONSULTATION</a></p>',
				'category' => 'Cost & Value',
			],
			[
				'question' => 'Do these structures add value to my home?',
				'answer'   => '<p>Easily. A well-designed outdoor living space can make a property more appealing and more functional. Many homeowners tell us it becomes one of their favorite parts of the house.</p>',
				'category' => 'Cost & Value',
			],

			// ── Permits & Regulations ────────────────────────────────────────
			[
				'question' => 'Do I need a permit for a structure like this?',
				'answer'   => '<p>Sometimes, but not always. Permit requirements vary by city and county. In Utah, Arizona, and California there are many cases where smaller structures fall below the permit threshold. We are very familiar with permit requirements and can help you figure out what size structure can fit in your space.</p>',
				'category' => 'Permits & Regulations, Timber Pergola Kits',
			],
			[
				'question' => 'What about HOA rules?',
				'answer'   => '<p>If your neighborhood has an HOA, they may have guidelines about structures, colors, or placement. Most homeowners simply submit the design for approval before building. We can certainly help with that process.</p>',
				'category' => 'Permits & Regulations',
			],
			[
				'question' => 'Can you help me understand local rules?',
				'answer'   => '<p>Yes. While every city has its own regulations, we work with homeowners across Utah, Arizona, and California and are familiar with the general requirements. We&rsquo;ve worked in these areas long enough to know pretty well what&rsquo;s allowed and what isn&rsquo;t. We&rsquo;re more than happy to point you in the right direction.</p>',
				'category' => 'Permits & Regulations',
			],

			// ── Installation ─────────────────────────────────────────────────
			[
				'question' => 'Can I install a Notched kit myself?',
				'answer'   => '<p>Many of our DIY kits are designed specifically for homeowners who enjoy building projects. The parts are precision cut and the instructions are straightforward. Some of our larger structures are better suited for a contractor. If you&rsquo;re unsure, we&rsquo;re happy to help you decide what makes the most sense.</p>',
				'category' => 'Installation, Timber Pergola Kits',
			],
			[
				'question' => 'How hard is installation?',
				'answer'   => '<p>Most homeowners describe it as a manageable project if you have basic tools and some building experience. The pieces fit together the way they are meant to, which makes the process much smoother than building something from scratch. We do suggest having at least two people on hand to get the timber in place.</p>',
				'category' => 'Installation',
			],
			[
				'question' => 'How long does installation take?',
				'answer'   => '<p>It depends on the size of the structure and the experience of the installer. Smaller kits may take as little as a couple hours (especially if a professional is on hand to help). Larger structures may take up to a week or more. A contractor can usually complete installation faster.</p>',
				'category' => 'Installation, Timber Pergola Kits',
			],
			[
				'question' => 'What if I get stuck during installation?',
				'answer'   => '<p>You&rsquo;re not on your own. Our team is always happy to answer questions. We want the process to go smoothly and we&rsquo;re here to help if you need guidance along the way. Happy to jump on a video call anytime. Just reach out!</p>',
				'category' => 'Installation',
			],

			// ── Materials & Durability ───────────────────────────────────────
			[
				'question' => 'What kind of wood do you use?',
				'answer'   => '<p>We use high quality Douglas Fir timbers. It is strong, durable, and widely used in traditional timber framing. When we mill our timbers, we cut them close to the heart of the tree. This part of the wood is naturally more stable, which helps reduce twisting and movement over time. The result is a stronger structure that holds its shape better and lasts longer.</p>',
				'category' => 'Materials & Durability, Timber Pergola Kits',
			],
			[
				'question' => 'Will the wood crack or weather over time?',
				'answer'   => '<p>All real timber develops small natural cracks called checking. This is completely normal and does not affect the strength of the structure. Many people actually love the character it adds. Regardless, these minor changes shouldn&rsquo;t affect the structure itself. That&rsquo;s why we back up every structure with a 25 year structural warranty.</p>',
				'category' => 'Materials & Durability',
			],
			[
				'question' => 'How long will a structure like this last?',
				'answer'   => '<p>With proper installation and basic maintenance (we provide guidelines and optional assistance after installation), a timber structure can last for decades. These are not temporary backyard pieces. They are built to be a lasting part of your home.</p>',
				'category' => 'Materials & Durability',
			],
			[
				'question' => 'What kind of maintenance do Notched structures need?',
				'answer'   => '<p>Maintaining your Notched structure is straightforward. A quick clean and occasional re-staining, based on your local climate and sun exposure, will keep it looking great for years.</p>',
				'category' => 'Materials & Durability',
			],

			// ── Shipping & Delivery ──────────────────────────────────────────
			[
				'question' => 'Do you ship outside of Utah?',
				'answer'   => '<p>Yes. We ship structures throughout the western United States and beyond. Many of our customers are located in Utah, Arizona, and California. We&rsquo;ve shipped all the way to the east coast in some cases. There may be some added shipping costs, but that&rsquo;s something we can discuss upfront, so there are no surprises.</p>',
				'category' => 'Shipping & Delivery, Timber Pergola Kits',
			],
			[
				'question' => 'How does delivery work?',
				'answer'   => '<p>Your structure is delivered by freight directly to your driveway or curb. Because these are large timber packages, they are shipped on pallets rather than standard parcel delivery.</p>'
					. '<p>Of course, if we&rsquo;re installing the structure for you, we&rsquo;ll bring it and install it right then and there.</p>',
				'category' => 'Shipping & Delivery',
			],
			[
				'question' => 'What if something arrives damaged?',
				'answer'   => '<p>While this is rare, shipping can sometimes be rough. If something arrives damaged, let us know right away and we will work with you to resolve it.</p>',
				'category' => 'Shipping & Delivery',
			],
		];
	}
}
