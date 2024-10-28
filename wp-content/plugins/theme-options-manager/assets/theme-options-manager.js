jQuery(document).ready(function ($) {
    // Add function to set mask
    function updateMask() {
        const type = $('#new-field-type').val();
        const $valueField = $('#new-field-value');

        // First, remove all previous masks
        $valueField.inputmask('remove');

        // Set mask depending on type
        if (type === 'phone') {
            $valueField.inputmask({
                mask: "+380 (99) 999-99-99",
                placeholder: "_",
                clearIncomplete: true
            });
        } else if (type === 'email') {
            $valueField.inputmask({
                alias: "email",
                placeholder: "_"
            });
        } else if (type === 'work_hours') {
            $valueField.inputmask({
                mask: "99:99 - 99:99",
                placeholder: "HH:MM - HH:MM",
                clearIncomplete: true
            });
        } else {
            $valueField.inputmask('remove');
        }
    }

    // Call the updateMask() function when changing the type
    $('#new-field-type').on('change', function () {
        updateMask();
    });

    // Call updateMask() when loading the page for the initial mask
    updateMask();

    // Validation email and phone
    function validateEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }

    function validatePhone(phone) {
        const regex = /^\+380 \(\d{2}\) \d{3}-\d{2}-\d{2}$/;
        return regex.test(phone);
    }

    function validateWorkHours(hours) {
        const regex = /^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9] - (0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/;
        return regex.test(hours);
    }

    // Error message
    function showAlert(message) {
        alert(message);
    }

    // Adding a new field
    $('#add-field-form').on('submit', function (e) {
        e.preventDefault();
        const name = $('#new-field-name').val().trim();
        const value = $('#new-field-value').val().trim();
        const type = $('#new-field-type').val();

        // Validate values
        if (!value) {
            showAlert("Please fill in the field value.");
            return;
        }
        if (type === 'email' && !validateEmail(value)) {
            showAlert("Please enter a valid email address.");
            return;
        }
        if (type === 'phone' && !validatePhone(value)) {
            showAlert("Please enter a valid phone number.");
            return;
        }
        if (type === 'work_hours' && !validateWorkHours(value)) {
            showAlert("Please enter a valid work hours format (HH:MM - HH:MM).");
            return;
        }

        const fields = {action: 'theme_options_manager_update_field', name, value, type};
        $.post(themeOptionsManager.ajaxUrl, fields).done(function () {
            location.reload();
        }).fail(function () {
            showAlert("Error adding field.");
        });
    });

    // Editing a field
    $('.edit-field').on('click', function (e) {
        e.preventDefault();
        const row = $(this).closest('tr');
        row.find('.field-name, .field-value, .field-type').hide();
        row.find('.edit-name, .edit-value, .edit-type, .save-field').show();
        $(this).hide();
    });

    // Saving changes
    $('.save-field').on('click', function (e) {
        e.preventDefault();
        const row = $(this).closest('tr');
        const index = row.data('index');
        const name = row.find('.edit-name').val().trim();
        const value = row.find('.edit-value').val().trim();
        const type = row.find('.edit-type').val();

        // Validate values
        if (!name || !value) {
            showAlert("Please fill in all fields.");
            return;
        }
        if (type === 'email' && !validateEmail(value)) {
            showAlert("Please enter a valid email address.");
            return;
        }
        if (type === 'phone' && !validatePhone(value)) {
            showAlert("Please enter a valid phone number.");
            return;
        }

        $.post(themeOptionsManager.ajaxUrl, {
            action: 'theme_options_manager_update_field', index, name, value, type
        }).done(function () {
            location.reload();
        }).fail(function () {
            showAlert("Error saving changes.");
        });
    });

    // Deleting a field
    $('.delete-field').on('click', function (e) {
        e.preventDefault();
        const index = $(this).data('index');
        $.post(themeOptionsManager.ajaxUrl, {action: 'theme_options_manager_delete_field', index})
            .done(function () {
                location.reload();
            })
            .fail(function () {
                showAlert("Error deleting field.");
            });
    });

    // Sorting fields
    $('#sortable').sortable({
        handle: '.handle',
        update: function () {
            const newOrder = $(this).sortable('toArray', {attribute: 'data-index'});
            $.post(themeOptionsManager.ajaxUrl, {action: 'theme_options_manager_update_field_order', order: newOrder})
                .done(function () {
                    location.reload();
                })
                .fail(function () {
                    showAlert("Error sorting fields.");
                });
        }
    });
});
