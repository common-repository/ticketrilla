<?php

/*
Plugin Name: Ticketrilla: Client
Plugin URI: https://ticketrilla.com/
Author: Daniil Babkin
Author URI: https://ticketrilla.com
Version: 1.1.1
Requires at least: 4.9.1
Requires PHP: 7.2
Description: Client's plugin for support of WP products
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: ttlc
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

defined( 'ABSPATH' ) || exit;

define( 'TTLC_TEXTDOMAIN', 'ttlc' );
define( 'TTLC_URL', plugin_dir_url( __FILE__ ) );
define( 'TTLC_PATH', plugin_dir_path( __FILE__ ) );
define( 'TTLC_PLUGIN', plugin_basename( __FILE__ ) );
define( 'TTLC_TEMPLATES', trailingslashit( TTLC_PATH . 'templates' ) );
define( 'TTLC_PLUGIN_VERSION', '1.1.1' );
define( 'TTLC_PPP', 10 );
define( 'TTLC_TEST_PHP', 'test.php' );
define( 'TTLC_HT_REWRITE_PARAM', 'rewrite_config' );
define( 'TTLC_DBUI', 5 ); // Minutes

require_once 'includes/class-functions.php';
require_once 'includes/class-init.php';

register_activation_hook( __FILE__, array( TTLC(), 'activate' ) );
register_deactivation_hook( __FILE__, array( TTLC(), 'deactivate') );
