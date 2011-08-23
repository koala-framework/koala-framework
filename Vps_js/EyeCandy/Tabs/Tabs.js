Vps.onContentReady(function() {
    var els = Ext.query('div.vpsTabs');
    els.forEach(function(el) {
        if (!el.tabsObject) {
            el = Ext.get(el);
            el.tabsObject = new Vps.Tabs(el);
        }
    });
});


Vps.Tabs = function(el) {
    this.addEvents({
        'beforeTabActivate': true,
        'tabActivate': true
    });

    this.el = el;
    this.el.addClass('vpsTabsFx');
    this._activeTabIdx = null;
    this.switchEls = Ext.query('.vpsTabsLink', this.el.dom);
    this.contentEls = Ext.query('.vpsTabsContent', this.el.dom);
    this.fxDuration = .5;

    this.tabsContents = this.el.createChild({
        tag: 'div', cls: 'vpsTabsContents'
    }, this.el.first());
    var tabsLinks = this.el.createChild({
        tag: 'div', cls: 'vpsTabsLinks'
    }, this.tabsContents);

    for (var i = 0; i < this.contentEls.length; i++) {
        this.tabsContents.appendChild(this.contentEls[i]);
    }

    var activeTabIdx = false;
    for (var i = 0; i < this.switchEls.length; i++) {
        tabsLinks.appendChild(this.switchEls[i]);
        var swEl = Ext.get(this.switchEls[i]);

        Ext.get(this.contentEls[i]).setVisible(false);
        Ext.get(this.switchEls[i]).removeClass('vpsTabsLinkActive');

        // if it is important, show on startup
        if (Ext.get(this.contentEls[i]).child('.vpsImportant')) {
            activeTabIdx = i;
        }

        if (activeTabIdx === false && Ext.get(this.contentEls[i]).hasClass('vpsTabsContentActive')) {
            activeTabIdx = i;
            Ext.get(this.contentEls[i]).removeClass('vpsTabsContentActive');
        }

        swEl.on('click', function() {
            this.tabsObject.activateTab(this.idx);
        }, { tabsObject: this, idx: i } );
    }


    tabsLinks.createChild({
        tag: 'div', cls: 'clear'
    });

    if (activeTabIdx !== false) {
        Ext.get(this.switchEls[activeTabIdx]).addClass('vpsTabsLinkActive');
        Ext.get(this.contentEls[activeTabIdx]).setVisible(true);
        this.tabsContents.setHeight(Ext.get(this.contentEls[activeTabIdx]).getHeight());
        this._activeTabIdx = activeTabIdx;
    }
};

Ext.extend(Vps.Tabs, Ext.util.Observable, {
    activateTab: function(idx) {
        // passed arguments are: tabsObject, newIndex, oldIndex
        this.fireEvent('beforeTabActivate', this, idx, this._activeTabIdx);
        if (this._activeTabIdx == idx) return;

        if (this._activeTabIdx !== null) {
            Ext.get(this.switchEls[this._activeTabIdx]).removeClass('vpsTabsLinkActive');
            Ext.get(this.contentEls[this._activeTabIdx]).fadeOut({
                duration: this.fxDuration,
                callback: function(el) {
                    this.oldEl.removeClass('vpsTabsContentActive');
                    this.oldEl.setStyle('z-index', '1');

                    this.newEl.setStyle('z-index', '2');
                    this.newEl.setVisible(true);
                    this.newEl.setOpacity(1);
                },
                scope: {
                    oldEl: Ext.get(this.contentEls[this._activeTabIdx]),
                    newEl: Ext.get(this.contentEls[idx])
                }
            });
        }
        Ext.get(this.switchEls[idx]).addClass('vpsTabsLinkActive');
        var newContentEl = Ext.get(this.contentEls[idx]);
        newContentEl.setStyle('z-index', '1');
        newContentEl.setOpacity(1);
        newContentEl.setVisible(true);
        newContentEl.addClass('vpsTabsContentActive');

        this.tabsContents.scale(undefined, newContentEl.getHeight(),
            { easing: 'easeOut', duration: this.fxDuration }
        );

        // passed arguments are: tabsObject, newIndex, oldIndex
        this.fireEvent('tabActivate', this, idx, this._activeTabIdx);

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
