<?php
$orders = $args['user_orders'];
$orders_link = wc_get_endpoint_url('orders');
?>
<div class="box-account-orders">
	<div class="box-title">
		<h2><?= _e('New Orders', 'woocommerce') ?></h2>
		<a href="<?= $orders_link ?>" class="link-account"><?= _e('All', 'woocommerce') ?></a>
	</div>
	<div class="table-responsive">
		<table class="woocommerce-table" cellspacing="0" cellpadding="0">
			<thead>
				<tr>
					<th>
						<?= _e('Order', 'woocommerce') ?>
					</th>
					<th><?= _e('Order date', 'woocommerce') ?></th>
					<th class="text-center"><?= _e('Status', 'woocommerce') ?></th>
					<th class="text-right"><?= _e('Total', 'woocommerce') ?></th>
					<th class="text-right">
						<?= _e('Actions', 'woocommerce') ?>
					</th>
				</tr>
			</thead>
			<?php if (!empty($orders)) : ?>
				<tbody>
					<?php foreach ($orders as $order) : ?>
						<tr>
							<td>
								<?php
								echo sprintf('<a href="%s" class="link-account">#%s</a>', esc_url($order->get_view_order_url()), $order->get_order_number());
								?>
							</td>
							<td><?= date_format($order->get_date_created(), 'd/m/Y') ?></td>
							<td class="woocommerce-orders-table__cell woocommerce-orders-table__cell-order-status text-center">
								<span class="<?= $order->get_status() ?>">
									<?= wc_get_order_status_name($order->get_status()) ?>
								</span>
							</td>
							<td class="text-right"><?= wc_price($order->get_total()) ?></td>
							<td class="text-right order-actions woocommerce-orders-table__cell woocommerce-orders-table__cell-order-actions">
								<div class="flex justify-end">
									<?php
									$actions = wc_get_account_orders_actions($order);

									if (! empty($actions)) {
										// Assume $wp_button_class is defined or not needed here, use default 'button'
										$wp_button_class = isset($wp_button_class) ? $wp_button_class : '';
										foreach ($actions as $key => $action) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
											echo '<a href="' . esc_url($action['url']) . '" class="woocommerce-button' . esc_attr($wp_button_class) . ' button ' . sanitize_html_class($key) . '">' . esc_html($action['name']) . '</a>';
										}
									}
									?>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			<?php else : ?>
				<tbody>
					<tr>
						<td colspan="5" class="text-center">
							<?= _e('No recent orders.', 'woocommerce') ?>
						</td>
					</tr>
				</tbody>
			<?php endif; ?>
		</table>
	</div>
</div>