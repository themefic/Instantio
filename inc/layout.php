<?php
/**
 * Instantio Layout Design
 *
 * @package  WooInstant
 */

defined( 'ABSPATH' ) || exit;
if ( !function_exists('wooinstant_layout') ) {
	function wooinstant_layout( ){

		$checkout_url = wc_get_checkout_url();


		if ( class_exists('Woocommerce') ): ?>
			<div class="wi-container">

				<div id="wi-toggler" class="wi-cart-header <?php if( WC()->cart->get_cart_contents_count() > 0 ){ echo 'hascart'; } ?>">
					<?php if( $checkout_url ) : ?>
						<a href="<?php echo $checkout_url; ?>" class="wi-inner">
							<div class="wooinstant-content">
								<?php esc_html_e( 'Checkout Now', 'instantio' ); ?>
							</div>
						</a>
					<?php endif; ?>

					<?php wi_svg_icon('shopping_cart'); ?>
					<?php echo wi_cart_count(); ?>
				</div>

			</div>
			<?php
		endif;
	}
}
add_action( 'wp_footer', 'wooinstant_layout' );

