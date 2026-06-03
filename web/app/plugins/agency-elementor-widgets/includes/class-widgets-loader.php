<?php
/**
 * Register Elementor widgets and category.
 *
 * @package Agency_Elementor_Widgets
 */

namespace AEW;

defined( 'ABSPATH' ) || exit;

use Elementor\Elements_Manager;
use Elementor\Widgets_Manager;

/**
 * Widget registration.
 */
final class Widgets_Loader {

	/**
	 * @return void
	 */
	public static function init(): void {
		add_action( 'elementor/elements/categories_registered', [ self::class, 'register_category' ] );
		add_action( 'elementor/widgets/register', [ self::class, 'register_widgets' ] );
	}

	/**
	 * @param Elements_Manager $elements_manager Elements manager.
	 * @return void
	 */
	public static function register_category( Elements_Manager $elements_manager ): void {
		$elements_manager->add_category(
			'agency-widgets',
			[
				'title' => esc_html__( 'Agency Widgets', 'agency-elementor-widgets' ),
				'icon'  => 'fa fa-plug',
			]
		);
	}

	/**
	 * @param Widgets_Manager $widgets_manager Widgets manager.
	 * @return void
	 */
	public static function register_widgets( Widgets_Manager $widgets_manager ): void {
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-header.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-header-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-hero.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-hero-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-icon-cards.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-heading-band.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-feature-rows.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-split-media.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-faq-accordion.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-card-row.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-cta-band.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-testimonial-grid.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-featured-image.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-process-steps.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-media-cta.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-overlay-banner.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-footer.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-footer-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-sticky-image.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-products-slider-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-benefits-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-region-cards-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-testimonials-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-consultation-form-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-parallax-image-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-welcome-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-feature-rows-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-cta-banner-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-faq-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-post-archive-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-single-post-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-recent-posts-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-comments-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-gallery-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-banner-hero-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-info-columns-v2.php';
		require_once AEW_PLUGIN_DIR . 'widgets/class-widget-booking-cards-v2.php';
		$widgets_manager->register( new Widget_Header() );
		$widgets_manager->register( new Widget_Header_V2() );
		$widgets_manager->register( new Widget_Hero() );
		$widgets_manager->register( new Widget_Hero_V2() );
		$widgets_manager->register( new Widget_Icon_Cards() );
		$widgets_manager->register( new Widget_Heading_Band() );
		$widgets_manager->register( new Widget_Feature_Rows() );
		$widgets_manager->register( new Widget_Split_Media() );
		$widgets_manager->register( new Widget_Faq_Accordion() );
		$widgets_manager->register( new Widget_Card_Row() );
		$widgets_manager->register( new Widget_Cta_Band() );
		$widgets_manager->register( new Widget_Testimonial_Grid() );
		$widgets_manager->register( new Widget_Featured_Image() );
		$widgets_manager->register( new Widget_Process_Steps() );
		$widgets_manager->register( new Widget_Media_Cta() );
		$widgets_manager->register( new Widget_Overlay_Banner() );
		$widgets_manager->register( new Widget_Footer() );
		$widgets_manager->register( new Widget_Footer_V2() );
		$widgets_manager->register( new Widget_Sticky_Image() );
		$widgets_manager->register( new Widget_Products_Slider_V2() );
		$widgets_manager->register( new Widget_Benefits_V2() );
		$widgets_manager->register( new Widget_Region_Cards_V2() );
		$widgets_manager->register( new Widget_Testimonials_V2() );
		$widgets_manager->register( new Widget_Consultation_Form_V2() );
		$widgets_manager->register( new Widget_Parallax_Image_V2() );
		$widgets_manager->register( new Widget_Welcome_V2() );
		$widgets_manager->register( new Widget_Feature_Rows_V2() );
		$widgets_manager->register( new Widget_Cta_Banner_V2() );
		$widgets_manager->register( new Widget_Faq_V2() );
		$widgets_manager->register( new Widget_Post_Archive_V2() );
		$widgets_manager->register( new Widget_Single_Post_V2() );
		$widgets_manager->register( new Widget_Recent_Posts_V2() );
		$widgets_manager->register( new Widget_Comments_V2() );
		$widgets_manager->register( new Widget_Gallery_V2() );
		$widgets_manager->register( new Widget_Banner_Hero_V2() );
		$widgets_manager->register( new Widget_Info_Columns_V2() );
		$widgets_manager->register( new Widget_Booking_Cards_V2() );
	}
}
