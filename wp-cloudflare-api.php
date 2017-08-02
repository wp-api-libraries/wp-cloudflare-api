<?php
/**
 * Library for accessing the CloudFlare API on WordPress
 *
 * @link https://api.cloudflare.com/ API Documentation
 * @package WP-API-Libraries\WP-IDX-Cloudflare-API
 */

/*
 * Plugin Name: Cloudflare API
 * Plugin URI: https://wp-api-libraries.com/
 * Description: Perform API requests.
 * Author: WP API Libraries
 * Version: 1.0.0
 * Author URI: https://wp-api-libraries.com
 * GitHub Plugin URI: https://github.com/imforza
 * GitHub Branch: master
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'CloudFlareAPI' ) ) {

	/**
	 * A WordPress API library for accessing the Cloudflare API.
	 *
	 * @version 1.1.0
	 * @link https://api.cloudflare.com/ API Documentation
	 * @package WP-API-Libraries\WP-IDX-Cloudflare-API
	 * @author Santiago Garza <https://github.com/sfgarza>
	 * @author imFORZA <https://github.com/imforza>
	 */
	class CloudFlareAPI {

		/**
		 * API Key.
		 *
		 * @var string
		 */
		static private $api_key;

		/**
		 * Auth Email
		 *
		 * @var string
		 */
		static private $auth_email;

		/**
		 * User Service Key
		 *
		 * @var string
		 */
		static private $user_service_key;

		/**
		 * CloudFlare BaseAPI Endpoint
		 *
		 * @var string
		 * @access protected
		 */
		protected $base_uri = 'https://api.cloudflare.com/client/v4/';

		/**
		 * Route being called.
		 *
		 * @var string
		 */
		protected $route = '';


		/**
		 * Class constructor.
		 *
		 * @param string $api_key               Cloudflare API Key.
		 * @param string $auth_email            Email associated to the account.
		 * @param string $user_service_key      User Service key.
		 */
		public function __construct( $api_key, $auth_email, $user_service_key = '' ) {
			static::$api_key = $api_key;
			static::$auth_email = $auth_email;
			static::$user_service_key = $user_service_key;
		}

		/**
		 * Prepares API request.
		 *
		 * @param  string $route   API route to make the call to.
		 * @param  array  $args    Arguments to pass into the API call.
		 * @param  array  $method  HTTP Method to use for request.
		 * @return self            Returns an instance of itself so it can be chained to the fetch method.
		 */
		protected function build_request( $route, $args = array(), $method = 'GET' ) {
			// Start building query.
			$this->set_headers();
			$this->args['method'] = $method;
			$this->route = $route;

			// Generate query string for GET requests.
			if ( 'GET' === $method ) {
				$this->route = add_query_arg( array_filter( $args ), $route );
			} elseif ( 'application/json' === $this->args['headers']['Content-Type'] ) {
				$this->args['body'] = wp_json_encode( $args );
			} else {
				$this->args['body'] = $args;
			}

			return $this;
		}


		/**
		 * Fetch the request from the API.
		 *
		 * @access private
		 * @return array|WP_Error Request results or WP_Error on request failure.
		 */
		protected function fetch() {
			_error_log( $this->args );
			// Make the request.
			$response = wp_remote_request( $this->base_uri . $this->route, $this->args );

			// Retrieve Status code & body.
			$code = wp_remote_retrieve_response_code( $response );
			$body = json_decode( wp_remote_retrieve_body( $response ) );

			_error_log( $body );
			// Return WP_Error if request is not successful.
			if ( ! $this->is_status_ok( $code ) ) {
				return new WP_Error( 'response-error', sprintf( __( 'Status: %d', 'wp-postmark-api' ), $code ), $body );
			}
			$this->clear();

			return $body;
		}


		/**
		 * Set request headers.
		 */
		protected function set_headers() {
			// Set request headers.
			$this->args['headers'] = array(
					'Content-Type' => 'application/json',
					'X-Auth-Email' => static::$auth_email,
					'X-Auth-Key' => static::$api_key,
			);
		}

		/**
		 * Clear query data.
		 */
		protected function clear() {
			$this->args = array();
			$this->query_args = array();
		}

		/**
		 * Check if HTTP status code is a success.
		 *
		 * @param  int $code HTTP status code.
		 * @return boolean       True if status is within valid range.
		 */
		protected function is_status_ok( $code ) {
			return ( 200 <= $code && 300 > $code );
		}

		/**
		 * Get User Properties
		 *
		 * Account Access: FREE, PRO, Business, Enterprise
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-user-details Documentation.
		 * @access public
		 * @return array  User information.
		 */
		public function get_user() {
			return $this->build_request( 'user' )->fetch();
		}


		/**
		 * Update User
		 *
		 * Account Access: FREE, PRO, Business, Enterprise
		 *
		 * @api PATCH
		 * @see https://api.cloudflare.com/#user-update-user Documentation.
		 * @access public
		 * @param string $first_name User's first name.
		 * @param string $last_name  User's last name.
		 * @param string $phone      User's telephone number.
		 * @param string $country    User's The country in which the user lives.
		 * @param string $zipcode    The zipcode or postal code where the user lives.
		 * @return array             Updated user info.
		 */
		public function update_user( $first_name = null, $last_name = null, $phone = null, $country = null, $zipcode = null ) {
			$args = array();

			if ( null !== $first_name ) {
				$args['first_name']  = $first_name;
			}
			if ( null !== $last_name ) {
				$args['last_name']  = $last_name;
			}
			if ( null !== $phone ) {
				$args['telephone']  = $phone;
			}
			if ( null !== $country ) {
				$args['country']  = $country;
			}
			if ( null !== $zipcode ) {
				$args['zipcode']  = $zipcode;
			}

			return $this->build_request( 'user', $args, 'PATCH' )->fetch();
		}


		/**
		 * Get User Billing Profile.
		 *
		 * Account Access: FREE, PRO, Business, Enterprise
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-billing-profile-billing-profile Documentation.
		 * @access public
		 * @return array   User billing profile.
		 */
		public function get_user_billing_profile() {
			return $this->build_request( 'user/billing/profile' )->fetch();
		}


		/**
		 * Get User Billing History
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-billing-history-billing-history Documentation
		 * @access public
		 * @return array User billing history.
		 */
		public function get_user_billing_history() {
			return $this->build_request( 'user/billing/history' )->fetch();
		}

		/**
		 * Get User Subscriptions.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-billing-history-billing-history Documentation
		 * @return array User subscriptions history.
		 */
		public function get_user_subscriptions() {
			return $this->build_request( 'user/subscriptions' )->fetch();
		}


		/**
		 * List, search, sort, and filter your zones
		 *
		 * @access public
		 * @param  array $args  Query args to send in to API call.
		 * @return array
		 */
		function get_zones( $args = array() ) {
			return $this->build_request( 'zones', $args )->fetch();
		}

		/**
		 * HTTP response code messages.
		 *
		 * @param  [String] $code : Response code to get message from.
		 * @return [String]       : Message corresponding to response code sent in.
		 */
		public function response_code_msg( $code = '' ) {
			switch ( $code ) {
				case 200:
					$msg = __( 'OK.', 'wp-cloudflare-api' );
				break;
				case 400:
					$msg = __( 'Bad Request: Required parameter missing or invalid.', 'wp-cloudflare-api' );
				break;
				case 401:
					$msg = __( 'Unauthorized: User does not have permission.', 'wp-cloudflare-api' );
				break;
				case 403:
					$msg = __( 'Forbidden: Request not authenticated.', 'wp-cloudflare-api' );
				break;
				case 405:
					$msg = __( 'Method Not Allowed: Incorrect HTTP method provided.', 'wp-cloudflare-api' );
				break;
				case 415:
					$msg = __( 'Unsupported Media Type: Response is not valid JSON.', 'wp-cloudflare-api' );
				break;
				case 429:
					$msg = __( 'Too many requests: Client is rate limited.', 'wp-cloudflare-api' );
				break;
				/* CloudFlare CA error codes. */
				case 1000;
					$msg = __( 'API errors encountered.', 'wp-cloudflare-api' );
				break;
				case 1001;
					$msg = __( 'Request had no Authorization header.', 'wp-cloudflare-api' );
				break;
				case 1002;
					$msg = __( 'Unsupported request_type.', 'wp-cloudflare-api' );
				break;
				case 1003;
					$msg = __( 'Failed to read contents of HTTP request.', 'wp-cloudflare-api' );
				break;
				case 1004;
					$msg = __( 'Failed to parse request JSON.', 'wp-cloudflare-api' );
				break;
				case 1005;
					$msg = __( 'Too many hostnames requested - you may only request up to 100 per certificate.', 'wp-cloudflare-api' );
				break;
				case 1006;
					$msg = __( 'One or more hostnames were duplicated in the request and have been removed prior to certificate generation.', 'wp-cloudflare-api' );
				break;
				case 1007;
					$msg = __( 'CSR parsed as empty.', 'wp-cloudflare-api' );
				break;
				case 1008;
					$msg = __( 'Error creating request to CA.', 'wp-cloudflare-api' );
				break;
				case 1009;
					$msg = __( 'Permitted values for the *requested_validity* parameter (specified in days) are: 7, 30, 90, 365, 730, 1095, and 5475 (default).', 'wp-cloudflare-api' );
				break;
				case 1010;
					$msg = __( 'Failed to validate SAN <hostname>: <reason for failure>.', 'wp-cloudflare-api' );
				break;
				case 1011;
					$msg = __( 'Failed to parse CSR.', 'wp-cloudflare-api' );
				break;
				case 1012;
					$msg = __( 'Please provide a zone id when requesting a stored certificate, or fetch by serial number.', 'wp-cloudflare-api' );
				break;
				case 1013;
					$msg = __( 'Please provide a certificate serial number when operating on a single certificate.', 'wp-cloudflare-api' );
				break;
				case 1014;
					$msg = __( 'Certificate already revoked.', 'wp-cloudflare-api' );
				break;
				case 1100;
					$msg = __( 'Failed to write certificate to database.', 'wp-cloudflare-api' );
				break;
				case 1101;
					$msg = __( 'Failed to read certificate from database.', 'wp-cloudflare-api' );
				break;
				case 1200;
					$msg = __( 'API Error: Failed to generate CA request.', 'wp-cloudflare-api' );
				break;
				case 1201;
					$msg = __( 'CA signing failure. Could not parse returned certificate.', 'wp-cloudflare-api' );
				break;
				case 1300;
					$msg = __( 'Failed to fetch keyless servers from API.', 'wp-cloudflare-api' );
				break;
				case 1301;
					$msg = __( 'The key server did not activate correctly.', 'wp-cloudflare-api' );
				break;
				case 1302;
					$msg = __( 'Could not get keyless server port for server <server>.', 'wp-cloudflare-api' );
				break;
				case 1303;
					$msg = __( 'Invalid hostname: <hostname>.', 'wp-cloudflare-api' );
				break;
				default:
					$msg = __( 'Response code unknown.', 'wp-cloudflare-api' );
				break;

				/* Zone Plan Error Codes. */

				/*
				case 1004;
				$msg = __( 'Cannot find a valid zone.','wp-cloudflare-api' );
				break;
				case 1005;
				$msg = __( 'Cannot find a valid plan.','wp-cloudflare-api' );
				break;
				case 1006;
				$msg = __( 'Cannot find a valid reseller plan.','wp-cloudflare-api' );
				break;
				case 1007;
				$msg = __( 'Cannot find a valid zone.','wp-cloudflare-api' );
				break;
				*/

				/* DNS Records for a Zone Error Codes. */

				case 1000;
					$msg = __( 'Invalid user.', 'wp-cloudflare-api' );
				break;
				case 1002;
					$msg = __( 'Invalid or missing zone_id.', 'wp-cloudflare-api' );
				break;
				case 1003;
					$msg = __( 'The per_page must be a positive integer.', 'wp-cloudflare-api' );
				break;
				case 1004;
					$msg = __( 'Invalid or missing zone.', 'wp-cloudflare-api' );
				break;
				case 1005;
					$msg = __( 'Invalid or missing record.', 'wp-cloudflare-api' );
				break;
				case 1007;
					$msg = __( 'Name required.', 'wp-cloudflare-api' );
				break;
				case 1008;
					$msg = __( 'Content required.', 'wp-cloudflare-api' );
				break;
				case 1009;
					$msg = __( 'Invalid or missing record id.', 'wp-cloudflare-api' );
				break;
				case 1010;
					$msg = __( 'Invalid or missing record.', 'wp-cloudflare-api' );
				break;
				case 1011;
					$msg = __( 'Zone file for \'<domain name>\' could not be found.', 'wp-cloudflare-api' );
				break;
				case 1012;
					$msg = __( 'Zone file for \'<domain name>\' is not modifiable.', 'wp-cloudflare-api' );
				break;
				case 1013;
					$msg = __( 'The record could not be found.', 'wp-cloudflare-api' );
				break;
				case 1014;
					$msg = __( 'You do not have permission to modify this zone.', 'wp-cloudflare-api' );
				break;

				/*
				case 1015;
				$msg = __( 'Unknown error','wp-cloudflare-api' );
				break;
				case 1017;
				$msg = __( 'Content for A record is invalid. Must be a valid IPv4 address','wp-cloudflare-api' );
				break;
				case 1018;
				$msg = __( 'Content for AAAA record is invalid. Must be a valid IPv6 address','wp-cloudflare-api' );
				break;
				case 1019;
				$msg = __( 'Content for CNAME record is invalid','wp-cloudflare-api' );
				break;
				case 1024;
				$msg = __( 'Invalid priority, priority must be set and be between 0 and 65535','wp-cloudflare-api' );
				break;
				case 1025;
				$msg = __( 'Invalid content for an MX record','wp-cloudflare-api' );
				break;
				case 1026;
				$msg = __( 'Invalid format for a SPF record. A valid example is \'v=spf1 a mx -all\'. You should not include either the word TXT or the domain name here in the content','wp-cloudflare-api' );
				break;
				case 1027;
				$msg = __( 'Invalid service value','wp-cloudflare-api' );
				break;
				case 1028;
				$msg = __( 'Invalid service value. Must be less than 100 characters','wp-cloudflare-api' );
				break;
				case 1029;
				$msg = __( 'Invalid protocol value','wp-cloudflare-api' );
				break;
				case 1030;
				$msg = __( 'Invalid protocol value. Must be less than 12 characters','wp-cloudflare-api' );
				break;
				case 1031;
				$msg = __( 'Invalid SRV name','wp-cloudflare-api' );
				break;
				case 1032;
				$msg = __( 'Invalid SRV name. Must be less than 90 characters','wp-cloudflare-api' );
				break;
				case 1033;
				$msg = __( 'Invalid weight value. Must be between 0 and 65,535','wp-cloudflare-api' );
				break;
				case 1034;
				$msg = __( 'Invalid port value. Must be between 0 and 65,535','wp-cloudflare-api' );
				break;
				case 1037;
				$msg = __( 'Invalid domain name for a SRV target host','wp-cloudflare-api' );
				break;
				case 1038;
				$msg = __( 'Invalid DNS record type','wp-cloudflare-api' );
				break;
				case 1039;
				$msg = __( 'Invalid TTL. Must be between 120 and 4,294,967,295 seconds, or 1 for automatic','wp-cloudflare-api' );
				break;
				case 1041;
				$msg = __( 'Priority must be set for SRV record','wp-cloudflare-api' );
				break;
				case 1042;
				$msg = __( 'Zone file for \'<domain name>\' could not be found','wp-cloudflare-api' );
				break;
				case 1043;
				$msg = __( 'Zone file for \'<domain name>\' is not editable','wp-cloudflare-api' );
				break;
				case 1044;
				$msg = __( 'A record with these exact values already exists. Please modify or remove this record','wp-cloudflare-api' );
				break;
				case 1045;
				$msg = __( 'The record could not be found','wp-cloudflare-api' );
				break;
				case 1046;
				$msg = __( 'A record with these exact values already exists. Please modify or cancel this edit','wp-cloudflare-api' );
				break;
				case 1047;
				$msg = __( 'You do not have permission to modify this zone','wp-cloudflare-api' );
				break;
				case 1048;
				$msg = __( 'You have reached the record limit for this zone','wp-cloudflare-api' );
				break;
				case 1049;
				$msg = __( 'The record content is missing','wp-cloudflare-api' );
				break;
				case 1050;
				$msg = __( 'Could not find record','wp-cloudflare-api' );
				break;
				case 1052;
				$msg = __( 'You can not point a CNAME to itself','wp-cloudflare-api' );
				break;
				case 1053;
				$msg = __( 'Invalid lat_degrees, must be an integer between 0 and 90 inclusive','wp-cloudflare-api' );
				break;
				case 1054;
				$msg = __( 'Invalid lat_minutes, must be an integer between 0 and 59 inclusive','wp-cloudflare-api' );
				break;
				case 1055;
				$msg = __( 'Invalid lat_seconds, must be a floating point number between 0 and 60, including 0 but not including 60','wp-cloudflare-api' );
				break;
				case 1056;
				$msg = __( 'Invalid or missing lat_direction. Values must be N or S','wp-cloudflare-api' );
				break;
				case 1057;
				$msg = __( 'Invalid long_degrees, must be an integer between 0 and 180','wp-cloudflare-api' );
				break;
				case 1058;
				$msg = __( 'Invalid long_minutes, must be an integer between 0 and 59','wp-cloudflare-api' );
				break;
				case 1059;
				$msg = __( 'Invalid long_seconds, must be a floating point number between 0 and 60, including 0 but not including 60','wp-cloudflare-api' );
				break;
				case 1060;
				$msg = __( 'Invalid or missing long_direction. Values must be E or S','wp-cloudflare-api' );
				break;
				case 1061;
				$msg = __( 'Invalid altitude, must be a floating point number between -100000.00 and 42849672.95','wp-cloudflare-api' );
				break;
				case 1062;
				$msg = __( 'Invalid size, must be a floating point number between 0 and 90000000.00','wp-cloudflare-api' );
				break;
				case 1063;
				$msg = __( 'Invalid precision_horz, must be a floating point number between 0 and 90000000.00','wp-cloudflare-api' );
				break;
				case 1064;
				$msg = __( 'Invalid precision_vert, must be a floating point number between 0 and 90000000.00','wp-cloudflare-api' );
				break;
				case 1065;
				$msg = __( 'Invalid or missing data for <type> record','wp-cloudflare-api' );
				break;
				case 1067;
				$msg = __( 'Invalid content for a NS record','wp-cloudflare-api' );
				break;
				case 1068;
				$msg = __( 'Target cannot be an IP address','wp-cloudflare-api' );
				break;
				case 1069;
				$msg = __( 'CNAME content cannot reference itself','wp-cloudflare-api' );
				break;
				case 1070;
				$msg = __( 'CNAME content cannot be an IP','wp-cloudflare-api' );
				break;
				case 1071;
				$msg = __( 'Invalid proxied mode. Record cannot be proxied','wp-cloudflare-api' );
				break;
				case 1072;
				$msg = __( 'Invalid record identifier','wp-cloudflare-api' );
				break;
				case 1073;
				$msg = __( 'Invalid TXT record. Must be less than 255 characters','wp-cloudflare-api' );
				break;
				case 1074;
				$msg = __( 'Invalid TXT record. Record may only contain printable ASCII!','wp-cloudflare-api' );
				break;
				*/

				/* Zone Error Codes */

				/*
				case 1000;
				$msg = __( 'Invalid or missing user','wp-cloudflare-api' );
				break;
				case 1002;
				$msg = __( 'The \'name\' must be a valid domain','wp-cloudflare-api' );
				break;
				case 1003;
				$msg = __( 'The \'jump_start\' must be boolean','wp-cloudflare-api' );
				break;
				case 1004;
				$msg = __( 'Failed to assign name servers','wp-cloudflare-api' );
				break;
				case 1006;
				$msg = __( 'Invalid or missing zone','wp-cloudflare-api' );
				break;
				case 1008;
				$msg = __( 'Invalid or missing Zone id','wp-cloudflare-api' );
				break;
				case 1010;
				$msg = __( 'Invalid Zone','wp-cloudflare-api' );
				break;
				case 1011;
				$msg = __( 'Invalid or missing zone','wp-cloudflare-api' );
				break;
				case 1012;
				$msg = __( 'Request must contain one of \'purge_everything\' or \'files\'','wp-cloudflare-api' );
				break;
				case 1013;
				$msg = __( '\'purge_everything\' must be true','wp-cloudflare-api' );
				break;
				case 1014;
				$msg = __( '\'files\' must be an array of urls','wp-cloudflare-api' );
				break;
				case 1015;
				$msg = __( 'Unable to purge <url>','wp-cloudflare-api' );
				break;
				case 1016;
				$msg = __( 'Unable to purge any urls','wp-cloudflare-api' );
				break;
				case 1017;
				$msg = __( 'Unable to purge all','wp-cloudflare-api' );
				break;
				case 1018;
				$msg = __( 'Invalid zone status','wp-cloudflare-api' );
				break;
				case 1019;
				$msg = __( 'Zone is already paused','wp-cloudflare-api' );
				break;
				case 1020;
				$msg = __( 'Invalid or missing zone','wp-cloudflare-api' );
				break;
				case 1021;
				$msg = __( 'Invalid zone status','wp-cloudflare-api' );
				break;
				case 1022;
				$msg = __( 'Zone is already unpaused','wp-cloudflare-api' );
				break;
				case 1023;
				$msg = __( 'Invalid or missing zone','wp-cloudflare-api' );
				break;
				case 1024;
				$msg = __( '<domain> already exists','wp-cloudflare-api' );
				break;
				case 1049;
				$msg = __( '<domain> is not a registered domain','wp-cloudflare-api' );
				break;
				case 1050;
				$msg = __( '<domain> is currently being tasted. It is not currently a registered domain','wp-cloudflare-api' );
				break;
				case 1051;
				$msg = __( 'CloudFlare is already hosting <domain>','wp-cloudflare-api' );
				break;
				case 1052;
				$msg = __( 'An error has occurred and it has been logged. We will fix this problem promptly. We apologize for the inconvenience','wp-cloudflare-api' );
				break;
				case 1053;
				$msg = __( '<domain> is already disabled','wp-cloudflare-api' );
				break;
				case 1054;
				$msg = __( '<domain> is already enabled','wp-cloudflare-api' );
				break;
				case 1055;
				$msg = __( 'Failed to disable <domain>','wp-cloudflare-api' );
				break;
				case 1056;
				$msg = __( 'preserve_ini must be a boolean','wp-cloudflare-api' );
				break;
				case 1057;
				$msg = __( 'Zone must be in \'initializing\' status','wp-cloudflare-api' );
				break;
				case 1059;
				$msg = __( 'Unable to delete zone','wp-cloudflare-api' );
				break;
				case 1061;
				$msg = __( '<domain> already exists','wp-cloudflare-api' );
				break;
				case 1062;
				$msg = __( 'Not allowed to update zone status','wp-cloudflare-api' );
				break;
				case 1063;
				$msg = __( 'Not allowed to update zone step','wp-cloudflare-api' );
				break;
				case 1064;
				$msg = __( 'Not allowed to update zone step. Bad zone status','wp-cloudflare-api' );
				break;
				case 1065;
				$msg = __( 'Not allowed to update zone step. Zone has already been set up','wp-cloudflare-api' );
				break;
				case 1066;
				$msg = __( 'Could not promote zone to step 3','wp-cloudflare-api' );
				break;
				case 1067;
				$msg = __( 'Invalid organization identifier passed in your organization variable','wp-cloudflare-api' );
				break;
				case 1068;
				$msg = __( 'Permission denied','wp-cloudflare-api' );
				break;
				case 1069;
				$msg = __( 'organization variable should be an organization object','wp-cloudflare-api' );
				break;
				case 1070;
				$msg = __( 'This operation requires a Business or Enterprise account.','wp-cloudflare-api' );
				break;
				case 1071;
				$msg = __( 'Vanity name server array expected.','wp-cloudflare-api' );
				break;
				case 1072;
				$msg = __( 'Vanity name server array cannot be empty.','wp-cloudflare-api' );
				break;
				case 1073;
				$msg = __( 'A name server provided is in the wrong format.','wp-cloudflare-api' );
				break;
				case 1074;
				$msg = __( 'Could not find a valid zone.','wp-cloudflare-api' );
				break;
				case 1075;
				$msg = __( 'Vanity name server array count is invalid','wp-cloudflare-api' );
				break;
				case 1076;
				$msg = __( 'Name servers have invalid IP addresses','wp-cloudflare-api' );
				break;
				case 1077;
				$msg = __( 'Could not find a valid zone.','wp-cloudflare-api' );
				break;
				case 1078;
				$msg = __( 'This zone has no valid vanity IPs.','wp-cloudflare-api' );
				break;
				case 1079;
				$msg = __( 'This zone has no valid vanity name servers.','wp-cloudflare-api' );
				break;
				case 1080;
				$msg = __( 'There is a conflict with one of the name servers.','wp-cloudflare-api' );
				break;
				case 1081;
				$msg = __( 'There are no valid vanity name servers to disable.','wp-cloudflare-api' );
				break;
				case 1082;
				$msg = __( 'Unable to purge \'<url>\'. You can only purge files for this zone','wp-cloudflare-api' );
				break;
				case 1083;
				$msg = __( 'Unable to purge \'<url>\'. Rate limit reached. Please wait if you need to perform more operations','wp-cloudflare-api' );
				break;
				case 1084;
				$msg = __( 'Unable to purge \'<url>\'.','wp-cloudflare-api' );
				break;
				case 1085;
				$msg = __( 'Only one property can be updated at a time','wp-cloudflare-api' );
				break;
				case 1086;
				$msg = __( 'Invalid property','wp-cloudflare-api' );
				break;
				case 1087;
				$msg = __( 'Zone is in an invalid state','wp-cloudflare-api' );
				break;
				*/

				/* Custom Pages for a Zone Error Codes. */

				/*
				case 1000;
				$msg = __( 'Invalid user','wp-cloudflare-api' );
				break;
				case 1001;
				$msg = __( 'Invalid request. Could not connect to database','wp-cloudflare-api' );
				break;
				case 1002;
				$msg = __( 'Validator dispatcher expects an array','wp-cloudflare-api' );
				break;
				case 1004;
				$msg = __( 'Cannot find a valid zone','wp-cloudflare-api' );
				break;
				case 1006;
				$msg = __( 'Cannot find a valid customization page','wp-cloudflare-api' );
				break;
				case 1007;
				$msg = __( 'Invalid validation method being called','wp-cloudflare-api' );
				break;
				case 1200;
				$msg = __( 'A URL is required','wp-cloudflare-api' );
				break;
				case 1201;
				$msg = __( 'The URL provided seems to be irregular','wp-cloudflare-api' );
				break;
				case 1202;
				$msg = __( 'Unable to grab the content for the URL provided. Please try again.','wp-cloudflare-api' );
				break;
				case 1203;
				$msg = __( 'Your custom page must be larger than <characters> characters','wp-cloudflare-api' );
				break;
				case 1204;
				$msg = __( 'Your custom page must be smaller than <characters> characters','wp-cloudflare-api' );
				break;
				case 1205;
				$msg = __( 'A <token> token was not detected on the error page, and must be added before this page can be integrated into CloudFlare. The default error page will show until this is corrected and rescanned.','wp-cloudflare-api' );
				break;
				case 1206;
				$msg = __( 'Could not find a valid zone','wp-cloudflare-api' );
				break;
				case 1207;
				$msg = __( 'That customization page is not modifiable','wp-cloudflare-api' );
				break;
				case 1208;
				$msg = __( 'An unknown error has occurred and has been logged. We will fix this problem promptly. We apologize for the inconvenience.','wp-cloudflare-api' );
				break;
				case 1209;
				$msg = __( 'Could not find a valid customization page for this operation','wp-cloudflare-api' );
				break;
				case 1210;
				$msg = __( 'That operation is no longer allowed for that domain.','wp-cloudflare-api' );
				break;
				case 1211;
				$msg = __( 'Could not find a valid customization page to disable','wp-cloudflare-api' );
				break;
				case 1212;
				$msg = __( 'An undocumented error has occurred and has been logged.','wp-cloudflare-api' );
				break;
				case 1213;
				$msg = __( 'That operation has already been performed for this challenge/error.','wp-cloudflare-api' );
				break;
				case 1214;
				$msg = __( 'Rate limit reached for this operation. Please try again in a minute','wp-cloudflare-api' );
				break;
				case 1215;
				$msg = __( 'Rate limit reached for this operation. Please try again in a minute','wp-cloudflare-api' );
				break;
				case 1217;
				$msg = __( 'Invalid state passed','wp-cloudflare-api' );
				break;
				case 1218;
				$msg = __( 'Missing Custom Page state','wp-cloudflare-api' );
				break;
				case 1219;
				$msg = __( 'Please upgrade to access this feature','wp-cloudflare-api' );
				break;
				case 1220;
				$msg = __( 'We were unable to scan the page provided. Please ensure it is accessible publicly and is larger than 100 characters','wp-cloudflare-api' );
				break;
				*/
			}
			return $msg;
		}
	}
}
