<?php
/**
 * Plugin Name: Theme Options Manager
 * Description: A plugin for creating and managing fields on the settings page.
 * Version: 1.1
 * Text Domain: theme-options-manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Theme_Options_Manager
 * Handles the settings page for managing custom theme options.
 */
class Theme_Options_Manager {

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
		add_action( 'wp_ajax_theme_options_manager_update_order', array( $this, 'update_order' ) );
	}

	/**
	 * Creates the settings page in the WordPress admin.
	 */
	public function create_settings_page() {
		add_options_page(
			__( 'Theme Options Manager', 'theme-options-manager' ),
			__( 'Theme Options Manager', 'theme-options-manager' ),
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
		?>
        <div class="wrap">
            <h1><?php _e( 'Theme Options Manager Settings', 'theme-options-manager' ); ?></h1>
            <table class="wp-list-table widefat fixed striped" id="fields-table">
                <thead>
                <tr>
                    <th><?php _e( 'Name', 'theme-options-manager' ); ?></th>
                    <th><?php _e( 'Value', 'theme-options-manager' ); ?></th>
                    <th><?php _e( 'Type', 'theme-options-manager' ); ?></th>
                    <th><?php _e( 'Actions', 'theme-options-manager' ); ?></th>
                </tr>
                </thead>
                <tbody id="sortable">
				<?php foreach ( $fields as $index => $field ) : ?>
                    <tr data-index="<?php echo $index; ?>">
                        <td class="handle">
                            <span class="dashicons dashicons-move"></span>
                            <span class="field-name"><?php echo esc_html( $field['name'] ); ?></span>
                            <input type="text" class="edit-name" value="<?php echo esc_attr( $field['name'] ); ?>"
                                   style="display: none;">
                        </td>
                        <td>
                            <span class="field-value"><?php echo esc_html( $field['value'] ); ?></span>
                            <input type="<?php echo $field['type'] === 'email' ? 'email' : 'text'; ?>"
                                   class="edit-value" value="<?php echo esc_attr( $field['value'] ); ?>"
                                   style="display: none;">
                        </td>
                        <td>
                            <span class="field-type"><?php echo esc_html( $field['type'] ); ?></span>
                            <select class="edit-type" style="display: none;">
                                <option value="text" <?php selected( $field['type'], 'text' ); ?>><?php _e( 'Text', 'theme-options-manager' ); ?></option>
                                <option value="phone" <?php selected( $field['type'], 'phone' ); ?>><?php _e( 'Phone Number', 'theme-options-manager' ); ?></option>
                                <option value="email" <?php selected( $field['type'], 'email' ); ?>><?php _e( 'Email', 'theme-options-manager' ); ?></option>
                                <option value="work_hours" <?php selected( $field['type'], 'work_hours' ); ?>><?php _e( 'Working Hours', 'theme-options-manager' ); ?></option>
                            </select>
                        </td>
                        <td>
                            <a href="#" class="button edit-field"><?php _e( 'Edit', 'theme-options-manager' ); ?></a>
                            <a href="#" class="button button-primary save-field"
                               style="display: none;"><?php _e( 'Save', 'theme-options-manager' ); ?></a>
                            <a href="#" class="button button-danger delete-field"
                               data-index="<?php echo $index; ?>"><?php _e( 'Delete', 'theme-options-manager' ); ?></a>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
            <h2><?php _e( 'Add New Field', 'theme-options-manager' ); ?></h2>
            <form id="add-field-form">
                <select id="new-field-type">
                    <option value="text"><?php _e( 'Text', 'theme-options-manager' ); ?></option>
                    <option value="phone"><?php _e( 'Phone Number', 'theme-options-manager' ); ?></option>
                    <option value="email"><?php _e( 'Email', 'theme-options-manager' ); ?></option>
                    <option value="work_hours"><?php _e( 'Working Hours', 'theme-options-manager' ); ?></option>
                </select>
                <input type="text" id="new-field-name" placeholder="<?php _e( 'Name', 'theme-options-manager' ); ?>"
                       required>
                <input type="text" id="new-field-value" placeholder="<?php _e( 'Value', 'theme-options-manager' ); ?>"
                       required>
                <button type="submit"
                        class="button button-primary"><?php _e( 'Add', 'theme-options-manager' ); ?></button>
            </form>
        </div>
		<?php
	}

	/**
	 * Enqueues required scripts and styles for the settings page.
	 *
	 * @param string $hook The current admin page hook.
	 */
	public function enqueue_scripts( $hook ) {
		if ( $hook !== 'settings_page_theme-options-manager' ) {
			return;
		}
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
			wp_die( __( 'Insufficient permissions', 'theme-options-manager' ) );
		}

		$fields = get_option( $this->option_name, array() );
		$index  = isset( $_POST['index'] ) ? intval( $_POST['index'] ) : null;

		// Add or update field
		$field_data = array(
			'name'  => sanitize_text_field( wp_unslash( $_POST['name'] ) ),
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
			wp_die( __( 'Insufficient permissions', 'theme-options-manager' ) );
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
	public function update_order() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'Insufficient permissions', 'theme-options-manager' ) );
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
