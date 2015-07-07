Kwf.namespace('Kwf.FrontendForm.ErrorStyle');
Kwf.FrontendForm.ErrorStyle.Abstract = function(form) {
    this.form = form;
};
Kwf.FrontendForm.ErrorStyle.Abstract.prototype = {
    _showErrorMessagesAbove: function(messages, r)
    {
        var html = '<div class="kwfup-webStandard kwcFormError kwfup-webFormError">';
        html += '<p class="error">' + r.errorPlaceholder + ':</p>';
        html += '<ul>';
        for (var i=0; i < messages.length; i++) {
            html += '<li>' + messages[i] + '</li>';
        }
        html += '</ul>';
        html += '</div>';
        $(html).insertAfter(this.form.el.find('form'));
        //TODO commonjs Kwf.callOnContentReady(this.form.el, {newRender: true});
    },
    showErrors: function() {
    },
    hideErrors: function() {
        $.each(this.form.fields, (function(index, field) {
            this.hideFieldError(field);
        }).bind(this));
    },
    hideFieldError: function(field) {
    }
};

Kwf.FrontendForm.errorStyles = {};
