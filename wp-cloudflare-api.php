<?php
/**
 * CloudFlare API (https://api.cloudflare.com/)
 *
 * @package wp-cloudflare-api
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'CloudFlareAPI' ) ) {

	/**
	 * CloudFlareAPI class.
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
		static private $auth_user_service_key;

		/**
		 * CloudFlare BaseAPI Endpoint
		 *
		 * @var string
		 * @access protected
		 */
		protected $base_uri = 'https://api.cloudflare.com/client/v4/';


		/**
		 * __construct function.
		 *
		 * @param [type]   $api_key               Cloudflare API Key.
		 * @param [type]   $auth_email            Email associated to the account.
		 * @param [string] $auth_user_service_key User Service key.
		 */
		public function __construct( $api_key, $auth_email, $auth_user_service_key = '' ) {

			static::$api_key = $api_key;
			static::$auth_email = $auth_email;
			static::$auth_user_service_key = $auth_user_service_key;

		}


		/**
		 * Fetch the request from the API.
		 *
		 * @access private
		 * @param mixed $request Request URL.
		 * @return $body Body.
		 */
		private function fetch( $request ) {

			$args = array(
				'headers' => array(
					'Content-Type' => 'application/json',
					'X-Auth-Email' => static::$auth_email,
					'X-Auth-Key' => static::$api_key,
				),
			);

			if ( isset( $request['body'] ) ) {
				$args['body'] = $request['body'];
			}

			if ( isset( $request['method'] ) ) {
				$args['method'] = $request['method'];
			}

			$response = wp_remote_request( $request['url'], $args );
			$code = wp_remote_retrieve_response_code( $response );

			if ( 200 !== $code ) {
				return new WP_Error( 'response-error', sprintf( __( 'Server response code: %d', 'text-domain' ), $code ) );
			}

			$body = wp_remote_retrieve_body( $response );

			return json_decode( $body );

		}


		/**
		 * Get User Properties (https://api.cloudflare.com/#user-properties).
		 *
		 * @accountaccess FREE, PRO, Business, Enterprise
		 * @access public
		 * @return [mixed]
		 */
		function get_user() {

			$request['url'] = $this->base_uri . 'user';

			return $this->fetch( $request );

		}


		/**
		 * Update User (https://api.cloudflare.com/#user-update-user).
		 *
		 * @accountaccess FREE, PRO, Business, Enterprise
		 * @access public
		 * @return void
		 */
		function update_user() {

		}


		/**
		 * Get User Billing Profile (https://api.cloudflare.com/#user-billing-profile-properties).
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_user_billing_profile() {

			$request['url'] = $this->base_uri . 'user/billing/profile';

			return $this->fetch( $request );

		}


		/**
		 * Get User Billing History (https://api.cloudflare.com/#user-billing-history-properties).
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_user_billing_history() {

			$request['url'] = $this->base_uri . 'user/billing/history';

			return $this->fetch( $request );

		}


		/**
		 * Function get_user_billing_subscriptions_apps.
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_user_billing_subscriptions_apps() {

			$request['url'] = $this->base_uri . 'user/billing/subscriptions/apps';

			return $this->fetch( $request );
		}


		/**
		 * Function get_subscriptions_zones.
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_subscriptions_zones() {

			$request['url'] = $this->base_uri . 'user/billing/subscriptions/zones';

			return $this->fetch( $request );
		}


		/**
		 * Function get_subscriptions_zones_billing.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_subscriptions_zones_billing( $zone_id ) {

			$request['url'] = $this->base_uri . 'user/billing/subscriptions/zones/' . $zone_id;

			return $this->fetch( $request );
		}


		/**
		 * Function get_user_firewall_access_rules.
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_user_firewall_access_rules() {

			$request['url'] = $this->base_uri . 'user/firewall/access_rules/rules';

			return $this->fetch( $request );
		}


		/**
		 * Function get_user_organizations.
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_user_organizations() {

			$request['url'] = $this->base_uri . 'user/organizations';

			return $this->fetch( $request );
		}


		/**
		 * Function get_user_invites.
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_user_invites() {

			$request['url'] = $this->base_uri . 'user/invites';

			return $this->fetch( $request );
		}


		/**
		 * Function get_user_invite.
		 *
		 * @access public
		 * @param mixed $invite_id Invite ID.
		 * @return [mixed]
		 */
		function get_user_invite( $invite_id ) {

			$request['url'] = $this->base_uri . 'user/invites/' . $invite_id;

			return $this->fetch( $request );
		}


		/**
		 * Function get_zones.
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_zones() {

			$request['url'] = $this->base_uri . 'zones';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_plans.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_plans( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/available_plans';

			return $this->fetch( $request );
		}

		/**
		 * Function get_zone_plans_details.
		 *
		 * @param  [type] $zone_id       Zone ID.
		 * @param  [type] $avail_plan_id Avail Plan ID.
		 * @return [mixed]
		 */
		function get_zone_plans_details( $zone_id, $avail_plan_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/available_plans/' . $avail_plan_id;

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_details.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_details( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id;

			return $this->fetch( $request );

		}


		/**
		 * Function get_zone_settings.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_settings( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings';

			return $this->fetch( $request );

		}


		/**
		 * Function get_zone_advanced_ddos.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_advanced_ddos( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/advanced_ddos';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_always_online.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_always_online( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/always_online';

			return $this->fetch( $request );

		}


		/**
		 * Function get_zone_browser_cache_ttl.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_browser_cache_ttl( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/browser_cache_ttl';

			return $this->fetch( $request );
		}

		/**
		 * Function get_zone_browser_check.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_browser_check( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/browser_check';

			return $this->fetch( $request );

		}

		/**
		 * Function get_zone_cache_level.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_cache_level( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/cache_level';

			return $this->fetch( $request );

		}


		/**
		 * Function get_zone_challenge_ttl.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_challenge_ttl( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/challenge_ttl';

			return $this->fetch( $request );

		}


		/**
		 * Function get_zone_development_mode.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_development_mode( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/development_mode';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_email_obfuscation.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_email_obfuscation( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/email_obfuscation';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_hotlink_protection.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_hotlink_protection( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/hotlink_protection';

			return $this->fetch( $request );

		}


		/**
		 * Function get_zone_ip_geolocation.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_ip_geolocation( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/ip_geolocation';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_ipv6.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_ipv6( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/ipv6';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_minify.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_minify( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/minify';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_mobile_redirect.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_mobile_redirect( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/mobile_redirect';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_mirage.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_mirage( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/mirage';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_origin_error_page_pass_thru.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_origin_error_page_pass_thru( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/origin_error_page_pass_thru';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_polish.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_polish( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/polish';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_prefetch_preload.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_prefetch_preload( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/prefetch_preload';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_response_buffering.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_response_buffering( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/response_buffering';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_rocket_loader.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_rocket_loader( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/rocket_loader';

			return $this->fetch( $request );
		}

		/**
		 * Function get_zone_security_header.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_security_header( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/security_header';

			return $this->fetch( $request );

		}


		/**
		 * Function get_zone_security_level.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_security_level( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/security_level';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_server_side_exclude.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_server_side_exclude( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/server_side_exclude';

			return $this->fetch( $request );

		}

		/**
		 * Function get_zone_sort_query_string_for_cache.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_sort_query_string_for_cache( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/sort_query_string_for_cache';

			return $this->fetch( $request );

		}

		/**
		 * Function get_zone_ssl.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_ssl( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/ssl';

			return $this->fetch( $request );
		}

		/**
		 * Function get_zone_tls_1_2_only.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_tls_1_2_only( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/tls_1_2_only';

			return $this->fetch( $request );
		}


		/**
		 * Get TLS Client Auth setting.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_tls_client_auth( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/tls_client_auth';

			return $this->fetch( $request );
		}


		/**
		 * Get True Client IP setting.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_true_client_ip_header( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/tls_client_auth';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_waf.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_waf( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/settings/waf';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_dns_records.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_dns_records( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/dns_records';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_dns_record_details.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @param mixed   $dns_record_id DNS Record ID.
		 * @return [mixed]
		 */
		function get_zone_dns_record_details( $zone_id, $dns_record_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/dns_records/' . $dns_record_id;

			return $this->fetch( $request );
		}

		/**
		 * Function get_zone_railguns.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_railguns( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/railguns';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_railgun_details.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @param [mixed] $railgun_id Railgun ID.
		 * @return [mixed]
		 */
		function get_zone_railgun_details( $zone_id, $railgun_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/railguns/' . $railgun_id;

			return $this->fetch( $request );
		}

		/**
		 * Function get_zone_railgun_connection.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @param [mixed] $railgun_id Railgun ID.
		 * @return [mixed]
		 */
		function get_zone_railgun_connection( $zone_id, $railgun_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/railguns/' . $railgun_id . '/diagnose';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_analytics_dashboard.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_analytics_dashboard( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/analytics/dashboard';

			return $this->fetch( $request );
		}


		/**
		 * Function get_zone_analytics_colos.
		 *
		 * @access public
		 * @param [mixed] $zone_id The zone ID.
		 * @return [mixed]
		 */
		function get_zone_analytics_colos( $zone_id ) {

			$request['url'] = $this->base_uri . 'zones/' . $zone_id . '/analytics/colos';

			return $this->fetch( $request );
		}

		/**
		 * Function get_cloudflare_ips.
		 *
		 * @access public
		 * @return [mixed]
		 */
		function get_cloudflare_ips() {

			$request['url'] = $this->base_uri . 'ips';

			return $this->fetch( $request );
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
					$msg = __( 'OK.', 'text-domain' );
				break;
				case 400:
					$msg = __( 'Bad Request: Required parameter missing or invalid.', 'text-domain' );
				break;
				case 401:
					$msg = __( 'Unauthorized: User does not have permission.', 'text-domain' );
				break;
				case 403:
					$msg = __( 'Forbidden: Request not authenticated.', 'text-domain' );
				break;
				case 405:
					$msg = __( 'Method Not Allowed: Incorrect HTTP method provided.', 'text-domain' );
				break;
				case 415:
					$msg = __( 'Unsupported Media Type: Response is not valid JSON.', 'text-domain' );
				break;
				case 429:
					$msg = __( 'Too many requests: Client is rate limited.', 'text-domain' );
				break;
				/* CloudFlare CA error codes. */
				case 1000;
					$msg = __( 'API errors encountered.', 'text-domain' );
				break;
				case 1001;
					$msg = __( 'Request had no Authorization header.', 'text-domain' );
				break;
				case 1002;
					$msg = __( 'Unsupported request_type.', 'text-domain' );
				break;
				case 1003;
					$msg = __( 'Failed to read contents of HTTP request.', 'text-domain' );
				break;
				case 1004;
					$msg = __( 'Failed to parse request JSON.', 'text-domain' );
				break;
				case 1005;
					$msg = __( 'Too many hostnames requested - you may only request up to 100 per certificate.', 'text-domain' );
				break;
				case 1006;
					$msg = __( 'One or more hostnames were duplicated in the request and have been removed prior to certificate generation.', 'text-domain' );
				break;
				case 1007;
					$msg = __( 'CSR parsed as empty.', 'text-domain' );
				break;
				case 1008;
					$msg = __( 'Error creating request to CA.', 'text-domain' );
				break;
				case 1009;
					$msg = __( 'Permitted values for the *requested_validity* parameter (specified in days) are: 7, 30, 90, 365, 730, 1095, and 5475 (default).', 'text-domain' );
				break;
				case 1010;
					$msg = __( 'Failed to validate SAN <hostname>: <reason for failure>.', 'text-domain' );
				break;
				case 1011;
					$msg = __( 'Failed to parse CSR.', 'text-domain' );
				break;
				case 1012;
					$msg = __( 'Please provide a zone id when requesting a stored certificate, or fetch by serial number.', 'text-domain' );
				break;
				case 1013;
					$msg = __( 'Please provide a certificate serial number when operating on a single certificate.', 'text-domain' );
				break;
				case 1014;
					$msg = __( 'Certificate already revoked.', 'text-domain' );
				break;
				case 1100;
					$msg = __( 'Failed to write certificate to database.', 'text-domain' );
				break;
				case 1101;
					$msg = __( 'Failed to read certificate from database.', 'text-domain' );
				break;
				case 1200;
					$msg = __( 'API Error: Failed to generate CA request.', 'text-domain' );
				break;
				case 1201;
					$msg = __( 'CA signing failure. Could not parse returned certificate.', 'text-domain' );
				break;
				case 1300;
					$msg = __( 'Failed to fetch keyless servers from API.', 'text-domain' );
				break;
				case 1301;
					$msg = __( 'The key server did not activate correctly.', 'text-domain' );
				break;
				case 1302;
					$msg = __( 'Could not get keyless server port for server <server>.', 'text-domain' );
				break;
				case 1303;
					$msg = __( 'Invalid hostname: <hostname>.', 'text-domain' );
				break;
				default:
					$msg = __( 'Response code unknown.', 'text-domain' );
				break;

				/* Zone Plan Error Codes. */

				/*
				case 1004;
				$msg = __( 'Cannot find a valid zone.','text-domain' );
				break;
				case 1005;
				$msg = __( 'Cannot find a valid plan.','text-domain' );
				break;
				case 1006;
				$msg = __( 'Cannot find a valid reseller plan.','text-domain' );
				break;
				case 1007;
				$msg = __( 'Cannot find a valid zone.','text-domain' );
				break;
				*/

				/* DNS Records for a Zone Error Codes. */

				case 1000;
					$msg = __( 'Invalid user.', 'text-domain' );
				break;
				case 1002;
					$msg = __( 'Invalid or missing zone_id.', 'text-domain' );
				break;
				case 1003;
					$msg = __( 'The per_page must be a positive integer.', 'text-domain' );
				break;
				case 1004;
					$msg = __( 'Invalid or missing zone.', 'text-domain' );
				break;
				case 1005;
					$msg = __( 'Invalid or missing record.', 'text-domain' );
				break;
				case 1007;
					$msg = __( 'Name required.', 'text-domain' );
				break;
				case 1008;
					$msg = __( 'Content required.', 'text-domain' );
				break;
				case 1009;
					$msg = __( 'Invalid or missing record id.', 'text-domain' );
				break;
				case 1010;
					$msg = __( 'Invalid or missing record.', 'text-domain' );
				break;
				case 1011;
					$msg = __( 'Zone file for \'<domain name>\' could not be found.', 'text-domain' );
				break;
				case 1012;
					$msg = __( 'Zone file for \'<domain name>\' is not modifiable.', 'text-domain' );
				break;
				case 1013;
					$msg = __( 'The record could not be found.', 'text-domain' );
				break;
				case 1014;
					$msg = __( 'You do not have permission to modify this zone.', 'text-domain' );
				break;

				/*
				case 1015;
				$msg = __( 'Unknown error','text-domain' );
				break;
				case 1017;
				$msg = __( 'Content for A record is invalid. Must be a valid IPv4 address','text-domain' );
				break;
				case 1018;
				$msg = __( 'Content for AAAA record is invalid. Must be a valid IPv6 address','text-domain' );
				break;
				case 1019;
				$msg = __( 'Content for CNAME record is invalid','text-domain' );
				break;
				case 1024;
				$msg = __( 'Invalid priority, priority must be set and be between 0 and 65535','text-domain' );
				break;
				case 1025;
				$msg = __( 'Invalid content for an MX record','text-domain' );
				break;
				case 1026;
				$msg = __( 'Invalid format for a SPF record. A valid example is \'v=spf1 a mx -all\'. You should not include either the word TXT or the domain name here in the content','text-domain' );
				break;
				case 1027;
				$msg = __( 'Invalid service value','text-domain' );
				break;
				case 1028;
				$msg = __( 'Invalid service value. Must be less than 100 characters','text-domain' );
				break;
				case 1029;
				$msg = __( 'Invalid protocol value','text-domain' );
				break;
				case 1030;
				$msg = __( 'Invalid protocol value. Must be less than 12 characters','text-domain' );
				break;
				case 1031;
				$msg = __( 'Invalid SRV name','text-domain' );
				break;
				case 1032;
				$msg = __( 'Invalid SRV name. Must be less than 90 characters','text-domain' );
				break;
				case 1033;
				$msg = __( 'Invalid weight value. Must be between 0 and 65,535','text-domain' );
				break;
				case 1034;
				$msg = __( 'Invalid port value. Must be between 0 and 65,535','text-domain' );
				break;
				case 1037;
				$msg = __( 'Invalid domain name for a SRV target host','text-domain' );
				break;
				case 1038;
				$msg = __( 'Invalid DNS record type','text-domain' );
				break;
				case 1039;
				$msg = __( 'Invalid TTL. Must be between 120 and 4,294,967,295 seconds, or 1 for automatic','text-domain' );
				break;
				case 1041;
				$msg = __( 'Priority must be set for SRV record','text-domain' );
				break;
				case 1042;
				$msg = __( 'Zone file for \'<domain name>\' could not be found','text-domain' );
				break;
				case 1043;
				$msg = __( 'Zone file for \'<domain name>\' is not editable','text-domain' );
				break;
				case 1044;
				$msg = __( 'A record with these exact values already exists. Please modify or remove this record','text-domain' );
				break;
				case 1045;
				$msg = __( 'The record could not be found','text-domain' );
				break;
				case 1046;
				$msg = __( 'A record with these exact values already exists. Please modify or cancel this edit','text-domain' );
				break;
				case 1047;
				$msg = __( 'You do not have permission to modify this zone','text-domain' );
				break;
				case 1048;
				$msg = __( 'You have reached the record limit for this zone','text-domain' );
				break;
				case 1049;
				$msg = __( 'The record content is missing','text-domain' );
				break;
				case 1050;
				$msg = __( 'Could not find record','text-domain' );
				break;
				case 1052;
				$msg = __( 'You can not point a CNAME to itself','text-domain' );
				break;
				case 1053;
				$msg = __( 'Invalid lat_degrees, must be an integer between 0 and 90 inclusive','text-domain' );
				break;
				case 1054;
				$msg = __( 'Invalid lat_minutes, must be an integer between 0 and 59 inclusive','text-domain' );
				break;
				case 1055;
				$msg = __( 'Invalid lat_seconds, must be a floating point number between 0 and 60, including 0 but not including 60','text-domain' );
				break;
				case 1056;
				$msg = __( 'Invalid or missing lat_direction. Values must be N or S','text-domain' );
				break;
				case 1057;
				$msg = __( 'Invalid long_degrees, must be an integer between 0 and 180','text-domain' );
				break;
				case 1058;
				$msg = __( 'Invalid long_minutes, must be an integer between 0 and 59','text-domain' );
				break;
				case 1059;
				$msg = __( 'Invalid long_seconds, must be a floating point number between 0 and 60, including 0 but not including 60','text-domain' );
				break;
				case 1060;
				$msg = __( 'Invalid or missing long_direction. Values must be E or S','text-domain' );
				break;
				case 1061;
				$msg = __( 'Invalid altitude, must be a floating point number between -100000.00 and 42849672.95','text-domain' );
				break;
				case 1062;
				$msg = __( 'Invalid size, must be a floating point number between 0 and 90000000.00','text-domain' );
				break;
				case 1063;
				$msg = __( 'Invalid precision_horz, must be a floating point number between 0 and 90000000.00','text-domain' );
				break;
				case 1064;
				$msg = __( 'Invalid precision_vert, must be a floating point number between 0 and 90000000.00','text-domain' );
				break;
				case 1065;
				$msg = __( 'Invalid or missing data for <type> record','text-domain' );
				break;
				case 1067;
				$msg = __( 'Invalid content for a NS record','text-domain' );
				break;
				case 1068;
				$msg = __( 'Target cannot be an IP address','text-domain' );
				break;
				case 1069;
				$msg = __( 'CNAME content cannot reference itself','text-domain' );
				break;
				case 1070;
				$msg = __( 'CNAME content cannot be an IP','text-domain' );
				break;
				case 1071;
				$msg = __( 'Invalid proxied mode. Record cannot be proxied','text-domain' );
				break;
				case 1072;
				$msg = __( 'Invalid record identifier','text-domain' );
				break;
				case 1073;
				$msg = __( 'Invalid TXT record. Must be less than 255 characters','text-domain' );
				break;
				case 1074;
				$msg = __( 'Invalid TXT record. Record may only contain printable ASCII!','text-domain' );
				break;
				*/

				/* Zone Error Codes */

				/*
				case 1000;
				$msg = __( 'Invalid or missing user','text-domain' );
				break;
				case 1002;
				$msg = __( 'The \'name\' must be a valid domain','text-domain' );
				break;
				case 1003;
				$msg = __( 'The \'jump_start\' must be boolean','text-domain' );
				break;
				case 1004;
				$msg = __( 'Failed to assign name servers','text-domain' );
				break;
				case 1006;
				$msg = __( 'Invalid or missing zone','text-domain' );
				break;
				case 1008;
				$msg = __( 'Invalid or missing Zone id','text-domain' );
				break;
				case 1010;
				$msg = __( 'Invalid Zone','text-domain' );
				break;
				case 1011;
				$msg = __( 'Invalid or missing zone','text-domain' );
				break;
				case 1012;
				$msg = __( 'Request must contain one of \'purge_everything\' or \'files\'','text-domain' );
				break;
				case 1013;
				$msg = __( '\'purge_everything\' must be true','text-domain' );
				break;
				case 1014;
				$msg = __( '\'files\' must be an array of urls','text-domain' );
				break;
				case 1015;
				$msg = __( 'Unable to purge <url>','text-domain' );
				break;
				case 1016;
				$msg = __( 'Unable to purge any urls','text-domain' );
				break;
				case 1017;
				$msg = __( 'Unable to purge all','text-domain' );
				break;
				case 1018;
				$msg = __( 'Invalid zone status','text-domain' );
				break;
				case 1019;
				$msg = __( 'Zone is already paused','text-domain' );
				break;
				case 1020;
				$msg = __( 'Invalid or missing zone','text-domain' );
				break;
				case 1021;
				$msg = __( 'Invalid zone status','text-domain' );
				break;
				case 1022;
				$msg = __( 'Zone is already unpaused','text-domain' );
				break;
				case 1023;
				$msg = __( 'Invalid or missing zone','text-domain' );
				break;
				case 1024;
				$msg = __( '<domain> already exists','text-domain' );
				break;
				case 1049;
				$msg = __( '<domain> is not a registered domain','text-domain' );
				break;
				case 1050;
				$msg = __( '<domain> is currently being tasted. It is not currently a registered domain','text-domain' );
				break;
				case 1051;
				$msg = __( 'CloudFlare is already hosting <domain>','text-domain' );
				break;
				case 1052;
				$msg = __( 'An error has occurred and it has been logged. We will fix this problem promptly. We apologize for the inconvenience','text-domain' );
				break;
				case 1053;
				$msg = __( '<domain> is already disabled','text-domain' );
				break;
				case 1054;
				$msg = __( '<domain> is already enabled','text-domain' );
				break;
				case 1055;
				$msg = __( 'Failed to disable <domain>','text-domain' );
				break;
				case 1056;
				$msg = __( 'preserve_ini must be a boolean','text-domain' );
				break;
				case 1057;
				$msg = __( 'Zone must be in \'initializing\' status','text-domain' );
				break;
				case 1059;
				$msg = __( 'Unable to delete zone','text-domain' );
				break;
				case 1061;
				$msg = __( '<domain> already exists','text-domain' );
				break;
				case 1062;
				$msg = __( 'Not allowed to update zone status','text-domain' );
				break;
				case 1063;
				$msg = __( 'Not allowed to update zone step','text-domain' );
				break;
				case 1064;
				$msg = __( 'Not allowed to update zone step. Bad zone status','text-domain' );
				break;
				case 1065;
				$msg = __( 'Not allowed to update zone step. Zone has already been set up','text-domain' );
				break;
				case 1066;
				$msg = __( 'Could not promote zone to step 3','text-domain' );
				break;
				case 1067;
				$msg = __( 'Invalid organization identifier passed in your organization variable','text-domain' );
				break;
				case 1068;
				$msg = __( 'Permission denied','text-domain' );
				break;
				case 1069;
				$msg = __( 'organization variable should be an organization object','text-domain' );
				break;
				case 1070;
				$msg = __( 'This operation requires a Business or Enterprise account.','text-domain' );
				break;
				case 1071;
				$msg = __( 'Vanity name server array expected.','text-domain' );
				break;
				case 1072;
				$msg = __( 'Vanity name server array cannot be empty.','text-domain' );
				break;
				case 1073;
				$msg = __( 'A name server provided is in the wrong format.','text-domain' );
				break;
				case 1074;
				$msg = __( 'Could not find a valid zone.','text-domain' );
				break;
				case 1075;
				$msg = __( 'Vanity name server array count is invalid','text-domain' );
				break;
				case 1076;
				$msg = __( 'Name servers have invalid IP addresses','text-domain' );
				break;
				case 1077;
				$msg = __( 'Could not find a valid zone.','text-domain' );
				break;
				case 1078;
				$msg = __( 'This zone has no valid vanity IPs.','text-domain' );
				break;
				case 1079;
				$msg = __( 'This zone has no valid vanity name servers.','text-domain' );
				break;
				case 1080;
				$msg = __( 'There is a conflict with one of the name servers.','text-domain' );
				break;
				case 1081;
				$msg = __( 'There are no valid vanity name servers to disable.','text-domain' );
				break;
				case 1082;
				$msg = __( 'Unable to purge \'<url>\'. You can only purge files for this zone','text-domain' );
				break;
				case 1083;
				$msg = __( 'Unable to purge \'<url>\'. Rate limit reached. Please wait if you need to perform more operations','text-domain' );
				break;
				case 1084;
				$msg = __( 'Unable to purge \'<url>\'.','text-domain' );
				break;
				case 1085;
				$msg = __( 'Only one property can be updated at a time','text-domain' );
				break;
				case 1086;
				$msg = __( 'Invalid property','text-domain' );
				break;
				case 1087;
				$msg = __( 'Zone is in an invalid state','text-domain' );
				break;
				*/

				/* Custom Pages for a Zone Error Codes. */

				/*
				case 1000;
				$msg = __( 'Invalid user','text-domain' );
				break;
				case 1001;
				$msg = __( 'Invalid request. Could not connect to database','text-domain' );
				break;
				case 1002;
				$msg = __( 'Validator dispatcher expects an array','text-domain' );
				break;
				case 1004;
				$msg = __( 'Cannot find a valid zone','text-domain' );
				break;
				case 1006;
				$msg = __( 'Cannot find a valid customization page','text-domain' );
				break;
				case 1007;
				$msg = __( 'Invalid validation method being called','text-domain' );
				break;
				case 1200;
				$msg = __( 'A URL is required','text-domain' );
				break;
				case 1201;
				$msg = __( 'The URL provided seems to be irregular','text-domain' );
				break;
				case 1202;
				$msg = __( 'Unable to grab the content for the URL provided. Please try again.','text-domain' );
				break;
				case 1203;
				$msg = __( 'Your custom page must be larger than <characters> characters','text-domain' );
				break;
				case 1204;
				$msg = __( 'Your custom page must be smaller than <characters> characters','text-domain' );
				break;
				case 1205;
				$msg = __( 'A <token> token was not detected on the error page, and must be added before this page can be integrated into CloudFlare. The default error page will show until this is corrected and rescanned.','text-domain' );
				break;
				case 1206;
				$msg = __( 'Could not find a valid zone','text-domain' );
				break;
				case 1207;
				$msg = __( 'That customization page is not modifiable','text-domain' );
				break;
				case 1208;
				$msg = __( 'An unknown error has occurred and has been logged. We will fix this problem promptly. We apologize for the inconvenience.','text-domain' );
				break;
				case 1209;
				$msg = __( 'Could not find a valid customization page for this operation','text-domain' );
				break;
				case 1210;
				$msg = __( 'That operation is no longer allowed for that domain.','text-domain' );
				break;
				case 1211;
				$msg = __( 'Could not find a valid customization page to disable','text-domain' );
				break;
				case 1212;
				$msg = __( 'An undocumented error has occurred and has been logged.','text-domain' );
				break;
				case 1213;
				$msg = __( 'That operation has already been performed for this challenge/error.','text-domain' );
				break;
				case 1214;
				$msg = __( 'Rate limit reached for this operation. Please try again in a minute','text-domain' );
				break;
				case 1215;
				$msg = __( 'Rate limit reached for this operation. Please try again in a minute','text-domain' );
				break;
				case 1217;
				$msg = __( 'Invalid state passed','text-domain' );
				break;
				case 1218;
				$msg = __( 'Missing Custom Page state','text-domain' );
				break;
				case 1219;
				$msg = __( 'Please upgrade to access this feature','text-domain' );
				break;
				case 1220;
				$msg = __( 'We were unable to scan the page provided. Please ensure it is accessible publicly and is larger than 100 characters','text-domain' );
				break;
				*/
			}
			return $msg;
		}
	}
}
