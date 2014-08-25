<?php

/**
 * Conversion tracking integration class
 *
 * @author Tareq Hasan
 */
class WeDevs_WC_Tracking_Integration extends WC_Integration {

    function __construct() {

        $this->id = 'wc_fb_pixel';
        $this->method_title = __( 'FB Tracking Pixel', 'wc-fb-pixel' );
        $this->method_description = __( 'Facebook conversion tracking pixel integration. Insert your pixel ids here:', 'wc-fb-pixel' );

        // Load the settings.
        $this->init_form_fields();
        $this->init_settings();

        add_action( 'woocommerce_update_options_integration', array($this, 'process_admin_options') );

        add_action( 'wp_head', array($this, 'code_handler') );
        add_action( 'woocommerce_thankyou', array($this, 'checkout_tracking') );
        add_action( 'woocommerce_after_main_content', array($this, 'single_product_tracking') );
    }

    /**
     * WooCommerce settings API fields for storing our codes
     *
     * @return void
     */
    function init_form_fields() {
        $this->form_fields = array(
            'website' => array(
                'title' => __( 'Website pixel id', 'wc-fb-pixel' ),
                'description' => __( 'Pixel id to track page views', 'wc-fb-pixel' ),
                'desc_tip' => true,
                'id' => 'website',
                'type' => 'text',
            ),
            'currency' => array(
                'title' => __( 'Currency', 'wc-fb-pixel' ),
                'description' => __( 'Select event tracking currency', 'wc-fb-pixel' ),
                'desc_tip' => true,
                'id' => 'currency',
                'type' => 'select',
                'options' => array(
                    'SGD' => __( 'Singapore dollar', 'wc-fb-pixel' ),
                    'RON' => __( 'Romanian new leu', 'wc-fb-pixel' ),
                    'EUR' => __( 'Euro', 'wc-fb-pixel' ),
                    'TRY' => __( 'Turkish lira', 'wc-fb-pixel' ),
                    'SEK' => __( 'Swedish krona', 'wc-fb-pixel' ),
                    'ZAR' => __( 'South African rand', 'wc-fb-pixel' ),
                    'HKD' => __( 'Hong Kong dollar', 'wc-fb-pixel' ),
                    'CHF' => __( 'Swiss franc', 'wc-fb-pixel' ),
                    'NIO' => __( 'Nicaraguan córdoba', 'wc-fb-pixel' ),
                    'JPY' => __( 'Japanese yen', 'wc-fb-pixel' ),
                    'ISK' => __( 'Icelandic króna', 'wc-fb-pixel' ),
                    'TWD' => __( 'New Taiwan dollar', 'wc-fb-pixel' ),
                    'NZD' => __( 'New Zealand dollar', 'wc-fb-pixel' ),
                    'CZK' => __( 'Czech koruna', 'wc-fb-pixel' ),
                    'AUD' => __( 'Australian dollar', 'wc-fb-pixel' ),
                    'THB' => __( 'Thai baht', 'wc-fb-pixel' ),
                    'BOB' => __( 'Boliviano', 'wc-fb-pixel' ),
                    'BRL' => __( 'Brazilian real', 'wc-fb-pixel' ),
                    'MXN' => __( 'Mexican peso', 'wc-fb-pixel' ),
                    'USD' => __( 'United States dollar', 'wc-fb-pixel' ),
                    'ILS' => __( 'Israeli new shekel', 'wc-fb-pixel' ),
                    'HNL' => __( 'Honduran lempira', 'wc-fb-pixel' ),
                    'MOP' => __( 'Macanese pataca', 'wc-fb-pixel' ),
                    'COP' => __( 'Colombian peso', 'wc-fb-pixel' ),
                    'UYU' => __( 'Uruguayan peso', 'wc-fb-pixel' ),
                    'CRC' => __( 'Costa Rican colon', 'wc-fb-pixel' ),
                    'DKK' => __( 'Danish krone', 'wc-fb-pixel' ),
                    'QAR' => __( 'Qatari riyal', 'wc-fb-pixel' ),
                    'PYG' => __( 'Paraguayan guaraní', 'wc-fb-pixel' ),
                    'CAD' => __( 'Canadian dollar', 'wc-fb-pixel' ),
                    'INR' => __( 'Indian rupee', 'wc-fb-pixel' ),
                    'KRW' => __( 'South Korean won', 'wc-fb-pixel' ),
                    'GTQ' => __( 'Guatemalan quetzal', 'wc-fb-pixel' ),
                    'AED' => __( 'United Arab Emirates dirham', 'wc-fb-pixel' ),
                    'VEF' => __( 'Venezuelan bolívar fuerte', 'wc-fb-pixel' ),
                    'SAR' => __( 'Saudi riyal', 'wc-fb-pixel' ),
                    'NOK' => __( 'Norwegian krone', 'wc-fb-pixel' ),
                    'CNY' => __( 'Chinese yuan', 'wc-fb-pixel' ),
                    'ARS' => __( 'Argentine peso', 'wc-fb-pixel' ),
                    'PLN' => __( 'Polish złoty', 'wc-fb-pixel' ),
                    'GBP' => __( 'Pound sterling', 'wc-fb-pixel' ),
                    'PEN' => __( 'Peruvian nuevo sol', 'wc-fb-pixel' ),
                    'PHP' => __( 'Philippine peso', 'wc-fb-pixel' ),
                    'VND' => __( 'Vietnamese dong', 'wc-fb-pixel' ),
                    'RUB' => __( 'Russian rouble', 'wc-fb-pixel' ),
                    'HUF' => __( 'Hungarian forint', 'wc-fb-pixel' ),
                    'MYR' => __( 'Malaysian ringgit', 'wc-fb-pixel' ),
                    'CLP' => __( 'Chilean peso', 'wc-fb-pixel' ),
                    'IDR' => __( 'Indonesian rupiah', 'wc-fb-pixel' ),
                )
            ),
            'checkout' => array(
                'title' => __( 'Checkout pixel id', 'wc-fb-pixel' ),
                'description' => __( 'Pixel id to track checkouts', 'wc-fb-pixel' ),
                'desc_tip' => true,
                'id' => 'checkout',
                'type' => 'text',
            ),
            'product' => array(
                'title' => __( 'Product pixel id', 'wc-fb-pixel' ),
                'description' => __( 'Pixel id to track product page views', 'wc-fb-pixel' ),
                'desc_tip' => true,
                'id' => 'product',
                'type' => 'text',
            ),
            'add-to-cart' => array(
                'title' => __( 'Add to cart pixel id', 'wc-fb-pixel' ),
                'description' => __( 'Pixel id to track add to cart actions', 'wc-fb-pixel' ),
                'desc_tip' => true,
                'id' => 'add-to-cart',
                'type' => 'text',
            ),
        );
    }

    /**
     * Code print handler on HEAD tag
     *
     * It prints conversion tracking pixels on cart page, order received page
     * and single product page
     *
     * @uses wp_head
     * @return void
     */
    function code_handler() {
        echo "<!-- Tracking pixel by WooCommerce FB Pixel plugin -->\n";
        echo <<<EOSTR
<script>(function() {
  var _fbq = window._fbq || (window._fbq = []);
  if (!_fbq.loaded) {
    var fbds = document.createElement('script');
    fbds.async = true;
    fbds.src = '//connect.facebook.net/en_US/fbds.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(fbds, s);
    _fbq.loaded = true;
  }
  _fbq.push(['addPixelId', '{$this->get_option( 'website' )}']);
})();
window._fbq = window._fbq || [];
window._fbq.push(['track', 'PixelInitialized', {'referrer': document.referrer, 'user_agent': navigator.userAgent, 'language': navigator.language}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id={$this->get_option( 'website' )}&amp;ev=NoScript" /></noscript>
EOSTR;
        echo "\n<!-- Tracking pixel by WooCommerce FB Pixel plugin -->\n";
    }

    function checkout_tracking() {
        global $order;

        if ( !in_array( $order->status, array( 'failed' ) ) ) {
            echo "<!-- Tracking pixel by WooCommerce FB Pixel plugin -->\n";
            $code = <<<EOSTR
<script>
window._fbq.push(['track', '{$this->get_option( 'checkout' )}', {'value':'{$order->get_total()}','currency':'{$this->get_option( 'currency' )}'}]);
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev={$this->get_option( 'checkout' )}&amp;cd[value]={$order->get_total()}&amp;cd[currency]={$this->get_option( 'currency' )}&amp;noscript=1" /></noscript>
EOSTR;
            echo "\n<!-- Tracking pixel by WooCommerce FB Pixel plugin -->\n";
        }
    }

    function single_product_tracking() {
        global $product;

        if ( is_product() ) {
            echo "<!-- Tracking pixel by WooCommerce FB Pixel plugin -->\n";
            echo <<<EOSTR
<script>
window._fbq.push(['track', '{$this->get_option( 'product' )}', {'value':'0','price':'{$product->get_price()}','currency':'{$this->get_option( 'currency' )}','sku':'{$product->get_sku()}'}]);

try {
  jQuery(document).ready(function() {
    jQuery('form.cart').submit(function() {
      try {
        var quantity = parseInt(jQuery('form.cart input[name=quantity]').val(), 10);
        var variation_id = jQuery('form.cart input[name=variation_id]').val();
        var variations_found = jQuery('form.cart').data('product_variations').filter(function(variation) {
          return variation.variation_id == variation_id;
        });
        if (variations_found.length > 0) {
          var price_match = variations_found[0].price_html.match(/\d+.\d+/);
          if (price_match.length > 0) {
            var price = parseFloat(price_match[0]);
            window._fbq.push(['track', '{$this->get_option( 'add-to-cart' )}', {'value':0,'price':quantity*price,'currency':'{$this->get_option( 'currency' )}','variation_id':variation_id}]);
          }
        }
      } catch(e) {
        window._fbq.push(['track', '{$this->get_option( 'add-to-cart' )}', {'value':0,'price':'{$product->get_price()}','currency':'{$this->get_option( 'currency' )}'}]);
      }
    });
  });
} catch(e) {
  // NOOP
}
</script>
<noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev={$this->get_option( 'product' )}&amp;cd[value]=0&amp;cd[price]={$product->get_price()}&amp;cd[currency]={$this->get_option( 'currency' )}&amp;cd[sku]={$product->get_sku()}&amp;noscript=1" /></noscript>
EOSTR;
            echo "\n<!-- Tracking pixel by WooCommerce FB Pixel plugin -->\n";
        }
    }

}
