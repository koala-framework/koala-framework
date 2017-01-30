var onReady = require('kwf/on-ready');
var Tabs = require('kwf/tabs/tabs');

onReady.onRender('.kwcClass', function tabs(el) {
    if (el.hasClass('kwfUp-kwfTabs')) {
        console.error("Remove kwfUp-kwfTabs class for", el);
        return;
    }
    el.data('tabsObject', new Tabs(el, {
        // provide the hashPrefix value in order for the internal link anchor functionality to work.
        hashPrefix: el.data('hash-prefix'),
        // we also need to include the class names for the link and content elements
        linkClass: 'kwcBem__link',
        contentClass: 'kwcBem__content'
    }));
});
