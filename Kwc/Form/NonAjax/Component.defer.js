var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');
var formRegistry = require('kwf/commonjs/frontend-form/form-registry');

onReady.onRender('.kwcClass', function(form) {
    var config = form.data('config');
    if (!config) return;
    this.config = config;

    this.getValues = function() {
        var ret = {};
        $.each(form.find('form').serializeArray(), function(i, f) {
            ret[f.name] = f.value;
        });
        return ret;
    };

    this.on = function() {
        return false;
    };

    formRegistry.formsByComponentId[this.config.componentId] = this;
}, { priority: -10, defer: true }); //initialize form very early, as many other components access it
