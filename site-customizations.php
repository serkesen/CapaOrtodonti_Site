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

/* capa-dentist-schema: Dentist (LocalBusiness) JSON-LD - lokal SEO. Yalniz ana sayfada. GBP ile hizali NAP. */
add_action('wp_head', function () {
    if (!is_front_page()) return;
    $schema = array(
        '@context' => 'https://schema.org',
        '@type' => 'Dentist',
        '@id' => 'https://capaortodonti.com/#dentist',
        'name' => 'Özel Çapa Ortodonti Diş Polikliniği',
        'alternateName' => 'Çapa Ortodonti',
        'url' => 'https://capaortodonti.com/',
        'logo' => 'https://capaortodonti.com/wp-content/uploads/logo-capa.svg',
        'image' => 'https://capaortodonti.com/wp-content/uploads/ortodonti.jpg',
        'telephone' => '+902125872424',
        'email' => 'info@capaortodonti.com',
        'address' => array(
            '@type' => 'PostalAddress',
            'streetAddress' => 'Şehremini Mh. Deniz Abdal Camii Sk. No: 23/A',
            'postalCode' => '34104',
            'addressLocality' => 'Fatih',
            'addressRegion' => 'İstanbul',
            'addressCountry' => 'TR',
        ),
        'hasMap' => 'https://maps.google.com/?cid=8021667602538020695',
        'geo' => array('@type' => 'GeoCoordinates', 'latitude' => 41.0144148, 'longitude' => 28.9320824),
        'openingHoursSpecification' => array(
            array('@type' => 'OpeningHoursSpecification', 'dayOfWeek' => array('Monday','Tuesday','Wednesday','Thursday','Friday'), 'opens' => '09:30', 'closes' => '19:00'),
            array('@type' => 'OpeningHoursSpecification', 'dayOfWeek' => 'Saturday', 'opens' => '09:30', 'closes' => '18:00'),
        ),
        'sameAs' => array(
            'https://www.facebook.com/capaortodonti',
            'https://www.instagram.com/capaortodonti/',
            'https://twitter.com/capaortodonti',
        ),
    );
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "
";
}, 99);

/* capa-faq-system: Tek kaynak FAQ - gorunur SSS (the_content) + FAQPage schema (wp_head). Gorunur metin ile schema birebir ayni. */
if (!function_exists('capa_faqs')) {
    function capa_faqs() {
        return array(
            8062 => array(
                array('Diş teli fiyatları neden kliniğe göre değişir?', 'Braket sistemi, tahmini tedavi süresi, hekimin uzmanlığı ve tedaviye dahil edilen hizmetler (kontroller, röntgen, retainer) her klinikte farklı olduğu için toplam maliyet de değişir.'),
                array('Tek çene diş teli daha mı ekonomiktir?', 'Kapsam daraldığı için tek çene tedavisi genellikle daha düşük maliyetlidir. Ancak dişlerin doğru kapanışı için çoğu vakada çift çene tedavisi önerilir; bu karar muayenede verilir.'),
                array('Diş teli tedavisi ne kadar sürer?', 'Vakanın zorluğuna göre değişmekle birlikte tedavi çoğunlukla 12–24 ay arasında sürer. Süre uzadıkça kontrol sayısı ve toplam maliyet de etkilenir.'),
                array('Şeffaf plak diş telinden pahalı mıdır?', 'Şeffaf plak sistemleri, üretim teknolojisi nedeniyle genellikle metal braketlere göre daha yüksek maliyetlidir. Size uygun seçenek muayenede belirlenir.'),
                array('Diş teli için hangi bölüme gidilir?', 'Diş teli tedavisi ortodonti bölümüne aittir; tedaviyi ortodonti uzmanı (ortodontist) planlar ve yürütür.'),
                array('Devlet hastanelerinde diş teli takılıyor mu?', 'Bazı devlet hastanelerinde ortodonti bölümleri bulunur; sıra süreleri ve yaş kriterleri değişkendir. Özel klinikte randevu esnekliği ve tedavi süreci farklı işler.'),
            ),
            7024 => array(
                array('Şeffaf plak gerçekten görünmez mi?', 'Yakın mesafede fark edilebilir ancak sosyal mesafede neredeyse görünmezdir; bu yüzden özellikle yetişkinler tarafından tercih edilir.'),
                array('Şeffaf plak günde kaç saat takılmalı?', 'Etkili bir tedavi için günde yaklaşık 20-22 saat takılması önerilir; yemek ve diş fırçalama sırasında çıkarılır.'),
                array('Şeffaf plak tedavisi ne kadar sürer?', 'Vakanın zorluğuna göre değişmekle birlikte çoğunlukla 6-18 ay arasındadır.'),
                array('Şeffaf plak diş telinden pahalı mıdır?', 'Üretim teknolojisi nedeniyle genellikle metal braketlere göre daha yüksek maliyetlidir. Size uygun seçenek muayenede belirlenir.'),
                array('Her ortodontik sorun şeffaf plakla çözülür mü?', 'Hayır. Karmaşık iskeletsel vakalarda braket veya kombine tedavi gerekebilir; uygunluk muayenede değerlendirilir.'),
                array('Şeffaf plak konuşmayı etkiler mi?', 'İlk günlerde kısa bir alışma süreci olabilir, ardından konuşma normale döner.'),
            ),
            4836 => array(
                array('Bonding nedir?', 'Bonding, diş yüzeyine kompozit reçine uygulanarak dişin şekil, renk veya boşluk gibi estetik sorunlarının giderildiği bir uygulamadır.'),
                array('Bonding ile dolgu arasındaki fark nedir?', 'Dolgu genellikle çürük tedavisi amacıyla yapılır; bonding ise estetik amaçlı diş şekillendirme ve renk düzeltme işlemidir.'),
                array('Bonding kaç yıl dayanır?', 'Ağız bakımına ve beslenme alışkanlıklarına bağlı olarak genellikle birkaç yıl kullanılabilir; renklenme veya kırılma durumunda yenilenebilir.'),
                array('Bonding işlemi rahat mıdır?', 'Çoğu vakada diş kesimi gerektirmediği için genellikle rahat bir işlemdir ve tek seansta tamamlanabilir.'),
                array('Bonding hangi durumlarda uygulanır?', 'Diş arası boşluk, kırık veya çatlak diş, renk ve şekil bozuklukları ile kısa görünen dişlerde tercih edilebilir.'),
                array('Bonding fiyatı neye göre değişir?', 'İşlem yapılan diş sayısı, vakanın kapsamı ve kullanılan malzemeye göre değişir; net fiyat muayenede belirlenir.'),
            ),
            // --- Tedavi SAYFALARI (gece paketi, 18 Tem 2026) ---
            4204 => array( // implant-tedavisi
                array('İmplant tedavisi nedir?', 'İmplant, eksik dişlerin yerine çene kemiğine yerleştirilen titanyum vida üzerine protez diş uygulanmasıdır; komşu dişlere zarar vermeden kalıcı bir çözüm sunar.'),
                array('İmplant tedavisi kaç seansta tamamlanır?', 'Genellikle implantın yerleştirilmesi tek seansta yapılır; kemikle kaynaşma süreci (osseointegrasyon) 2-4 ay sürer ve ardından kalıcı protez takılır.'),
                array('İmplant sırasında ağrı olur mu?', 'İşlem lokal anestezi altında yapılır; çoğu hasta işlem sırasında ağrı hissetmez, sonrasındaki hafif hassasiyet hekimin önerdiği yöntemlerle kontrol edilir.'),
                array('Kemik yetersizse implant yapılabilir mi?', 'Kemik hacmi yetersiz olduğunda greft (kemik tozu) veya sinüs yükseltme gibi ek işlemlerle implant çoğu vakada mümkün olur; uygunluk muayene ve görüntülemeyle belirlenir.'),
                array('İmplant fiyatları neye göre belirlenir?', 'İmplant sayısı, kullanılan marka, kemik durumu ve ek işlem gereksinimine göre değişir; net planlama ücretsiz ilk değerlendirme sonrası yapılır.'),
            ),
            3607 => array( // ortodonti
                array('Ortodonti tedavisi hangi yaşta başlamalı?', 'İlk ortodontik kontrol için ideal yaş 7 civarıdır; tedavi ihtiyacı ve zamanlaması hekim tarafından çocuğun gelişimine göre planlanır. Yetişkinlerde ise yaş sınırı yoktur.'),
                array('Diş teli mi şeffaf plak mı daha iyi?', 'İkisi de etkilidir; seçim vakanın karmaşıklığına, yaşam tarzına ve estetik beklentiye göre yapılır. Muayenede her iki seçeneğin size uygunluğu değerlendirilir.'),
                array('Ortodonti tedavisi ne kadar sürer?', 'Vakaya göre değişmekle birlikte çoğu tedavi 6-24 ay aralığında tamamlanır; düzenli kontrollere gelmek süreyi kısaltır.'),
                array('Tedavi bittikten sonra dişler bozulur mu?', 'Tedavi sonrası pekiştirme (retainer) kullanımı önerilir; düzenli kullanıldığında dişlerin eski konumuna dönmesi büyük ölçüde engellenir.'),
                array('Ortodonti tedavisi sırasında ağrı olur mu?', 'Tel takıldıktan veya aktivasyondan sonraki ilk günlerde hafif baskı hissi normaldir; kısa sürede geçer ve günlük yaşamı engellemez.'),
            ),
            4846 => array( // kanal-tedavisi-endodonti
                array('Kanal tedavisi nedir, neden yapılır?', 'Dişin içindeki iltihaplı veya hasar görmüş sinir dokusunun temizlenip kanalların doldurulmasıdır; dişi çekilmekten kurtarmanın en etkili yoludur.'),
                array('Kanal tedavisi ağrılı mıdır?', 'Lokal anestezi altında yapıldığı için işlem sırasında ağrı hissedilmez; tedavi genellikle var olan diş ağrısını ortadan kaldırır.'),
                array('Kanal tedavisi kaç seans sürer?', 'Çoğu vaka tek seansta biter; iltihabın yoğun olduğu durumlarda 2-3 seans gerekebilir.'),
                array('Kanal tedavili diş ne kadar dayanır?', 'Doğru restorasyon ve iyi ağız bakımıyla kanal tedavili dişler uzun yıllar sorunsuz kullanılabilir.'),
                array('Kanal tedavisinden sonra nelere dikkat etmeliyim?', 'Kalıcı dolgu/kaplama tamamlanana kadar o bölgeyle sert gıda çiğnememek ve hekimin önerdiği kontrollere gelmek önemlidir.'),
            ),
            4857 => array( // gulus-tasarimi
                array('Gülüş tasarımı nedir?', 'Diş rengi, şekli, diş eti seviyesi ve yüz hatları birlikte değerlendirilerek kişiye özel estetik ve fonksiyonel bir gülüş planlanmasıdır.'),
                array('Gülüş tasarımında hangi işlemler uygulanır?', 'İhtiyaca göre beyazlatma, bonding, laminate/zirkonyum kaplama, diş eti düzenlemesi ve ortodontik düzeltmeler kombinlenebilir.'),
                array('Gülüş tasarımı kaç seans sürer?', 'Kapsama göre değişir; yalnız beyazlatma ve bonding içeren planlar birkaç seansta, kaplama içeren kapsamlı planlar birkaç haftada tamamlanabilir.'),
                array('Sonuç doğal görünür mü?', 'Amaç yüz hatlarınıza uyumlu, doğal bir görünümdür; tasarım aşamasında dijital önizleme ve prova ile birlikte karar verilir.'),
                array('Gülüş tasarımı fiyatı neye göre belirlenir?', 'Uygulanacak işlemlerin türü ve diş sayısına göre değişir; net plan ve bilgilendirme ücretsiz ilk muayenede yapılır.'),
            ),
            5635 => array( // cocuk-dis-hekimligi-pedodonti-istanbul
                array('Çocuğumu ilk kez ne zaman diş hekimine getirmeliyim?', 'İlk diş sürdükten sonra, en geç 1 yaş civarında ilk kontrol önerilir; erken tanışma çocuğun diş hekimi korkusu geliştirmesini önler.'),
                array('Süt dişleri nasılsa dökülecek, tedavi gerekli mi?', 'Gereklidir; süt dişlerindeki çürükler ağrıya, iltihaba ve kalıcı dişlerin yer/dizilim sorunlarına yol açabilir.'),
                array('Fissür örtücü ve flor uygulaması nedir?', 'Fissür örtücü azı dişlerinin girintilerini kapatarak, flor ise mineyi güçlendirerek çürüğü önleyen koruyucu uygulamalardır; ağrısızdır ve dakikalar içinde biter.'),
                array('Çocuğum diş hekiminden korkuyor, ne yapmalıyım?', 'Pedodonti yaklaşımımız çocuğun güvenini kazanmaya dayanır; kısa tanışma seansları ve oyunlaştırılmış anlatımla çoğu çocuk tedaviyi rahat tamamlar.'),
                array('Çocuklarda ortodontik kontrol ne zaman yapılmalı?', '7 yaş civarında ilk ortodontik değerlendirme önerilir; erken tespit edilen sorunlar daha basit yöntemlerle çözülebilir.'),
            ),
            4209 => array( // dis-beyazlatma-yontemleri
                array('Diş beyazlatma dişlere zarar verir mi?', 'Hekim kontrolünde uygulanan beyazlatma güvenlidir; mineye kalıcı zarar vermez. Geçici hassasiyet olabilir ve kısa sürede geçer.'),
                array('Beyazlatma etkisi ne kadar kalıcıdır?', 'Beslenme ve bakım alışkanlıklarına göre ortalama 6 ay-2 yıl sürer; kahve, çay ve sigara süreyi kısaltır.'),
                array('Ofis tipi ve ev tipi beyazlatma farkı nedir?', 'Ofis tipi klinikte tek seansta yapılır; ev tipinde kişiye özel plakla evde birkaç hafta uygulanır. İhtiyaca göre ikisi kombinlenebilir.'),
                array('Herkese beyazlatma yapılabilir mi?', 'Çürük ve diş eti sorunları önce tedavi edilmelidir; kaplama ve dolgular beyazlamaz. Uygunluk muayenede değerlendirilir.'),
                array('Beyazlatma sonrası nelere dikkat etmeliyim?', 'İlk 48 saat renkli içecek ve gıdalardan kaçınmak, düzenli fırçalama ve hekim kontrolleri sonucun kalıcılığını artırır.'),
            ),
            8886 => array( // protez-tedavisi
                array('Hangi protez türleri uygulanıyor?', 'Sabit protezler (kuron-köprü, implant üstü) ile hareketli protezler (total, bölümlü, hassas tutuculu) kliniğimizde uygulanmaktadır.'),
                array('İmplant üstü protez ile klasik protez farkı nedir?', 'İmplant üstü protezler çene kemiğine sabitlenen implantlardan destek alır; tutuculuğu ve çiğneme konforu klasik hareketli protezlere göre daha yüksektir.'),
                array('Protez yapımı kaç seans sürer?', 'Ölçü, prova ve teslim aşamalarıyla genellikle birkaç seansta tamamlanır; implant üstü protezlerde implant iyileşme süresi eklenir.'),
                array('Proteze alışmak ne kadar sürer?', 'İlk günlerde konuşma ve çiğnemede yadırgama normaldir; çoğu hasta birkaç hafta içinde tam uyum sağlar.'),
                array('Protez bakımı nasıl yapılır?', 'Hareketli protezler her gün özel fırça ile temizlenmeli, gece çıkarılmalı; sabit protezlerde diş ipi ve arayüz fırçası kullanımı önemlidir.'),
            ),
            8969 => array( // periodontoloji-dis-eti-tedavisi
                array('Diş eti kanaması neden olur?', 'En sık nedeni plak birikimine bağlı diş eti iltihabıdır (gingivitis); erken dönemde profesyonel temizlik ve doğru fırçalama ile geri döndürülebilir.'),
                array('Periodontitis nedir, neden önemlidir?', 'Tedavi edilmeyen diş eti iltihabının kemiğe ilerlemiş halidir; diş kaybının önde gelen nedenlerindendir ve erken tedaviyle durdurulabilir.'),
                array('Diş taşı temizliği dişlere zarar verir mi?', 'Vermez; diş taşları mineye değil üzerine yapışmıştır. Düzenli temizlik diş eti sağlığını korur, önerilen aralık genellikle 6 aydır.'),
                array('Diş eti çekilmesi tedavi edilebilir mi?', 'İlerlemesi durdurulabilir; uygun vakalarda diş eti operasyonlarıyla çekilen bölgeler kapatılabilir. Değerlendirme muayenede yapılır.'),
                array('Pembe estetik nedir?', 'Gülüşte diş etlerinin seviye, renk ve simetrisinin düzenlenmesidir; gülüş tasarımının önemli bir parçasıdır.'),
            ),
        );
    }
}
add_filter('the_content', function ($content) {
    if (!is_singular(array('post', 'page'))) return $content;
    $id = get_the_ID();
    $only = array(4204, 3607, 4846, 4857, 5635, 4209, 8886, 8969); // gorunur SSS yalniz tedavi sayfalarinda (postlarda SSS bloklarin icinde)
    if (!in_array($id, $only, true)) return $content;
    static $done = array();
    if (isset($done[$id])) return $content;
    $faqs = capa_faqs();
    if (empty($faqs[$id])) return $content;
    $done[$id] = true;
    $html = '<section class="capa-faq"><h2>Sık Sorulan Sorular</h2>';
    foreach ($faqs[$id] as $qa) {
        $html .= '<h3>' . esc_html($qa[0]) . '</h3><p>' . esc_html($qa[1]) . '</p>';
    }
    $html .= '</section>';
    return $content . $html;
}, 12);
add_action('wp_head', function () {
    if (!is_singular(array('post', 'page'))) return;
    $id = get_the_ID();
    $faqs = capa_faqs();
    if (empty($faqs[$id])) return;
    $items = array();
    foreach ($faqs[$id] as $qa) {
        $items[] = array('@type' => 'Question', 'name' => $qa[0], 'acceptedAnswer' => array('@type' => 'Answer', 'text' => $qa[1]));
    }
    $schema = array('@context' => 'https://schema.org', '@type' => 'FAQPage', 'mainEntity' => $items);
    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>';
}, 99);

// H1 hijyeni: protez (8886) + periodontoloji (8969) — blok kendi hero H1'ini
// veriyor; Hello temasinin entry-title basligini kapat (cift H1'i kaldirir).
add_filter('hello_elementor_page_title', function ($show) {
    if (is_page(array(8886, 8969))) {
        return false;
    }
    return $show;
});
