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

/**
 * Mobil menu davranisi (Smartmenus):
 * - collapsibleBehavior=link: yaziya tiklama sayfaya gider,
 *   sagdaki ok alt menuyu acar/kapatir.
 * - Menu bosluguna tiklama acik alt menuleri kapatir.
 */
add_action('wp_footer', function () {
    if (is_admin()) return;
    ?>
<script>
(function(){
  function ensureLinkMode(){
    if (!window.jQuery || !jQuery.fn || !jQuery.fn.smartmenus) return;
    jQuery('.elementor-location-header ul.elementor-nav-menu').each(function(){
      var sm = jQuery(this).data('smartmenus');
      if (sm && sm.opts) sm.opts.collapsibleBehavior = 'link';
    });
  }
  document.addEventListener('click', function(e){
    ensureLinkMode();
    var t = e.target;
    if (!t || !t.closest) return;
    var nav = t.closest('nav.elementor-nav-menu--dropdown');
    if (nav && !t.closest('a')) {
      if (window.jQuery && jQuery.fn && jQuery.fn.smartmenus) {
        jQuery(nav).find('ul.elementor-nav-menu').each(function(){
          var sm = jQuery(this).data('smartmenus');
          if (sm) { try { sm.menuHideAll(); } catch(err){} }
        });
      }
    }
  }, true);
  window.addEventListener('load', ensureLinkMode);
})();
</script>
    <?php
});

add_filter('the_content', function ($content) {
    if (is_admin() || is_feed()) return $content;
    if (is_singular('post') && is_main_query() && in_the_loop()) {
        $cta = '<div class="capa-post-cta"><h3>G&#252;l&#252;&#351;&#252;n&#252;z i&#231;in ilk ad&#305;m&#305; at&#305;n</h3><p>&#304;lk muayenenizde hekimlerimiz sizi birlikte de&#287;erlendirsin, size &#246;zel tedavi plan&#305;n&#305;z&#305; beraber olu&#351;tural&#305;m.</p><div class="cpc-btns"><a class="cpc-btn cpc-o" href="/online-randevu/">Online Randevu Al</a><a class="cpc-btn cpc-w" href="https://wa.me/905548319411" target="_blank" rel="noopener">WhatsApp</a></div></div>';
        return $content . $cta;
    }
    return $content;
}, 20);

add_action('wp_head', function () {
    if (is_singular('post') && has_post_thumbnail()) {
        $u = esc_url(get_the_post_thumbnail_url(get_the_ID(), 'large'));
        echo '<style id="capa-post-hero">body.single-post .elementor-widget-icon-box > .elementor-widget-container{background-image:linear-gradient(120deg,rgba(23,86,170,.93),rgba(16,66,124,.86) 55%,rgba(12,52,99,.82)),url(' . $u . ') !important;background-size:cover !important;background-position:center !important}</style>';
    }
}, 100);
