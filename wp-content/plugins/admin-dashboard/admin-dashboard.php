<?php
/*
Plugin Name: Admin Dashboard
Description: A plugin for creating and managing fields on the settings page.
Version: 1.1
*/

if (!defined('ABSPATH')) exit;

class AdminDashboard {
	private $option_name = 'admin_dashboard_fields';

	public function __construct() {
		add_action('admin_menu', [$this, 'create_settings_page']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
		add_action('wp_ajax_admin_dashboard_update_field', [$this, 'update_field']);
		add_action('wp_ajax_admin_dashboard_delete_field', [$this, 'delete_field']);
		add_action('wp_ajax_admin_dashboard_update_order', [$this, 'update_order']);
	}

	public function create_settings_page() {
		add_options_page(
			'Admin Dashboard',
			'Admin Dashboard',
			'manage_options',
			'admin-dashboard',
			[$this, 'render_settings_page']
		);
	}

	public function render_settings_page() {
		$fields = get_option($this->option_name, []);
		?>
        <div class="wrap">
            <h1>Admin Dashboard Settings</h1>
            <table class="wp-list-table widefat fixed striped" id="fields-table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Value</th>
                    <th>Type</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody id="sortable">
				<?php foreach ($fields as $index => $field) : ?>
                    <tr data-index="<?php echo $index; ?>">
                        <td class="handle">
                            <span class="dashicons dashicons-move"></span>
                            <span class="field-name"><?php echo esc_html($field['name']); ?></span>
                            <input type="text" class="edit-name" value="<?php echo esc_attr($field['name']); ?>" style="display: none;">
                        </td>
                        <td>
                            <span class="field-value"><?php echo esc_html($field['value']); ?></span>
                            <input type="<?php echo $field['type'] === 'email' ? 'email' : 'text'; ?>" class="edit-value" value="<?php echo esc_attr($field['value']); ?>" style="display: none;">
                        </td>
                        <td>
                            <span class="field-type"><?php echo esc_html($field['type']); ?></span>
                            <select class="edit-type" style="display: none;">
                                <option value="text" <?php selected($field['type'], 'text'); ?>>Text</option>
                                <option value="phone" <?php selected($field['type'], 'phone'); ?>>Phone Number</option>
                                <option value="email" <?php selected($field['type'], 'email'); ?>>Email</option>
                                <option value="work_hours" <?php selected($field['type'], 'work_hours'); ?>>Working Hours</option>
                            </select>
                        </td>
                        <td>
                            <a href="#" class="button edit-field">Edit</a>
                            <a href="#" class="button button-primary save-field" style="display: none;">Save</a>
                            <a href="#" class="button button-danger delete-field" data-index="<?php echo $index; ?>">Delete</a>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
            <h2>Add New Field</h2>
            <form id="add-field-form">
                <select id="new-field-type">
                    <option value="text">Text</option>
                    <option value="phone">Phone Number</option>
                    <option value="email">Email</option>
                    <option value="work_hours">Working Hours</option>
                </select>
                <input type="text" id="new-field-name" placeholder="Name" required>
                <input type="text" id="new-field-value" placeholder="Value" required>
                <button type="submit" class="button button-primary">Add</button>
            </form>
        </div>
		<?php
	}

	public function enqueue_scripts($hook) {
		if ($hook !== 'settings_page_admin-dashboard') return;
		wp_enqueue_script('jquery');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('admin-dashboard-script', plugins_url('src/admin-dashboard.js', __FILE__), ['jquery', 'jquery-ui-sortable'], null, true);
		wp_enqueue_style('admin-dashboard-style', plugins_url('src/admin-dashboard.css', __FILE__));
		wp_enqueue_script('inputmask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.8/jquery.inputmask.min.js');
		wp_localize_script('admin-dashboard-script', 'adminDashboard', ['ajaxUrl' => admin_url('admin-ajax.php')]);
	}

	public function update_field() {
		if (!current_user_can('manage_options')) wp_send_json_error();

		$fields = get_option($this->option_name, []);
		$index = isset($_POST['index']) ? intval($_POST['index']) : null;

		// Check whether to add a new field or update an existing one
		if ($index !== null && array_key_exists($index, $fields)) {
			// Updating an existing field
			$fields[$index] = [
				'name' => sanitize_text_field($_POST['name']),
				'value' => sanitize_text_field($_POST['value']),
				'type' => sanitize_text_field($_POST['type'])
			];
		} else {
			// Adding a new field
			$fields[] = [
				'name' => sanitize_text_field($_POST['name']),
				'value' => sanitize_text_field($_POST['value']),
				'type' => sanitize_text_field($_POST['type'])
			];
		}

		update_option($this->option_name, $fields);
		wp_send_json_success();
	}

	public function delete_field() {
		if (!current_user_can('manage_options')) wp_send_json_error();

		$fields = get_option($this->option_name, []);
		$index = intval($_POST['index']);
		unset($fields[$index]);
		update_option($this->option_name, array_values($fields)); // Re-indexing
		wp_send_json_success();
	}

	public function update_order() {
		if (!current_user_can('manage_options')) wp_send_json_error();

		$order = isset($_POST['order']) ? $_POST['order'] : [];
		$fields = get_option($this->option_name, []);

		// Reordering fields based on the new sequence
		$sorted_fields = [];
		foreach ($order as $index) {
			if (isset($fields[$index])) {
				$sorted_fields[] = $fields[$index];
			}
		}

		update_option($this->option_name, $sorted_fields);
		wp_send_json_success();
	}
}

new AdminDashboard();
