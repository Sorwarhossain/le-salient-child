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

<th class="head-name"><span><?php esc_attr_e( 'Product', 'woocommerce-delivery-notes' ); ?></span></th>
<th class="head-quantity"><span><?php esc_attr_e( 'Quantity', 'woocommerce-delivery-notes' ); ?></span></th>
<th class="head-price"><span><?php esc_attr_e( 'Total', 'woocommerce-delivery-notes' ); ?></span></th>
					
				</tr>
			</thead>

			<tbody class="le_p_items">

			<?php

$order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );

			do_action( 'woocommerce_order_details_before_order_table_items', $order );

			foreach ( $order_items as $item_id => $item ) {
				$product = $item->get_product();

				wc_get_template(
					'order/order-print-details-item.php',
					array(
						'order'              => $order,
						'item_id'            => $item_id,
						'item'               => $item,
						'show_purchase_note' => $show_purchase_note,
						'purchase_note'      => $product ? $product->get_purchase_note() : '',
						'product'            => $product,
						'print_type'		 => 'invoice'
					)
				);
			}

			do_action( 'woocommerce_order_details_after_order_table_items', $order );
			?>

			</tbody>

			<tfoot>
				<?php
				$totals_arr = $order->get_order_item_totals();
				if ( $totals_arr ) :

					foreach ( $totals_arr as $total ) :
						?>
						<tr>
							<td class="total-name"><span><?php echo wp_kses_post( $total['label'] ); ?></span></td>
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

<tbody class="le_p_items">
				


<?php

$order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );

do_action( 'woocommerce_order_details_before_order_table_items', $order );

foreach ( $order_items as $item_id => $item ) {
	$product = $item->get_product();

	wc_get_template(
		'order/order-print-details-item.php',
		array(
			'order'              => $order,
			'item_id'            => $item_id,
			'item'               => $item,
			'show_purchase_note' => $show_purchase_note,
			'purchase_note'      => $product ? $product->get_purchase_note() : '',
			'product'            => $product,
			'print_type'		 => 'receipt'
		)
	);
}

do_action( 'woocommerce_order_details_after_order_table_items', $order );
?>

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