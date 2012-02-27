Ext.ns('Kwf.FrontendForm.ErrorStyle');
Kwf.FrontendForm.ErrorStyle.Abstract = function(form) {
    this.form = form;
};
Kwf.FrontendForm.ErrorStyle.Abstract.prototype = {
    _showErrorMessagesAbove: function(messages)
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
    }
};

Kwf.FrontendForm.errorStyles = {};
