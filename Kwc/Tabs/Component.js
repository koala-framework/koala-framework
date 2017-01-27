var onReady = require('kwf/on-ready');
var Tabs = require('kwf/tabs/tabs');

onReady.onRender('.kwcClass', function tabs(el) {
    el.tabsObject = new Tabs(el);
});
