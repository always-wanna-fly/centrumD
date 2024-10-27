# Theme Options Manager

**Theme Options Manager** is a WordPress plugin that allows you to easily create and manage custom theme option fields through the admin panel. You can add fields with custom names and types, as well as change their order by simply dragging and dropping.

## Installation

1. Download the plugin.
2. Unzip the archive and upload the `theme-options-manager` folder to the `wp-content/plugins/` directory. Also, upload the `twentyseventeen` folder (if you do not have this default theme installed) and the `twentyseventeen-child` folder to the `wp-content/themes/` directory.
3. Log in to the WordPress admin panel and go to **Plugins**.
4. Find **Theme Options Manager** in the list and click **Activate**.
5. Also, go to **Appearance** > **Themes** and activate the **Twenty Seventeen Child** theme.

## Usage

1. After activating the plugin, go to **Settings** > **Theme Options** in the WordPress admin panel.
2. You will see a form for adding a new field:
   - **Field Name**: The name of the field you want to use.
   - **Field Value**: The value of the field.
   - **Field Type**: The type of the field (e.g., `Text` or `Number`).
3. Click the **Add Field** button to add a new field to the list.
4. Existing fields will be displayed in a table above. Use the corresponding buttons in the table row to edit or delete a field.
5. You can change the order of the fields by simply dragging them in the list. Once the order is changed, it is saved automatically.

## Displaying in a Custom Child Theme

You can display the added fields in the footer of your theme. To do this, add the following code to the `footer.php` file of your theme:

```php
<?php
$fields = get_option('theme_options_manager_fields', []);

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