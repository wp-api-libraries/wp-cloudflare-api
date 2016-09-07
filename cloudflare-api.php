<?php
/**
 * CloudFlare API (https://api.cloudflare.com/)
 *
 * @package wp-cloudflare-api
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * CloudFlareAPI class.
 */
class CloudFlareAPI {



	/**
	 * Get User Properties (https://api.cloudflare.com/#user-properties).
	 *
	 * @accountaccess FREE, PRO, Business, Enterprise
	 * @access public
	 * @return void
	 */
	function get_user() {

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
	 * @return void
	 */
	function get_user_billing_profile() {

	}


	/**
	 * Get User Billing History (https://api.cloudflare.com/#user-billing-history-properties).
	 *
	 * @access public
	 * @return void
	 */
	function get_user_billing_history() {

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
				$msg = __( 'OK.','text-domain' );
				break;
			case 400:
				$msg = __( 'Bad Request: Required parameter missing or invalid.','text-domain' );
				break;
			case 401:
				$msg = __( 'Unauthorized: User does not have permission.','text-domain' );
				break;
			case 403:
				$msg = __( 'Forbidden: Request not authenticated.','text-domain' );
				break;
			case 405:
				$msg = __( 'Method Not Allowed: Incorrect HTTP method provided.','text-domain' );
				break;
			case 415:
				$msg = __( 'Unsupported Media Type: Response is not valid JSON.','text-domain' );
				break;
			case 429:
				$msg = __( 'Too many requests: Client is rate limited.','text-domain' );
				break;
			case 1000;
				$msg = __( 'API errors encountered.','text-domain' );
				break;
			case 1001;
				$msg = __( 'Request had no Authorization header.','text-domain' );
				break;
			case 1002;
				$msg = __( 'Unsupported request_type.','text-domain' );
				break;
			case 1003;
				$msg = __( 'Failed to read contents of HTTP request.','text-domain' );
				break;
			case 1004;
				$msg = __( 'Failed to parse request JSON.','text-domain' );
				break;
			case 1005;
				$msg = __( 'Too many hostnames requested - you may only request up to 100 per certificate.','text-domain' );
				break;
			case 1006;
				$msg = __( 'One or more hostnames were duplicated in the request and have been removed prior to certificate generation.','text-domain' );
				break;
			case 1007;
				$msg = __( 'CSR parsed as empty.','text-domain' );
				break;
			case 1008;
				$msg = __( 'Error creating request to CA.','text-domain' );
				break;
			case 1009;
				$msg = __( 'Permitted values for the *requested_validity* parameter (specified in days) are: 7, 30, 90, 365, 730, 1095, and 5475 (default).','text-domain' );
				break;
			case 1010;
				$msg = __( 'Failed to validate SAN <hostname>: <reason for failure>.','text-domain' );
				break;
			case 1011;
				$msg = __( 'Failed to parse CSR.','text-domain' );
				break;
			case 1012;
				$msg = __( 'Please provide a zone id when requesting a stored certificate, or fetch by serial number.','text-domain' );
				break;
			case 1013;
				$msg = __( 'Please provide a certificate serial number when operating on a single certificate.','text-domain' );
				break;
			case 1014;
				$msg = __( 'Certificate already revoked.','text-domain' );
				break;
			case 1100;
				$msg = __( 'Failed to write certificate to database.','text-domain' );
				break;
			case 1101;
				$msg = __( 'Failed to read certificate from database.','text-domain' );
				break;
			case 1200;
				$msg = __( 'API Error: Failed to generate CA request.','text-domain' );
				break;
			case 1201;
				$msg = __( 'CA signing failure. Could not parse returned certificate.','text-domain' );
				break;
			case 1300;
				$msg = __( 'Failed to fetch keyless servers from API.','text-domain' );
				break;
			case 1301;
				$msg = __( 'The key server did not activate correctly.','text-domain' );
				break;
			case 1302;
				$msg = __( 'Could not get keyless server port for server <server>.','text-domain' );
				break;
			case 1303;
				$msg = __( 'Invalid hostname: <hostname>.','text-domain' );
				break;
			default:
				$msg = __( 'Response code unknown.', 'text-domain' );
				break;
		}
		return $msg;
	}
}
