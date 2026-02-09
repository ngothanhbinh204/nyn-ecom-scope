<?php /*
Template name: Page - Giỏ hàng
*/ ?>
<?php get_header() ?>
<div class="global-breadcrumb">
			<div class="container">
				<?php 
				if ( function_exists('yoast_breadcrumb') ) {
					yoast_breadcrumb( '<nav class="rank-math-breadcrumb" aria-label="breadcrumbs">','</nav>' );
				} elseif ( function_exists('rank_math_the_breadcrumbs') ) {
					rank_math_the_breadcrumbs();
				} else {
					woocommerce_breadcrumb();
				}
				?>
			</div>
		</div>
<section class="section-cart-overview section-py">
	<div class="container">
		<?php the_content() ?>
	</div>
</section>
<style>
	.wc-block-cart__submit-container a:hover, .wc-block-cart__submit-container button:hover {
		background-color: var(--color-primary);
		color: var(--color-secondary)
	}
	.wc-block-cart__submit-container a, .wc-block-cart__submit-container button {
		width: 100%;
		justify-content: center;
		border-radius: 44px;
		font-size: 0.8333rem;
		transition: .3s all  ease-in-out;
		height: 2.2916666667rem;
		transition: 0.3s all ease-in-out;
		border: 1px solid var(--color-primary);
		color: var(--color-primary);
	}
	.wp-block-woocommerce-cart-order-summary-coupon-form-block{
		display: none;
	}
	.wp-block-woocommerce-cart-order-summary-shipping-block {
		display: none;
	}
</style>
<?php get_footer() ?>
