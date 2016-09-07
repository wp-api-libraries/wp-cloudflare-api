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
				$msg = __( 'OK.','textdomain' );
				break;
			case 400:
				$msg = __( 'Bad Request: Required parameter missing or invalid.','textdomain' );
				break;
			case 401:
				$msg = __( 'Unauthorized: User does not have permission.','textdomain' );
				break;
			case 403:
				$msg = __( 'Forbidden: Request not authenticated.','textdomain' );
				break;
			case 405:
				$msg = __( 'Method Not Allowed: Incorrect HTTP method provided.','textdomain' );
				break;
			case 415:
				$msg = __( 'Unsupported Media Type: Response is not valid JSON.','textdomain' );
				break;
			case 429:
				$msg = __( 'Too many requests: Client is rate limited.','textdomain' );
				break;
			default:
				$msg = __( 'Response code unknown.', 'textdomain' );
				break;
		}
		return $msg;
	}
}
