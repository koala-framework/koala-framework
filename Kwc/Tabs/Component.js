var onReady = require('kwf/commonjs/on-ready');
var Tabs = require('kwf/commonjs/tabs/tabs');

onReady.onRender('.kwcClass', function tabs(el) {
    el.data('tabsObject', new Tabs(el, {
        // provide the hashPrefix value in order for the internal link anchor functionality to work.
        hashPrefix: el.data('hash-prefix'),
        bemClass: '.kwcClass'.substr(1)
    }));
});
