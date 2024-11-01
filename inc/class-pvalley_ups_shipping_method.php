<?php
if( ! defined("ABSPATH") ) {
	exit;
}

if( ! class_exists( "Pvalley_Ups_Shipping_Method" ) ) {
	class Pvalley_Ups_Shipping_Method extends WC_Shipping_Method {
		/**
		 * Constructor.
		 */
		public function __construct() {
			$this->id = Pvalley_UPS_Shipping_Main::$id;
			$this->method_title			= __( "UPS", "ups-woocommerce-shipping" );		// Title shown in admin menu
			$this->method_description	= __( "Description of your shipping method", "ups-woocommerce-shipping" );
			$this->init();
		}

		/**
		 * Initializes the shipping methods.
		 */
		public function init() {
			// Load the settings API
			$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
			$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
			
			$this->format_settings( $this->settings );
			// Save settings in admin if you have any defined
			add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		/**
		 * Init form fields / settings page.
		 */
		public function init_form_fields() {
			require "pvalley-ups-settings.php";
		}

		/**
		 * Initialise Settings.
		 *
		 * Store all settings in a single database entry
		 * and make sure the $settings array is either the default
		 * or the settings stored in the database.
		 *
		 * @since 1.0.0
		 * @uses get_option(), add_option()
		 */
		public function init_settings() {
			$this->settings = get_option( $this->get_option_key(), null );

			// If there are no settings defined, use defaults.
			if ( ! is_array( $this->settings ) ) {
				$form_fields    = $this->get_form_fields();
				$this->settings = array_merge( array_fill_keys( array_keys( $form_fields ), '' ), wp_list_pluck( $form_fields, 'default' ) );
			}
		}

		/**
		 * Get the form fields after they are initialized.
		 *
		 * @return array of options
		 */
		public function get_form_fields() {
			return apply_filters( 'woocommerce_settings_api_form_fields_' . $this->id, array_map( array( $this, 'set_defaults' ), $this->form_fields ) );
		}

		public function format_settings( $settings ) {
			$settings = apply_filters( "pvalley_ups_settings", $settings, "rate" );							// Allow to change Settings from external plugins
			$this->enabled				= ! empty( $settings['enabled']) ? $settings['enabled'] : "no";		// By default disabled.
			$this->user_id				= ! empty( $settings['user_id'] ) ? $settings['user_id'] : "";		// UPS User Id
			$this->password				= ! empty( $settings['password'] ) ? $settings['password'] : "";	// UPS Password
			$this->access_key			= ! empty( $settings['access_key'] ) ? $settings['access_key'] : "";	// UPS Access Key
			$this->account_number		= ! empty( $settings['account_number'] ) ? $settings['account_number'] : "";	// UPS Account Number
			$this->api_mode				= ! empty( $settings['api_mode'] ) ? $settings["api_mode"] : "test";	// API Mode
			if( $this->api_mode == "test" ) {
				$this->rate_endpoint = "https://wwwcie.ups.com/rest/Rate";
			}
			else{
				$this->rate_endpoint = "https://onlinetools.ups.com/rest/Rate";
			}

			$this->ups_weight_dim_unit	= ! empty( $settings["ups_weight_dim_unit"] ) ? $settings["ups_weight_dim_unit"] : "imperial";
			if( $this->ups_weight_dim_unit == "imperial" ) {
				$this->weight_unit = 'LBS';
				$this->dim_unit    = 'IN';
			}
			else{
				$this->weight_unit = 'KGS';
				$this->dim_unit    = 'CM';
			}

			// Shipper Address
			$this->shipper_address_line_1	= ! empty( $settings['shipper_address_line_1'] ) ? $settings['shipper_address_line_1'] : "";
			$this->shipper_address_line_2	= ! empty( $settings['shipper_address_line_2'] ) ? $settings['shipper_address_line_2'] : "";
			$this->shipper_address_line_3	= ! empty( $settings['shipper_address_line_3'] ) ? $settings['shipper_address_line_3'] : "";
			$this->shipper_city				= ! empty( $settings['shipper_city'] ) ? $settings['shipper_city'] : "";
			$this->shipper_postcode			= ! empty( $settings['shipper_postcode'] ) ? $settings['shipper_postcode'] : "";
			// Shipping Country and Shipping State.
			if( ! empty( $settings['shipper_country'] ) ) {
				$temp = explode( ":", $settings['shipper_country']);
				$this->shipper_country	= array_shift($temp);
				$this->shipper_state	= array_shift($temp);
			}
			else{
				$this->shipper_country	= null;
				$this->shipper_state	= null;
			}
			$this->service_codes_arr = ( ! empty($this->shipper_country) && isset(Pvalley_Ups_Shipping_Constant::SERVICE_CODES[$this->shipper_country]) ) ? Pvalley_Ups_Shipping_Constant::SERVICE_CODES[$this->shipper_country] : Pvalley_Ups_Shipping_Constant::SERVICE_CODES["OTHERS"];	// Service Codes
			$this->packaging_method = ! empty($this->settings['packaging_method']) ? $this->settings['packaging_method'] : "per_item";		// Packaging method
			$this->is_negotiated_rates = empty( $this->settings["negotiated_rates"] ) || $this->settings["negotiated_rates"] == "no" ? false : true;	// Negotiated rates
			$this->debug_mode = empty( $this->settings["debug_mode"]) || $this->settings["debug_mode"] == "no" ? false : true;	// Debug Mode
		}

		/**
		 * Calculate Shipping.
		 * @param $package Cart Package.
		 */
		public function calculate_shipping( $package = array() ) {
			// WC_Logger
			if( $this->debug_mode ) {
				$this->logger = wc_get_logger();
			}

			$shipping_costs = $this->get_shipping_costs( $package );
			if( ! empty( $shipping_costs) ) {
				$this->add_rates( $shipping_costs );
			}
		}

		/**
		 * Add the rates in WooCommerce Cart or Checkout page.
		 * @param $shipping_costs Array of Shipping Cost.
		 * @return void.
		 */
		public function add_rates( $shipping_costs = array() ) {
			foreach( $shipping_costs as $shipping_cost_detail ) {
				$rate = [
					"id"	=>	$this->id .":". $shipping_cost_detail["ServiceCode"],
					"label"	=>	$shipping_cost_detail["ServiceName"],
					"cost"	=>	$shipping_cost_detail["cost"],
					"meta_data"	=>	[
						"pvalleyShippingId"	=>	$this->id .":". $shipping_cost_detail["ServiceCode"],
						"pvalleyServicecode"	=>	$shipping_cost_detail["ServiceCode"]
					]
				];
				$this->add_rate( $rate );
			}
		}

		/**
		 * Get the UPS rates by calling the UPS API.
		 * @param $package UPS package as array.
		 * @return $rates Processed rates from UPS response.
		 */
		public function get_shipping_costs( $package = array() ) {
			$rates = [];
			$this->wc_package = $package;
			$ups_response_json = null;
			$ups_package = $this->get_ups_packages( $package );
			$request_body = json_encode( $ups_package );
			
			$response = wp_remote_post( $this->rate_endpoint, array(
				"method" => "POST",
				"headers" => array(
					"Content-Type" => "application/json",
					"Access-Control-Allow-Headers" => "Origin, X-Requested-With, Content-Type, Accept",
					"Access-Control-Allow-Methods" => "POST",
					"Access-Control-Allow-Origin" => "*"
				),
				"body" => $request_body,
				"timeout" => 45
			));

			if ( ! is_wp_error( $response ) ) {
				$ups_response_json = $response["body"];
				if( $this->debug_mode ) {
					$this->logger->debug( "***** UPS Request *****". PHP_EOL . PHP_EOL . print_r($request_body,true). PHP_EOL, Pvalley_Ups_Shipping_Constant::LOGGER_CONTEXT );	// Log in wc_log
					$this->logger->debug( "***** UPS Response *****". PHP_EOL . PHP_EOL . print_r($ups_response_json,true), Pvalley_Ups_Shipping_Constant::LOGGER_CONTEXT );	// Log in wc_log
				}
				$rates = $this->process_ups_response( $ups_response_json);
			} else {
				$this->logger->debug( "***** UPS Request *****". PHP_EOL . PHP_EOL . print_r($request_body,true). PHP_EOL, Pvalley_Ups_Shipping_Constant::LOGGER_CONTEXT );	// Log in wc_log
				$this->logger->debug( "***** UPS Response *****". PHP_EOL . PHP_EOL . print_r( $response->get_error_message(),true), Pvalley_Ups_Shipping_Constant::LOGGER_CONTEXT );	// Log in wc_log
			}

			return $rates;
		}

		/**
		 * Process the rate response of UPS.
		 * @param $ups_response_json JSON UPS response.
		 */
		public function process_ups_response( $ups_response_json ) {
			$response = json_decode( $ups_response_json );
			$rates = [];
			if ( $response->RateResponse && $response->RateResponse->Response->ResponseStatus->Code == "1") {
				$rated_shipment = $response->RateResponse->RatedShipment;
				foreach( $rated_shipment as $rate_details ) {
					$shipping_cost_detail = $this->is_negotiated_rates ? $rate_details->NegotiatedRateCharges->TotalCharge : $rate_details->TotalCharges;
					$rates[] = [
						"ServiceCode"	=>	$rate_details->Service->Code,
						"ServiceName"	=>	$this->service_codes_arr[ $rate_details->Service->Code ],
						"cost"			=>	$shipping_cost_detail->MonetaryValue,
						"currency"		=>	$shipping_cost_detail->CurrencyCode
					];
				}
			} else{

			}
			return $rates;
		}

		/**
		 * Get UPS Packages based on the packaging methods.
		 * @param $package WooCommerce Packages.
		 * @return array UPS Packages.
		 */
		public function get_ups_packages( $package ) {
			switch( $this->packaging_method ) {
				case "per_item" 	:	$packages = $this->per_item_packaging( $package );
										break;
				case "weight_based"	:	$packages = $this->weight_based_packaging( $package );
										break;
				default				:	$packages = $this->weight_based_packaging( $package );
										break;
			}
			return apply_filters( "pvalley_ups_packages", $packages, $package );
		}

		/**
		 * Get UPS Package for per_item packaging.
		 */
		public function per_item_packaging( $package ) {
			$package_data = $this->getPerItemUpsPackageDetails( $package );
			$request = array(
				"UPSSecurity"	=>	$this->getUpsSecurity(),
				"RateRequest"	=>	array(
					"Request"	=>	array(
						"RequestOption"	=>	"Shop",		// Shop for all the rates and Shoptimeintransit for all the rates and time.
						"TransactionReference"	=>	array(
							"CustomerContext"	=>	"PluginValley UPS Rate Request ....."
						),
					),
					"Shipment"	=>	array(
						"Shipper"	=>	$this->shipperDetails(),
						"ShipTo"	=>	$this->getShiptoDetails( $package ),
						// "ShipFrom" => $this->getShipFromAddress(),
						"Package" => $package_data
					)
				)
			);

			// Shipment Level options
			$shipmentRatingOptions = $this->getShipmentRatingOptions();
			if( ! empty($shipmentRatingOptions) ) {
				$request["RateRequest"]["Shipment"]["ShipmentRatingOptions"] = $shipmentRatingOptions;
			}
			return $request;
		}

		/**
		 * Get Shipment Level options.
		 * @return array
		 */
		public function getShipmentRatingOptions() {
			$shipmentRatingOptions = [];
			if( $this->is_negotiated_rates ) {
				$shipmentRatingOptions["NegotiatedRatesIndicator"] = "";		// Enable Negotiated rates
			}
			return $shipmentRatingOptions;
		}

		/**
		 * Get UPS Security Details.
		 */
		public function getUpsSecurity() {
			$ups_security = array(
				"UsernameToken" => array(
					"Username"	=>	$this->user_id,
					"Password"	=>	$this->password
				),
				"ServiceAccessToken"	=>	array(
					"AccessLicenseNumber"	=>	$this->access_key
				)
			);
			return $ups_security;
		}

		/**
		 * Get shipper details.
		 */
		public function shipperDetails() {
			$shipper_details = array(
				"Name"	=>	"Shipper Name",
				"ShipperNumber"	=>	$this->account_number,
				"Address"	=>	array(
					"AddressLine"	=>	array(
						$this->shipper_address_line_1,
						$this->shipper_address_line_2,
						$this->shipper_address_line_3
					),
					"City"	=>	$this->shipper_city,
					"StateProvinceCode"	=>	$this->shipper_state,
					"PostalCode"	=>	$this->shipper_postcode,
					"CountryCode"	=>	$this->shipper_country
				)
			);
			return $shipper_details;
		}

		/**
		 * Get Ship to address.
		 */
		public function getShiptoDetails( $package ) {
			$destination = $package["destination"];
			$ship_to_details = array(
				"Name"	=>	"Ship To Name",
				"Address"	=>	array(
					"AddressLine" => array(
						$destination["address_1"],
						$destination["address_2"],
					),
					"City" => $destination["city"],
					"StateProvinceCode" => $destination["state"],
					"PostalCode" => $destination["postcode"],
					"CountryCode" => $destination["country"]
				),
			);
			return $ship_to_details;
		}

		/**
		 * Get Ups Package based on per Item Shipping.
		 */
		public function getPerItemUpsPackageDetails( $package ) {
			$package_data = [];
			foreach( $package["contents"] as $line_item ) {
				$product = $line_item["data"];
				$weight = $product->get_weight();
				if( $weight > 0 ) {
					$product_as_package = array(
						"PackagingType" => array(
							"Code" => "02",			// Package
							"Description" => "Description"
						),
						"Dimensions" => array(
							"UnitOfMeasurement" => array(
								"Code" => $this->dim_unit,
								"Description" => "Dimension unit description"
							),
							"Length" => $product->get_length(),
							"Width" => $product->get_width(),
							"Height" => $product->get_height()
						),
						"PackageWeight" => array(
							"UnitOfMeasurement" => array(
								"Code" => $this->weight_unit,
								"Description" => "Weight unit description"
							),
							"Weight" => $product->get_weight()
						),
	
					);
					for( $count = 0; $count < $line_item["quantity"]; $count++ ) {
						$package_data[] = $product_as_package;
					}
				}
			}
			return $package_data;
		}
	}
}