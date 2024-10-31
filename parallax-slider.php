<?php
/*
Plugin Name: Parallax Slider
Plugin URI: https://tishonator.com/plugins/parallax-slider
Description: Responsive Horizontal Parallax Sliding Slider using Swiper.js.
Author: tishonator
Version: 1.0.0
Author URI: http://tishonator.com/
Contributors: tishonator
Text Domain: parallax-slider
*/

if ( !class_exists('tishonator_ps_ParallaxSliderPlugin') ) :

    /**
     * Register the plugin.
     *
     * Display the administration panel, insert JavaScript etc.
     */
    class tishonator_ps_ParallaxSliderPlugin {
        
    	/**
    	 * Instance object
    	 *
    	 * @var object
    	 * @see get_instance()
    	 */
    	protected static $instance = NULL;


        /**
         * Constructor
         */
        public function __construct() {}

        /**
         * Setup
         */
        public function setup() {

            if ( class_exists('tishonator_ParallaxSliderProPlugin') )
                return;

            register_deactivation_hook( __FILE__, array( &$this, 'deactivate' ) );


            if ( is_admin() ) { // admin actions

                add_action('admin_menu', array(&$this, 'add_admin_page'));

                add_action('admin_enqueue_scripts', array(&$this, 'admin_scripts'));
            }

            add_action( 'init', array(&$this, 'register_shortcode') );
        }

        public function register_shortcode() {

            add_shortcode( 'parallax-slider', array(&$this, 'display_shortcode') );
        }

        public function display_shortcode($atts) {

            $result = '';

            $options = get_option( 'parallax_slider_options' );
            
            if ( ! $options )
                return $result;

            $result .= '';

            $result .= '<div class="swiper-container main-slider loading"><div class="swiper-wrapper">';
            
            for ( $slideNumber = 1; $slideNumber <= 3; ++$slideNumber ) {

                $slideImage = array_key_exists('slide_' . $slideNumber . '_image', $options)
                                ? $options[ 'slide_' . $slideNumber . '_image' ] : '';

                if ( $slideImage ) :

                    $slideTitle = array_key_exists('slide_' . $slideNumber . '_title', $options)
                                ? $options[ 'slide_' . $slideNumber . '_title' ] : '';

                    $slideText = array_key_exists('slide_' . $slideNumber . '_text', $options)
                                ? $options[ 'slide_' . $slideNumber . '_text' ] : '';

                    $result .= '<div class="swiper-slide">';

                    $result .= '<figure class="slide-bgimg" style="background-image:url(' . "'" . esc_url($slideImage) . "'" . ');">';

                    $result .= '<img src="' . esc_url($slideImage) . '" class="entity-img" />';
                    
                    $result .= '</figure>';

                    $result .= '<div class="content">';

                    if ($slideTitle) {
                        $result .= '<p class="title">' . esc_attr($slideTitle) . '</p>';
                    }

                    if ($slideText) {
                       $result .= '<span class="caption">' . esc_attr($slideText) . '</span>';
                    }

                    $result .= '</div>';
                    $result .= '</div>'; // .swiper-slide
                    
                endif;
            }

            $result .= '</div>'; // .swiper-wrapper

            $result .= '<div class="swiper-button-prev swiper-button-white"></div>';
            $result .= '<div class="swiper-button-next swiper-button-white"></div>';

            $result .= '</div>'; // .swiper-container

            // Thumbnail navigation
            $result .= '<div class="swiper-container nav-slider loading">';
            $result .= '<div class="swiper-wrapper" role="navigation">';

            $numberOfSlides = 0;

            for ( $slideNumber = 1; $slideNumber <= 3; ++$slideNumber ) {

                $slideImage = array_key_exists('slide_' . $slideNumber . '_image', $options)
                                ? $options[ 'slide_' . $slideNumber . '_image' ] : '';

                if ( $slideImage ) :

                    $slideTitle = array_key_exists('slide_' . $slideNumber . '_title', $options)
                                ? $options[ 'slide_' . $slideNumber . '_title' ] : '';

                    $slideText = array_key_exists('slide_' . $slideNumber . '_text', $options)
                                ? $options[ 'slide_' . $slideNumber . '_text' ] : '';

                    $result .= '<div class="swiper-slide">';

                    $result .= '<figure class="slide-bgimg" style="background-image:url(' . "'" . esc_url($slideImage) . "'" . ');">';

                    $result .= '<img src="' . esc_url($slideImage) . '" class="entity-img" />';
                    
                    $result .= '</figure>';

                    $result .= '<div class="content"><p class="title">';

                    if ($slideTitle) {
                        $result .= esc_attr($slideTitle);
                    }

                    $result .= '</p></div>';
                    $result .= '</div>'; // .swiper-slide

                    ++$numberOfSlides;
                    
                endif;
            }

            $result .= '</div>'; // .swiper-container
            $result .= '</div>'; // .swiper-wrapper

            $result .= '<div style="clear:both;"></div>';

            // JS
            wp_register_script('parallax-swiper-js', plugins_url('js/swiper.js', __FILE__), array());
            
            wp_register_script('parallax-slider-js', plugins_url('js/parallax.js', __FILE__), array('parallax-swiper-js'));

            wp_enqueue_script('parallax-slider-js',
                    plugins_url('js/parallax.js', __FILE__), array() );

            $data = array( 'numberOfSlides' => $numberOfSlides > 2 ? ($numberOfSlides - 1) : 1 );

            wp_localize_script('parallax-slider-js', 'parallax_slider_options', $data);

             // CSS
            wp_register_style('tishonator_parallax_slider_swiper_css',
                plugins_url('css/swiper.css', __FILE__), true);

            wp_enqueue_style( 'tishonator_parallax_slider_swiper_css',
                plugins_url('css/swiper.css', __FILE__), array( ) );

            wp_register_style('tishonator_parallax_slider_parallaxslider_css',
                plugins_url('css/parallax-slider.css', __FILE__), true);

            wp_enqueue_style( 'tishonator_parallax_slider_parallaxslider_css',
                plugins_url('css/parallax-slider.css', __FILE__), array( ) );

            return $result;
        }

        public function admin_scripts($hook) {

            wp_enqueue_script('media-upload');
            wp_enqueue_script('thickbox');

            wp_register_script('tishonator_parallax_slider_upload_media',
                plugins_url('js/parallax-upload-media.js', __FILE__), array('jquery'));
            wp_enqueue_script('tishonator_parallax_slider_upload_media');

            wp_enqueue_style('thickbox');
        }

    	/**
    	 * Used to access the instance
         *
         * @return object - class instance
    	 */
    	public static function get_instance() {

    		if ( NULL === self::$instance ) {
                self::$instance = new self();
            }

    		return self::$instance;
    	}

        /**
         * Unregister plugin settings on deactivating the plugin
         */
        public function deactivate() {

            unregister_setting('parallax_slider', 'parallax_slider_options');
        }

        /** 
         * Print the Section text
         */
        public function print_section_info() {}

        public function admin_init_settings() {

            register_setting('parallax_slider', 'parallax_slider_options');

            // add separate sections for each of Sliders
            add_settings_section( 'parallax_slider_section',
                __( 'Slider Settings', 'parallax-slider' ),
                array(&$this, 'print_section_info'),
                'parallax_slider' );

            for ( $slideNumber = 1; $slideNumber <= 3; ++$slideNumber ) {

                // Slide Title
                add_settings_field(
                    'slide_' . $slideNumber . '_title',
                    sprintf( __( 'Slide %s Title', 'parallax-slider' ), $slideNumber ),
                    array(&$this, 'input_callback'),
                    'parallax_slider',
                    'parallax_slider_section',
                    [ 'label_for' => 'slide_' . $slideNumber . '_title',
                      'page' =>  'parallax_slider_options' ]
                );

                // Slide Text
                add_settings_field(
                    'slide_' . $slideNumber . '_text',
                    sprintf( __( 'Slide %s Text', 'parallax-slider' ), $slideNumber ),
                    array(&$this, 'textarea_callback'),
                    'parallax_slider',
                    'parallax_slider_section',
                    [ 'label_for' => 'slide_' . $slideNumber . '_text',
                      'page' =>  'parallax_slider_options' ]
                );

                // Slide Image
                add_settings_field(
                    'slide_' . $slideNumber . '_image',
                    sprintf( __( 'Slide %s Image', 'parallax-slider' ), $slideNumber ),
                    array(&$this, 'image_callback'),
                    'parallax_slider',
                    'parallax_slider_section',
                    [ 'label_for' => 'slide_' . $slideNumber . '_image',
                      'page' =>  'parallax_slider_options' ]
                );
            }
        }

        public function input_callback($args) {

            // get the value of the setting we've registered with register_setting()
            $options = get_option( $args['page'] );
 
            // output the field
            $fieldValue = ($options && $args['label_for'] && array_key_exists(esc_attr( $args['label_for'] ), $options))
                                ? $options[ esc_attr( $args['label_for'] ) ] : 
                                    (array_key_exists('default_val', $args) ? $args['default_val'] : '');
            ?>

            <input type="text" id="<?php echo $args['page'] . '[' . $args['label_for'] . ']'; ?>"
                name="<?php echo $args['page'] . '[' . $args['label_for'] . ']'; ?>" class="regular-text"
                value="<?php echo $fieldValue; ?>" />
<?php
        }

        public function image_callback($args) {

            // get the value of the setting we've registered with register_setting()
            $options = get_option( $args['page'] );
 
            // output the field

            $fieldValue = $options && $args['label_for'] && array_key_exists(esc_attr( $args['label_for'] ), $options)
                                ? $options[ esc_attr( $args['label_for'] ) ] : '';
            ?>

            <input type="text" id="<?php echo $args['page'] . '[' . $args['label_for'] . ']'; ?>"
                name="<?php echo $args['page'] . '[' . $args['label_for'] . ']'; ?>" class="regular-text"
                value="<?php echo esc_url($fieldValue); ?>" />
            <input class="upload_image_button button button-primary" type="button" value="Change Image" />

            <p><img class="slider-img-preview" <?php if ( $fieldValue ) : ?> src="<?php echo esc_url($fieldValue); ?>" <?php endif; ?> style="max-width:300px;height:auto;" /><p>

<?php         
        }

        public function textarea_callback($args) {

            // get the value of the setting we've registered with register_setting()
            $options = get_option( $args['page'] );
 
            // output the field

            $fieldValue = $options && $args['label_for'] && array_key_exists(esc_attr( $args['label_for'] ), $options)
                                ? $options[ esc_attr( $args['label_for'] ) ] : '';
            ?>

            <textarea id="<?php echo $args['page'] . '[' . $args['label_for'] . ']'; ?>"
                name = "<?php echo $args['page'] . '[' . $args['label_for'] . ']'; ?>"
                rows="10" cols="39"><?php echo esc_attr($fieldValue); ?></textarea>
<?php
        }

        public function add_admin_page() {

            add_menu_page( __('Parallax Slider Settings', 'parallax-slider'),
                __('Parallax Slider', 'parallax-slider'), 'manage_options',
                'parallax-slider.php', array(&$this, 'show_settings'),
                'dashicons-format-gallery', 6 );

            //call register settings function
            add_action( 'admin_init', array(&$this, 'admin_init_settings') );
        }

        /**
         * Display the settings page.
         */
        public function show_settings() { ?>

            <div class="wrap">
                <div id="icon-options-general" class="icon32"></div>

                <div class="notice notice-info"> 
                    <p><strong><?php _e('Upgrade to ParallaxSliderPro Plugin', 'parallax-slider'); ?>:</strong></p>
                    <ol>
                        <li><?php _e('Configure Up to 10 Different Sliders', 'parallax-slider'); ?></li>
                        <li><?php _e('Insert Up to 10 Slides per Slider', 'parallax-slider'); ?></li>
                        <li><?php _e('Color Options: Title and Text, Prev/Next Links', 'parallax-slider'); ?></li>
                        <li><?php _e('Sliding Settings: Speed Duration', 'parallax-slider'); ?></li>
                    </ol>
                    <a href="https://tishonator.com/plugins/parallax-slider" class="button-primary">
                        <?php _e('Upgrade to Parallax Slider PRO Plugin', 'parallax-slider'); ?>
                    </a>
                    <p></p>
                </div>


                <h2><?php _e('Parallax Slider Settings', 'parallax-slider'); ?></h2>

                <form action="options.php" method="post">
                <?php settings_fields('parallax_slider'); ?>
                <?php do_settings_sections('parallax_slider'); ?>

                <h3>
                  Usage
                </h3>
                <p>
                    <?php _e('Use the shortcode', 'parallax-slider'); ?> <code>[parallax-slider]</code> <?php echo _e( 'to display Slider to any page or post.', 'parallax-slider' ); ?>
                </p>

                <?php submit_button(); ?>
              </form>
            </div>
    <?php
        }
    }

endif; // tishonator_ps_ParallaxSliderPlugin

add_action('plugins_loaded', array( tishonator_ps_ParallaxSliderPlugin::get_instance(), 'setup' ), 10);
