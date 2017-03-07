var onReady = require('kwf/commonjs/on-ready');
var Form = require('kwf/commonjs/frontend-form/form');

onReady.onRender('.kwcClass', function form(form) {
    if (!form.get(0).kwcForm) {
        new Form(form);
    }
}, { priority: -10, defer: true }); //initialize form very early, as many other components access it

