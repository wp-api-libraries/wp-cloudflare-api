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
			/* CloudFlare CA error codes. */
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

			/* DNS Records for a Zone Error Codes. */


			case 1000;
				$msg = __( 'Invalid user.','text-domain' );
				break;
			case 1002;
				$msg = __( 'Invalid or missing zone_id.','text-domain' );
				break;
			case 1003;
				$msg = __( 'The per_page must be a positive integer.','text-domain' );
				break;
			case 1004;
				$msg = __( 'Invalid or missing zone.','text-domain' );
				break;
			case 1005;
				$msg = __( 'Invalid or missing record.','text-domain' );
				break;
			case 1007;
				$msg = __( 'Name required.','text-domain' );
				break;
			case 1008;
				$msg = __( 'Content required.','text-domain' );
				break;
			case 1009;
				$msg = __( 'Invalid or missing record id.','text-domain' );
				break;
			case 1010;
				$msg = __( 'Invalid or missing record.','text-domain' );
				break;
			case 1011;
				$msg = __( 'Zone file for \'<domain name>\' could not be found.','text-domain' );
				break;
			case 1012;
				$msg = __( 'Zone file for \'<domain name>\' is not modifiable.','text-domain' );
				break;
			case 1013;
				$msg = __( 'The record could not be found.','text-domain' );
				break;
			case 1014;
				$msg = __( 'You do not have permission to modify this zone.','text-domain' );
				break;


		/*
case 1015;
	$msg = __( 'Unknown error','text-domain' );
case 1017;
	$msg = __( 'Content for A record is invalid. Must be a valid IPv4 address','text-domain' );
case 1018;
	$msg = __( 'Content for AAAA record is invalid. Must be a valid IPv6 address','text-domain' );
case 1019;
	$msg = __( 'Content for CNAME record is invalid','text-domain' );
case 1024;
	$msg = __( 'Invalid priority, priority must be set and be between 0 and 65535','text-domain' );
case 1025;
	$msg = __( 'Invalid content for an MX record','text-domain' );
case 1026;
	$msg = __( 'Invalid format for a SPF record. A valid example is \'v=spf1 a mx -all\'. You should not include either the word TXT or the domain name here in the content','text-domain' );
case 1027;
	$msg = __( 'Invalid service value','text-domain' );
case 1028;
	$msg = __( 'Invalid service value. Must be less than 100 characters','text-domain' );
case 1029;
	$msg = __( 'Invalid protocol value','text-domain' );
case 1030;
	$msg = __( 'Invalid protocol value. Must be less than 12 characters','text-domain' );
case 1031;
	$msg = __( 'Invalid SRV name','text-domain' );
case 1032;
	$msg = __( 'Invalid SRV name. Must be less than 90 characters','text-domain' );
case 1033;
	$msg = __( 'Invalid weight value. Must be between 0 and 65,535','text-domain' );
case 1034;
	$msg = __( 'Invalid port value. Must be between 0 and 65,535','text-domain' );
case 1037;
	$msg = __( 'Invalid domain name for a SRV target host','text-domain' );
case 1038;
	$msg = __( 'Invalid DNS record type','text-domain' );
case 1039;
	$msg = __( 'Invalid TTL. Must be between 120 and 4,294,967,295 seconds, or 1 for automatic','text-domain' );
case 1041;
	$msg = __( 'Priority must be set for SRV record','text-domain' );
case 1042;
	$msg = __( 'Zone file for \'<domain name>\' could not be found','text-domain' );
case 1043;
	$msg = __( 'Zone file for \'<domain name>\' is not editable','text-domain' );
case 1044;
	$msg = __( 'A record with these exact values already exists. Please modify or remove this record','text-domain' );
case 1045;
	$msg = __( 'The record could not be found','text-domain' );
case 1046;
	$msg = __( 'A record with these exact values already exists. Please modify or cancel this edit','text-domain' );
case 1047;
	$msg = __( 'You do not have permission to modify this zone','text-domain' );
case 1048;
	$msg = __( 'You have reached the record limit for this zone','text-domain' );
case 1049;
	$msg = __( 'The record content is missing','text-domain' );
case 1050;
	$msg = __( 'Could not find record','text-domain' );
case 1052;
	$msg = __( 'You can not point a CNAME to itself','text-domain' );
case 1053;
	$msg = __( 'Invalid lat_degrees, must be an integer between 0 and 90 inclusive','text-domain' );
case 1054;
	$msg = __( 'Invalid lat_minutes, must be an integer between 0 and 59 inclusive','text-domain' );
case 1055;
	$msg = __( 'Invalid lat_seconds, must be a floating point number between 0 and 60, including 0 but not including 60','text-domain' );
case 1056;
	$msg = __( 'Invalid or missing lat_direction. Values must be N or S','text-domain' );
case 1057;
	$msg = __( 'Invalid long_degrees, must be an integer between 0 and 180','text-domain' );
case 1058;
	$msg = __( 'Invalid long_minutes, must be an integer between 0 and 59','text-domain' );
case 1059;
	$msg = __( 'Invalid long_seconds, must be a floating point number between 0 and 60, including 0 but not including 60','text-domain' );
case 1060;
	$msg = __( 'Invalid or missing long_direction. Values must be E or S','text-domain' );
case 1061;
	$msg = __( 'Invalid altitude, must be a floating point number between -100000.00 and 42849672.95','text-domain' );
case 1062;
	$msg = __( 'Invalid size, must be a floating point number between 0 and 90000000.00','text-domain' );
case 1063;
	$msg = __( 'Invalid precision_horz, must be a floating point number between 0 and 90000000.00','text-domain' );
case 1064;
	$msg = __( 'Invalid precision_vert, must be a floating point number between 0 and 90000000.00','text-domain' );
case 1065;
	$msg = __( 'Invalid or missing data for <type> record','text-domain' );
case 1067;
	$msg = __( 'Invalid content for a NS record','text-domain' );
case 1068;
	$msg = __( 'Target cannot be an IP address','text-domain' );
case 1069;
	$msg = __( 'CNAME content cannot reference itself','text-domain' );
case 1070;
	$msg = __( 'CNAME content cannot be an IP','text-domain' );
case 1071;
	$msg = __( 'Invalid proxied mode. Record cannot be proxied','text-domain' );
case 1072;
	$msg = __( 'Invalid record identifier','text-domain' );
case 1073;
	$msg = __( 'Invalid TXT record. Must be less than 255 characters','text-domain' );
case 1074;
	$msg = __( 'Invalid TXT record. Record may only contain printable ASCII!','text-domain' );
*/

			/* Zone Error Codes */
		/*
case 1000;
  $msg = __( 'Invalid or missing user','text-domain' );
case 1002;
	$msg = __( 'The \'name\' must be a valid domain','text-domain' );
case 1003;
	$msg = __( 'The \'jump_start\' must be boolean','text-domain' );
case 1004;
	$msg = __( 'Failed to assign name servers','text-domain' );
case 1006;
	$msg = __( 'Invalid or missing zone','text-domain' );
case 1008;
	$msg = __( 'Invalid or missing Zone id','text-domain' );
case 1010;
	$msg = __( 'Invalid Zone','text-domain' );
case 1011;
	$msg = __( 'Invalid or missing zone','text-domain' );
case 1012;
	$msg = __( 'Request must contain one of \'purge_everything\' or \'files\'','text-domain' );
case 1013;
	$msg = __( '\'purge_everything\' must be true','text-domain' );
case 1014;
	$msg = __( '\'files\' must be an array of urls','text-domain' );
case 1015;
	$msg = __( 'Unable to purge <url>','text-domain' );
case 1016;
	$msg = __( 'Unable to purge any urls','text-domain' );
case 1017;
	$msg = __( 'Unable to purge all','text-domain' );
case 1018;
	$msg = __( 'Invalid zone status','text-domain' );
case 1019;
	$msg = __( 'Zone is already paused','text-domain' );
case 1020;
	$msg = __( 'Invalid or missing zone','text-domain' );
case 1021;
	$msg = __( 'Invalid zone status','text-domain' );
case 1022;
	$msg = __( 'Zone is already unpaused','text-domain' );
case 1023;
	$msg = __( 'Invalid or missing zone','text-domain' );
case 1024;
	$msg = __( '<domain> already exists','text-domain' );
case 1049;
	$msg = __( '<domain> is not a registered domain','text-domain' );
case 1050;
	$msg = __( '<domain> is currently being tasted. It is not currently a registered domain','text-domain' );
case 1051;
	$msg = __( 'CloudFlare is already hosting <domain>','text-domain' );
case 1052;
	$msg = __( 'An error has occurred and it has been logged. We will fix this problem promptly. We apologize for the inconvenience','text-domain' );
case 1053;
	$msg = __( '<domain> is already disabled','text-domain' );
case 1054;
	$msg = __( '<domain> is already enabled','text-domain' );
case 1055;
	$msg = __( 'Failed to disable <domain>','text-domain' );
case 1056;
	$msg = __( 'preserve_ini must be a boolean','text-domain' );
case 1057;
	$msg = __( 'Zone must be in \'initializing\' status','text-domain' );
case 1059;
	$msg = __( 'Unable to delete zone','text-domain' );
case 1061;
	$msg = __( '<domain> already exists','text-domain' );
case 1062;
	$msg = __( 'Not allowed to update zone status','text-domain' );
case 1063;
	$msg = __( 'Not allowed to update zone step','text-domain' );
case 1064;
	$msg = __( 'Not allowed to update zone step. Bad zone status','text-domain' );
case 1065;
	$msg = __( 'Not allowed to update zone step. Zone has already been set up','text-domain' );
case 1066;
	$msg = __( 'Could not promote zone to step 3','text-domain' );
case 1067;
	$msg = __( 'Invalid organization identifier passed in your organization variable','text-domain' );
case 1068;
	$msg = __( 'Permission denied','text-domain' );
case 1069;
	$msg = __( 'organization variable should be an organization object','text-domain' );
case 1070;
	$msg = __( 'This operation requires a Business or Enterprise account.','text-domain' );
case 1071;
	$msg = __( 'Vanity name server array expected.','text-domain' );
case 1072;
	$msg = __( 'Vanity name server array cannot be empty.','text-domain' );
case 1073;
	$msg = __( 'A name server provided is in the wrong format.','text-domain' );
case 1074;
	$msg = __( 'Could not find a valid zone.','text-domain' );
case 1075;
	$msg = __( 'Vanity name server array count is invalid','text-domain' );
case 1076;
	$msg = __( 'Name servers have invalid IP addresses','text-domain' );
case 1077;
	$msg = __( 'Could not find a valid zone.','text-domain' );
case 1078;
	$msg = __( 'This zone has no valid vanity IPs.','text-domain' );
case 1079;
	$msg = __( 'This zone has no valid vanity name servers.','text-domain' );
case 1080;
	$msg = __( 'There is a conflict with one of the name servers.','text-domain' );
case 1081;
	$msg = __( 'There are no valid vanity name servers to disable.','text-domain' );
case 1082;
	$msg = __( 'Unable to purge \'<url>\'. You can only purge files for this zone','text-domain' );
case 1083;
	$msg = __( 'Unable to purge \'<url>\'. Rate limit reached. Please wait if you need to perform more operations','text-domain' );
case 1084;
	$msg = __( 'Unable to purge \'<url>\'.','text-domain' );
case 1085;
	$msg = __( 'Only one property can be updated at a time','text-domain' );
case 1086;
	$msg = __( 'Invalid property','text-domain' );
case 1087;
	$msg = __( 'Zone is in an invalid state','text-domain' );
*/


			/* Custom Pages for a Zone Error Codes. */
			/*
case 1000;
	$msg = __( 'Invalid user','text-domain' );
case 1001;
	$msg = __( 'Invalid request. Could not connect to database','text-domain' );
case 1002;
	$msg = __( 'Validator dispatcher expects an array','text-domain' );
case 1004;
	$msg = __( 'Cannot find a valid zone','text-domain' );
case 1006;
	$msg = __( 'Cannot find a valid customization page','text-domain' );
case 1007;
	$msg = __( 'Invalid validation method being called','text-domain' );
case 1200;
	$msg = __( 'A URL is required','text-domain' );
case 1201;
	$msg = __( 'The URL provided seems to be irregular','text-domain' );
case 1202;
	$msg = __( 'Unable to grab the content for the URL provided. Please try again.','text-domain' );
case 1203;
	$msg = __( 'Your custom page must be larger than <characters> characters','text-domain' );
case 1204;
	$msg = __( 'Your custom page must be smaller than <characters> characters','text-domain' );
case 1205;
	$msg = __( 'A <token> token was not detected on the error page, and must be added before this page can be integrated into CloudFlare. The default error page will show until this is corrected and rescanned.','text-domain' );
case 1206;
	$msg = __( 'Could not find a valid zone','text-domain' );
case 1207;
	$msg = __( 'That customization page is not modifiable','text-domain' );
case 1208;
	$msg = __( 'An unknown error has occurred and has been logged. We will fix this problem promptly. We apologize for the inconvenience.','text-domain' );
case 1209;
	$msg = __( 'Could not find a valid customization page for this operation','text-domain' );
case 1210;
	$msg = __( 'That operation is no longer allowed for that domain.','text-domain' );
case 1211;
	$msg = __( 'Could not find a valid customization page to disable','text-domain' );
case 1212;
	$msg = __( 'An undocumented error has occurred and has been logged.','text-domain' );
case 1213;
	$msg = __( 'That operation has already been performed for this challenge/error.','text-domain' );
case 1214;
	$msg = __( 'Rate limit reached for this operation. Please try again in a minute','text-domain' );
case 1215;
	$msg = __( 'Rate limit reached for this operation. Please try again in a minute','text-domain' );
case 1217;
	$msg = __( 'Invalid state passed','text-domain' );
case 1218;
	$msg = __( 'Missing Custom Page state','text-domain' );
case 1219;
	$msg = __( 'Please upgrade to access this feature','text-domain' );
case 1220;
	$msg = __( 'We were unable to scan the page provided. Please ensure it is accessible publicly and is larger than 100 characters','text-domain' );
*/
		}
		return $msg;
	}
}
