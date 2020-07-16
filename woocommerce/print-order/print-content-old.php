<?php
/**
 * Print order content. Copy this file to your themes
 * directory /woocommerce/print-order to customize it.
 *
 * @package WooCommerce Print Invoice & Delivery Note/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	<?php if(wcdn_get_template_type() != 'receipt') : ?>
	<div class="order-branding">
		<div class="company-info">
			<h1 class="company-name"><?php wcdn_company_name(); ?></h1>
			<div class="company-address"><?php wcdn_company_info(); ?></div>
		</div>

		<?php do_action( 'wcdn_after_branding', $order ); ?>
	</div><!-- .order-branding -->
	<?php endif; ?>

<?php if(wcdn_get_template_type() != 'receipt') : ?>
	<div class="order-addresses <?php if ( ! wcdn_has_shipping_address( $order ) ) : ?> no-shipping-address<?php endif; ?>">
	
		
<?php if ( ! wcdn_has_shipping_address( $order ) ) : ?>
	<div class="billing-address">
		<h3><?php esc_attr_e( 'Adresse du client', 'woocommerce-delivery-notes' ); ?></h3>
		<address>

			<?php
			if ( ! $order->get_formatted_billing_address() ) {
				esc_attr_e( 'N/A', 'woocommerce-delivery-notes' );
			} else {
				echo wp_kses_post( apply_filters( 'wcdn_address_billing', $order->get_formatted_billing_address(), $order ) );
			}
			?>
		</address>
	</div>
<?php else: ?>
	<div class="shipping-address">						
		<h3><?php esc_attr_e( 'Adresse du client', 'woocommerce-delivery-notes' ); ?></h3>
		<address>

			<?php
			if ( ! $order->get_formatted_shipping_address() ) {
				esc_attr_e( 'N/A', 'woocommerce-delivery-notes' );
			} else {
				echo wp_kses_post( apply_filters( 'wcdn_address_shipping', $order->get_formatted_shipping_address(), $order ) );
			}
			?>

		</address>
	</div>	
<?php endif; ?>
		
		
		<?php do_action( 'wcdn_after_addresses', $order ); ?>
	</div><!-- .order-addresses -->
<?php endif; ?>



	<div class="order-info">
		
		
		<?php if(wcdn_get_template_type() == 'invoice') : ?>
			<h4>COMMANDE A LIVRER N°1</h4>
		<?php endif; ?>
		
		<?php if(wcdn_get_template_type() == 'receipt') : ?>
			<h4>FABRICATION COMPTOIR</h4>
		<?php endif; ?>
		

		<ul class="info-list">
			<?php
			$fields = apply_filters( 'wcdn_order_info_fields', wcdn_get_order_info( $order ), $order );
			?>
			<?php foreach ( $fields as $key => $field ) : 
			// Remove customer email
			if($key == 'billing_email') continue;
			
			// Remove payment method and billing phone only for receipt
			if(wcdn_get_template_type() == 'receipt') {
				if($key == 'payment_method') continue;
				if($key == 'billing_phone') continue;
			}
			
			?>
				<li>
					<strong><?php echo wp_kses_post( apply_filters( 'wcdn_order_info_name', $field['label'], $field ) ); ?></strong>
					<span><?php echo wp_kses_post( apply_filters( 'wcdn_order_info_content', $field['value'], $field ) ); ?></span>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php do_action( 'wcdn_after_info', $order ); ?>
	</div><!-- .order-info -->

<?php if(wcdn_get_template_type() != 'receipt') : ?>
	<div class="order-items">
		<table>
			<thead>
				<tr>


				<th class="woocommerce-table__product-name product-name"><?php esc_html_e( 'Product', 'woocommerce' ); ?></th>
				<th class="woocommerce-table__product-table product-total"><?php esc_html_e( 'Total', 'woocommerce' ); ?></th>
					
				</tr>
			</thead>

			<tbody>
				<?php

				if ( count( $order->get_items() ) > 0 ) :
					?>
					<?php foreach ( $order->get_items() as $item ) : ?>

						<?php
							$product = apply_filters( 'wcdn_order_item_product', $order->get_product_from_item( $item ), $item );

						if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
							$item_meta = new WC_Order_Item_Product( $item['item_meta'], $product );
						} else {
							$item_meta = new WC_Order_Item_Meta( $item['item_meta'], $product );
						}
						?>
						<tr>
							<td class="product-name">
								<?php do_action( 'wcdn_order_item_before', $product, $order, $item ); ?>
								<span class="name">
								<?php

								$addon_name  = $item->get_meta( '_wc_pao_addon_name', true );
								$addon_value = $item->get_meta( '_wc_pao_addon_value', true );
								$is_addon    = ! empty( $addon_value );

								if ( $is_addon ) { // Displaying options of product addon.
									$addon_html = '<div class="wc-pao-order-item-name">' . esc_html( $addon_name ) . '</div><div class="wc-pao-order-item-value">' . esc_html( $addon_value ) . '</div></div>';

									echo wp_kses_post( $addon_html );
								} else {

									$product_id   = $item['product_id'];
									$prod_name    = get_post( $product_id );
									$product_name = $prod_name->post_title;

									echo wp_kses_post( apply_filters( 'wcdn_order_item_name', $product_name, $item ) );
									?>
									</span>

									<?php

									if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
										if ( isset( $item['variation_id'] ) && 0 !== $item['variation_id'] ) {
											$variation = wc_get_product( $item['product_id'] );
											foreach ( $item['item_meta'] as $key => $value ) {
												if ( ! ( 0 === strpos( $key, '_' ) ) ) {
													if ( is_array( $value ) ) {
														continue;
													}
													$term_wp        = get_term_by( 'slug', $value, $key );
													$attribute_name = wc_attribute_label( $key, $variation );
													if ( isset( $term_wp->name ) ) {
														echo '<br>' . wp_kses_post( $attribute_name . ':' . $term_wp->name );
													} else {
														echo '<br>' . wp_kses_post( $attribute_name . ':' . $value );
													}
												}
											}
										} else {
											foreach ( $item['item_meta'] as $key => $value ) {
												if ( ! ( 0 === strpos( $key, '_' ) ) ) {
													if ( is_array( $value ) ) {
														continue;
													}
													echo '<br>' . wp_kses_post( $key . ':' . $value );
												}
											}
										}
									} else {
										$item_meta_new = new WC_Order_Item_Meta( $item['item_meta'], $product );
										$item_meta_new->display();

									}
									?>
									<br>
									<dl class="extras">
										<?php if ( $product && $product->exists() && $product->is_downloadable() && $order->is_download_permitted() ) : ?>

											<dt><?php esc_attr_e( 'Download:', 'woocommerce-delivery-notes' ); ?></dt>
											<dd>
											<?php
											// translators: files count.
											printf( esc_attr_e( '%s Files', 'woocommerce-delivery-notes' ), count( $item->get_item_downloads() ) );
											?>
											</dd>

										<?php endif; ?>

										<?php

											$fields = apply_filters( 'wcdn_order_item_fields', array(), $product, $order, $item );

										foreach ( $fields as $field ) :
											?>

											<dt><?php echo esc_html( $field['label'] ); ?></dt>
											<dd><?php echo esc_html( $field['value'] ); ?></dd>

										<?php endforeach; ?>
									</dl>
								<?php } ?>
								<?php do_action( 'wcdn_order_item_after', $product, $order, $item ); ?>
							</td>
							<td class="product-item-price">
								<span><?php echo wp_kses_post( wcdn_get_formatted_item_price( $order, $item ) ); ?></span>
							</td>
							<td class="product-quantity">
								<span><?php echo esc_attr( apply_filters( 'wcdn_order_item_quantity', $item['qty'], $item ) ); ?></span>
							</td>
							<td class="product-price">
								<span><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></span>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>

			<tfoot>
				<?php
				$totals_arr = $order->get_order_item_totals();
				if ( $totals_arr ) :

					foreach ( $totals_arr as $total ) :
						?>
						<tr>
							<td class="total-name"><span><?php echo wp_kses_post( $total['label'] ); ?></span></td>
							<td class="total-item-price"></td>
							<td class="total-quantity"></td>
							<td class="total-price"><span><?php echo wp_kses_post( $total['value'] ); ?></span></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tfoot>
		</table>

		<?php do_action( 'wcdn_after_items', $order ); ?>
	</div><!-- .order-items -->

<?php else : ?>
	<div class="order-items">
		<table>
			<thead>
				<tr>
					<th class="head-name"><span><?php esc_attr_e( 'Product', 'woocommerce-delivery-notes' ); ?></span></th>
					<th class="head-quantity"><span><?php esc_attr_e( 'Quantity', 'woocommerce-delivery-notes' ); ?></span></th>

				</tr>
			</thead>

			<tbody>
				<?php

				if ( count( $order->get_items() ) > 0 ) :
					?>
					<?php foreach ( $order->get_items() as $item ) : ?>

						<?php
							$product = apply_filters( 'wcdn_order_item_product', $order->get_product_from_item( $item ), $item );

						if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
							$item_meta = new WC_Order_Item_Product( $item['item_meta'], $product );
						} else {
							$item_meta = new WC_Order_Item_Meta( $item['item_meta'], $product );
						}
						?>
						<tr>
							<td class="product-name">
								<?php do_action( 'wcdn_order_item_before', $product, $order, $item ); ?>
								<span class="name">
								<?php

								$addon_name  = $item->get_meta( '_wc_pao_addon_name', true );
								$addon_value = $item->get_meta( '_wc_pao_addon_value', true );
								$is_addon    = ! empty( $addon_value );

								if ( $is_addon ) { // Displaying options of product addon.
									$addon_html = '<div class="wc-pao-order-item-name">' . esc_html( $addon_name ) . '</div><div class="wc-pao-order-item-value">' . esc_html( $addon_value ) . '</div></div>';

									echo wp_kses_post( $addon_html );
								} else {

									$product_id   = $item['product_id'];
									$prod_name    = get_post( $product_id );
									$product_name = $prod_name->post_title;

									echo wp_kses_post( apply_filters( 'wcdn_order_item_name', $product_name, $item ) );
									?>
									</span>

									<?php

									if ( version_compare( get_option( 'woocommerce_version' ), '3.0.0', '>=' ) ) {
										if ( isset( $item['variation_id'] ) && 0 !== $item['variation_id'] ) {
											$variation = wc_get_product( $item['product_id'] );
											foreach ( $item['item_meta'] as $key => $value ) {
												if ( ! ( 0 === strpos( $key, '_' ) ) ) {
													if ( is_array( $value ) ) {
														continue;
													}
													$term_wp        = get_term_by( 'slug', $value, $key );
													$attribute_name = wc_attribute_label( $key, $variation );
													if ( isset( $term_wp->name ) ) {
														echo '<br>' . wp_kses_post( $attribute_name . ':' . $term_wp->name );
													} else {
														echo '<br>' . wp_kses_post( $attribute_name . ':' . $value );
													}
												}
											}
										} else {
											foreach ( $item['item_meta'] as $key => $value ) {
												if ( ! ( 0 === strpos( $key, '_' ) ) ) {
													if ( is_array( $value ) ) {
														continue;
													}
													echo '<br>' . wp_kses_post( $key . ':' . $value );
												}
											}
										}
									} else {
										$item_meta_new = new WC_Order_Item_Meta( $item['item_meta'], $product );
										$item_meta_new->display();

									}
									?>
									<br>
									<dl class="extras">
										<?php if ( $product && $product->exists() && $product->is_downloadable() && $order->is_download_permitted() ) : ?>

											<dt><?php esc_attr_e( 'Download:', 'woocommerce-delivery-notes' ); ?></dt>
											<dd>
											<?php
											// translators: files count.
											printf( esc_attr_e( '%s Files', 'woocommerce-delivery-notes' ), count( $item->get_item_downloads() ) );
											?>
											</dd>

										<?php endif; ?>

										<?php

											$fields = apply_filters( 'wcdn_order_item_fields', array(), $product, $order, $item );

										foreach ( $fields as $field ) :
											?>

											<dt><?php echo esc_html( $field['label'] ); ?></dt>
											<dd><?php echo esc_html( $field['value'] ); ?></dd>

										<?php endforeach; ?>
									</dl>
								<?php } ?>
								<?php do_action( 'wcdn_order_item_after', $product, $order, $item ); ?>
							</td>

							<td class="product-quantity">
								<span><?php echo esc_attr( apply_filters( 'wcdn_order_item_quantity', $item['qty'], $item ) ); ?></span>
							</td>

						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tbody>
			
			<tfoot>
				<?php
				$totals_arr = $order->get_order_item_totals();
				if ( $totals_arr ) :
	
					foreach ( $totals_arr as $key => $total ) :
					if('shipping' != $key) continue;
						?>
						<tr>
							<td class="total-name"><span><?php echo wp_kses_post( $total['label'] ); ?></span></td>
							<td class="total-price"><span><?php echo wp_kses_post( $total['value'] ); ?></span></td>
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
			</tfoot>

			
		</table>

		<?php do_action( 'wcdn_after_items', $order ); ?>
	</div><!-- .order-items -->
<?php endif; ?>


<?php if(wcdn_get_template_type() == 'invoice') : ?>



<div class="product_vat_info_table order-items">
	<table id="product_vat_table">
<thead>
<tr>
	<th class="head-name"></th>
    <th class="head-item-price">HT</th>
    <th class="head-quantity">TVA</th>
    <th class="head-price">TTC</th>
</tr>
</thead>
<tbody>




<?php  

if ( count( $order->get_items() ) > 0 ) :

	$price_6 = 0;
    $price_21 = 0;

	foreach ( $order->get_items() as $item ) {
    	
        $item_price_6 = 0;
   	 	$item_price_21 = 0;
    
    	$product = apply_filters( 'wcdn_order_item_product', $order->get_product_from_item( $item ), $item );
        
        $terms = get_the_terms( $product->get_id(), 'product_cat' );
        if(is_array($terms) && $terms[0]->slug  == 'boissons'){
        	$item_price_21 = $item_price_21 + $order->get_line_subtotal( $item );
        } else {
        	$item_price_6 = $item_price_6 + $order->get_line_subtotal( $item );
        }
        
        $price_6 = $price_6 + $item_price_6;
    	$price_21 = $price_21 + $item_price_21;
        
    }

    if($price_6 > 0){
    	$price_6_base_price = $price_6 / 1.06;
    	$price_6_vat = $price_6 - $price_6_base_price;
        
        $price_6 = $price_6_base_price;
    } else {
    	$price_6_vat = 0;
    }
    
    
    if($price_21 > 0){
    	$price_21_base_price = $price_21 / 1.21;
    	$price_21_vat = $price_21 - $price_21_base_price;
        
        $price_21 = $price_21_base_price;
    } else {
    	$price_21_vat = 0;
    }
    

    
    $le_total_price = $price_6 + $price_21;
    $total_vat = $price_6_vat + $price_21_vat;
    
    
    $all_total_price_6 = $price_6 + $price_6_vat;
    $all_total_price_21 = $price_21 + $price_21_vat;
   

?>





<tr>
	<td class="product-name">21%(A)</td>
    <td class="product-item-price"><?php echo wc_price($price_21); ?></td>
    <td class="product-quantity"><?php echo wc_price($price_21_vat); ?></td>
    <td class="product-price"><?php echo wc_price($all_total_price_21); ?></td>
</tr>


<tr>
	<td class="product-name">6%(B)</td>
    <td class="product-item-price"><?php echo wc_price($price_6); ?></td>
    <td class="product-quantity"><?php echo wc_price($price_6_vat); ?></td>
    <td class="product-price"><?php echo wc_price($all_total_price_6); ?></td>
</tr>


<tr class="footer_row">
	<td class="product-name"></td>
    <td class="product-item-price"><?php echo wc_price($le_total_price); ?></td>
    <td class="product-quantity"><?php echo wc_price($total_vat); ?></td>
    <td class="product-price"><?php echo isset($totals_arr['order_total']) ? $totals_arr['order_total']['value'] : ''; ?></td>
</tr>
</tbody>
    </table>
</div>

<?php endif; endif; ?>



	<div class="order-notes">
		<?php if ( wcdn_has_customer_notes( $order ) ) : ?>
			<h4><?php esc_attr_e( 'Customer Note', 'woocommerce-delivery-notes' ); ?></h4>
			<?php wcdn_customer_notes( $order ); ?>
		<?php endif; ?>

		<?php do_action( 'wcdn_after_notes', $order ); ?>
	</div><!-- .order-notes -->

	<div class="order-thanks">
		<?php wcdn_personal_notes(); ?>

		<?php do_action( 'wcdn_after_thanks', $order ); ?>
	</div><!-- .order-thanks -->

	<div class="order-colophon">
		<div class="colophon-policies">
			<?php wcdn_policies_conditions(); ?>
		</div>

		<div class="colophon-imprint">
			<?php wcdn_imprint(); ?>
		</div>	

		<?php do_action( 'wcdn_after_colophon', $order ); ?>
	</div><!-- .order-colophon -->