<?php

/******************************************************************************************
 * Copyright (C) Smackcoders 2016 - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * You can contact Smackcoders at email address info@smackcoders.com.
 *******************************************************************************************/
if ( ! defined( 'ABSPATH' ) )
	exit; // Exit if accessed directly

require_once('SmackContactFormGenerator.php');
add_action('woocommerce_checkout_order_processed', array('wc_place_order_handling' , 'send_order_info'));
add_action( 'woocommerce_order_status_completed' , array('wc_place_order_handling' , 'convert_lead_into_contact'), 10, 1 );
add_action( 'woocommerce_store_api_checkout_order_processed', array('wc_place_order_handling','send_order_info_new'));

class wc_place_order_handling {

	public static function convert_lead_into_contact($order_id) {
		$ecom_module_selected = get_option( 'ecom_wc_module' );
		$convert_lead = get_option( "ecom_wc_convert_lead" );
		$product=[];

		if( $ecom_module_selected == 'Leads' && $convert_lead == 'on' )
		{
			$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
			$get_ecom_lead_config = get_option( "ecom_wc_{$activated_crm}_Leads_config" );
			global $wpdb , $woocommerce , $wcdn,$current_user;
			$order = new WC_Order( $order_id );

			$get_lead_id = $wpdb->get_results( $wpdb->prepare("select crmid,lead_no,contact_no from wp_smack_ecom_info where order_id=%d" , $order_id ));
			$ecom_lead_id = $get_lead_id[0]->crmid;
			$ecom_lead_no = $get_lead_id[0]->lead_no;
			$ecom_cont_no = $get_lead_id[0]->contact_no;

			//MAKE FINAL ARRAY - Sales Order

			$final_sales_order_array = array();
			switch( $activated_crm )
			{
				case 'wptigerpro';
				$final_sales_order_array['subject'] = $order->billing_first_name." SO-".$order_id;
				$final_sales_order_array['sostatus'] = 'complete';
				$final_sales_order_array['bill_street'] = $order->billing_address_1.','.$order->billing_address_2;
				$final_sales_order_array['ship_street'] = $order->shipping_address_1.','.$order->shipping_address_2;
				$final_sales_order_array['bill_city']	= $order->billing_city;
				$final_sales_order_array['ship_city']	= $order->shipping_city;
				$final_sales_order_array['bill_state']  = $order->billing_state;
				$final_sales_order_array['ship_state']  = $order->shipping_state;
				$final_sales_order_array['bill_code']   = $order->billing_postcode;
				$final_sales_order_array['ship_code']   = $order->shipping_postcode;
				$final_sales_order_array['bill_country']= $order->billing_country; 
				$final_sales_order_array['ship_country']= $order->shipping_country;
				$final_sales_order_array['hdnGrandTotal'] = $order->get_total();
				$final_sales_order_array['assigned_user_id'] = $get_ecom_lead_config['ecom_assignedto'];
				$final_sales_order_array['invoicestatus'] = 'complete';
				$final_sales_order_array['purchaseorder'] = $order_id;

				$get_ecom_products = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=%d" , $order_id ) );
				$explode_prod_ids = explode( ',' , $get_ecom_products[0]->product_id ); 
				$i = 0;
				foreach ( $order->get_items() as $item_id => $item ) {
					$product[$i]     = array(
						'productid' => $explode_prod_ids[$i],
						'quantity'  => $item['qty'],
						'listprice' => $item['line_subtotal'],
					);
					$i++;
				}

				$final_sales_order_array['productid'] = $explode_prod_ids[0];
				$final_sales_order_array['LineItems'] = $product ;
				break;

			case 'wpzohopro':
			case 'wpzohopluspro':
				$final_sales_order_array['Subject'] = $order->billing_first_name." SO-".$order_id;
				$final_sales_order_array['Purchase Order'] = $order_id;
				$final_sales_order_array['Status'] = 'Delivered';
				$final_sales_order_array['Billing Street']  = $order->billing_address_1.','.$order->billing_address_2;
				$final_sales_order_array['Shipping Street'] = $order->shipping_address_1.','.$order->shipping_address_2;
				$final_sales_order_array['Billing City']    = $order->billing_city;
				$final_sales_order_array['Shipping City']   = $order->shipping_city;
				$final_sales_order_array['Billing State']   = $order->billing_state;
				$final_sales_order_array['Shipping State']  = $order->shipping_state;
				$final_sales_order_array['Billing Code']    = $order->billing_postcode;
				$final_sales_order_array['Shipping Code']   = $order->shipping_postcode;
				$final_sales_order_array['Billing Country'] = $order->billing_country; 
				$final_sales_order_array['Shipping Country']= $order->shipping_country;
				$final_sales_order_array['Sub Total'] = $order->get_total();
				$final_sales_order_array['Grand Total'] = $order->get_total();
				$final_sales_order_array['Purchase Order'] = $order_id;

				$get_ecom_products = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=%d" , $order_id ) );
				$explode_prod_ids = explode( ',' , $get_ecom_products[0]->product_id ); 
				$i = 1; $j = 0;
				foreach ( $order->get_items() as $item_id => $item ) {
					$wc_product = new WC_Product( $item['product_id'] );
					$price = $wc_product->price;
					$product[$i]     = array(
						'Product Id' => $explode_prod_ids[$j],
						'Quantity'  => $item['qty'],
						'Unit Price' => $price,
						'List Price' => $price,
						'Total' => $item['line_subtotal'],
						'Total After Discount' => $item['line_subtotal'],
						'Net Total' => $item['line_subtotal'],
					);
					$i++; $j++;
				}
				$final_sales_order_array['Product Details'] = $product;
				break;

			case 'wpsalesforcepro':
				$final_sales_order_array = array();
				$final_sales_order_array['Status'] = 'Draft';
				$final_sales_order_array['EffectiveDate'] = date('Y-m-d' , current_time( 'timestamp', 0 ));
				$final_sales_order_array['BillingStreet']  = $order->billing_address_1.','.$order->billing_address_2;
				$final_sales_order_array['ShippingStreet'] = $order->shipping_address_1.','.$order->shipping_address_2;
				$final_sales_order_array['BillingCity']    = $order->billing_city;
				$final_sales_order_array['ShippingCity']   = $order->shipping_city;
				$final_sales_order_array['BillingState']   = $order->billing_state;
				$final_sales_order_array['ShippingState']  = $order->shipping_state;
				$final_sales_order_array['BillingPostalCode']    = $order->billing_postcode;
				$final_sales_order_array['ShippingPostalCode']   = $order->shipping_postcode;
				$final_sales_order_array['BillingCountry'] = $order->billing_country;
				$final_sales_order_array['ShippingCountry']= $order->shipping_country;
				$final_sales_order_array['Description'] = "Order ID : ".$order_id." , Total Amount : ". $order->get_total();
				break;

			}

			$capture_obj = new CapturingProcessClassPRO();
			$capture_obj->ecom_convert_lead($ecom_lead_id , $order_id , $ecom_lead_no , $final_sales_order_array );
		}

		//Contacts
		if( $ecom_module_selected == 'Contacts')
		{
			$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
			$get_ecom_contact_config = get_option( "ecom_wc_{$activated_crm}_Contacts_config" );
			global $wpdb , $woocommerce , $wcdn,$current_user;
			$order = new WC_Order( $order_id );

			$get_lead_id = $wpdb->get_results( $wpdb->prepare("select crmid,lead_no,contact_no from wp_smack_ecom_info where order_id=%d" , $order_id ));
			$ecom_lead_id = $get_lead_id[0]->crmid;
			$ecom_cont_no = $get_lead_id[0]->contact_no;

			//MAKE FINAL ARRAY - Sales Order
			switch( $activated_crm )
			{	
			case 'wptigerpro':
				$final_sales_order_array = array();
				$final_sales_order_array['subject'] = $order->billing_first_name." SO-".$order_id;
				$final_sales_order_array['sostatus'] = 'complete';
				$final_sales_order_array['bill_street'] = $order->billing_address_1.','.$order->billing_address_2;
				$final_sales_order_array['ship_street'] = $order->shipping_address_1.','.$order->shipping_address_2;
				$final_sales_order_array['bill_city']	= $order->billing_city;
				$final_sales_order_array['ship_city']	= $order->shipping_city;
				$final_sales_order_array['bill_state']  = $order->billing_state;
				$final_sales_order_array['ship_state']  = $order->shipping_state;
				$final_sales_order_array['bill_code']   = $order->billing_postcode;
				$final_sales_order_array['ship_code']   = $order->shipping_postcode;
				$final_sales_order_array['bill_country']= $order->billing_country; 
				$final_sales_order_array['ship_country']= $order->shipping_country;
				$final_sales_order_array['hdnGrandTotal'] = $order->get_total();
				$final_sales_order_array['assigned_user_id'] = $get_ecom_contact_config['ecom_assignedto'];
				$final_sales_order_array['contact_id'] = $ecom_cont_no;
				$final_sales_order_array['invoicestatus'] = 'complete';
				$final_sales_order_array['purchaseorder'] = $order_id;

				$get_ecom_products = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=%d" , $order_id ) );
				$explode_prod_ids = explode( ',' , $get_ecom_products[0]->product_id ); 
				$i = 0;
				foreach ( $order->get_items() as $item_id => $item ) {
					$product[$i]     = array(
						'productid' => $explode_prod_ids[$i],
						'quantity'  => $item['qty'],
						'listprice' => $item['line_subtotal'],
					);
					$i++;
				}

				$final_sales_order_array['productid'] = $explode_prod_ids[0];
				$final_sales_order_array['LineItems'] = $product ;
				break;

			case 'wpzohopro':
			case 'wpzohopluspro':
				$final_sales_order_array = array();
				$final_sales_order_array['Subject'] = $order->billing_first_name." SO-".$order_id;
				$final_sales_order_array['Purchase Order'] = $order_id;
				$final_sales_order_array['Status'] = 'Delivered';
				$final_sales_order_array['Billing Street']  = $order->billing_address_1.','.$order->billing_address_2;
				$final_sales_order_array['Shipping Street'] = $order->shipping_address_1.','.$order->shipping_address_2;
				$final_sales_order_array['Billing City']    = $order->billing_city;
				$final_sales_order_array['Shipping City']   = $order->shipping_city;
				$final_sales_order_array['Billing State']   = $order->billing_state;
				$final_sales_order_array['Shipping State']  = $order->shipping_state;
				$final_sales_order_array['Billing Code']    = $order->billing_postcode;
				$final_sales_order_array['Shipping Code']   = $order->shipping_postcode;
				$final_sales_order_array['Billing Country'] = $order->billing_country;
				$final_sales_order_array['Shipping Country']= $order->shipping_country;
				$final_sales_order_array['Sub Total'] = $order->get_total();
				$final_sales_order_array['Grand Total'] = $order->get_total();
				$final_sales_order_array['Purchase Order'] = $order_id;

				$get_ecom_products = $wpdb->get_results( $wpdb->prepare( "select product_id from wp_smack_ecom_info where order_id=%d" , $order_id ) );
				$explode_prod_ids = explode( ',' , $get_ecom_products[0]->product_id );
				$i = 1; $j = 0;
				foreach ( $order->get_items() as $item_id => $item ) {
					$wc_product = new WC_Product( $item['product_id'] );
					$price = $wc_product->price;
					$product[$i]     = array(
						'Product Id' => $explode_prod_ids[$j],
						'Quantity'  => $item['qty'],
						'Unit Price' => $price,
						'List Price' => $price,
						'Total' => $item['line_subtotal'],
						'Total After Discount' => $item['line_subtotal'],
						'Net Total' => $item['line_subtotal'],
					);
					$i++; $j++;
				}
				$final_sales_order_array['Product Details'] = $product;
				break;

			case 'wpsalesforcepro':
				$final_sales_order_array = array();
				$final_sales_order_array['Status'] = 'Draft';
				$final_sales_order_array['EffectiveDate'] = date('Y-m-d' , current_time( 'timestamp', 0 ));
				$final_sales_order_array['BillingStreet']  = $order->billing_address_1.','.$order->billing_address_2;
				$final_sales_order_array['ShippingStreet'] = $order->shipping_address_1.','.$order->shipping_address_2;
				$final_sales_order_array['BillingCity']    = $order->billing_city;
				$final_sales_order_array['ShippingCity']   = $order->shipping_city;
				$final_sales_order_array['BillingState']   = $order->billing_state;
				$final_sales_order_array['ShippingState']  = $order->shipping_state;
				$final_sales_order_array['BillingPostalCode']    = $order->billing_postcode;
				$final_sales_order_array['ShippingPostalCode']   = $order->shipping_postcode;
				$final_sales_order_array['BillingCountry'] = $order->billing_country;
				$final_sales_order_array['ShippingCountry']= $order->shipping_country;
				$final_sales_order_array['Description'] = "Order ID : ".$order_id." , Total Amount : ". $order->get_total();
				break;

			}

			$capture_obj = new CapturingProcessClassPRO();
			if( $activated_crm != 'wpsugarpro' && $activated_crm != 'freshsales' )
			{
				$capture_obj->ecom_create_sales_order( $order_id , $ecom_cont_no , $final_sales_order_array);
			}
		}
	}
	public static function send_order_info_new( $order ) {
		// Do something with the $order object.
		$data_array=[];
		$ArraytoApi=[];
		$ecom_wc_module_name = get_option( 'ecom_wc_module' ); 
		$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$ecom_option_name = get_option( "ecom_wc_{$activated_crm}_{$ecom_wc_module_name}_config" );

		$ecom_config = $ecom_option_name['fields'];
		$order_id = $order->id;
		$order = new WC_Order( $order_id );
		$customer = new WC_Customer( $order_id );
		$items = $order->get_items();
		$myuser_id = (int)$order->user_id;
		$user_info = get_userdata($myuser_id);
		$items = $order->get_items();

		foreach( $items as $item ) {

			$product_name = $item['name'];
			$product_id = $item['product_id'];
			// $product_variation_id = $item['variation_id'];
			$product = get_product( $product_id );
			$product_type = $product->product_type;
			$product_price = $product->get_price();
			$product_sku = $product->get_sku();
			$product_stock_quantity = $product->get_stock_quantity();
			$product_weight = $product->get_weight();

			$make_items = array();
			switch( $activated_crm )
			{
				case 'wptigerpro';
				$make_items[$item['name']] = array(
					'productname' => $product_name,
					'assigned_user_id' => $ecom_option_name['ecom_assignedto'],
					'unit_price' => $product_price,
					'qtyinstock' => $product_stock_quantity,
					'productcategory' => $product_type,
					'serial_no' => $product_id,
					'discontinued' => '1',
					'description' => 'SKU:'. $product_sku);	
				break;

				case 'wpzohopro';
			case 'wpzohopluspro':
				$make_items[$item['name']] = array(
					'Product Name' => $product_name,
					'SMOWNERID' => $ecom_option_name['ecom_assignedto'],
					'Handler' => $ecom_option_name['ecom_assignedto'],
					'Unit Price' => $product_price,
					'Qty in Stock' => $product_stock_quantity,
					'Product Category' => $product_type,
					'Product Code' => $product_id,
					'Product Active' => 'true',
					'Description' => 'SKU:'. $product_sku);
				break;

			case 'wpsalesforcepro':
				$make_items[$item['name']] = array(
					'Name' => $product_name,
					'ProductCode' => 'PID-'.$product_id,
					'IsActive' => 1,
					'Description' => 'SKU : '. $product_sku .' , Price : '.$product_price .' , Quantity : '.$product_stock_quantity,
				);		
			}
		}

		//IMPORTANT
		$checkout_fields = new WC_Countries();
		$checkout_fields = $checkout_fields->get_address_fields();
		//Get Woocommerce Checkout Fields       
		$wc_field_labels = array();
		foreach( $checkout_fields as $check_key => $check_value )
		{
			if( $check_value['label'] != "" )
			{
				$wc_field_labels[$check_key] = $check_value['label'];
			}
			else
			{
				if( $check_key == "billing_address_2" )
				{
					$wc_field_labels[$check_key] = 'Address2';
				}
			}
		}
		$billing_data = $order->data['billing'];
		$billing =array();
		foreach($billing_data as $key => $val){
			$keys ='billing_'.$key;
			$billing[$keys] = $val;
		}
		foreach( $billing as $ecom_key => $ecom_val )
		{
			foreach( $ecom_config as $config_key => $config_value )
			{
				if( $config_key == $wc_field_labels[$ecom_key]  )	
				{
					$data_array[$config_value] = $ecom_val;
				}
			}
		}

		$ArraytoApi['posted'] = $data_array;
		$ArraytoApi['ecom_module'] = $ecom_wc_module_name;
		$ArraytoApi['ecom_crm'] = $ecom_option_name['ecom_crm'];
		$ArraytoApi['ecom_assignedto'] = $ecom_option_name['ecom_assignedto'];
		$ArraytoApi['duplicate_option'] = 'create';
		$ArraytoApi['ecom_shortcode'] = "ecom_wc_{$activated_crm}_{$ecom_wc_module_name}_config";
		$ArraytoApi['products'] = $make_items;
		$ArraytoApi['order_id'] = $order_id;
		require_once( SM_LB_PRO_DIR."includes/Functions.php" );
		$capture_obj = new CapturingProcessClassPRO();
		$capture_obj->ecom_mapped_submission($ArraytoApi);
	}


	 
	public static function send_order_info($order_id) {
		$data_array=[];
		$ArraytoApi=[];
		$ecom_wc_module_name = get_option( 'ecom_wc_module' ); 
		$activated_crm = get_option( 'WpLeadBuilderProActivatedPlugin' );
		$ecom_option_name = get_option( "ecom_wc_{$activated_crm}_{$ecom_wc_module_name}_config" );

		$ecom_config = $ecom_option_name['fields'];

		$order = new WC_Order( $order_id );
		$customer = new WC_Customer( $order_id );
		$items = $order->get_items();
		$myuser_id = (int)$order->user_id;
		$user_info = get_userdata($myuser_id);
		$items = $order->get_items();

		foreach( $items as $item ) {

			$product_name = $item['name'];
			$product_id = $item['product_id'];
			// $product_variation_id = $item['variation_id'];
			$product = get_product( $product_id );
			$product_type = $product->product_type;
			$product_price = $product->get_price();
			$product_sku = $product->get_sku();
			$product_stock_quantity = $product->get_stock_quantity();
			$product_weight = $product->get_weight();

			$make_items = array();
			switch( $activated_crm )
			{
				case 'wptigerpro';
				$make_items[$item['name']] = array(
					'productname' => $product_name,
					'assigned_user_id' => $ecom_option_name['ecom_assignedto'],
					'unit_price' => $product_price,
					'qtyinstock' => $product_stock_quantity,
					'productcategory' => $product_type,
					'serial_no' => $product_id,
					'discontinued' => '1',
					'description' => 'SKU:'. $product_sku);	
				break;

				case 'wpzohopro';
			case 'wpzohopluspro':
				$make_items[$item['name']] = array(
					'Product Name' => $product_name,
					'SMOWNERID' => $ecom_option_name['ecom_assignedto'],
					'Handler' => $ecom_option_name['ecom_assignedto'],
					'Unit Price' => $product_price,
					'Qty in Stock' => $product_stock_quantity,
					'Product Category' => $product_type,
					'Product Code' => $product_id,
					'Product Active' => 'true',
					'Description' => 'SKU:'. $product_sku);
				break;

			case 'wpsalesforcepro':
				$make_items[$item['name']] = array(
					'Name' => $product_name,
					'ProductCode' => 'PID-'.$product_id,
					'IsActive' => 1,
					'Description' => 'SKU : '. $product_sku .' , Price : '.$product_price .' , Quantity : '.$product_stock_quantity,
				);		
			}
		}

		//IMPORTANT
		$checkout_fields = new WC_Countries();
		$checkout_fields = $checkout_fields->get_address_fields();
		//Get Woocommerce Checkout Fields       
		$wc_field_labels = array();
		foreach( $checkout_fields as $check_key => $check_value )
		{
			if( $check_value['label'] != "" )
			{
				$wc_field_labels[$check_key] = $check_value['label'];
			}
			else
			{
				if( $check_key == "billing_address_2" )
				{
					$wc_field_labels[$check_key] = 'Address2';
				}
			}
		}

		foreach( $_POST as $ecom_key => $ecom_val )
		{
			foreach( $ecom_config as $config_key => $config_value )
			{
				if( $config_key == $wc_field_labels[$ecom_key]  )	
				{
					$data_array[$config_value] = $ecom_val;
				}
			}
		}

		$ArraytoApi['posted'] = $data_array;
		$ArraytoApi['ecom_module'] = $ecom_wc_module_name;
		$ArraytoApi['ecom_crm'] = $ecom_option_name['ecom_crm'];
		$ArraytoApi['ecom_assignedto'] = $ecom_option_name['ecom_assignedto'];
		$ArraytoApi['duplicate_option'] = 'create';
		$ArraytoApi['ecom_shortcode'] = "ecom_wc_{$activated_crm}_{$ecom_wc_module_name}_config";
		$ArraytoApi['products'] = $make_items;
		$ArraytoApi['order_id'] = $order_id;
		require_once( SM_LB_PRO_DIR."includes/Functions.php" );
		$capture_obj = new CapturingProcessClassPRO();
		$capture_obj->ecom_mapped_submission($ArraytoApi);
	}

} // Class End
?>
