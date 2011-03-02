<?php
/**
 Plugin Name: Plugin updates blocker
 Plugin URI: #
 Description: Lets you disable unwanted updates for plugins
 Version: 0.1
 Author: Erwan Jegouzo
 Author URI: http://www.erwanjegouzo.com

    Plugin: Copyright 2011 Erwan Jegouzo  (email : erwan.jegouzo@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('PUB_NAME', 'Plugin updates blocker');
define('PUB_SLUG', 'plugin-updates-blocker');
define('PUB_UPDATE_DEACTIVATED', 'pub_update_deactivated');

if (!function_exists('get_plugins')){ require_once (ABSPATH."wp-admin/includes/plugin.php"); }
if (!function_exists('wp_update_plugins')){ require_once (ABSPATH."includes/update.php"); }
if (!function_exists('current_user_can')){ require_once (ABSPATH."includes/capabilities.php"); }

add_action('plugins_loaded','hein_init');

function hein_init(){
	if(is_admin() && current_user_can('update_plugins')){
		add_action('init','pub_init');
		add_action('wp_head', 'pub_wp_head');
	}
}

function pub_init(){
	add_submenu_page('tools.php', PUB_NAME, PUB_NAME, 8, PUB_SLUG, 'pub_menu');
}

function pub_menu(){
	include dirname(__FILE__) . '/dpu-menu.php';	
}

function pub_wp_head() {
	wp_print_scripts('jquery');
}

function pub_http_request_args( $r, $url ) {
	if ( 0 !== strpos( $url, 'http://api.wordpress.org/plugins/update-check' ) ){
		return $r;
	}
	
	$pub_plugins = unserialize(get_option(PUB_UPDATE_DEACTIVATED));
	if(count($pub_plugins) == 0){ return $r; }
	
	$wp_plugins = unserialize($r['body']['plugins'] );
	
	foreach($pub_plugins as $key => $p){
		unset( $wp_plugins->plugins[ $key ] );
		unset( $wp_plugins->active[ array_key_exists($key, $wp_plugins) ] );
	}
	$r['body']['plugins'] = serialize( $wp_plugins );
	
	return $r;
}

add_filter('http_request_args', 'pub_http_request_args', 5, 2 );





?>