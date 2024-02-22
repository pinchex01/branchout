var SettingsJs = {
    config: {
        selector: 'settings',
        cancelButtonClass: 'edit-setting-cancel'
    },


    init: function (config) {
        $.extend(this.config, config);
        this.addListeners()
    },

    addListeners: function () {
        $('table.' + SettingsJs.config.selector + ' tr.settings-row').on('click', function () {
            var field_id = $(this).attr("id");
            SettingsJs.editRow(field_id);
        });

        $('.' + SettingsJs.config.cancelButtonClass).click(function (e) {
            e.stopPropagation();
            var row = $(this).closest('tr');
            var field_id = row.attr('id');

            SettingsJs.cancelEdit(field_id);

            row.removeClass('selected');
        });

        $('table.' + SettingsJs.config.selector + ' input').on('click', function (e) {
            e.stopPropagation();
        });

        $('table.' + SettingsJs.config.selector + ' button').on('click', function (e) {
            e.stopPropagation();
        });

        $('table.' + SettingsJs.config.selector + ' select').on('click', function (e) {
            e.stopPropagation();
        });

        /**
         * Check whether form should be submitted async or manually
         */
        $('table.' + SettingsJs.config.selector + ' form').on('submit', function (e) {
            e.preventDefault();
            var $form = $(this);
            //check if form has custom submit handler
            var custom_action = $form.data('onsubmit')? $form.data('onsubmit') : null;

            //call custom action
            if (custom_action != null){
                window[custom_action].call(SettingsJs,$form);
                return;
            }

            var submit_type = $form.attr('rel') ? $form.attr('rel') : null;

            if (submit_type == null) {
                $form.unbind('submit').submit();
                return;
            }

            //check if form is configured for async or manual submit
            if (submit_type == 'async') {
                SettingsJs.submitHandler($form);
                return false;
            }

            //otherwise just submit the form
            $form.unbind('submit').submit();

        });

    },

    submitHandler: function (form) {
        $.ajax({
            url: form.attr('action'),
            type: form.attr('method'),
            data: form.serializeArray(),
            success: function (response) {
                if (response.status == 200)
                    SettingsJs.submitSuccess(form, response);
                else
                    SettingsJs.submitFailure(form, response);
            },
            error: function (jqXhr, textStatus, errorThrown) {
                console.log(errorThrown);
            }
        });
    },

    submitSuccess: function (form, data) {
        var field_id = form.closest('tr').attr('id');
        this.hideForm(field_id);
        var label = this.getLabel(field_id);
        label.html(data.data.value);
        var row = label.closest('tr');
        row.addClass('bg-success');
        var cell = row.find('td:last');
        cell.html('<p> ' + data.message + ' </p>');
        this.showLabel(field_id);
    },

    submitFailure: function (form, data) {
        form.prepend('<div class="alert alert-danger">' +
            data.message + '</div>');
        $('.edit-setting-submit').button('reset');
    },

    showForm: function (field_id) {
        this.getForm(field_id).removeClass('hide');
    },

    hideForm: function (field_id) {
        this.getForm(field_id).addClass('hide');
    },

    showLabel: function (field_id) {
        this.getLabel(field_id).show();
    },

    /**
     *
     * @param field_id
     * @returns {*|jQuery|HTMLElement}
     */
    getForm: function (field_id) {
        return $('tr#' + field_id + ' .edit-setting-form');
    },

    /**
     *
     * @param field_id
     */
    getLabel: function (field_id) {
        return $('tr#' + field_id + ' .edit-setting-label');
    },

    /**
     *
     * @param field_id
     * @returns {*|jQuery|HTMLElement}
     */
    getRow: function (field_id) {
        return $('table.settings tr#' + field_id);
    },

    hideLabel: function (field_id) {
        this.getLabel(field_id).hide();
    },

    editRow: function (field_id) {
        var row = this.getRow(field_id);
        this.reset();
        this.hideLabel(field_id);
        this.showForm(field_id);
        row.addClass('selected');
    },

    cancelEdit: function (field_id) {
        this.hideForm(field_id);
        this.showLabel(field_id);
    },

    reset: function () {
        //first, hide all settings fields just in case any was visible
        $('.edit-setting-form').addClass('hide');

        //show all field labels
        $('.edit-setting-label').show();

    },

    hasClass: function (element, cls) {
        return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
    }

};