<?php
use Plugin\Theme_Options_Manager\Theme_Options_Manager;
?>
<div class="wrap">
	<h1><?php _e( 'Theme Options Manager - Settings', 'theme-options-manager' ); ?></h1>
	<table class="wp-list-table widefat fixed striped" id="fields-table">
		<thead>
		<tr>
			<th><?php _e( 'Name', Theme_Options_Manager::TEXT_DOMAIN ); ?></th>
			<th><?php _e( 'Value', Theme_Options_Manager::TEXT_DOMAIN ); ?></th>
			<th><?php _e( 'Type', Theme_Options_Manager::TEXT_DOMAIN ); ?></th>
			<th><?php _e( 'Actions', Theme_Options_Manager::TEXT_DOMAIN ); ?></th>
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
						<option value="text" <?php selected( $field['type'], 'text' ); ?>><?php _e( 'Text', Theme_Options_Manager::TEXT_DOMAIN ); ?></option>
						<option value="phone" <?php selected( $field['type'], 'phone' ); ?>><?php _e( 'Phone Number', Theme_Options_Manager::TEXT_DOMAIN ); ?></option>
						<option value="email" <?php selected( $field['type'], 'email' ); ?>><?php _e( 'Email', Theme_Options_Manager::TEXT_DOMAIN ); ?></option>
						<option value="work_hours" <?php selected( $field['type'], 'work_hours' ); ?>><?php _e( 'Working Hours', Theme_Options_Manager::TEXT_DOMAIN ); ?></option>
					</select>
				</td>
				<td>
					<a href="#" class="button edit-field"><?php _e( 'Edit', Theme_Options_Manager::TEXT_DOMAIN ); ?></a>
					<a href="#" class="button button-primary save-field" style="display: none;"><?php _e( 'Save', Theme_Options_Manager::TEXT_DOMAIN ); ?></a>
					<a href="#" class="button button-danger delete-field" data-index="<?php echo $index; ?>"><?php _e( 'Delete', Theme_Options_Manager::TEXT_DOMAIN ); ?></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<h2><?php _e( 'Add New Field', Theme_Options_Manager::TEXT_DOMAIN ); ?></h2>
	<form id="add-field-form">
		<select id="new-field-type">
			<option value="text"><?php _e( 'Text', Theme_Options_Manager::TEXT_DOMAIN ); ?></option>
			<option value="phone"><?php _e( 'Phone Number', Theme_Options_Manager::TEXT_DOMAIN ); ?></option>
			<option value="email"><?php _e( 'Email', Theme_Options_Manager::TEXT_DOMAIN ); ?></option>
			<option value="work_hours"><?php _e( 'Working Hours', Theme_Options_Manager::TEXT_DOMAIN ); ?></option>
		</select>
		<input type="text" id="new-field-name" placeholder="<?php _e( 'Name', Theme_Options_Manager::TEXT_DOMAIN ); ?>">
		<input type="text" id="new-field-value" placeholder="<?php _e( 'Value', Theme_Options_Manager::TEXT_DOMAIN ); ?>" required>
		<button type="submit" class="button button-primary"><?php _e( 'Add', Theme_Options_Manager::TEXT_DOMAIN ); ?></button>
	</form>
</div>
