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

    for (var i = 0; i < this.switchEls.length; i++) {
        tabsLinks.appendChild(this.switchEls[i]);
        var swEl = Ext.get(this.switchEls[i]);

        if (Ext.get(this.contentEls[i]).hasClass('vpsTabsContentActive')) {
            this._activeTabIdx = i;
        }

        swEl.on('click', function() {
            this.tabsObject.activateTab(this.idx);
        }, { tabsObject: this, idx: i } );
    }

    this.tabsContents.setHeight(Ext.get(this.contentEls[this._activeTabIdx]).getHeight());

    tabsLinks.createChild({
        tag: 'div', cls: 'clear'
    });
};

Ext.extend(Vps.Tabs, Ext.util.Observable, {
    activateTab: function(idx) {
        if (this._activeTabIdx == idx) return;

        if (this._activeTabIdx !== null) {
            Ext.get(this.switchEls[this._activeTabIdx]).removeClass('vpsTabsLinkActive');
            Ext.get(this.contentEls[this._activeTabIdx]).fadeOut({
                duration: this.fxDuration,
                callback: function(el) {
                    el.removeClass('vpsTabsContentActive');
                }
            });
        }
        var newContentEl = Ext.get(this.contentEls[idx]);
        Ext.get(this.switchEls[idx]).addClass('vpsTabsLinkActive');
        newContentEl.addClass('vpsTabsContentActive');

        this.tabsContents.scale(undefined, newContentEl.getHeight(),
            { easing: 'easeOut', duration: this.fxDuration }
        );

        newContentEl.fadeIn({ duration: this.fxDuration });

        this._activeTabIdx = idx;
    }
});
