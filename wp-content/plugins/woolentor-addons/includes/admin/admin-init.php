<?php

if( ! defined( 'ABSPATH' ) ) exit(); // Exit if accessed directly

class Woolentor_Admin_Init{

    /**
     * Parent Menu Page Slug
     */
    const MENU_PAGE_SLUG = 'woolentor_page';

    /**
     * Menu capability
     */
    const MENU_CAPABILITY = 'manage_options';

    /**
     * [$parent_menu_hook] Parent Menu Hook
     * @var string
     */
    static $parent_menu_hook = '';
    
    /**
     * [$_instance]
     * @var null
     */
    private static $_instance = null;

    /**
     * [instance] Initializes a singleton instance
     * @return [Woolentor_Admin_Init]
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct(){
        $this->remove_all_notices();
        $this->include();
        $this->init();
    }

    /**
     * [init] Assets Initializes
     * @return [void]
     */
    public function init(){

        add_action( 'admin_menu', [ $this, 'add_menu' ], 25 );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
        Woolentor_Admin_Fields_Manager::instance()->init();

        add_action( 'admin_footer', [ $this, 'print_module_setting_popup' ], 99 );

        add_action( 'wp_ajax_woolentor_save_opt_data', [ $this, 'save_data' ] );
        add_action( 'wp_ajax_woolentor_module_data', [ $this, 'module_data' ] );

    }

    /**
     * [include] Load Necessary file
     * @return [void]
     */
    public function include(){
        require_once('include/admin_field-manager.php');
        require_once('include/admin_fields.php');
        if( is_plugin_active('woolentor-addons-pro/woolentor_addons_pro.php') && file_exists( WOOLENTOR_ADDONS_PL_PATH_PRO.'includes/admin/admin_fields.php' ) ){
            require_once WOOLENTOR_ADDONS_PL_PATH_PRO.'includes/admin/admin_fields.php';
        }
        require_once('include/template-library.php');
        require_once('include/class.extension-manager.php');
    }

    /**
     * [add_menu] Admin Menu
     */
    public function add_menu(){

        global $submenu;

        self::$parent_menu_hook = add_menu_page( 
            esc_html__( 'WooLentor', 'woolentor' ),
            esc_html__( 'WooLentor', 'woolentor' ), 
            self::MENU_CAPABILITY, 
            self::MENU_PAGE_SLUG, 
            NULL, 
            WOOLENTOR_ADDONS_PL_URL.'includes/admin/assets/images/menu-icon.png',
            100
        );

        add_submenu_page(
            'woolentor_page', 
            esc_html__( 'Settings', 'woolentor' ),
            esc_html__( 'Settings', 'woolentor' ), 
            'manage_options', 
            'woolentor', 
            [ $this, 'plugin_page' ] 
        );

        // Remove Parent Submenu
        remove_submenu_page( 'woolentor_page','woolentor_page' );
        

    }

    /**
     * [enqueue_scripts] Add Scripts Base Menu Slug
     * @param  [string] $hook
     * @return [void]
     */
    public function enqueue_scripts( $hook  ) {
        if( $hook === 'woolentor_page_woolentor' || $hook === 'woolentor_page_woolentor_templates' || $hook === 'woolentor_page_woolentor_extension'){
            wp_enqueue_style('woolentor-admin');
            wp_enqueue_script('woolentor-install-manager');

            wp_enqueue_style( 'simple-line-icons-wl' );
            wp_enqueue_style(
                'fonticonpicker',
                WOOLENTOR_ADDONS_PL_URL . 'assets/lib/iconpicker/css/jquery.fonticonpicker.min.css', 
                array(),
                WOOLENTOR_VERSION 
            );

            wp_enqueue_style(
                'fonticonpicker-bootstrap',
                WOOLENTOR_ADDONS_PL_URL . 'assets/lib/iconpicker/css/jquery.fonticonpicker.bootstrap.min.css', 
                array(),
                WOOLENTOR_VERSION 
            );

            // JS
            wp_enqueue_script( 
                'fonticonpicker', 
                WOOLENTOR_ADDONS_PL_URL . 'assets/lib/iconpicker/js/jquery.fonticonpicker.min.js', 
                array( 'jquery' ), 
                WOOLENTOR_VERSION, 
                TRUE
            );

            wp_enqueue_script( 
                'select2', 
                WOOLENTOR_ADDONS_PL_URL . 'includes/admin/assets/lib/js/select2.min.js', 
                array( 'jquery' ), 
                WOOLENTOR_VERSION, 
                TRUE
            );

            wp_enqueue_script('woolentor-admin-main');

            wp_localize_script( 
                'woolentor-admin-main', 
                'woolentor_fields', 
                [
                    'iconset' => Woolentor_Icon_List::icon_sets(),
                ]
            );

        }
    }

    /**
     * [load_template] Template load
     * @param  [string] $template template suffix
     * @return [void]
     */
    private static function load_template( $template ) {
        $tmp_file = WOOLENTOR_ADDONS_PL_PATH . 'includes/admin/templates/dashboard-' . $template . '.php';
        if ( file_exists( $tmp_file ) ) {
            include_once( $tmp_file );
        }
    }

    /**
     * [plugin_page] Load plugin page template
     * @return [void]
     */
    public function plugin_page(){
        ?>
        <div class="wrap woolentor-admin-wrapper">
            <div class="woolentor-admin-main-content">
                <?php self::load_template('navs'); ?>
                <div class="woolentor-admin-main-body">
                    <?php self::load_template('welcome'); ?>
                    <?php self::load_template('settings'); ?>
                    <?php self::load_template('element'); ?>
                    <?php self::load_template('renamelabel'); ?>
                    <?php self::load_template('sales-notification'); ?>
                    <?php self::load_template('style'); ?>
                    <?php self::load_template('module'); ?>
                    <?php self::load_template('extension'); ?>
                </div>
                <?php self::load_template('popup'); ?>
            </div>
            <?php self::load_template('sidebar'); ?>
            <?php Woolentor_Admin_Fields_Manager::instance()->script(); ?>
        </div>
        <?php
    }

    /**
     * [print_module_setting_popup] addmin_footer Callback
     * @return [void]
     */
    public function print_module_setting_popup(){
        $screen = get_current_screen();
        if ( 'woolentor_page_woolentor' == $screen->base ) {
            self::load_template('module-setting-popup');
        }
    }

    /**
     * [remove_all_notices] remove addmin notices
     * @return [void]
     */
    public function remove_all_notices(){
        add_action('in_admin_header', function (){
            $screen = get_current_screen();
            if ( 'woolentor_page_woolentor' == $screen->base ) {
                remove_all_actions('admin_notices');
                remove_all_actions('all_admin_notices');
            }
        }, 1000);
    }

    /**
     * [save_data] Wp Ajax Callback
     * @return [JSON|Null]
     */
    public function save_data(){

        if ( ! current_user_can( self::MENU_CAPABILITY ) ) {
            return;
        }

        check_ajax_referer( 'woolentor_save_opt_nonce', 'nonce' );

        $data     = ( !empty( $_POST['data'] ) ? woolentor_clean( $_POST['data'] ) : '' );
        $section  = ( !empty( $_POST['section'] ) ? sanitize_text_field( $_POST['section'] ) : '' );
        $fileds   = ( !empty( $_POST['fileds'] ) ? woolentor_clean( $_POST['fileds'] ) : '' );

        if( empty( $section ) || empty( $fileds ) ){
            return;
        }

        if( empty( $data ) ){
            $data = array();
        }

        if ( false == get_option( $section ) ) {
            add_option( $section );
        }
        
        $options_data  = [];

        foreach( $fileds as $field_key => $filed ){
            if ( array_key_exists( $filed, $data ) ) {
                $value = $data[$filed];
            }else{
                $value = Null;
            }
            $this->update_option( $section, $filed, $value );
        }

        wp_send_json_success([
            'message' => esc_html__( 'Data Saved successfully!', 'woolentor' ),
            'data' => $data,
            'savedata' => get_option('woolentor_elements_tabs'),
        ]);

    }

    /**
     * [update_option]
     * @return [void]
     */
    public function update_option( $section, $option_key, $new_value ){
        if( $new_value === Null ){ $new_value = ''; }
        $options_datad = is_array( get_option( $section ) ) ? get_option( $section ) : array();
        $options_datad[$option_key] = $new_value;
        update_option( $section, $options_datad );
    }

    /**
     * [module_data] Wp Ajax Callback
     * @return [JSON|Null]
     */
    public function module_data(){
        check_ajax_referer( 'woolentor_save_opt_nonce', 'nonce' );

        $subaction  = ( !empty( $_POST['subaction'] ) ? sanitize_text_field( $_POST['subaction'] ) : '' );
        $section    = ( !empty( $_POST['section'] ) ? sanitize_text_field( $_POST['section'] ) : '' );
        $fileds     = ( !empty( $_POST['fileds'] ) ? woolentor_clean( $_POST['fileds'] ) : '' );

        if( empty( $section ) || empty( $fileds ) ){
            return;
        }

        $response_content = $message = $element_keys = $field_html = '';
        if( $subaction === 'get_data' ){
            foreach( $fileds as $key => $field ){
                ob_start();
                Woolentor_Admin_Fields_Manager::instance()->add_field( $field, $section );
                $field_html .= ob_get_clean();
            }
            $message = esc_html__( 'Data Fetch successfully!', 'woolentor' );
            $response_content = $field_html;

            $element_keys = array_map( function( $field ){
                return $field['name'];
            }, $fileds );

        }

        wp_send_json_success([
            'message' => $message,
            'content' => $response_content,
            'fields'  => wp_json_encode( $element_keys )
        ]);

    }


}

Woolentor_Admin_Init::instance();