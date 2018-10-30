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
	 * @author Bradley Moore <https://github.com/bradleymoore111>
	 * @author imFORZA <https://github.com/imforza>
	 */
	class CloudFlareHostAPI {

		/**
		 * Build request function: prepares the class for a fetch request.
		 *
		 * @param  string $route    URL to be accessed.
		 * @param  array  $args     Arguments to pass in. If the method is GET, will be passed as query arguments attached to the route. If the method is not get, but the content type as defined in headers is 'application/json', then the body of the request will be set to a json_encode of $args. Otherwise, they will be passed as the body.
		 * @param  string $method (Default: 'GET') The method.
		 * @return [type]           The return of the function.
		 */
		protected function build_request( $route, $body = array(), $method = 'GET' ) {
			// Sets method.
			$this->args['method'] = $method;
			// Sets route.
			$this->route = $route;

			// Merge bodies.
			if ( isset( $this->args['body'] ) ) {
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
		protected function fetch() {

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
		 */
		protected function is_status_ok( $code ) {
			return ( 200 <= $code && 300 > $code );
		}

		/**
		 * API Key.
		 *
		 * @var string
		 */
		private $api_key;

		private $auth_email;

		private $user_service_key;

		private $args;

		private $is_debug;


		/**
		 * CloudFlare Host Base API Endpoint
		 *
		 * @var string
		 * @access protected
		 */
		private $base_uri = 'https://api.cloudflare.com/host-gw.html';

		/**
		 * Class constructor.
		 *
		 * @param string $host_api_key          Cloudflare Host API Key.
		 * @param string $auth_email            Email associated to the account.
		 * @param string $user_service_key      User Service key.
		 */
		public function __construct( $host_api_key, $debug = false ) {
			$this->api_key = $host_api_key;
			$this->debug = false;
		}

		/**
		 * Function to be overwritten, gets called after the request has been made (if status code was ok). Should be used to reset headers.
		 */
		private function clear() {
			$this->args = array();
		}

		private function run( $act, $args = array() ) {
			$args['act'] = $act;
			$args['host_key'] = $this->api_key;
			return $this->build_request( '', $args, 'POST' )->fetch();
		}

		private function parse_args( $args, $merge = array() ) {
			$results = array();

			foreach ( $args as $key => $val ) {
				if ( $val !== null ) {
					$results[ $key ] = $val;
				} elseif ( is_array( $val ) && ! empty( $val ) ) {
					$results[ $key ] = $val;
				}
			}

			return array_merge( $merge, $results );
		}

		/**
		 * Create a Cloudflare account mapped to your user (required).
		 *
		 * This act parameter is used to create a new Cloudflare account on behalf of
		 * one of your users. If you know that your User already has an existing account
		 * with Cloudflare, use the "user_auth" operation, described in Section 3.2.2,
		 * instead. For your convenience, Cloudflare will automatically perform a "user_auth"
		 * operation (Section 3.2.2) if the Cloudflare account already exists.
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.1
		 * @param string $cloudflare_email    The User's e-mail address for the new Cloudflare account.
		 * @param string $cloudflare_pass     The User's password for the new Cloudflare account.
		 *                                    Cloudflare will never store this password in clear text.
		 * @param string $cloudflare_username (Default: null) The User's username for the new Cloudflare
		 *                                    account. Cloudflare will auto-generate one if it is not
		 *                                    specified.
		 * @param string $unique_id           (Default: null) Set a unique string identifying the User.
		 *                                    This identifier will serve as an alias to the user's
		 *                                    Cloudflare account. Typically you would set this value to
		 *                                    the unique ID in your system (e.g., the internal customer
		 *                                    number or username stored in your own system). This
		 *                                    parameter can be used to retrieve a user_key when it is
		 *                                    required. The unique_id must be an ASCII string with a
		 *                                    maximum length of 100 characters.
		 * @param string $clobber_unique_id   (Default: null) Any operations that can set a unique_id can
		 *                                    be set to automatically "clobber" or unset a previously
		 *                                    assigned unique_id.
		 * @return object                     If the information to create a new Cloudflare user account is
		 *                                    valid and no errors occur, the "user_create" action will echo
		 *                                    the request and return a user_key. A user_key is a string of
		 *                                    ASCII characters with a maximum length of 32 characters.
		 *
		 *                                    Important: If no unique_id is specified, the user_key should
		 *                                    be stored in your system. Certain actions to modify the new
		 *                                    user's setup, such as user_lookup or zone_set, will require
		 *                                    the user_key. If you have specified a unique_id (e.g., the
		 *                                    internal customer number or username stored in your own system)
		 *                                    then you can always use that to retrieve the user_key. You
		 *                                    should be able to lookup from your own system the user_key,
		 *                                    the unique_id, or both.
		 *
		 *                                    The cloudflare_pass field will never be echoed back. If there
		 *                                    is no msg returned, that parameter will be set to NULL.
		 */
		public function create_user( $cloudflare_email, $cloudflare_pass, $cloudflare_username = null, $unique_id = null, $clobber_unique_id = null ) {
			$args = $this->parse_args(array(
				'cloudflare_email'    => $cloudflare_email,
				'cloudflare_pass'     => $cloudflare_pass,
				'cloudflare_username' => $cloudflare_username,
				'unique_id'           => $unique_id,
				'clobber_unique_id'   => $clobber_unique_id,
			));

			return $this->run( 'user_create', $args );
		}

		/**
		 * Setup a User's zone for CNAME hosting (required).
		 *
		 * This act parameter is used to set up CNAME record hosting for a User's zone.
		 * The user_key is required. If you do not have the user_key on hand, perform a
		 * "user_lookup" (see Section 3.2.4).
		 *
		 * If run, the intent of the above action would be to configure the Cloudflare
		 * system such that it would accept traffic to www.someexample.com and blog.someexample.com.
		 * After having been routed through the Cloudflare network, if the traffic is
		 * clean and cannot be handled by the Cloudflare cache, it will be relayed to
		 * cloudflare-resolve-to.someexample.com. Traffic to another subdomain (e.g.,
		 * forum.someexample.com) would not be setup to run through Cloudflare.
		 *
		 * Note: The zone_set action replaces any previous setup for the particular
		 * zone_name. If are adding an additional subdomain to an account that already
		 * has some subdomains setup, you should specify all the subdomains not only
		 * the new subdomains.
		 *
		 * Note: The wordpress subdomain above is configured to be relayed to the CNAME
		 * cloudflare-rs2.someexample.com, rather than the default
		 * cloudflare-resolve-to.someexample.com.
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.2
		 * @param string $user_key   The unique 32 hex character auth string, identifying
		 *                           the user's Cloudflare Account. Generated from a user_create
		 *                           (Section 3.2.1) or user_auth (Section 3.2.2).
		 * @param string $zone_name  The zone you'd like to run CNAMES through Cloudflare
		 *                           for, e.g. "example.com".
		 * @param string $resolve_to The CNAME that Cloudflare should ultimately resolve
		 *                           web connections to after they have been filtered, e.g.
		 *                           "resolve-to-cloudflare.example.com". This record should
		 *                           ultimately resolve to the one or more IP addresses of
		 *                           the hosts for the particular website for all the specified
		 *                           subdomains. Note: it CANNOT be the naked zone name, in
		 *                           this case example.com
		 * @param mixed  $subdomains A comma-separated string of subdomain(s) that Cloudflare
		 *                           should host, e.g. "www,blog,forums" or
		 *                           "www.example.com,blog.example.com,forums.example.com".
		 *                           This library will also accept an array, and will convert
		 *                           it into a comma separated list for you.
		 * @return object            If the information to setup a zone is valid and no errors
		 *                           occur, the "zone_set" action will return the zone_name
		 *                           (string), resolving_to (string), hosted_cnames (map), and
		 *                           forward_tos (map) as confirmation. The forward_tos will be
		 *                           in the format of the fully qualified domain plus
		 *                           cdn.cloudflare.net (e.g., www.example.com.cdn.cloudflare.net
		 *                           or blog.example.com.cdn.cloudflare.net).
		 *
		 *                           Important: To complete setup for your user, you should
		 *                           automatically update your user's DNS settings to resolve the
		 *                           subdomains specified in zone_set or zone_lookup to the
		 *                           corresponding forward_tos. Until you complete this step,
		 *                           traffic will not be routed through the Cloudflare system.
		 *                           For your DNS settings, we recommend a TTL for the record of
		 *                           not less than 900 seconds. You should setup your user's DNS
		 *                           records for each subdomain to mirror the following results:
		 *                           ;; ANSWER SECTION:
		 *                           www.someexample.com.  900  IN  CNAME  www.someexample.com.cdn.cloudflare.net.
		 */
		public function zone_set( $user_key, $zone_name, $resolve_to, $subdomains ) {
			if ( is_array( $subdomains ) ) {
				$subdomains = implode( ',', $subdomains );
			}

			$args = array(
				'user_key'   => $user_key,
				'zone_name'  => $zone_name,
				'resolve_to' => $resolve_to,
				'subdomains' => $subdomains,
			);

			return $this->run( 'zone_set', $args );
		}

		/**
		 * Add a zone using the full setup method.
		 *
		 * This act parameter is used add a zone using the full setup. The user_key is
		 * required. If you do not have the user_key on hand, perform a "user_lookup"
		 * (see Section 3.2.4).
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.3
		 * @param  string $user_key  The unique 32 hex character auth string, identifying
		 *                           the user's Cloudflare Account. Generated from a user_create
		 *                           (Section 3.2.1) or user_auth (Section 3.2.2).
		 * @param  string $zone_name The zone you want to add to Cloudflare.
		 * @return object            If the information to setup a zone is valid and no
		 *                           errors occur, the "full_zone_set" action will return the
		 *                           zone_name (string), jumpstart (string), msg (string).
		 *
		 *                           Important: To complete setup your users will have to point
		 *                           the domain at the assigned nameservers as shown in the "msg" field.
		 *
		 *                           Additionally the zone is now registered in Cloudflare, but
		 *                           is likely missing or has inaccurate DNS information. You
		 *                           will need to create and verify all the necessary DNS zone
		 *                           records in Cloudflare.
		 */
		public function full_zone_set( $user_key, $zone_name ) {
			$args = array(
				'user_key' => $user_key,
				'zone_name' => $zone_name,
			);

			return $this->run( 'full_zone_set', $args );
		}

		/**
		 * Lookup a user's Cloudflare account information (optional).
		 *
		 * This act parameter is used to lookup information about a User's existing
		 * Cloudflare account. This action is typically used to check if the account
		 * exists or to retrieve a user_key. You do not need to support this function
		 * if you plan to store the user_key on your system.
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.4
		 * @param  string $identifier Lookup a user's account information or status by
		 *                            either cloudflare_email or unique_id.
		 * @param  string $type       Either 'cloudflare_email' or 'unique_id'.
		 * @return object             If the information to lookup the status of a Cloudflare
		 *                            account is valid and no errors occur, the "user_lookup"
		 *                            action will return the user_key (string), user_exists
		 *                            (boolean), user_authed (boolean), cloudflare_email
		 *                            (string), unique_id (string), and hosted_zones (array).
		 *
		 *                            The hosted_zones array lists all the zones that you are registered as hosting.
		 */
		public function user_lookup( $identifier, $type ) {
			$type = ($type === 'unique_id' ? 'unique_id' : 'cloudflare_email');

			return $this->run( 'user_lookup', array( $type => $identifier ) );
		}

		/**
		 * Authorize access to a user's existing Cloudflare account (optional).
		 *
		 * This act parameter is used to gain access to a User's existing Cloudflare
		 * account. This action is automatically called by "user_create" (Section 3.2.1)
		 * if the Cloudflare account already exists. In most cases, when setting up an
		 * account, you should use user_create unless you know a user already has
		 * a Cloudflare account.
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.5
		 * @param  string $cloudflare_email  The User's e-mail address for the new Cloudflare account.
		 * @param  string $cloudflare_pass   The User's password for the new Cloudflare account.
		 *                                   Cloudflare will never store this password in clear text.
		 * @param  string $unique_id         Set a unique string identifying the User. This identifier
		 *                                   will serve as an alias to the user's Cloudflare account.
		 *                                   Typically you would set this value to the unique ID in your
		 *                                   system. This parameter can be used as an alias for other
		 *                                   actions (e.g., it can substitute for the cloudflare_email
		 *                                   and cloudflare_password if you choose not to store those
		 *                                   fields in your system).
		 * @param  string $clobber_unique_id Any operations that can set a unique_id can be set to
		 *                                   automatically "clobber" or unset a previously assigned unique_id.
		 * @return object                    If the information to auth a new Cloudflare user account is valid
		 *                                   and no errors occur, the "user_auth" action will return a
		 *                                   user_key. If no unique_id is specified, the user_key should be
		 *                                   stored in your system. Certain actions, such as user_lookup or
		 *                                   zone_set require the user_key.
		 */
		public function user_auth( $cloudflare_email, $cloudflare_pass, $unique_id = null, $clobber_unique_id = null ) {
			$args = $this->parse_args(array(
				'cloudflare_email'  => $cloudflare_email,
				'cloudflare_pass'   => $cloudflare_pass,
				'unique_id'         => $unique_id,
				'clobber_unique_id' => $clobber_unique_id,
			));

			return $this->run( 'user_auth', $args );
		}

		/**
		 * Lookup a specific user's zone (optional).
		 *
		 * This act parameter is used to lookup information about a User's zone in the
		 * Cloudflare system. This action is typically used to check if the zone exists
		 * (zone_exits is true) or if you register as the host (zone_hosted is true).
		 *
		 * The following describes specific statuses of certain elements on the response.
		 *   ssl_status: This status will be set to "ready" when the zone's SSL certificate has been activated on Cloudflare.
		 *   ssl_meta_tag: The SSL META tag needs to be inserted in the HEAD of the index page of the zone in order for the SSL certificate to be issued.
		 *   sub_label: This subscription label will be set to a reseller specific value used to identify the zone's subscription.
		 *   sub_status: The subscription status value will be "V" for active and "D" for deleted.
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.6
		 * @param  string $user_key  The unique 32 hex character auth string, identifying the
		 *                           user's Cloudflare Account. Generated from a user_create
		 *                           (Section 3.2.1) or user_auth (Section 3.2.2).
		 * @param  string $zone_name The zone you'd like to lookup, e.g. "example.com".
		 * @return object            If the information to lookup the status of a Cloudflare
		 *                           account is valid and no errors occur, the "zone_lookup"
		 *                           action will return the zone_name (string), zones_exists
		 *                           (boolean), zone_hosted (boolean), hosted_cnames (map),
		 *                           and forward_tos (map).
		 */
		public function zone_lookup( $user_key, $zone_name ) {
			$args = array(
				'user_key'  => $user_key,
				'zone_name' => $zone_name,
			);

			return $this->run( 'zone_lookup', $args );
		}

		/**
		 * Delete a specific zone on behalf of a user (optional).
		 *
		 * This act parameter is used to delete a User's previously "zone_set" zone in
		 * the Cloudflare system. Cloudflare will stop honoring DNS requests for deleted
		 * zones after a short period of time. Be sure to unset all Cloudflare forwarded
		 * CNAMEs prior to or immediately after a "zone_delete".
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.7
		 * @param  string $user_key  The unique 32 hex character auth string, identifying the
		 *                           user's Cloudflare Account. Generated from a user_create
		 *                           (Section 3.2.1) or user_auth (Section 3.2.2).
		 * @param  string $zone_name The zone you'd like to delete, e.g. "example.com".
		 * @return object            If the information to lookup the status of a Cloudflare
		 *                           account is valid and no errors occur, the "zone_delete"
		 *                           action will return a success zone_name (string),
		 *                           zone_deleted (boolean).
		 */
		public function zone_delete( $zone_name ) {
			$args = array(
				'user_key'  => $user_key,
				'zone_name' => $zone_name,
			);

			return $this->run( 'zone_delete', $args );
		}

		/**
		 * Regenerate your host key(optional)
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.9
		 * @return object If the information to regenerate a new host key for the Cloudflare
		 *                host provider account is valid and no errors occur, the "host_key_regen"
		 *                action will echo the request and return the newly generated host_key.
		 *                The host_key is a string of ASCII characters with a maximum length of
		 *                32 characters.
		 *
		 *                If there is no msg returned, that parameter will be set to NULL.
		 */
		public function host_key_regen() {
			return $this->run( 'host_key_regen' );
		}

		/**
		 * List the domains currently active on Cloudflare for the given host (optional).
		 *
		 * Zone status codes are
		 *   V: Active
		 *   D: Deleted
		 *   P: Pending
		 * Subscription status codes are
		 *   V: Active
		 *   CNL: Canceled
		 *   Note: subscriptions are per zone. If a zone does not have a subscription, the subscription information will be null.
		 *
		 * @access public
		 * @docs https://www.cloudflare.com/docs/host-api/#s3.2.10
		 * @param  mixed $limit       (Default: 100) The limit.
		 * @param  mixed $offset      (Default: 0) The offset.
		 * @param  mixed $zone_name   (Default: null) The zone name.
		 * @param  mixed $sub_id Sub  (Default: null) Applies to Cloudflare Resellers only.
		 * @param  mixed $zone_status (Default: null) The zone_status parameter has valid values of
		 *                            V,D,ALL, where V shows active zones only, D deleted, and ALL all.
		 * @param  mixed $sub_status  (Default: null) The sub_status parameter has valid values of
		 *                            V,CNL,ALL, where V shows zones with an active subscription only,
		 *                            CNL canceled, and ALL all.
		 * @return object             If the information to lookup the status of a Cloudflare account
		 *                            is valid and no errors occur, the "zone_list" action will
		 *                            return a list of zones.
		 */
		public function zone_list( $limit = 100, $offset = 0, $zone_name = null, $sub_id = null, $zone_status = null, $sub_status = null ) {
			$args = $this->parse_args(array(
				'limit'       => $limit,
				'offset'      => $offset,
				'zone_name'   => $zone_name,
				'sub_id'      => $sub_id,
				'zone_status' => $zone_status,
				'sub_status'  => $sub_status,
			));

			return $this->run( 'zone_list', $args );
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
