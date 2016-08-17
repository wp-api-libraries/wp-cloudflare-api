<?php
/**
 * CloudFlare WPCLI
 *
 * @package wp-cloudflare-wpcli
 */

/* Exit if accessed directly */
if ( ! defined( 'ABSPATH' ) ) { exit; }


/**
 * CloudFlare_WP_CLI_Commands class.
 *
 * @extends WP_CLI_Command
 */
class CloudFlare_WP_CLI_Commands extends WP_CLI_Command {

	/* TBD: CLOUDFLARE WP-CLI COMMANDS */

}

WP_CLI::add_command( 'cloudflare', 'CloudFlare_WP_CLI_Commands' );