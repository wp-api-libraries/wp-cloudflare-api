<?php
/**
 * Library for accessing the CloudFlare Railgun API on WordPress as a partner
 *
 * Let it be noted, that there is also an API for client access, rather than partners:
 * https://www.cloudflare.com/docs/railgun/api/client_api.html
 *
 * @link https://www.cloudflare.com/docs/railgun/api/partner_api.html API Documentation
 * @link https://partners.cloudflare.com Parners Control Panel Login
 * @package WP-API-Libraries\WP-IDX-Cloudflare-API
 */


/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

if ( ! class_exists( 'CloudFlareRailgunAPI' ) ) {

	/**
	 * A WordPress API library for accessing the Cloudflare Railgun API as a partner.
	 *
	 * @version 1.1.0
	 * @link https://www.cloudflare.com/docs/railgun/api/partner_api.html API Documentation
	 * @package WP-API-Libraries\WP-IDX-Cloudflare-API
	 * @author Bradley Moore <https://github.com/bradleymoore111>
	 * @author imFORZA <https://github.com/imforza>
	 */
	class CloudFlareRailgunAPI {

		/**
		 * Build request function: prepares the class for a fetch request.
		 *
		 * @param  string $route  URL to be accessed.
		 * @param  array  $args   Arguments to pass in. If the method is GET, will be
		 *                        passed as query arguments attached to the route. If
		 *                        the method is not get, but the content type as
		 *                        defined in headers is 'application/json', then the
		 *                        body of the request will be set to a json_encode of
		 *                        $args. Otherwise, they will be passed as the body.
		 * @param  string $method (Default: 'GET') The method.
		 * @return object         The return of theThe response.
		 */
		private function build_request( $route, $body = array(), $method = 'GET' ) {
			$this->set_headers();
			// Sets method.
			$this->args['method'] = $method;
			// Sets route.
			$this->route = $route;

      // Merge bodies.
      if( isset( $this->args['body'] ) ){
        $body = array_merge( $this->args['body'], $body );
      }
			// If method is get, then there is no body.
			if ( 'GET' === $method ) {
				$this->route = add_query_arg( array_filter( $body ), $route );
			} // Otherwise, if the content type is application/json, then the body needs to be json_encoded
			elseif ( isset( $this->args['headers']['Content-Type'] ) && 'application/json' === $this->args['headers']['Content-Type'] ) {
				$this->args['body'] = wp_json_encode( $body );
			} // Anything else, let the user take care of it. TODO: add support for other content-types.
			else {
				$this->args['body'] = $body;
			}
			return $this;
		}

		/**
		 * Execute the prepared call.
		 *
		 * @return object The response.
		 */
		private function fetch() {

			$response = wp_remote_request( $this->base_uri . $this->route, $this->args );

			// Retrieve status code and body.
			$code = wp_remote_retrieve_response_code( $response );
			$body = json_decode( wp_remote_retrieve_body( $response ) );

			// Clear last request.
			$this->clear();

			if ( ! $this->is_status_ok( $code ) && ! $this->is_debug ) {
				return new WP_Error( 'response-error', sprintf( __( 'Status: &d', 'wp-postmark-api' ), $code ), $body );
			}
			return $body;
		}

		/**
		 * Returns whether status is in [ 200, 300 ).
		 *
		 * @return bool ^.
		 */
		private function is_status_ok( $code ) {
			return ( 200 <= $code && 300 > $code );
		}

		/**
		 * API Key.
		 *
		 * @var string
		 * @access private
		 */
		private $api_key;

		/**
		 * The total arguments for the call.
		 *
		 * @var array
		 * @access private
		 */
		private $args;

		/**
		 * Whether in development mode or not.
		 *
		 * @var bool
		 * @access private
		 */
		private $is_debug;


		/**
		 * CloudFlare Host Base API Endpoint
		 *
		 * @var string
		 * @access private
		 */
		private $base_uri = 'https://api.cloudflare.com/api/v2/railgun/';

		/**
		 * Class constructor.
		 *
		 * @param string $host_api_key  Cloudflare Host API Key.
		 * @param bool   $debug         Email associated to the account.
		 * @return CloudflareRailgunAPI $this.
		 */
		public function __construct( $host_api_key, $debug = false ) {
			$this->api_key = $host_api_key;
			$this->debug = false;
		}

		/**
		 * Set headers for the outgoing call.
		 *
		 * @return void
		 */
		private function set_headers(){
			$this->args['headers'] = array(
				'Accept'       => '*/*',
				'Content-Type' => 'application/x-www-form-urlencoded'
			);
		}

		/**
		 * Function to be overwritten, gets called after the request has been made (if status code was ok). Should be used to reset headers.
		 *
		 * @access private
		 * @return void
		 */
		private function clear(){
			$this->args = array();
		}

		/**
		 * Wrapper for build_request()->fetch().
		 *
		 * Also adds host_key, so kinda helpful.
		 *
		 * @param  string $route  The route to execute calls on.
		 * @param  array  $args   Additional arguments to pass.
		 * @param  string $method (Default: 'POST') The method.
		 * @return object         The response.
		 */
		private function run( $route, $args = array(), $method = 'POST' ){
			$args['host_key'] = $this->api_key;
			return $this->build_request( $route, $args, $method )->fetch();
		}

		/**
		 * Go through an associative array of key => vals, and only keep that which
		 * do not have a null value.
		 *
		 * @param  array  $args  The arguments to pass.
		 * @param  array  $merge (Default: array()) Additional arguments to merge with.
		 * @return array         The merged/parsed arguments.
		 */
		private function parse_args( $args, $merge = array() ){
	    $results = array();

	    foreach( $args as $key => $val ){
	      if( $val !== null ){
	        $results[$key] = $val;
	      }else if( is_array( $val ) && ! empty( $val ) ){
	        $results[$key] = $val;
	      }
	    }

	    return array_merge( $merge, $results );
	  }

		/**
		 * Create a Railgun. If request is successful, a new Railgun is added to a user
		 * account and placed in initializing status (INI).
		 *
		 * @param  string $name    (Default: null) Name of Railgun.
		 * @param  string $pubname (Default: null) Name of Railgun shown to users.
		 * @return object          The return object.
		 */
		public function init_railgun( $name = null, $pubname = null ){
			$args = $this->parse_args(array(
				'name' => $name,
				'pubname' => $pubname
			));

			return $this->run( 'init', $args );
		}

		/**
		 * Delete a Railgun. If request is successful, the Railgun with a token matching
		 * rtkn is removed from the account and set to deleted status (D).
		 *
		 * @param  string $token The railgun token.
		 * @return object        The response.
		 */
		public function delete_railgun( $token ){
			return $this->run( 'delete', array( 'rtkn' => $token ) );
		}

		/**
		 * The following API calls can be used to determine details and the status or
		 * one or more Railguns assigned to an account. These calls are sometimes
		 * needed to determine the unique rtkn or id assigned to a Railgun.
		 *
		 * @return object The response.
		 */
		public function list_railguns(){
			return $this->run( 'host_get_all', array(), 'GET' );
		}

		/**
		 * List all active Railgun connections for a domain.
		 *
		 * @param  string $domain The domain to check for railguns under.
		 * @return object         The response.
		 */
		public function list_railguns_by_domain( $domain ){
			return $this->run( 'zone_conn_get_active', array( 'z' => $domain ), 'GET' );
		}

		/**
		 * After a Railgun has been activated, it can be exposed to a particular domain
		 * with the suggestion_set API call. suggestion_set also accepts the auto_enabled
		 * parameter to assign and enable Railgun for the domain globally in a single API
		 * call. If auto_enabled is not set to 1, then the connection needed to enable
		 * Railgun for the domain must be made manually using the conn_set method.
		 * conn_setmode_enabled and conn_setmode_disabled can be used to toggle Railgun
		 * on or off for the domain globally. zone_conn_get_active can be used to
		 * view active Railgun connections.
		 */

		/**
		 * Expose a verified Railgun to a domain via the CloudFlare Settings user-interface.
		 * This method allows an end-user to select and enable the specified Railgun within
		 * the CloudFlare Settings user-interface. If auto_enabled is set to  0, it is also
		 * necessary to perform a conn_set for the Railgun in order to setup a connection
		 * with the domain.
		 *
		 * @param string $domain       The domain name.
		 * @param string $token        The Railgun token.
		 * @param bool   $auto_enabled (Default: null) Railgun operation mode.
		 *                             1 for active, 0 for inactive.
		 * @return object              The response.
		 */
		public function suggestion_set( $domain, $token, $auto_enabled = null ){
			$args = array(
				'z'    => $domain,
				'rtkn' => $token
			);

			if( null !== $auto_enabled ){
				$args['auto_enabled'] = abs( intval( $auto_enabled ) ) % 2; // Little bit of data validation...
			}

			return $this->run( 'suggestion_set', $args );
		}

		/**
		 * Establish a connection between a domain and a Railgun without requiring the
		 * domain’s user to utilize the CloudFlare Settings user-interface to change
		 * or deactivate it. The mode parameter can be set to 1 in order to enable
		 * the Railgun globally if conn_set succeeds.
		 *
		 * @param string $domain The domain name.
		 * @param string $token  The railgun token.
		 * @param bool   $mode   ailgun operation mode, 1 for active 0 for inactive.
		 * @return object The response       .
		 */
		public function connection_set( $domain, $token, $mode ){
			$args = array(
				'z'    => $domain,
				'rtkn' => $token,
				'mode' => abs( intval( $mode ) ) % 2
			);

			return $this->run( 'conn_set', $args );
		}

		/**
		 * Enable a Railgun. If request is successful, the specified Railgun will be
		 * enabled and traffic for the specified domain will be proxied through Railgun.
		 *
		 * @param string $domain The domain name.
		 * @param string $token  The railgun token.
		 * @return object The response       .
		 */
		public function enable_connection_set( $domain, $token ){
			$args = array(
				'z'    => $domain,
				'rtkn' => $token
			);

			return $this->run( 'conn_setmode_enabled', $args );
		}

		/**
		 * Disable a Railgun. If request is successful, the specified Railgun will be
		 * disabled and traffic for the specified domain will be proxied through Railgun.
		 *
		 * @param string $domain The domain name.
		 * @param string $token  The railgun token.
		 * @return object        The response.
		 */
		public function disable_connection_set( $domain, $token ){
			$args = array(
				'z'    => $domain,
				'rtkn' => $token
			);

			return $this->run( 'conn_setmode_disabled', $args );
		}

		/**
		 * Remove a connection between a domain and a Railgun. This API call will
		 * allow a connected Railgun to be assigned to a different domain. Removing
		 * the connection of an enabled Railgun and domain will disable Railgun for
		 * the domain until a new connection is made with conn_set.
		 *
		 * @param  string $domain The domain name.
		 * @param  string $token  The railgun token.
		 * @return object         The response.
		 */
		public function delete_connection( $domain, $token ){
			$args = array(
				'z'    => $domain,
				'rtkn' => $token
			);

			return $this->run( 'conn_delete', $args );
		}

		/**
		 * Set the IP range(s) for a Railgun. This will expose the railgun to domains
		 * whose origin IP(s) are contained within the Railgun’s IP range.
		 *
		 * The ipr holds IPv4 or IPv6 host addresses, and optionally their subnet, all
		 * in one field. The subnet is represented by the number of network address
		 * bits present in the host address (the “netmask”). If the netmask is 32 AND
		 * the address is IPv4, then the value does not indicate a subnet, only a single
		 * host. In IPv6, the address length is 128 bits, so 128 bits specify a unique
		 * host address.
		 *
		 * The input format for this type is address/y where address is an IPv4 or IPv6
		 * address and y is the number of bits in the netmask. If the /y portion is
		 * missing, the netmask is 32 for IPv4 and 128 for IPv6, so the value represents
		 * just a single host. On display, the /y portion is suppressed if the netmask
		 * specifies a single host.
		 *
		 * For internal reasons, CloudFlare restricts the netmask range of 8 < netmask
		 * < 32 for IPv4 and 112 < netmask < 128 for IPv6.
		 *
		 * @param string  $token    The railgun token.
		 * @param string  $ipr      One or more Railgun IP ranges. If $allow_me,
		 *                          you can pass an array of IP ranges, and we will json_encode
		 *                          them for you, along with attempt to validate them (TODO).
		 * @param boolean $allow_me (Default: true) Whether to attempt to validate the $ipr format.
		 * @return object           The response.
		 */
		public function set_ip_range( $token, $ipr, $allow_me = true ){
			if( $allow_me && is_array( $ipr ) || strpos( $ipr, '[' ) !== false ){
				$ipr = wp_json_encode( $ipr );
			}

			$args = array(
				'rtkn' => $token,
				'ipr'  => $ipr
			);

			return $this->run( 'ipr_set', $args );
		}

	}
}
