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
			/*
1000	Invalid user
1002	Invalid or missing zone_id
1003	per_page must be a positive integer
1004	Invalid or missing zone
1005	Invalid or missing record
1007	name required
1008	content required
1009	Invalid or missing record id
1010	Invalid or missing record
1011	Zone file for '<domain name>' could not be found
1012	Zone file for '<domain name>' is not modifiable
1013	The record could not be found
1014	You do not have permission to modify this zone
1015	Unknown error
1017	Content for A record is invalid. Must be a valid IPv4 address
1018	Content for AAAA record is invalid. Must be a valid IPv6 address
1019	Content for CNAME record is invalid
1024	Invalid priority, priority must be set and be between 0 and 65535
1025	Invalid content for an MX record
1026	Invalid format for a SPF record. A valid example is 'v=spf1 a mx -all'. You should not include either the word TXT or the domain name here in the content
1027	Invalid service value
1028	Invalid service value. Must be less than 100 characters
1029	Invalid protocol value
1030	Invalid protocol value. Must be less than 12 characters
1031	Invalid SRV name
1032	Invalid SRV name. Must be less than 90 characters
1033	Invalid weight value. Must be between 0 and 65,535
1034	Invalid port value. Must be between 0 and 65,535
1037	Invalid domain name for a SRV target host
1038	Invalid DNS record type
1039	Invalid TTL. Must be between 120 and 4,294,967,295 seconds, or 1 for automatic
1041	Priority must be set for SRV record
1042	Zone file for '<domain name>' could not be found
1043	Zone file for '<domain name>' is not editable
1044	A record with these exact values already exists. Please modify or remove this record
1045	The record could not be found
1046	A record with these exact values already exists. Please modify or cancel this edit
1047	You do not have permission to modify this zone
1048	You have reached the record limit for this zone
1049	The record content is missing
1050	Could not find record
1052	You can not point a CNAME to itself
1053	Invalid lat_degrees, must be an integer between 0 and 90 inclusive
1054	Invalid lat_minutes, must be an integer between 0 and 59 inclusive
1055	Invalid lat_seconds, must be a floating point number between 0 and 60, including 0 but not including 60
1056	Invalid or missing lat_direction. Values must be N or S
1057	Invalid long_degrees, must be an integer between 0 and 180
1058	Invalid long_minutes, must be an integer between 0 and 59
1059	Invalid long_seconds, must be a floating point number between 0 and 60, including 0 but not including 60
1060	Invalid or missing long_direction. Values must be E or S
1061	Invalid altitude, must be a floating point number between -100000.00 and 42849672.95
1062	Invalid size, must be a floating point number between 0 and 90000000.00
1063	Invalid precision_horz, must be a floating point number between 0 and 90000000.00
1064	Invalid precision_vert, must be a floating point number between 0 and 90000000.00
1065	Invalid or missing data for <type> record
1067	Invalid content for a NS record
1068	Target cannot be an IP address
1069	CNAME content cannot reference itself
1070	CNAME content cannot be an IP
1071	Invalid proxied mode. Record cannot be proxied
1072	Invalid record identifier
1073	Invalid TXT record. Must be less than 255 characters
1074	Invalid TXT record. Record may only contain printable ASCII!
*/

			/* Zone Error Codes */
			/*
1000	Invalid or missing user
1002	'name' must be a valid domain
1003	'jump_start' must be boolean
1004	Failed to assign name servers
1006	Invalid or missing zone
1008	Invalid or missing Zone id
1010	Invalid Zone
1011	Invalid or missing zone
1012	Request must contain one of 'purge_everything' or 'files'
1013	'purge_everything' must be true
1014	'files' must be an array of urls
1015	Unable to purge <url>
1016	Unable to purge any urls
1017	Unable to purge all
1018	Invalid zone status
1019	Zone is already paused
1020	Invalid or missing zone
1021	Invalid zone status
1022	Zone is already unpaused
1023	Invalid or missing zone
1024	<domain> already exists
1049	<domain> is not a registered domain
1050	<domain> is currently being tasted. It is not currently a registered domain
1051	CloudFlare is already hosting <domain>
1052	An error has occurred and it has been logged. We will fix this problem promptly. We apologize for the inconvenience
1053	<domain> is already disabled
1054	<domain> is already enabled
1055	Failed to disable <domain>
1056	preserve_ini must be a boolean
1057	Zone must be in 'initializing' status
1059	Unable to delete zone
1061	<domain> already exists
1062	Not allowed to update zone status
1063	Not allowed to update zone step
1064	Not allowed to update zone step. Bad zone status
1065	Not allowed to update zone step. Zone has already been set up
1066	Could not promote zone to step 3
1067	Invalid organization identifier passed in your organization variable
1068	Permission denied
1069	organization variable should be an organization object
1070	This operation requires a Business or Enterprise account.
1071	Vanity name server array expected.
1072	Vanity name server array cannot be empty.
1073	A name server provided is in the wrong format.
1074	Could not find a valid zone.
1075	Vanity name server array count is invalid
1076	Name servers have invalid IP addresses
1077	Could not find a valid zone.
1078	This zone has no valid vanity IPs.
1079	This zone has no valid vanity name servers.
1080	There is a conflict with one of the name servers.
1081	There are no valid vanity name servers to disable.
1082	Unable to purge '<url>'. You can only purge files for this zone
1083	Unable to purge '<url>'. Rate limit reached. Please wait if you need to perform more operations
1084	Unable to purge '<url>'.
1085	Only one property can be updated at a time
1086	Invalid property
1087	Zone is in an invalid state
*/


			/* Custom Pages for a Zone Error Codes. */
			/*
1000	Invalid user
1001	Invalid request. Could not connect to database
1002	Validator dispatcher expects an array
1004	Cannot find a valid zone
1006	Cannot find a valid customization page
1007	Invalid validation method being called
1200	A URL is required
1201	The URL provided seems to be irregular
1202	Unable to grab the content for the URL provided. Please try again.
1203	Your custom page must be larger than <characters> characters
1204	Your custom page must be smaller than <characters> characters
1205	A <token> token was not detected on the error page, and must be added before this page can be integrated into CloudFlare. The default error page will show until this is corrected and rescanned.
1206	Could not find a valid zone
1207	That customization page is not modifiable
1208	An unknown error has occurred and has been logged. We will fix this problem promptly. We apologize for the inconvenience.
1209	Could not find a valid customization page for this operation
1210	That operation is no longer allowed for that domain.
1211	Could not find a valid customization page to disable
1212	An undocumented error has occurred and has been logged.
1213	That operation has already been performed for this challenge/error.
1214	Rate limit reached for this operation. Please try again in a minute
1215	Rate limit reached for this operation. Please try again in a minute
1217	Invalid state passed
1218	Missing Custom Page state
1219	Please upgrade to access this feature
1220	We were unable to scan the page provided. Please ensure it is accessible publicly and is larger than 100 characters
*/
		}
		return $msg;
	}
}
