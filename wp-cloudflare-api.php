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
		 * Update a User Subscription.
		 *
		 * @api PUT
		 * @see https://api.cloudflare.com/#user-subscription-update-a-user-subscription Documentation
		 * @param  string $subscription_id Subscription identifier tag.
		 * @param  int    $price           The price of the subscription that will be billed, in US dollars.
		 * @param  string $currency        The monetary unit in which pricing information is displayed.
		 * @param  string $frequency       How often the subscription is renewed automatically.
		 * @param  array  $optional_args   Array with optional parameters. See API docs for details.
		 * @return array                   Updated user subscription info.
		 */
		public function update_user_subscriptions( $subscription_id, $price, $currency, $frequency, $optional_args = array() ) {
			$args = array(
				'id'        => $subscription_id,
				'price'     => $price,
				'currency'  => $currency,
				'frequency' => $frequency,
			);
			$args = array_merge( $optional_args, $args );

			return $this->build_request( "user/subscriptions/$subscription_id", $args, 'PUT' )->fetch();
		}

		/**
		 * Deletes a user's subscriptions
		 *
		 * @api DELETE
		 * @see https://api.cloudflare.com/#user-subscription-delete-user-subscriptions Documentation
		 * @param  string $subscription_id Subscription identifier tag.
		 * @return array                   JSON array with deleted subscription ID.
		 */
		public function delete_user_subscriptions( $subscription_id ) {
			return $this->build_request( "user/subscriptions/$subscription_id", '', 'DELETE' )->fetch();
		}

		/**
		 * List access rules.
		 *
		 * Search, sort, and filter IP/country access rules
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-level-firewall-access-rule-list-access-rules Documentation
		 * @param  array $args  Array with optional parameters. See API docs for details.
		 * @return array         List of user-level firewall access rules.
		 */
		public function get_user_access_rules( $args = array() ) {
			return $this->build_request( 'user/firewall/access_rules/rules', $args )->fetch();
		}

		/**
		 * Create access rule
		 *
		 * Make a new IP, IP range, or country access rule for all zones owned by the user. Note: If you would like to create
		 * an access rule that applies to a specific zone only, use the zone firewall endpoints.
		 *
		 * @api POST
		 * @see https://api.cloudflare.com/#user-level-firewall-access-rule-create-access-rule Documentation
		 * @param  string $mode          The action to apply to a matched request. Valid values: block, challenge, whitelist.
		 * @param  string $config_target Rule configuration target. valid values: ip, ip_range, country.
		 * @param  string $config_value  Rule configuration value. See API docs for details.
		 * @param  string $notes         A personal note about the rule. Typically used as a reminder or explanation for the rule.
		 * @return array                 New access rule info.
		 */
		public function create_user_accesss_rule( $mode, $config_target, $config_value, $notes = '' ) {
			$args = array(
				'mode' => $mode,
				'configuration' => array(
					'target' => $config_target,
					'value'  => $config_value,
				),
				'notes' => $notes,
			);

			return $this->build_request( 'user/firewall/access_rules/rules', $args, 'POST' )->fetch();
		}

		/**
		 * Update access rule.
		 *
		 * Update rule state and/or configuration. This will be applied across all zones owned by the user.
		 *
		 * @param  string $id            Access rule ID.
		 * @param  string $mode          The action to apply to a matched request. Valid values: block, challenge, whitelist.
		 * @param  string $config_target Rule configuration target. valid values: ip, ip_range, country.
		 * @param  string $config_value  Rule configuration value. See API docs for details.
		 * @param  string $notes         A personal note about the rule. Typically used as a reminder or explanation for the rule.
		 * @return array                 Updated access rule info.
		 */
		public function update_user_access_rule( $id, $mode, $config_target, $config_value, $notes = '' ) {
			$args = array(
				'mode' => $mode,
				'configuration' => array(
					'target' => $config_target,
					'value'  => $config_value,
				),
				'notes' => $notes,
			);

			return $this->build_request( "user/firewall/access_rules/rules/$id", $args, 'PATCH' )->fetch();
		}

		/**
		 * Delete access rule.
		 *
		 * Remove an access rule so it is no longer evaluated during requests. This will apply to all zones owned by the user.
		 *
		 * @api DELETE
		 * @see https://api.cloudflare.com/#user-level-firewall-access-rule-delete-access-rule Documentation
		 * @param  string $id  Subscription identifier tag.
		 * @return array       Array with deleted access rule ID.
		 */
		public function delete_user_accesss_rule( $id ) {
			return $this->build_request( "user/firewall/access_rules/rules/$id", '', 'DELETE' )->fetch();
		}

		/* User Organization routes. Enterprise Only */

		/**
		 * List organizations.
		 *
		 * List organizations the user is associated with.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-s-organizations-list-organizations Documentation
		 * @param  array $args  Array with optional parameters. See API docs for details.
		 * @return array         List of user organizations.
		 */
		public function get_user_orgs( $args = array() ) {
			return $this->build_request( 'user/organizations', $args )->fetch();
		}

		/**
		 * Organization details.
		 *
		 * Get a specific organization the user is associated with.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-s-organizations-organization-details Documentation
		 * @param  string $id  Organization id.
		 * @return array       Array with org details.
		 */
		public function get_user_org_details( $id ) {
			return $this->build_request( "user/organizations/$id" )->fetch();
		}

		/**
		 * Leave organization
		 *
		 * Remove association to an organization.
		 *
		 * @api DELETE
		 * @see https://api.cloudflare.com/#user-s-organizations-leave-organization Documentation
		 * @param  string $id  Organization id.
		 * @return array       Array with removed org ID.
		 */
		public function leave_user_org( $id ) {
			return $this->build_request( "user/organizations/$id", '', 'DELETE' )->fetch();
		}

		/* User invitations route. Enterprise Only */

		/**
		 * List invitations
		 *
		 * List all invitations associated with my user.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-s-invites-list-invitations Documentation
		 * @return array  List of user invites
		 */
		public function get_user_invites() {
			return $this->build_request( 'user/invites' )->fetch();
		}

		/**
		 * Invitation details
		 *
		 * Get a specific organization the user is associated with.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#user-s-invites-invitation-details Documentation
		 * @param  string $id  Invite id.
		 * @return array       Array with invites details.
		 */
		public function get_user_invite_details( $id ) {
			return $this->build_request( "user/invites/$id" )->fetch();
		}

		/**
		 * Respond to Invitation
		 *
		 * @api PATCH
		 * @see https://api.cloudflare.com/#user-s-invites-respond-to-invitation Documentation
		 * @param  string $id     Invite id.
		 * @param  bool   $status Invite status. Set to true to accept, and false to reject.
		 * @return array          Array with invites details.
		 */
		public function respond_user_invite( $id, bool $status ) {
			$args = array(
				'status' => ( true === $status ) ? 'accepted' : 'rejected',
			);

			return $this->build_request( "user/invites/$id", $args, 'PATCH' )->fetch();
		}

		/* Welcome to.. the Danger Zones.(´･_･`). Cloudflare Zone routes that is. */

		/**
		 * Create a zone.
		 *
		 * @api POST
		 * @see https://api.cloudflare.com/#zone-create-a-zone Documentation
		 * @param  string $domain     The domain name. i.e. "example.com".
		 * @param  bool   $jump_start Automatically attempt to fetch existing DNS records.
		 * @param  array  $org        To create a zone owned by an organization, specify the organization parameter.
		 *                            Organization objects can be found in the User or User's Organizations endpoints. You
		 *                            must pass at least the ID of the organization.
		 * @return array              New zone details.
		 */
		public function create_zone( $domain, $jump_start = '', $org = array() ) {
			$args = array(
				'name' => $domain,
				'jump_start' => $jump_start,
				'org' => $org,
			);

			return $this->build_request( 'zones', $args, 'POST' )->fetch();
		}

		/**
		 * Initiate another zone activation check.
		 *
		 * @api PUT
		 * @see https://api.cloudflare.com/#zone-initiate-another-zone-activation-check Documentation
		 * @param  string $id Zone ID.
		 * @return array      Array with results.
		 */
		public function initiate_zone_activation_check( $id ) {
			return $this->build_request( "zones/$id/activation_check", '', 'PUT' )->fetch();
		}

		/**
		 * List zones.
		 *
		 * List, search, sort, and filter your zones.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-list-zones Documentation
		 * @param  array $args  Query args to send in to API call. See API docs for details.
		 * @return array        List of available zones.
		 */
		public function get_zones( $args = array() ) {
			return $this->build_request( 'zones', $args )->fetch();
		}

		/**
		 * Edit Zone Properties.
		 *
		 * Only one zone property can be changed at a time.
		 *
		 * @api PATCH
		 * @see https://api.cloudflare.com/#zone-edit-zone-properties Documentation
		 * @param  string $id                  Zone ID.
		 * @param  bool   $paused              Indicates if the zone is only using Cloudflare DNS services. A true value
		 *                                     means the zone will not receive security or performance benefits.
		 *                                     Valid values: true|false.
		 * @param  array  $vanity_name_servers An array of domains used for custom name servers. This is only available for
		 *                                     Business and Enterprise plans.
		 * @param  array  $plan                The desired plan for the zone. Changing this value will create/cancel
		 *                                     associated subscriptions. To view available plans for this zone, see Zone Plans.
		 * @return array                       Updated zone info.
		 */
		public function update_zone( $id, bool $paused = null, array $vanity_name_servers = null, array $plan = null ){
			$args = array(
				'paused' => $paused,
				'vanity_name_servers' => $vanity_name_servers,
				'plan' => $plan,
			);
			return $this->build_request( "zones/$id", $args, 'PATCH' )->fetch();
		}


		/**
		 * Purge all files.
		 *
		 * Remove ALL files from Cloudflare's cache.
		 *
		 * @api DELETE
		 * @see https://api.cloudflare.com/#zone-purge-all-files Documentation
		 * @param  string $id Zone Id.
		 * @return array      Purge results.
		 */
		public function purge_zone_cache_all( string $id ){
			$args = array(
				'purge_everything' => true,
			);
			return $this->build_request( "zones/$id/purge_cache", $args, 'DELETE' )->fetch();
		}

		/**
		 * Purge individual files by URL and Cache-Tags.
		 *
		 * Granularly remove one or more files from Cloudflare's cache either by specifying the URL or the associated
		 * Cache-Tag. All tiers can purge by URL. Cache-Tag is for Enterprise only.
		 *
		 * Cache-Tag purging has a rate limit of up to 2,000 purge API calls in every 24 hour period. You may purge up to 30
		 * tags in one API call.
		 *
		 * @api DELETE
		 * @see https://api.cloudflare.com/#zone-purge-individual-files-by-url-and-cache-tags Documentation
		 * @param  string $id    Zone Id.
		 * @param  array  $files An array of URLs that should be removed from cache.
		 * @param  array  $tags  Any assets served with a Cache-Tag header that matches one of the provided values will be
		 *                       purged from the Cloudflare cache.
		 * @return array         Purge results.
		 */
		public function purge_zone_cache_individual( string $id, array $files = null, array $tags = null ){
			$args = array(
				'files' => $files,
				'tags' => $tags,
			);
			return $this->build_request( "zones/$id/purge_cache", $args, 'DELETE' )->fetch();
		}

		/**
		 * Delete a zone.
		 *
		 * @api DELETE
		 * @see https://api.cloudflare.com/#zone-delete-a-zone Documentation
		 * @param  string $id Zone ID.
		 * @return array      Deleted zone info.
		 */
		public function delete_zone( string $id ){
			return $this->build_request( "zones/$id/purge_cache", '', 'DELETE' )->fetch();
		}

		/**
		 * Available Rate Plans.
		 *
		 * List all rate plans the zone can subscribe to.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-rate-plan-available-rate-plans Documentation
		 * @param  string $id Zone ID.
		 * @return array      Available rate plan info.
		 */
		public function get_zone_available_rate_plans( string $id ){
			return $this->build_request( "zones/$id/available_rate_plans" )->fetch();
		}

		/**
		 * Get all Zone settings
		 *
		 * Available settings for your user in relation to a zone
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-all-zone-settings Documentation
		 * @param  string $id Zone ID.
		 * @return array      Zone setting info.
		 */
		public function get_zone_settings( string $id ){
			return $this->build_request( "zones/$id/settings" )->fetch();
		}

		/**
		 * TODO: Complete method.
		 */
		public function get_zone_settings_advanced_ddos(){}

		/**
		 * Get Always Online setting
		 *
		 * When enabled, Always Online will serve pages from our cache if your server is offline
		 * (https://support.cloudflare.com/hc/en-us/articles/200168006)
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-always-online-setting Documentation
		 * @param  string $id Zone ID.
		 * @return array      Always online setting info.
		 */
		public function get_zone_settings_always_online( $id ){
			return $this->build_request( "zones/$id/settings/always_online" )->fetch();
		}

		/**
		 * Get Always Use HTTPS setting
		 *
		 * Reply to all requests for URLs that use "http" with a 301 redirect to the equivalent "https" URL. If you only
		 * want to redirect for a subset of requests, consider creating an "Always use HTTPS" page rule.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-always-use-https-setting Documentation
		 * @param  string $id Zone ID.
		 * @return array      Always online setting info.
		 */
		public function get_zone_settings_always_use_https( $id ){
			return $this->build_request( "zones/$id/settings/always_use_https" )->fetch();
		}

		/**
		 * Get Automatic HTTPS Rewrites setting.
		 *
		 * Enable the Automatic HTTPS Rewrites feature for this zone.
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-automatic-https-rewrites-setting Documentation
		 * @param  string $id Zone ID.
		 * @return array      Auto https rewrites setting info.
		 */
		public function get_zone_settings_automatic_https_rewrites( $id ){
			return $this->build_request( "zones/$id/settings/automatic_https_rewrites" )->fetch();
		}

		/**
		 * Get Browser Cache TTL setting.
		 *
		 * Browser Cache TTL (in seconds) specifies how long Cloudflare-cached resources will remain on your visitors'
		 * computers. Cloudflare will honor any larger times specified by your server.
		 * (https://support.cloudflare.com/hc/en-us/articles/200168276).
		 *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-browser-cache-ttl-setting Documentation
		 * @param  string $id Zone ID.
		 * @return array      Browser cache ttl setting info.
		 */
		public function get_zone_settings_browser_cache_ttl(){
			return $this->build_request( "zones/$id/settings/browser_cache_ttl" )->fetch();
		}

    /**
     * Get Browser Check setting.
     *
     * Browser Integrity Check is similar to Bad Behavior and looks for common HTTP headers abused most commonly by
     * spammers and denies access to your page. It will also challenge visitors that do not have a user agent or a non
     * standard user agent (also commonly used by abuse bots, crawlers or visitors).
     * (https://support.cloudflare.com/hc/en-us/articles/200170086).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-browser-check-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Browser check info.
     */
		public function get_zone_settings_browser_check( $id ){
			return $this->build_request( "zones/$id/settings/browser_check" )->fetch();
		}

		/**
     * Get Cache Level setting.
     *
     * Cache Level functions based off the setting level. The basic setting will cache most static resources
     * (i.e., css, images, and JavaScript). The simplified setting will ignore the query string when delivering a cached
     * resource. The aggressive setting will cache all static resources, including ones with a query string.
     * (https://support.cloudflare.com/hc/en-us/articles/200168256).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-cache-level-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Cache level seting info.
     */
		public function get_zone_settings_cache_level( $id ){
			return $this->build_request( "zones/$id/settings/cache_level" )->fetch();
		}

		/**
     * Get Challenge TTL setting.
     *
     * Specify how long a visitor is allowed access to your site after successfully completing a challenge (such as a
     * CAPTCHA). After the TTL has expired the visitor will have to complete a new challenge. We recommend a 15 - 45
     * minute setting and will attempt to honor any setting above 45 minutes.
     * (https://support.cloudflare.com/hc/en-us/articles/200170136).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-challenge-ttl-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Challenge TTL setting info.
     */
		public function get_zone_settings_challenge_ttl( $id ){
			return $this->build_request( "zones/$id/settings/challenge_ttl" )->fetch();
		}

		/**
     * Get Development Mode setting.
     *
     * Development Mode temporarily allows you to enter development mode for your websites if you need to make changes
     * to your site. This will bypass Cloudflare's accelerated cache and slow down your site, but is useful if you are
     * making changes to cacheable content (like images, css, or JavaScript) and would like to see those changes right
     * away. Once entered, development mode will last for 3 hours and then automatically toggle off.
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-development-mode-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Development mode setting info.
     */
		public function get_zone_settings_development_mode( $id ){
			return $this->build_request( "zones/$id/settings/development_mode" )->fetch();
		}

		/**
     * Get Email Obfuscation setting.
     *
     * Encrypt email adresses on your web page from bots, while keeping them visible to humans.
     * (https://support.cloudflare.com/hc/en-us/articles/200170016).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-email-obfuscation-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Email obfuscation setting info.
     */
		public function get_zone_settings_email_obfuscation( $id ){
			return $this->build_request( "zones/$id/settings/email_obfuscation" )->fetch();
		}

		/**
     * Get Hotlink Protection setting.
     *
     * When enabled, the Hotlink Protection option ensures that other sites cannot suck up your bandwidth by building
     * pages that use images hosted on your site. Anytime a request for an image on your site hits Cloudflare, we check
     * to ensure that it's not another site requesting them. People will still be able to download and view images from
     * your page, but other sites won't be able to steal them for use on their own pages.
     * (https://support.cloudflare.com/hc/en-us/articles/200170026).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-hotlink-protection-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Hotlink protection setting info.
     */
		public function get_zone_settings_hotlink_protection( $id ){
			return $this->build_request( "zones/$id/settings/hotlink_protection" )->fetch();
		}

		/**
     * Get IP Geolocation setting.
     *
     * Enable IP Geolocation to have Cloudflare geolocate visitors to your website and pass the country code to you.
     * (https://support.cloudflare.com/hc/en-us/articles/200168236).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-ip-geolocation-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Hotlink protection setting info.
     */
		public function get_zone_settings_ip_geolocation( $id ){
			return $this->build_request( "zones/$id/settings/ip_geolocation" )->fetch();
		}

		/**
     * Get IPv6 setting.
     *
     * Enable IPv6 on all subdomains that are Cloudflare enabled.
     * (https://support.cloudflare.com/hc/en-us/articles/200168586).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-ipv6-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Ipv6 setting info.
     */
		public function get_zone_settings_ipv6( $id ){
			return $this->build_request( "zones/$id/settings/ipv6" )->fetch();
		}

		/**
     * Get Minify setting.
     *
     * Automatically minify certain assets for your website
     * (https://support.cloudflare.com/hc/en-us/articles/200168196).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-minify-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Minify setting info.
     */
		public function get_zone_settings_minify( $id ){
			return $this->build_request( "zones/$id/settings/minify" )->fetch();
		}

		/**
     * Get Mobile Redirect setting.
     *
     * Automatically redirect visitors on mobile devices to a mobile-optimized subdomain
     * (https://support.cloudflare.com/hc/en-us/articles/200168336).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-mobile-redirect-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Mobile redirect setting info.
     */
		public function get_zone_settings_mobile_redirect( $id ){
			return $this->build_request( "zones/$id/settings/mobile_redirect" )->fetch();
		}

		/**
     * Get Mirage setting.
     *
     * Automatically optimize image loading for website visitors on mobile devices
     * (http://blog.cloudflare.com/mirage2-solving-mobile-speed).
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-mirage-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Mirage setting info.
     */
		public function get_zone_settings_mirage( $id ){
			return $this->build_request( "zones/$id/settings/mirage" )->fetch();
		}

		/**
		 * TODO: Complete.
		 */
		public function get_zone_settings_origin_error_page_pass_thru(){}

		/**
     * Get Opportunistic Encryption setting.
     *
     * Enable the Opportunistic Encryption feature for this zone.
     *
		 * @api GET
		 * @see https://api.cloudflare.com/#zone-settings-get-opportunistic-encryption-setting Documentation
		 * @param  string $id Zone ID.
     * @return array      Opportunistic encryption setting info.
     */
		public function get_zone_settings_opportunistic_encryption( $id ){
			return $this->build_request( "zones/$id/settings/opportunistic_encryption" )->fetch();
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
