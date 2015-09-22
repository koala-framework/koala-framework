Kwf.onElementReady('div.kwfTabs', function tabs(el) {
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
    this.switchEls = Ext.query('> .kwfTabsLink', this.el.dom);
    this.contentEls = Ext.query('> .kwfTabsContent', this.el.dom);
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
        var swEl = Ext.get(this.switchEls[i]);

        Ext.get(this.contentEls[i]).enableDisplayMode('block');
        Ext.get(this.contentEls[i]).setVisible(false);
        Ext.get(this.switchEls[i]).removeClass('kwfTabsLinkActive');

        // if it is important, show on startup
        if (Ext.get(this.contentEls[i]).child('.kwfImportant')) {
            activeTabIdx = i;
        }

        if (activeTabIdx === false && Ext.get(this.contentEls[i]).hasClass('kwfTabsContentActive')) {
            activeTabIdx = i;
            Ext.get(this.contentEls[i]).removeClass('kwfTabsContentActive');
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
        Ext.get(this.switchEls[activeTabIdx]).addClass('kwfTabsLinkActive');
        Ext.get(this.contentEls[activeTabIdx]).setVisible(true);
        this._activeTabIdx = activeTabIdx;
    }
};

Ext.extend(Kwf.Tabs, Ext.util.Observable, {
    activateTab: function(idx) {
        // passed arguments are: tabsObject, newIndex, oldIndex
        this.fireEvent('beforeTabActivate', this, idx, this._activeTabIdx);
        if (this._activeTabIdx == idx) return;

        var newContentEl = Ext.get(this.contentEls[idx]);
        Ext.get(this.switchEls[idx]).addClass('kwfTabsLinkActive');
        newContentEl.setStyle('z-index', '1');
        newContentEl.setOpacity(1);
        newContentEl.setVisible(true);
        newContentEl.addClass('kwfTabsContentActive');

        var oldContentEl = Ext.get(this.contentEls[this._activeTabIdx]);

        oldContentEl.stopFx();
        newContentEl.stopFx();
        this.tabsContents.stopFx();
        if (this._activeTabIdx !== null) {
            Ext.get(this.switchEls[this._activeTabIdx]).removeClass('kwfTabsLinkActive');
            oldContentEl.setStyle({
                'z-index': 2,
                'position': 'absolute'
            });
            newContentEl.setStyle({
                'z-index': 1,
                'position': 'absolute'
            });
            Kwf.callOnContentReady(this.contentEls[idx], {newRender: false});
            oldContentEl.setVisible(false);
        }
        if (this._activeTabIdx !== null) {
            oldContentEl.setVisible(true);

            newContentEl.show();
            Kwf.callOnContentReady(this.el, {action: 'show'});
            newContentEl.hide();

            newContentEl.fadeIn({
                duration: this.fxDuration
            });
            oldContentEl.fadeOut({
                duration: this.fxDuration,
                callback: function(el) {
                    this.oldEl.setStyle('position', 'static');
                    this.oldEl.removeClass('kwfTabsContentActive');

                    this.newEl.setStyle('position', 'static');
                    this.newEl.setVisible(true);
                    this.newEl.setOpacity(1);
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
                el.applyStyles({
                    height: 'auto'
                });
                if (Ext.isIE7) {
                    (function() {
                        this.enableDisplayMode('block');
                        this.hide();
                        this.show();
                    }).defer(1, el);
                }
            }
        });

        // passed arguments are: tabsObject, newIndex, oldIndex
        this.fireEvent('tabActivate', this, idx, this._activeTabIdx);
        Kwf.Statistics.count(document.location.href + '#tab' + (idx + 1));

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
