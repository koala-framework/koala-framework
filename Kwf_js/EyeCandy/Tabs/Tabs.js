var onReady = require('kwf/on-ready');
var statistics = require('kwf/statistics');
var $ = require('jQuery');
var Tabs = require('kwf/tabs/tabs');

onReady.onRender('div.kwfUp-kwfTabs', function tabs(el) {
    el.tabsObject = new Tabs(el);
});
