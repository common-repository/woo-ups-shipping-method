<?php

if( ! defined("ABSPATH") ) {
	exit;
}

$country_state_options = array();
if ( isset( WC()->countries ) ) {
	$country_state_options = WC()->countries->get_countries();
	foreach ( $country_state_options as $country_code => $country ) {
		$states = WC()->countries->get_states( $country_code );
		if ( $states ) {
			unset( $country_state_options[ $country_code ] );
			foreach ( $states as $state_code => $state_name ) {
				$country_state_options[ $country_code . ':' . $state_code ] = $country . ' &mdash; ' . $state_name;
			}
		}
	}
}

$this->form_fields = array(

	array(
		'title'       		=> __( 'API Settings', PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		'type'        		=> 'title',
		'description' 		=> sprintf( __( 'You need to obtain UPS account credentials by registering on their %swebsite →%s', PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ), '<a href="https://www.ups.com/upsdeveloperkit" target="_blank">', '</a>' ),
	),
	"enabled" => array(
		"title"				=>	__( "Realtime Rates", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"checkbox",
		"label"				=>	"Enable",
		"default"			=>	"no",
		"description"		=> __( "Enable Realtime rates on cart and checkout page.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
	),
	"user_id"	=>	array(
		"title"				=>	__( "UPS User Id", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"text",
		"description"		=>	__( "Provide your UPS Account User Id.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_api_settings"
	),
	"password"	=>	array(
		"title"				=>	__( "UPS Password", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"password",
		"description"		=>	__( "Provide your UPS Account Password.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_api_settings"
	),
	"access_key"	=>	array(
		"title"				=>	__( "UPS Access Key", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"text",
		"description"		=>	__( "Provide your UPS Access Key.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_api_settings"
	),
	"account_number"	=>	array(
		"title"				=>	__( "UPS Account Number", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"text",
		"description"		=>	__( "Provide your UPS Account Number.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_api_settings"
	),
	"api_mode"				=>	array(
		"title"				=>	__( "API Mode", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"select",
		"options"			=>	array(
			"live"			=>	__( "Live Mode", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
			"test"			=>	__( "Test Mode", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		),
	),
	"debug_mode"	=> array(
		"title"	=> __( "Debug Mode", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"label"	=> __( "Enable Debug Mode", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"	=> "checkbox"
	),
	"weight_dim_units"	=>	array(
		"title"				=>	__( "UPS Unit", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"select",
		"default"			=>	"imperial",
		"options"			=>	array(
			"metric"		=>	__( "KG / CM", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
			"imperial"		=>	__( "LB / IN", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		),
		"description"		=>	__( "Toggle the selected option, if you're getting 'This measurement system is not valid for the selected country'", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
	),
	array(
		"title"				=>	__( "Address", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN),
		"type"				=>	"title",
		"description"		=>	__( "Configure Shipper Address here→", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
	),
	"shipper_address_line_1"		=>	array(
		"title"				=>	__( "Shipping Address Line 1", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"text",
		"description"		=>	__( "Provide Ship from address.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_shipping_address"
	),
	"shipper_address_line_2"		=>	array(
		"title"				=>	__( "Shipping Address Line 2", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"text",
		"description"		=>	__( "Provide Ship from address.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_shipping_address"
	),
	"shipper_address_line_3"		=>	array(
		"title"				=>	__( "Shipping Address Line 3", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"text",
		"description"		=>	__( "Provide Ship from address.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_shipping_address"
	),
	"shipper_city"		=>	array(
		"title"				=>	__( "Shipping City", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"text",
		"description"		=>	__( "Provide Ship from city name.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_shipping_address"
	),
	"shipper_postcode"		=>	array(
		"title"				=>	__( "Shipping Postcode", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"text",
		"description"		=>	__( "Provide Shipping Postalcode.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_shipping_address"
	),
	"shipper_country"		=>	array(
		"title"				=>	__( "Shipping Country / State", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"select",
		"options"			=>	$country_state_options,
		"description"		=>	__( "Provide Shipping country and state.", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_shipping_address"
	),
	"packaging_method"		=> array(
		"title"				=>	__( "Packaging Method", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"				=>	"select",
		"options"			=> array(
			"per_item"		=>	__( "Per Item Individually", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		),
		"description"		=>	__( "Packaging Methods", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"desc_tip"			=>	true,
		"class"				=>	"pvalley_ups_shipping_address"
	),
	"negotiated_rates"	=>	array(
		"title"	=>	__( "Negotiated Rates", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"label"	=>	__( "Enable Negotiated rates", PVALLEY_UPS_SHIPPING_TEXT_DOMAIN ),
		"type"	=>	"checkbox",
		"desc_tip"	=> true
	)
);