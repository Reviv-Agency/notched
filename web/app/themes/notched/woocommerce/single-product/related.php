<?php
/**
 * Related Products — overridden to render with the Products Slider V2 look.
 *
 * Replaces WooCommerce's default related-products grid with the same markup +
 * styling as the `agency-products-slider-v2` Elementor widget (slider mode,
 * category badge, "From $price"). The related products themselves still come
 * from WooCommerce's built-in related algorithm ($related_products).
 *
 * Override of woocommerce/templates/single-product/related.php
 *
 * @package Notched
 */

defined( 'ABSPATH' ) || exit;

if ( empty( $related_products ) ) {
	return;
}

$heading = apply_filters( 'woocommerce_product_related_products_heading', __( 'Related products', 'woocommerce' ) );

// Build the per-product data the slider markup expects (mirrors the widget).
$items = [];
foreach ( $related_products as $related_product ) {
	if ( ! $related_product instanceof WC_Product ) {
		continue;
	}
	$img_id = $related_product->get_image_id();
	$img    = $img_id ? wp_get_attachment_image_url( $img_id, 'large' ) : ( function_exists( 'wc_placeholder_img_src' ) ? wc_placeholder_img_src( 'large' ) : '' );

	// price: variable products show their "from" price; format like the widget ("$7,093").
	// Contractor Kits are quote-only — no price on their cards.
	$price_raw = $related_product->get_price();
	$price     = ( '' !== $price_raw && null !== $price_raw && ! ( function_exists( 'notched_is_quote_only' ) && notched_is_quote_only( $related_product->get_id() ) ) )
		? '$' . number_format( (float) $price_raw, 0 )
		: '';

	// badge = first non-uncategorized category name (same rule as the widget)
	$badge = '';
	$terms = get_the_terms( $related_product->get_id(), 'product_cat' );
	if ( is_array( $terms ) ) {
		foreach ( $terms as $t ) {
			if ( is_object( $t ) && ! empty( $t->name ) && 'uncategorized' !== ( $t->slug ?? '' ) ) {
				$badge = $t->name;
				break;
			}
		}
	}

	$items[] = [
		'title' => $related_product->get_name(),
		'url'   => get_permalink( $related_product->get_id() ),
		'img'   => $img ? $img : '',
		'price' => $price,
		'badge' => $badge,
	];
}

if ( empty( $items ) ) {
	return;
}
?>
<section class="aew-prsv2 aew-prsv2--related" data-aew-products-slider-v2>
	<div class="aew-prsv2__inner">
		<div class="aew-prsv2__head">
			<div class="aew-prsv2__head-main">
				<?php if ( $heading ) : ?>
					<h2 class="aew-prsv2__heading"><?php echo esc_html( $heading ); ?></h2>
				<?php endif; ?>
			</div>
		</div>

		<div class="aew-prsv2__viewport">
			<button type="button" class="aew-prsv2__arrow aew-prsv2__arrow--prev" data-aew-prs-prev aria-label="<?php esc_attr_e( 'Previous products', 'notched' ); ?>">
				<svg class="aew-prsv2__chev" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 5l-7 7 7 7"/></svg>
			</button>

			<ul class="aew-prsv2__track" data-aew-prs-track>
				<?php foreach ( $items as $p ) : ?>
					<li class="aew-prsv2__slide">
						<a class="aew-prsv2__card" href="<?php echo esc_url( $p['url'] ); ?>">
							<span class="aew-prsv2__media"<?php echo $p['img'] ? ' style="background-image:url(\'' . esc_url( $p['img'] ) . '\');"' : ''; ?> role="img" aria-label="<?php echo esc_attr( $p['title'] ); ?>">
								<?php if ( '' !== $p['badge'] ) : ?>
									<span class="aew-prsv2__badge"><?php echo esc_html( $p['badge'] ); ?></span>
								<?php endif; ?>
							</span>
							<span class="aew-prsv2__title"><?php echo esc_html( $p['title'] ); ?></span>
							<?php if ( '' !== $p['price'] ) : ?>
								<span class="aew-prsv2__price"><?php echo esc_html( 'From ' . $p['price'] ); ?></span>
							<?php endif; ?>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>

			<button type="button" class="aew-prsv2__arrow aew-prsv2__arrow--next" data-aew-prs-next aria-label="<?php esc_attr_e( 'Next products', 'notched' ); ?>">
				<svg class="aew-prsv2__chev" viewBox="0 0 24 24" width="24" height="24" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5l7 7-7 7"/></svg>
			</button>
		</div>

		<div class="aew-prsv2__dots" data-aew-prs-dots aria-hidden="true"></div>
	</div>
</section>
<?php
wp_reset_postdata();
