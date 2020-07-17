<?php

// Remove my account download link
add_filter( 'woocommerce_account_menu_items', 'custom_remove_downloads_my_account', 999 );
function custom_remove_downloads_my_account( $items ) {
    unset($items['downloads']);
    return $items;
}



add_filter( 'woocommerce_shipping_fields' , 'customize_shipping_postcode_field' );
function customize_shipping_postcode_field( $shipping_fields ) {

    $shipping_fields['shipping_postcode']['type'] = 'select';
    $shipping_fields['shipping_postcode']['options'] = array(
        ''         => __('Select your postcode', 'woocommerce'),
        '1420' => 'Braine l\'Alleud (1420)',
        '1428' => 'Lillois (1428)',
        '1410' => 'Waterloo (1410)',
        '1421' => 'Ophain (1421)'
    );

    return $shipping_fields;
}


add_filter( 'woocommerce_default_address_fields' , 'customize_postcode_fields' );
function customize_postcode_fields( $adresses_fields ) {

    $adresses_fields['postcode']['type'] = 'select';
    $adresses_fields['postcode']['options'] = array(
        ''         => __('Select your postcode', 'woocommerce'),
        '1420' => 'Braine l\'Alleud (1420)',
        '1428' => 'Lillois (1428)',
        '1410' => 'Waterloo (1410)',
        '1421' => 'Ophain (1421)'
    );

    return $adresses_fields;
}



add_shortcode( 'le_purchased_products', 'bbloomer_products_bought_by_curr_user' );
   
function bbloomer_products_bought_by_curr_user() {
   
    // GET CURR USER
    $current_user = wp_get_current_user();
    if ( 0 == $current_user->ID ) return;
   
    // GET USER ORDERS (COMPLETED + PROCESSING)
    $customer_orders = get_posts( array(
        'numberposts' => -1,
        'meta_key'    => '_customer_user',
        'meta_value'  => $current_user->ID,
        'post_type'   => wc_get_order_types(),
        'post_status' => array_keys( wc_get_is_paid_statuses() ),
    ) );
   
    // LOOP THROUGH ORDERS AND GET PRODUCT IDS
    if ( ! $customer_orders ) return;
    $product_ids = array();
    foreach ( $customer_orders as $customer_order ) {
        $order = wc_get_order( $customer_order->ID );
        $items = $order->get_items();
        foreach ( $items as $item ) {
            $product_id = $item->get_product_id();
            $product_ids[] = $product_id;
        }
    }
    $product_ids = array_unique( $product_ids );
    $product_ids_str = implode( ",", $product_ids );
   
    // PASS PRODUCT IDS TO PRODUCTS SHORTCODE
    return do_shortcode("[products ids='$product_ids_str']");
   
}



add_action('woocommerce_after_my_account', 'customized_woocommerce_after_my_account');
function customized_woocommerce_after_my_account(){
	echo '<h3>'. __('Products you have purchased before', 'salient') .'</h3>';
	echo do_shortcode('[le_purchased_products]');
}


add_filter('woocommerce_available_payment_gateways','misha_change_wc_gateway_if_empty', 9999, 1 );
function misha_change_wc_gateway_if_empty($allowed_gateways){
    
    if(!empty($allowed_gateways)){
    	if(isset($allowed_gateways['stripe'])){
        	$allowed_gateways['stripe']->title = '';
            $allowed_gateways['stripe']->description = '';
        }
    	if(isset($allowed_gateways['paypal'])){
        	$allowed_gateways['paypal']->title = '';
            $allowed_gateways['paypal']->description = '';
        }
    }

	return $allowed_gateways;
}



add_action('wp_footer', 'lecustom_add_scripts_to_footer');
function lecustom_add_scripts_to_footer(){ ?>
<script>
	var le_payment_icons = jQuery('.woocommerce-checkout-payment .payment_method_stripe label[for="payment_method_stripe"]').empty();
	var le_icons_html = '<img src="https://le-crescendo.be/wp-content/plugins/woocommerce-gateway-stripe/assets/images/bancontact.svg" class="stripe-bancontact-icon stripe-icon" alt="Bancontact" /><img src="https://le-crescendo.be/wp-content/plugins/woocommerce-gateway-stripe/assets/images/mastercard.svg" class="stripe-mastercard-icon stripe-icon" alt="Mastercard" /><img src="https://le-crescendo.be/wp-content/plugins/woocommerce-gateway-stripe/assets/images/visa.svg" class="stripe-visa-icon stripe-icon" alt="Visa" />';
	le_payment_icons.append(le_icons_html);
	
	jQuery( document ).ajaxComplete(function( event, request, settings ) {
	  	var le_payment_icons = jQuery('.woocommerce-checkout-payment .payment_method_stripe label[for="payment_method_stripe"]').empty();
		var le_icons_html = '<img src="https://le-crescendo.be/wp-content/plugins/woocommerce-gateway-stripe/assets/images/bancontact.svg" class="stripe-bancontact-icon stripe-icon" alt="Bancontact" /><img src="https://le-crescendo.be/wp-content/plugins/woocommerce-gateway-stripe/assets/images/mastercard.svg" class="stripe-mastercard-icon stripe-icon" alt="Mastercard" /><img src="https://le-crescendo.be/wp-content/plugins/woocommerce-gateway-stripe/assets/images/visa.svg" class="stripe-visa-icon stripe-icon" alt="Visa" />';
		le_payment_icons.append(le_icons_html);
	});
</script>
<?php }







/* customize login screen */
function le_stylize_custom_login_page() {
    echo '<style type="text/css">
        .login h1 a { background-image:url("'. get_stylesheet_directory_uri().'/assets/images/Logo-white.png") !important; height: 100px !important; width: 100% !important; margin: 0 auto !important; background-size: contain !important; }
		h1 a:focus { outline: 0 !important; box-shadow: none; }
        body.login { background-image:url("'. get_stylesheet_directory_uri().'/assets/images/le-crescendo.jpg") !important; background-repeat: no-repeat !important; background-attachment: fixed !important; background-position: center !important; background-size: cover !important; position: relative; z-index: 999;}
  		body.login:before {background-color: rgba(0,0,0,0.7); position: absolute; width: 100%; height: 100%; left: 0; top: 0; content: ""; z-index: -1; }
  		.login form {
  			background: rgba(255,255,255, 0.8) !important;
  		}
		.login form .input, .login form input[type=checkbox], .login input[type=text] {
			background: transparent !important;
			color: #ddd;
		}
        body.login #nav a, 
        body.login #backtoblog a, 
        body.login label, 
        body.login .message, 
        body.login a {
            color: #f79420 !important;
        }
		.login label {
			color: #DDD !important;
		}
		.login #login_error, .login .message {
			color: #ddd;
			margin-top: 20px;
			background: rgba(255,255,255, 0.2) !important;
		}
		#login {
		    padding: 7% 0 0;
		}
        body.login .submit input[type="submit"] {
            background: #f79420;
            border-color: #f79420;
        }
        .login form .input[type="text"], .login form .input[type="password"] {
            color: #111 !important;
        }
		
		.login #nav a, .login #backtoblog a, .login label, .login .message{
			color:#000 !important;
		}
    </style>';
}
add_action('login_head', 'le_stylize_custom_login_page', 99);

function le_login_logo_url_title() {
 	return 'LE CRESCENDO';
}
add_filter( 'login_headertitle', 'le_login_logo_url_title' );

function le_login_logo_url() {
	return get_bloginfo( 'url' );
}
add_filter( 'login_headerurl', 'le_login_logo_url' );



function remove_footer_admin () {
    echo '<span id="footer-thankyou">powered by <a href="https://feducom.com/" target="_blank">Feducom.com</a></span>';
}
add_filter('admin_footer_text', 'remove_footer_admin');


add_action( 'wp_before_admin_bar_render', 'binaryfork_before_admin_bar_render', 999 ); 
function binaryfork_before_admin_bar_render()
{
    global $wp_admin_bar;

	if(!current_user_can('administrator')){
		$wp_admin_bar->remove_menu('contextHelp');
		$wp_admin_bar->remove_menu('screenOptions');
		$wp_admin_bar->remove_menu('wp-logo');
		$wp_admin_bar->remove_menu('mitoSupport');
		$wp_admin_bar->remove_menu('wpGuide');
		$wp_admin_bar->remove_menu('wpLessons');
	}

}

/**
 * Adds a "My Page" link to the Toolbar.
 *
 * @param WP_Admin_Bar $wp_admin_bar Toolbar instance.
 */
// function toolbar_link_to_mypage( $wp_admin_bar ) {
//     $args = array(
//         'id'    => 'le_switch_the_shop',
//         'title' => __( 'Close Shop', 'textdomain' ),
//         'href'  => admin_url( 'admin.php?page=le_switch_shop', 'https' ),
//         'meta'  => array(
//             'class' => 'le_switch_shop_status'
//         )
//     );
//     $wp_admin_bar->add_node( $args );
// }
// add_action( 'admin_bar_menu', 'toolbar_link_to_mypage', 999 );


// function my_admin_menu() {
// 	add_menu_page(
// 		__( 'Switch Shop', 'my-textdomain' ),
// 		__( 'Switch Shop', 'my-textdomain' ),
// 		'manage_categories',
// 		'le_switch_shop',
// 		'my_admin_page_contents',
// 		'dashicons-schedule',
// 		3
// 	);
// }
// add_action( 'admin_menu', 'my_admin_menu' );

// function my_admin_page_contents() {
// 	//echo get_current_status();
// }
// update_option('zh_dbd3f_79985_993c0_30064', '');
// update_option('zh_dbd3f_79985_993c0_30064', array(1));


/* It will remove the tabs, not hide them with CSS */
add_filter('show_admin_bar', '__return_false');


function le_delivery_note_custom_styles() {
    ?>
<style>
	.company-address {
		font-size: 0.9em;
	}
	h1.company-name {
		margin-bottom: 5px;
		font-size: 1.4em !important;
	}
	.shipping-address > h3,
	.billing-address > h3 {
		margin-bottom: 6px;
		font-size: 1.2em;
	}
	.order-branding {
		text-align: center;
		margin-bottom: 20px;
	}
	.order-addresses {
		margin-bottom: 20px;
	}
	body.receipt .order-items td.product-name {
		font-size: 15px;
		line-height: 22px;
	}
    .product_vat_info_table.order-items {
        margin-top: 50px;
    }
    tr.footer_row {
        border-bottom: 0;
        border-top: 3px solid #000;
        font-weight: 700;
	}
	ul.wc-item-meta {
		margin-bottom: 0;
		margin-top: 6px;
		padding-left: 14px;
	}
	ul.wc-item-meta li {
		padding: 0;
	}
	.billing-address address {
		font-size: 17px;
		line-height: 23px;
	}
	.billing-address h3 {
		font-size: 20px;
	}
</style>
    <?php
}
add_action( 'wcdn_head', 'le_delivery_note_custom_styles', 99 );



add_filter('wcdn_template_registration_invoice', 'custom_le_wcdn_template_registration_invoice');
function custom_le_wcdn_template_registration_invoice($invoice){
	$invoice['labels']['print'] = "Ticket Restaurant";
	return $invoice;
}


add_filter('wcdn_template_registration_receipt', 'custom_le_wcdn_template_registration_receipt');
function custom_le_wcdn_template_registration_receipt($receipt){
	$receipt['labels']['print'] = "Ticket Cuisine";
	return $receipt;
}



/**
 * Set a minimum order amount for checkout
 */




	
add_action( 'woocommerce_checkout_process', 'le_wc_minimum_order_amount' );
add_action( 'woocommerce_before_cart' , 'le_wc_minimum_order_amount' );

function le_wc_minimum_order_amount() {
	// Set this variable to specify a minimum order value
	$le_mim_able_ship_methods = array('flat_rate:7', 'flat_rate:17', 'flat_rate:14', 'flat_rate:11');

	$chosen_methods = WC()->session->get( 'chosen_shipping_methods' );
	$chosen_shipping = $chosen_methods[0]; 
	$location = '';
	
	if(!in_array($chosen_shipping, $le_mim_able_ship_methods)) {
		return;
	} else {
		// For Braine area 
		if($chosen_shipping == 'flat_rate:7') {
			$minimum = 12;
			$location = 'Braine l\'Alleud';
		}
		
		// For Lillois area 
		if($chosen_shipping == 'flat_rate:17') {
			$minimum = 18;
			$location = 'Lillois';
		}
		
		// For Waterlo area 
		if($chosen_shipping == 'flat_rate:14') {
			$minimum = 20;
			$location = 'Waterloo';
		}
		
		// For Ophain area 
		if($chosen_shipping == 'flat_rate:11') {
			$minimum = 18;
			$location = 'Ophain';
		}
	}

	if ( WC()->cart->get_subtotal() < $minimum ) {

		if( is_cart() ) {

			wc_print_notice( 
				sprintf( 'Le total de votre commande est de %s - Vous devez commander un minimum de %s pour être livré à  %s.' , 
						wc_price( WC()->cart->get_subtotal() ), 
						wc_price( $minimum ),
						$location
					   ), 'error' 
			);

		} else {
			wc_add_notice( 
				sprintf( 'Le total de votre commande est de %s - Vous devez commander un minimum de %s pour être livré à  %s.' , 
						wc_price( WC()->cart->get_subtotal() ), 
						wc_price( $minimum ),
						$location
					   ), 'error' 
			);

		}
	}
} // end wc_minimum_order_amount
	


 



//echo var_dump(get_option('testing'));