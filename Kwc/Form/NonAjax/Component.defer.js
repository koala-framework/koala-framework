var onReady = require('kwf/commonjs/on-ready');
var Form = require('kwf/commonjs/frontend-form/form');

onReady.onRender('.kwcClass', function form(form) {
    if (!form.get(0).kwcForm) {
        var formObject = new Form(form);
        formObject.submit = function() {
            this.el.find('form').submit();
        };
    }
}, { priority: -10, defer: true }); //initialize form very early, as many other components access it
