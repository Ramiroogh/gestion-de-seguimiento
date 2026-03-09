<?php
/**
 * Plugin Name: Gestor de Seguimientos
 * Description: Sistema de rastreo frontend usando ACF y WooCommerce.
 * Version: 1.1.0
 * Author: Digitalweb Patagonia Studio
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'GS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'GS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

require_once GS_PLUGIN_DIR . 'includes/class-gs-shortcode.php';
require_once GS_PLUGIN_DIR . 'includes/class-gs-ajax.php';

add_action( 'plugins_loaded', function() {
    new GS_Shortcode();
    new GS_Ajax();
});

add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'gs-frontend', GS_PLUGIN_URL . 'assets/css/gs-frontend.css', array(), '1.1.0' );
    wp_enqueue_script( 'gs-frontend', GS_PLUGIN_URL . 'assets/js/gs-frontend.js', array(), '1.1.0', true );
    
    wp_localize_script( 'gs-frontend', 'gs_ajax_obj', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
        'nonce'    => wp_create_nonce( 'gs_seguridad_nonce' )
    ));
});