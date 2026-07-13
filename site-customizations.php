<?php
/**
 * Plugin Name: Capa Ortodonti Site Customizations
 * Description: Site-wide CSS/PHP customizations under version control.
 * Version: 1.0.0
 * Author: Serdar
 */

if (!defined('ABSPATH')) exit;

add_action('wp_enqueue_scripts', function () {
    $css_path = plugin_dir_path(__FILE__) . 'assets/css/site-custom.css';
    $css_url  = plugin_dir_url(__FILE__) . 'assets/css/site-custom.css';
    if (file_exists($css_path)) {
        wp_enqueue_style('capa-site-custom', $css_url, array(), filemtime($css_path));
    }
}, 999);

add_action('wp_footer', function () {
    if (is_admin()) {
        return;
    }
    echo '<a href="/online-randevu/" class="capa-randevu-fab" aria-label="Online Randevu Al"><svg class="capa-fab-ico" viewBox="0 0 24 24" fill="none" stroke="#ffffff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg><span class="capa-fab-label"><span>Online</span><span>Randevu</span></span></a>';
});
