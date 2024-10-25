<?php
$fields = get_option('admin_dashboard_fields', []);

if (!empty($fields)) {
	echo '<div class="custom-fields">';
	foreach ($fields as $field) {
		echo '<div class="field">';
		echo '<strong>' . esc_html($field['name']) . ':</strong> ';
		echo esc_html($field['value']);
		echo '</div>';
	}
	echo '</div>';
}
?>
