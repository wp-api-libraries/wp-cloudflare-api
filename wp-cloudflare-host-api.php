<?php
/**
 * Library for accessing the CloudFlare Host API on WordPress
 *
 * @link https://www.cloudflare.com/docs/host-api/#s3.2.1/ API Documentation
 * @link https://partners.cloudflare.com Parners Control Panel Login
 * @package WP-API-Libraries\WP-IDX-Cloudflare-API
 */


/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'CloudFlareHostAPI' ) ) {

	/**
	 * A WordPress API library for accessing the Cloudflare Host API.
	 *
	 * @version 1.1.0
	 * @link https://www.cloudflare.com/docs/host-api/#s3.2.1/ API Documentation
	 * @package WP-API-Libraries\WP-IDX-Cloudflare-API
	 * @author Santiago Garza <https://github.com/sfgarza>
	 * @author imFORZA <https://github.com/imforza>
	 */
	class CloudFlareHostAPI {
		
		/**
		 * API Key.
		 *
		 * @var string
		 */
		static protected $host_api_key;

		
		/**
		 * CloudFlare Host Base API Endpoint
		 *
		 * @var string
		 * @access protected
		 */
		protected $base_uri = 'https://api.cloudflare.com/host-gw.html';
		
		
		/**
		 * Route being called.
		 *
		 * @var string
		 */
		protected $route = '';
		
		/**
		 * Class constructor.
		 *
		 * @param string $host_api_key          Cloudflare Host API Key.
		 * @param string $auth_email            Email associated to the account.
		 * @param string $user_service_key      User Service key.
		 */
		public function __construct( $host_api_key, $auth_email, $user_service_key = '' ) {
			static::$api_key = $api_key;
			static::$auth_email = $auth_email;
			static::$user_service_key = $user_service_key;
		}

		
		
		/**
		 * Create a Cloudflare account mapped to your user (required).
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.1
		 * @param mixed $cloudflare_email
		 * @param mixed $cloudflare_pass
		 * @param string $cloudflare_username (default: '')
		 * @param string $unique_id (default: '')
		 * @param string $clobber_unique_id (default: '')
		 * @return void
		 */
		public function create_user( $cloudflare_email, $cloudflare_pass, $cloudflare_username = '', $unique_id = '', $clobber_unique_id = '' ) {
			
		}
		
		/**
		 * zone_set function.
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.2
		 * @param mixed $user_key
		 * @param mixed $zone_name
		 * @param mixed $resolve_to
		 * @param mixed $subdomains
		 * @return void
		 */
		public function zone_set( $user_key, $zone_name, $resolve_to, $subdomains ) {
			
		}
		
		/**
		 * full_zone_set function.
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.3
		 * @param mixed $user_key
		 * @param mixed $zone_name
		 * @return void
		 */
		public function full_zone_set( $user_key, $zone_name ) {
			
		}
		
		/**
		 * user_lookup function.
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.4
		 * @param mixed $cloudflare_email
		 * @return void
		 */
		public function user_lookup( $cloudflare_email ) {
			
		}
		
		/**
		 * user_auth function.
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.5
		 * @param mixed $cloudflare_email
		 * @param mixed $cloudflare_pass
		 * @param mixed $unique_id
		 * @param mixed $clobber_unique_id
		 * @return void
		 */
		public function user_auth( $cloudflare_email, $cloudflare_pass, $unique_id, $clobber_unique_id ) {
			
		}
		
		/**
		 * zone_lookup function.
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.6
		 * @param mixed $zone_name
		 * @return void
		 */
		public function zone_lookup( $zone_name ) {
			
		}
		
		/**
		 * zone_delete function.
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.7
		 * @param mixed $zone_name
		 * @return void
		 */
		public function zone_delete( $zone_name ) {
			
		}
		
		/**
		 * host_key_regen function.
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.9
		 * @return void
		 */
		public function host_key_regen() {
			
		}
		
		/**
		 * List the domains currently active on Cloudflare for the given host (optional).
		 * 
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.10
		 * @param mixed $limit Limit.
		 * @param mixed $offset Offset.
		 * @param mixed $zone_name Zone Name.
		 * @param mixed $sub_id Sub ID.
		 * @param mixed $zone_status Zone Status. - The zone_status parameter has valid values of V,D,ALL, where V shows active zones only, D deleted, and ALL all. 
		 * @param mixed $sub_status Sub Status - The sub_status parameter has valid values of V,CNL,ALL, where V shows zones with an active subscription only, CNL canceled, and ALL all.
		 * @return void
		 */
		public function zone_list( $limit, $offset, $zone_name, $sub_id, $zone_status, $sub_status ) {
			
		}
		
		/**
		 * HTTP response code messages.
		 *
		 * @param  [String] $code : Response code to get message from.
		 * @return [String]       : Message corresponding to response code sent in.
		 */
		public function response_code_msg( $code = '' ) {
			switch ( $code ) {
				case 100:
					$msg = __( 'No or invalid host_key.', 'wp-cloudflare-api' );
				break;
				case 101:
					$msg = __( 'No or invalid act.', 'wp-cloudflare-api' );
				break;
				case 103:
					$msg = __( 'Please provide a Cloudflare e-mail address.', 'wp-cloudflare-api' );
				break;
				case 104:
					$msg = __( 'Invalid unique_id. Allowed character set is \'-_a-z0-9#@+.,\'.', 'wp-cloudflare-api' );
				break;
				case 105:
					$msg = __( 'Invalid unique_id. Max size exceeed.', 'wp-cloudflare-api' );
				break;
				case 106:
					$msg = __( 'Invalid clobber_unique_id. Must be 0 or 1.', 'wp-cloudflare-api' );
				break;
				case 107:
					$msg = __( 'That action requires either cloudflare_email or unique_id to be defined.', 'wp-cloudflare-api' );
				break;
			}
			

		}

		
	}
	
}
	
	