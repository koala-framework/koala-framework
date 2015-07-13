var onReady = require('kwf/on-ready-ext2');
var statistics = require('kwf/statistics');

onReady.onRender('div.kwfTabs', function tabs(el) {
    el.tabsObject = new Kwf.Tabs(el);
});


Kwf.Tabs = function(el) {
    this.addEvents({
        'beforeTabActivate': true,
        'tabActivate': true
    });

    this.el = el;
    this.el.addClass('kwfTabsFx');
    this._activeTabIdx = null;
    this.switchEls = Ext2.query('> .kwfTabsLink', this.el.dom);
    this.contentEls = Ext2.query('> .kwfTabsContent', this.el.dom);
    this.fxDuration = .5;

    this.tabsContents = this.el.createChild({
        tag: 'div', cls: 'kwfTabsContents', 'data-width': '100%'
    }, this.el.first());
    var tabsLinks = this.el.createChild({
        tag: 'div', cls: 'kwfTabsLinks'
    }, this.tabsContents);

    for (var i = 0; i < this.contentEls.length; i++) {
        this.tabsContents.appendChild(this.contentEls[i]);
    }

    var activeTabIdx = false;
    for (var i = 0; i < this.switchEls.length; i++) {
        tabsLinks.appendChild(this.switchEls[i]);
        var swEl = Ext2.get(this.switchEls[i]);

        Ext2.get(this.contentEls[i]).enableDisplayMode('block');
        Ext2.get(this.contentEls[i]).setVisible(false);
        Ext2.get(this.switchEls[i]).removeClass('kwfTabsLinkActive');

        // if it is important, show on startup
        if (Ext2.get(this.contentEls[i]).child('.kwfImportant')) {
            activeTabIdx = i;
        }

        if (activeTabIdx === false && Ext2.get(this.contentEls[i]).hasClass('kwfTabsContentActive')) {
            activeTabIdx = i;
            Ext2.get(this.contentEls[i]).removeClass('kwfTabsContentActive');
        }

        swEl.on('click', function() {
            this.tabsObject.activateTab(this.idx);
        }, { tabsObject: this, idx: i } );
    }


    tabsLinks.createChild({
        tag: 'div', cls: 'clear'
    });

    //show first tab as default
    if (activeTabIdx === false && this.switchEls.length) {
        activeTabIdx = 0;
    }

    if (activeTabIdx !== false) {
        Ext2.get(this.switchEls[activeTabIdx]).addClass('kwfTabsLinkActive');
        Ext2.get(this.contentEls[activeTabIdx]).setVisible(true);
        this._activeTabIdx = activeTabIdx;
    }
};

Ext2.extend(Kwf.Tabs, Ext2.util.Observable, {
    activateTab: function(idx) {
        // passed arguments are: tabsObject, newIndex, oldIndex
        this.fireEvent('beforeTabActivate', this, idx, this._activeTabIdx);
        if (this._activeTabIdx == idx) return;

        var newContentEl = Ext2.get(this.contentEls[idx]);
        Ext2.get(this.switchEls[idx]).addClass('kwfTabsLinkActive');
        newContentEl.setStyle('z-index', '1');
        newContentEl.setOpacity(1);
        newContentEl.setVisible(true);
        newContentEl.addClass('kwfTabsContentActive');

        var oldContentEl = Ext2.get(this.contentEls[this._activeTabIdx]);

        oldContentEl.stopFx();
        newContentEl.stopFx();
        this.tabsContents.stopFx();
        if (this._activeTabIdx !== null) {
            Ext2.get(this.switchEls[this._activeTabIdx]).removeClass('kwfTabsLinkActive');
            oldContentEl.setStyle({
                'z-index': 2,
                'position': 'absolute'
            });
            newContentEl.setStyle({
                'z-index': 1,
                'position': 'absolute'
            });
            onReady.callOnContentReady(this.contentEls[idx], {newRender: false});
            oldContentEl.setVisible(false);
        }
        if (this._activeTabIdx !== null) {
            oldContentEl.setVisible(true);

            newContentEl.fadeIn({
                duration: this.fxDuration,
                callback: function(el) {
                    el.parent().setStyle('height', 'auto');     //set the height after animation to auto because there are Components who change height when they are inside a tab
                }
            });
            oldContentEl.fadeOut({
                duration: this.fxDuration,
                callback: function(el) {
                    this.oldEl.setStyle('position', 'static');
                    this.oldEl.removeClass('kwfTabsContentActive');

                    this.newEl.setStyle('position', 'static');
                    this.newEl.setVisible(true);
                    this.newEl.setOpacity(1);
                    this.newEl.setStyle('height', 'auto');
                },
                scope: {
                    oldEl: oldContentEl,
                    newEl: newContentEl
                }
            });
        }

        this.tabsContents.setHeight(oldContentEl.getHeight());
        this.tabsContents.scale(undefined, newContentEl.getHeight(), {
            easing: 'easeOut',
            duration: this.fxDuration,
            callback: function(el) {
                el.setStyle('height', 'auto');     //set the height after animation to auto because there are Components who change height when they are inside a tab
            }
        });

        // passed arguments are: tabsObject, newIndex, oldIndex
        this.fireEvent('tabActivate', this, idx, this._activeTabIdx);
        statistics.count(document.location.href + '#tab' + (idx + 1));

        this._activeTabIdx = idx;
    },

    getIdxByContentEl: function(el) {
        if (el.dom) el = el.dom;
        for (var i = 0; i < this.contentEls.length; i++) {
            if (this.contentEls[i] === el) return i;
        }
        return (-1);
    },

    getContentElByIdx: function(idx) {
        if (this.contentEls[idx]) return this.contentEls[idx];
        return null;
    }
});
