<?php
/**
 * The Base
 * The base class for all the classes
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( ! class_exists( 'WPS_Base' ) ):

/**
 * Base Class
 */
class WPS_Base {

	/**
	 * Admin page ID
	 * @var string
	 */
	public $id;

	/**
	 * Add action
	 *
	 * @see add_action
	 */
	protected function add_action( $hook, $func, $priority = 10, $args = 1 ) {
		add_action( $hook, array( &$this, $func ), $priority, $args );
	}

	/**
	 * Add filter
	 *
	 * @see add_filter
	 */
	protected function add_filter( $hook, $func, $priority = 10, $args = 1 ) {
		add_filter( $hook, array( &$this, $func ), $priority, $args );
	}

	/**
	 * Remove Action
	 *
	 * @see remove_action
	 */
	protected function remove_action( $hook, $func, $priority = 10, $args = 1 ) {
		remove_action( $hook, array( &$this, $func ), $priority, $args );
    }

	/**
	 * Remove filter
	 *
	 * @see remove_filter
	 */
	protected function remove_filter( $hook, $func, $priority = 10, $args = 1 ) {
		remove_filter( $hook, array( &$this, $func ), $priority, $args );
    }

	/**
	 * Inject config into class
	 *
	 * @param  array  $config
	 * @return void
	 */
    protected function config( $config = array() ) {

        // check
        if( empty( $config ) ) {
            return;
        }

        foreach( $config as $key => $value ) {
            $this->$key = $value;
        }
    }

	/**
	 * Is current page equals this
	 *
	 * @return boolean
	 */
	protected function is_current_page() {
        $page = isset( $_GET['page'] ) && !empty( $_GET['page'] ) ? $_GET['page'] : false;
        return $page === $this->id;
    }
}

endif;
