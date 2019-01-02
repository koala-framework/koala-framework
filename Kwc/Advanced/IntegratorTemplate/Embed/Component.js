'use strict';
var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');
var BaseUrl = require('kwf/commonjs/base-url');

onReady.onRender('.kwcClass', function(el) {
    //if embed template is used turn all xhr into cross domain xhr
    $.ajaxPrefilter(function(options) {
        if (options.url.substr(0, 1) == '/') {
            options.url = el.data('base-url')+options.url;
            options.withCredentials = true;
        }
    });
    BaseUrl.set(el.data('base-url'));
}, { priority: -10 });
