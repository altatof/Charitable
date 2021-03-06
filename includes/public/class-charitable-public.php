<?php 
/**
 * Charitable Public class. 
 *
 * @package 	Charitable/Classes/Charitable_Public
 * @version     1.0.0
 * @author 		Eric Daams
 * @copyright 	Copyright (c) 2015, Studio 164a
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License  
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Charitable_Public' ) ) : 

/**
 * Charitable Public class. 
 *
 * @final
 * @since 	    1.0.0
 */
final class Charitable_Public {

    /**
     * The single instance of this class.  
     *
     * @var     Charitable_Public|null
     * @access  private
     * @static
     */
    private static $instance = null;    

    /**
     * Returns and/or create the single instance of this class.  
     *
     * @return  Charitable_Public
     * @access  public
     * @since   1.2.0
     */
    public static function get_instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new Charitable_Public();
        }

        return self::$instance;
    }

	/**
	 * Set up the class. 
	 *
	 * @access 	private
	 * @since 	1.0.0
	 */
	private function __construct() {				
        add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_scripts') );
        add_filter( 'post_class', array( $this, 'campaign_post_class' ) );

        /**
         * We are registering this object only for backwards compatibility. It
         * will be removed in or after Charitable 1.3.
         *
         * @deprecated
         */
        charitable()->register_object( Charitable_Session::get_instance() );
        charitable()->register_object( Charitable_Templates::get_instance() );

		do_action( 'charitable_public_start', $this );
	}    

	/**
	 * Loads public facing scripts and stylesheets. 
	 *
	 * @return 	void
	 * @access 	public
	 * @since 	1.0.0
	 */
	public function wp_enqueue_scripts() {						
		$vars = apply_filters( 'charitable_javascript_vars', array( 
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );

		wp_register_script( 'charitable-script', charitable()->get_path( 'assets', false ) . 'js/charitable.js', array( 'jquery' ), charitable()->get_version() );
        wp_localize_script( 'charitable-script', 'CHARITABLE_VARS', $vars );
        wp_enqueue_script( 'charitable-script' );

		wp_register_style( 'charitable-styles', charitable()->get_path( 'assets', false ) . 'css/charitable.css', array(), charitable()->get_version() );
		wp_enqueue_style( 'charitable-styles' );

		/* Lean Modal is registered but NOT enqueued yet. */
		if ( 'modal' == charitable_get_option( 'donation_form_display', 'separate_page' ) ) {
			wp_register_script( 'lean-modal', charitable()->get_path( 'assets', false ) . 'js/libraries/jquery.leanModal.js', array( 'jquery' ), charitable()->get_version() );
			wp_register_style( 'lean-modal-css', charitable()->get_path( 'assets', false ) . 'css/modal.css', array(), charitable()->get_version() );
		}
	}

    /**
     * Adds custom post classes when viewing campaign. 
     *
     * @return  string[] 
     * @access  public
     * @since   1.0.0
     */
    public function campaign_post_class( $classes ) {
        $campaign = charitable_get_current_campaign();

        if ( ! $campaign ) {
        	return $classes;
        }

        $classes[] = $campaign->has_goal() ? 'campaign-has-goal' : 'campaign-has-no-goal';
        $classes[] = $campaign->is_endless() ? 'campaign-is-endless' : 'campaign-has-end-date';
        return $classes;
    }

}

endif;