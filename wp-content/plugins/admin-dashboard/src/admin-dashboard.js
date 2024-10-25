jQuery(document).ready(function ($) {
    // Додаємо функцію для встановлення маски
    function updateMask() {
        const type = $('#new-field-type').val();
        const $valueField = $('#new-field-value');

        // Спочатку знімаємо всі попередні маски
        $valueField.inputmask('remove');

        // Встановлюємо маску залежно від типу
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

    // call the updateMask() function when changing the type
    $('#new-field-type').on('change', function () {
        updateMask();
    });

    // call updateMask() when loading the page for the initial mask
    updateMask();
    // validation email and phone
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

    // Повідомлення про помилку
    function showAlert(message) {
        alert(message);
    }

    // Додавання нового поля
    $('#add-field-form').on('submit', function (e) {
        e.preventDefault();
        const name = $('#new-field-name').val().trim();
        const value = $('#new-field-value').val().trim();
        const type = $('#new-field-type').val();

        // Валідація значень
        if (!name || !value) {
            showAlert("Заповніть всі поля.");
            return;
        }
        if (type === 'email' && !validateEmail(value)) {
            showAlert("Введіть дійсну email адресу.");
            return;
        }
        if (type === 'phone' && !validatePhone(value)) {
            showAlert("Введіть дійсний номер телефону.");
            return;
        }
        if (type === 'work_hours' && !validateWorkHours(value)) {
            showAlert("Введіть дійсний формат робочих годин (HH:MM - HH:MM).");
            return;
        }

        const fields = { action: 'admin_dashboard_update_field', name, value, type };
        $.post(adminDashboard.ajaxUrl, fields).done(function () {
            location.reload();
        }).fail(function () {
            showAlert("Помилка додавання поля.");
        });
    });

    // Редагування поля
    $('.edit-field').on('click', function (e) {
        e.preventDefault();
        const row = $(this).closest('tr');
        row.find('.field-name, .field-value, .field-type').hide();
        row.find('.edit-name, .edit-value, .edit-type, .save-field').show();
        $(this).hide();
    });

    // Збереження змін
    $('.save-field').on('click', function (e) {
        e.preventDefault();
        const row = $(this).closest('tr');
        const index = row.data('index');
        const name = row.find('.edit-name').val().trim();
        const value = row.find('.edit-value').val().trim();
        const type = row.find('.edit-type').val();

        // Валідація значень
        if (!name || !value) {
            showAlert("Заповніть всі поля.");
            return;
        }
        if (type === 'email' && !validateEmail(value)) {
            showAlert("Введіть дійсну email адресу.");
            return;
        }
        if (type === 'phone' && !validatePhone(value)) {
            showAlert("Введіть дійсний номер телефону.");
            return;
        }

        $.post(adminDashboard.ajaxUrl, {
            action: 'admin_dashboard_update_field', index, name, value, type
        }).done(function () {
            location.reload();
        }).fail(function () {
            showAlert("Помилка збереження змін.");
        });
    });

    // Видалення поля
    $('.delete-field').on('click', function (e) {
        e.preventDefault();
        const index = $(this).data('index');
        $.post(adminDashboard.ajaxUrl, { action: 'admin_dashboard_delete_field', index })
            .done(function () {
                location.reload();
            })
            .fail(function () {
                showAlert("Помилка видалення поля.");
            });
    });

    // Сортування полів
    $('#sortable').sortable({
        handle: '.handle',
        update: function () {
            const newOrder = $(this).sortable('toArray', { attribute: 'data-index' });
            $.post(adminDashboard.ajaxUrl, { action: 'admin_dashboard_update_order', order: newOrder })
                .done(function () {
                    location.reload();
                })
                .fail(function () {
                    showAlert("Помилка сортування полів.");
                });
        }
    });
});
