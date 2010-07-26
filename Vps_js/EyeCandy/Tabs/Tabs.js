Vps.onContentReady(function() {
    var els = Ext.query('div.vpsTabs');
    els.forEach(function(el) {
        el = Ext.get(el);
        el.tabsObject = new Vps.Tabs(el);
    });
});


Vps.Tabs = function(el) {
    this.addEvents({
        'beforeTabActivate': true,
        'tabActivate': true
    });

    this.el = el;
    this._activeTabIdx = null;
    this.switchEls = Ext.query('.vpsTabsLink', this.el.dom);
    this.contentEls = Ext.query('.vpsTabsContent', this.el.dom);

    var tabsWrapper = this.el.createChild({
        tag: 'div', cls: 'vpsTabsLinks'
    }, this.el.first());

    for (var i = 0; i < this.switchEls.length; i++) {
        tabsWrapper.appendChild(this.switchEls[i]);
        var swEl = Ext.get(this.switchEls[i]);

        if (Ext.get(this.contentEls[i]).hasClass('vpsTabsContentActive')) {
            this._activeTabIdx = i;
        }

        swEl.on('click', function() {
            this.tabsObject.activateTab(this.idx);
        }, { tabsObject: this, idx: i } );
    }

    tabsWrapper.createChild({
        tag: 'div', cls: 'clear'
    });
};

Ext.extend(Vps.Tabs, Ext.util.Observable, {
    activateTab: function(idx) {
        // passed arguments are: tabsObject, newIndex, oldIndex
        this.fireEvent('beforeTabActivate', this, idx, this._activeTabIdx);

        if (this._activeTabIdx !== null) {
            Ext.get(this.switchEls[this._activeTabIdx]).removeClass('vpsTabsLinkActive');
            Ext.get(this.contentEls[this._activeTabIdx]).removeClass('vpsTabsContentActive');
        }
        Ext.get(this.switchEls[idx]).addClass('vpsTabsLinkActive');
        Ext.get(this.contentEls[idx]).addClass('vpsTabsContentActive');

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
