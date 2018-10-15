'use strict';
var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var BaseUrl = require('kwf/base-url');

onReady.onRender('.kwcClass', function(el) {
    //if embed template is used turn all xhr into cross domain xhr
    $.ajaxPrefilter(function(options) {
        if (options.url.substr(0, 1) == '/') {
            options.url = el.data('kwfUp-base-url')+options.url;
            options.withCredentials = true;
        }
    });
    BaseUrl.set(el.data('kwfUp-base-url'));
}, { priority: -10 });
