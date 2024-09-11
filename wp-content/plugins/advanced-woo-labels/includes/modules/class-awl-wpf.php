<?php

/**
 * AWL Product Filter by WBW plugin support
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (!class_exists('AWL_WPF')) :

    /**
     * Class for main plugin functions
     */
    class AWL_WPF {

        /**
         * @var AWL_WPF The single instance of the class
         */
        protected static $_instance = null;

        /**
         * Main AWL_WPF Instance
         *
         * Ensures only one instance of AWL_WPF is loaded or can be loaded.
         *
         * @static
         * @return AWL_WPF - Main instance
         */
        public static function instance()
        {
            if (is_null(self::$_instance)) {
                self::$_instance = new self();
            }
            return self::$_instance;
        }

        /**
         * Constructor
         */
        public function __construct() {

            add_action( 'woocommerce_product_query', array( $this, 'woocommerce_product_query' ), 999 );

        }

        /*
         * Fix query to get all available plugin labels
         */
        function woocommerce_product_query( $q ) {
            if ( $q->query_vars && isset( $q->query_vars['_is_awl_query'] ) && $q->query_vars['_is_awl_query'] ) {
                $q->set( 'post_type', 'awl-labels' );
            }
        }

    }

endif;

AWL_WPF::instance();