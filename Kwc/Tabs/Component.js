var onReady = require('kwf/on-ready');
var Tabs = require('kwf/tabs/tabs');

onReady.onRender('.kwcClass', function tabs(el) {
    if (el.hasClass('kwfUp-kwfTabs')) {
        console.error("Remove kwfUp-kwfTabs class for", el);
        return;
    }
    el.tabsObject = new Tabs(el, {
        // provide the hashPrefix value in order for the internal link anchor functionality to work.
        hashPrefix: el.data('hash-prefix')
    });
});
