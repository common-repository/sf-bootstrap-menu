<?php 
/**
 * Plugin Name: SF Bootstrap Menu
 * Plugin URI: https://studiofreya.com/sf-bootstrap-menu
 * Description: Responsive menu for child pages with Bootstrap 3.0
 * Version: 2.4.1
 * Author: Studiofreya AS
 * Author URI: https://studiofreya.com
 * License: GPL3
 */

function sf_bootstrap_menu_init() {
	require('sf-menu-widget.php' );
	require('sf-navwalker.php');
	require('sf-navwalker-horizontal.php');
	
	register_widget('SfMenuWidget');
}
add_action('widgets_init', 'sf_bootstrap_menu_init');

add_action('in_widget_form', 'spice_get_widget_id');
function spice_get_widget_id($widget_instance)
{
    if ($widget_instance->number=="__i__"){  
		echo "<p><strong>Widget ID is</strong>: Pls save the widget first!</p>"   ;
	}  else {
       echo "<p><strong>Widget ID: </strong>" .$widget_instance->id. "</p>";
    }
}

function sf_menu_load_scripts() {
    $main_style = 'sf_menu_style';
	if( ( ! wp_style_is( $main_style, 'queue' ) ) && ( ! wp_style_is( $main_style, 'done' ) ) ) {
		wp_enqueue_style( $main_style, plugin_dir_url( __FILE__ ) . 'css/style.min.css' );
	}
	
	$bootstrap = 'bootstrap';
	$bs_bootstrap = 'bs_bootstrap';
	if( ( ! wp_style_is( $bootstrap, 'queue' ) ) && ( ! wp_style_is( $bootstrap, 'done' ) ) 
		&& ( ! wp_style_is( $bs_bootstrap, 'queue' ) ) && ( ! wp_style_is( $bs_bootstrap, 'done' ) ) ) {
		wp_enqueue_style( $bootstrap, plugin_dir_url( __FILE__ ) . 'css/bootstrap.min.css' );
	}
		
	$style_font = 'font-awesome';
	if( ( ! wp_style_is( $style_font, 'queue' ) ) && ( ! wp_style_is( $style_font, 'done' ) ) ) {
		wp_enqueue_style( $style_font, plugin_dir_url( __FILE__ ) . 'css/font-awesome.min.css' );
	}
	
	if ( (!wp_script_is( $bootstrap, 'queue' ) ) && ( ! wp_script_is( $bootstrap, 'done' ) ) 
		&& ( ! wp_style_is( $bs_bootstrap, 'queue' ) ) && ( ! wp_style_is( $bs_bootstrap, 'done' ) ) ) {
       wp_register_script( 'bootstrap', plugin_dir_url(__FILE__).'js/bootstrap.min.js', array('jquery'), false, true);
       wp_enqueue_script( 'bootstrap' );
    }
}
add_action( 'wp_enqueue_scripts', 'sf_menu_load_scripts' );


function sf_bootstrap_menu_load() {
  $plugin_dir = basename(dirname(__FILE__));
  load_plugin_textdomain('sf-bootstrap-menu', false, $plugin_dir . '/languages');
}
add_action('plugins_loaded', 'sf_bootstrap_menu_load');

?>
