<?php

if ( ! defined("ABSPATH") ) {
	exit;
}

final class Pvalley_Ups_Shipping_Constant {
	
	/**
	 * Logger Constant.
	 */
	const LOGGER_CONTEXT = array( "source" => "Pvalley-Ups-Shipping" );
	
	/**
	 * US Service Codes
	 */
	const SERVICE_CODES = [
		"US"	=>	[
			// Domestic Service Codes
			"02"	=>	"UPS 2nd Day Air",
			"59"	=>	"UPS 2nd Day Air A.M.",
			"12"	=>	"UPS 3 Day Select",
			"03"	=>	"UPS Ground",
			"01"	=>	"UPS Next Day Air",
			"14"	=>	"UPS Next Day Air Early",
			"13"	=>	"UPS Next Day Air Saver",
			"75"	=>	"UPS Heavy Goods",
			// International or Originating from US
			"11"	=>	"UPS Standard",
			"07"	=>	"UPS Worldwide Express",
			"08"	=>	"UPS Worldwide Expedited",
			"54"	=>	"UPS Worldwide Express Plus",
			"65"	=>	"UPS Worldwide Saver",
			// Freight
			"96"	=>	"UPS Worldwide Express Freight",
			"71"	=>	"UPS Worldwide Express Freight Midday"
		],
		"CA"	=>	[
			// Domestic Service Codes
			"02"	=>	"UPS Expedited",
			"13"	=>	"UPS Express Saver",
			"12"	=>	"UPS 3 Day Select",
			"70"	=>	"UPS Access Point Economy",
			"01"	=>	"UPS Express",
			"14"	=>	"UPS Express Early",
			// International Service Codes
			"65"	=>	"UPS Express Saver",
			"11"	=>	"UPS Standard",
			"08"	=>	"UPS Worldwide Expedited",
			"07"	=>	"UPS Worldwide Express",
			"54"	=>	"UPS Worldwide Express Plus",
			// Freight
			"96"	=>	"UPS Worldwide Express Freight",
			"71"	=>	"UPS Worldwide Express Freight Midday"
		],
		"MX"	=>	[
			"70"	=>	"UPS Access Point Economy",
			"08"	=>	"UPS Expedited",
			"07"	=>	"UPS Express",
			"11"	=>	"UPS Standard",
			"54"	=>	"UPS Worldwide Express Plus",
			"65"	=>	"UPS Worldwide Saver",
			// Freight
			"96"	=>	"UPS Worldwide Express Freight",
			"71"	=>	"UPS Worldwide Express Freight Midday"
		],
		"PL"	=>	[
			"70"	=>	"UPS Access Point Economy",
			"83"	=>	"UPS Today Dedicated Courier",
			"85"	=>	"UPS Today Express",
			"86"	=>	"UPS Today Express Saver",
			"82"	=>	"UPS Today Standard",
			"08"	=>	"UPS Expedited",
			"07"	=>	"UPS Express",
			"54"	=>	"UPS Express Plus",
			"65"	=>	"UPS Express Saver",
			"11"	=>	"UPS Standard",
			// Freight
			"96"	=>	"UPS Worldwide Express Freight",
			"71"	=>	"UPS Worldwide Express Freight Midday"
		],
		"PR"	=>	[
			"02"	=>	"UPS 2nd Day Air",
			"03"	=>	"UPS Ground",
			"01"	=>	"UPS Next Day Air",
			"14"	=>	"UPS Next Day Air Early",
			"08"	=>	"UPS Worldwide Expedited",
			"07"	=>	"UPS Worldwide Express",
			"54"	=>	"UPS Worldwide Express Plus",
			"65"	=>	"UPS Worldwide Saver",
			// Freight
			"96"	=>	"UPS Worldwide Express Freight",
			"71"	=>	"UPS Worldwide Express Freight Midday"
		],
		"DE"	=>	[
			"74"	=>	"UPS Express 12:00",
			"07"	=>	"UPS Express",
			"11"	=>	"UPS Standard",
			"08"	=>	"UPS Worldwide Expedited",
			"54"	=>	"UPS Worldwide Express Plus",
			"65"	=>	"UPS Worldwide Saver",
			// Freight
			"96"	=>	"UPS Worldwide Express Freight",
			"71"	=>	"UPS Worldwide Express Freight Midday"
		],
		"OTHERS" =>	[
			"07"	=>	"UPS Express",
			"11"	=>	"UPS Standard",
			"08"	=>	"UPS Worldwide Expedited",
			"54"	=>	"UPS Worldwide Express Plus",
			"65"	=>	"UPS Worldwide Saver",
			// Freight
			"96"	=>	"UPS Worldwide Express Freight",
			"71"	=>	"UPS Worldwide Express Freight Midday"
		]
	];
}