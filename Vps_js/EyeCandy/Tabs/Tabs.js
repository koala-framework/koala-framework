Vps.onContentReady(function() {
    var els = Ext.query('div.vpsTabs');
    els.forEach(function(el) {
        el = Ext.get(el);
        el.tabsObject = new Vps.Tabs(el);
    });
});


Vps.Tabs = function(el) {
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
        if (this._activeTabIdx !== null) {
            Ext.get(this.switchEls[this._activeTabIdx]).removeClass('vpsTabsLinkActive');
            Ext.get(this.contentEls[this._activeTabIdx]).removeClass('vpsTabsContentActive');
        }
        Ext.get(this.switchEls[idx]).addClass('vpsTabsLinkActive');
        Ext.get(this.contentEls[idx]).addClass('vpsTabsContentActive');

        this._activeTabIdx = idx;
    }
});
