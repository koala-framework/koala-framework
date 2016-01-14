var $ = require('jQuery');
var onReady = require('kwf/on-ready');

var ErrorStyleAbstract = function(form) {
    this.form = form;
};
ErrorStyleAbstract.prototype = {
    _showErrorMessagesAbove: function(messages, r)
    {
        var html = '<div class="kwfUp-webStandard kwfUp-kwcFormError kwfUp-webFormError">';
        html += '<p class="error">' + r.errorPlaceholder + ':</p>';
        html += '<ul>';
        for (var i=0; i < messages.length; i++) {
            html += '<li>' + messages[i] + '</li>';
        }
        html += '</ul>';
        html += '</div>';
        $(html).insertBefore(this.form.el.find('form'));
        onReady.callOnContentReady(this.form.el, {newRender: true});
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

module.exports = ErrorStyleAbstract;
