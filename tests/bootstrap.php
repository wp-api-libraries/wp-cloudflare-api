<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Wp_Cloudflare_Api
 */

//Add support for PHPUnit 6.
//See: https://core.trac.wordpress.org/browser/trunk/tests/phpunit/includes/phpunit6-compat.php?order=date&desc=1
if ( class_exists( 'PHPUnit\Runner\Version' ) && version_compare( PHPUnit\Runner\Version::id(), '6.0', '>=' ) ) {
    class_alias( 'PHPUnit\Framework\TestCase',                   'PHPUnit_Framework_TestCase' );
    class_alias( 'PHPUnit\Framework\Exception',                  'PHPUnit_Framework_Exception' );
    class_alias( 'PHPUnit\Framework\ExpectationFailedException', 'PHPUnit_Framework_ExpectationFailedException' );
    class_alias( 'PHPUnit\Framework\Error\Notice',               'PHPUnit_Framework_Error_Notice' );
    class_alias( 'PHPUnit\Framework\Test',                       'PHPUnit_Framework_Test' );
    class_alias( 'PHPUnit\Framework\Warning',                    'PHPUnit_Framework_Warning' );
    class_alias( 'PHPUnit\Framework\AssertionFailedError',       'PHPUnit_Framework_AssertionFailedError' );
    class_alias( 'PHPUnit\Framework\TestSuite',                  'PHPUnit_Framework_TestSuite' );
    class_alias( 'PHPUnit\Framework\TestListener',               'PHPUnit_Framework_TestListener' );
    class_alias( 'PHPUnit\Util\GlobalState',                     'PHPUnit_Util_GlobalState' );
    class_alias( 'PHPUnit\Util\Getopt',                          'PHPUnit_Util_Getopt' );
    class PHPUnit_Util_Test extends PHPUnit\Util\Test {
        public static function getTickets( $className, $methodName )
        {
            return array();
        }
    }
}

$_tests_dir = getenv( 'WP_TESTS_DIR' );
if ( ! $_tests_dir ) {
	$_tests_dir = '/tmp/wordpress-tests-lib';
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	require dirname( dirname( __FILE__ ) ) . '/wp-cloudflare-api.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';
