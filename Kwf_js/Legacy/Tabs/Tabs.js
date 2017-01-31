var onReady = require('kwf/on-ready');
var Tabs = require('kwf/tabs/tabs');

onReady.onRender('div.kwfUp-kwfTabs', function tabs(el) {
    el.data('tabsObject', new Tabs(el, {
        linkClass: 'kwfUp-kwfTabsLink',
        linksClass: 'kwfUp-kwfTabsLinks',
        linkActiveClass: 'kwfUp-kwfTabsLinkActive',
        contentClass: 'kwfUp-kwfTabsContent',
        contentsClass: 'kwfUp-kwfTabsContents',
        contentActiveClass: 'kwfUp-kwfTabsContentActive',
        tabFxClass: 'kwfUp-kwfTabsFx'
    }));
});
