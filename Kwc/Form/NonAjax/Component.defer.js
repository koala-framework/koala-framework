var onReady = require('kwf/commonjs/on-ready');
var Form = require('kwf/commonjs/frontend-form/form');

onReady.onRender('.kwcClass', function form(form) {
    if (!form.get(0).kwcForm) {
        var formObject = new Form(form);
        var button = form.find('form button.kwfUp-submit');
        if (button) {
            // removing submit-button onclick listener to prevent JS form.submit()
            // as this does not include the submit-button name/value pair in post-request
            // which is required for kwc-form to work (does not save if missing)
            button.off('click.kwfUp-commonjsFrontendFormForm');
        }
    }
}, { priority: -10, defer: true }); //initialize form very early, as many other components access it
