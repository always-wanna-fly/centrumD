<?php
/**
 * Plugin Name: Theme Options Manager
 * Description: A plugin for creating and managing fields on the settings page.
 * Version: 1.1
 * Text Domain: theme-options-manager
 */
namespace Plugin\Theme_Options_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Theme_Options_Manager
 * Handles the settings page for managing custom theme options.
 */
class Theme_Options_Manager {

	/**
	 *  Plugin domain
	 */
	const TEXT_DOMAIN = 'theme-options-manager';

	/**
	 * Option name for storing fields.
	 *
	 * @var string
	 */
	private $option_name = 'theme_options_manager_fields';

	/**
	 * Theme_Options_Manager constructor.
	 * Initializes actions for admin page creation and AJAX functionality.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_settings_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'wp_ajax_theme_options_manager_update_field', array( $this, 'update_field' ) );
		add_action( 'wp_ajax_theme_options_manager_delete_field', array( $this, 'delete_field' ) );
		add_action( 'wp_ajax_theme_options_manager_update_field_order', array( $this, 'update_field_order' ) );
	}

	/**
	 * Creates the settings page in the WordPress admin.
	 */
	public function create_settings_page() {
		add_options_page(
			__( 'Theme Options Manager', self::TEXT_DOMAIN ),
			__( 'Theme Options Manager', self::TEXT_DOMAIN ),
			'manage_options',
			'theme-options-manager',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Renders the HTML for the settings page.
	 */
	public function render_settings_page() {
		$fields = get_option( $this->option_name, array() );
		include plugin_dir_path( __FILE__ ) . 'templates/settings-page.php';
	}

	/**
	 * Enqueues required scripts and styles for the settings page.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		if ( $hook !== 'settings_page_theme-options-manager' ) { return; }
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'theme-options-manager-script', plugins_url( 'assets/theme-options-manager.js', __FILE__ ), array(
			'jquery',
			'jquery-ui-sortable'
		), null, true );
		wp_enqueue_style( 'theme-options-manager-style', plugins_url( 'assets/theme-options-manager.css', __FILE__ ) );
		wp_enqueue_script( 'inputmask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js' );
		wp_localize_script( 'theme-options-manager-script', 'themeOptionsManager', array( 'ajaxUrl' => admin_url( 'admin-ajax.php' ) ) );
	}

	/**
	 * Updates or adds a new field via AJAX.
	 */
	public function update_field() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions', self::TEXT_DOMAIN ) );
		}

		$fields = get_option( $this->option_name, array() );
		$index  = isset( $_POST['index'] ) ? intval( $_POST['index'] ) : null;

		// Add or update field
		$field_data = array(
			'name'  => !empty($_POST['name']) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '',
			'value' => sanitize_text_field( wp_unslash( $_POST['value'] ) ),
			'type'  => sanitize_text_field( wp_unslash( $_POST['type'] ) ),
		);

		if ( isset( $index ) && array_key_exists( $index, $fields ) ) {
			$fields[ $index ] = $field_data;
		} else {
			$fields[] = $field_data;
		}

		update_option( $this->option_name, $fields );
		wp_send_json_success();
	}

	/**
	 * Deletes a field via AJAX.
	 */
	public function delete_field() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions', self::TEXT_DOMAIN ) );
		}

		$fields = get_option( $this->option_name, array() );
		$index  = intval( $_POST['index'] );

		if ( isset( $fields[ $index ] ) ) {
			unset( $fields[ $index ] );
			update_option( $this->option_name, array_values( $fields ) ); // Re-index fields
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}

	/**
	 * Updates the order of fields via AJAX.
	 */
	public function update_field_order() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions', self::TEXT_DOMAIN ) );
		}

		$order  = isset( $_POST['order'] ) ? $_POST['order'] : [];
		$fields = get_option( $this->option_name, [] );

		// Reordering fields based on the new sequence
		$sorted_fields = [];
		foreach ( $order as $index ) {
			if ( isset( $fields[ $index ] ) ) {
				$sorted_fields[] = $fields[ $index ];
			}
		}

		update_option( $this->option_name, $sorted_fields );
		wp_send_json_success();
	}
}

new Theme_Options_Manager();
