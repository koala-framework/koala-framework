Ext2.ns('Kwf.FrontendForm.ErrorStyle');
Kwf.FrontendForm.ErrorStyle.Abstract = function(form) {
    this.form = form;
};
Kwf.FrontendForm.ErrorStyle.Abstract.prototype = {
    _showErrorMessagesAbove: function(messages, r)
    {
        var html = '<div class="webStandard kwcFormError webFormError">';
        html += '<p class="error">' + r.errorPlaceholder + ':</p>';
        html += '<ul>';
        for (var i=0; i < messages.length; i++) {
            html += '<li>' + messages[i] + '</li>';
        }
        html += '</ul>';
        html += '</div>';
        this.form.el.parent().createChild(html, this.form.el.down('form'));
        Kwf.callOnContentReady(this.form.el.dom, {newRender: true});
    },
    showErrors: function() {
    },
    hideErrors: function() {
        this.form.fields.each(function(field) {
            this.hideFieldError(field);
        }, this);
    },
    hideFieldError: function(field) {
    }
};

Kwf.FrontendForm.errorStyles = {};
