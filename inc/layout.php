<?php
/**
 * Instantio Layout Design
 *
 * @package  WooInstant
 */

defined( 'ABSPATH' ) || exit;

function wooinstant_layout( ){

	if ( class_exists('Woocommerce') ): ?>
		<div class="wi-container <?php esc_attr_e( $direction ); ?> <?php if( $wiopt["wi-window-type"] == '1' ){ echo ' single-step '; } ?>">

			<a id="wi-toggler" class="wi-cart-header <?php if( $wiopt['wi-cart-image']['url'] && $wiopt['wi-icon-choice']==2 ){ echo 'icon-img'; }?> <?php if( WC()->cart->get_cart_contents_count() > 0 ){ echo 'hascart'; } ?>">

					<?php wi_svg_icon('shopping_cart'); ?>

				<?php echo wi_cart_count(); ?>
			</a>

			<div class="wi-inner">
				<div class="wooinstant-content woocommerce">

				</div>
			</div>

		</div>
		<?php
	endif;
}
add_action( 'wp_footer', 'wooinstant_layout' );