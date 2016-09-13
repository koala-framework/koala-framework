'use strict';
var $ = require('jQuery');
var onReady = require('kwf/on-ready');

onReady.onRender('.kwcClassMaster', function(el) {
    //if embed template is used turn all xhr into cross domain xhr
    $.ajaxPrefilter(function(options) {
        if (options.url.substr(0, 1) == '/') {
            options.url = el.data('kwfUp-domain')+options.url;
            options.withCredentials = true;
        }
    });
});
