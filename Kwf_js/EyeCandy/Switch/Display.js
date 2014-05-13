// um flackern zu unterbinden
document.write('<style type="text/css"> div.kwfSwitchDisplay div.switchContent { display: none; } </style>');

Kwf.onContentReady(function() {
    var els = Ext.query('div.kwfSwitchDisplay');
    els.forEach(function(el) {
        el = Ext.get(el);
        // Attach Switch.Display-Object to dom because ext-element is still existent even though
        // it's no part of dom anymore when html is changed e.g. because of ajax-view
        if (!el.dom.switchDisplayObject) {
            el.dom.switchDisplayObject = new Kwf.Switch.Display(el);
        }
    });
});

Kwf.Switch.Display = function(el, config) {
    this.addEvents({
        'beforeOpen': true,
        'beforeClose': true,
        'opened': true,
        'closed': true
    });
    this._lockAnimation = false;
    var defaultConfig = {
        animation: { 
            duration: .5
        }
    };
    this.config = Ext.apply(defaultConfig, config);

    this.el = el;
    this.switchLink = Ext.get(Ext.query('.switchLink', this.el.dom)[0]);
    if (!this.switchLink) this.switchLink = Ext.get(Ext.query('.switchLinkHover', this.el.dom)[0]);
    this.switchContent = Ext.get(Ext.query('.switchContent', this.el.dom)[0]);
    this.kwfSwitchCloseLink = Ext.query('.switchCloseLink', this.el.dom);
    if (this.kwfSwitchCloseLink.length) {
        this.kwfSwitchCloseLink = Ext.get(this.kwfSwitchCloseLink[0]);
    } else {
        this.kwfSwitchCloseLink = false;
    }

    if (!this.switchContent.scaleHeight) {
        // durch unterbinden von flackern (ganz oben) muss das auf block
        // gesetzt werden, damit die hoehe gemessen werden kann
        this.switchContent.setStyle('display', 'block');
        this.switchContent.scaleHeight = this.switchContent.getHeight();
        this.switchContent.setHeight(0);
        // und schnell wieder auf 'none' bevors wer merkt :)
        this.switchContent.setStyle('display', 'none');
    }

    // if it is important, show on startup
    if (this.switchContent.child('.kwfImportant')) {
        this.switchContent.setStyle('display', 'block');
        this.switchContent.setStyle('height', 'auto');
        this.switchLink.addClass('switchLinkOpened');
        if (Ext.isIE6) {
            this.switchContent.setWidth(this.switchContent.getWidth());
        }
    }

    if (this.switchLink && this.switchContent) {
        if (this.switchLink.hasClass('switchLinkHover')) {
            Kwf.Event.on(this.el.dom, 'mouseEnter', function() {
                this.doOpen();
            }, this);
            Kwf.Event.on(this.el.dom, 'mouseLeave', function() {
                this.doClose();
            }, this);
        } else {
            Ext.EventManager.addListener(this.switchLink, 'click', function(e) {
                if (this.switchLink.hasClass('switchLinkOpened')) {
                    this.doClose();
                } else {
                    this.doOpen();
                }
            }, this, { stopEvent: true });
        }
    }

    if (this.kwfSwitchCloseLink) {
        Ext.EventManager.addListener(this.kwfSwitchCloseLink, 'click', function(e) {
            this.doClose();
        }, this, { stopEvent: true });
    }
};

Ext.extend(Kwf.Switch.Display, Ext.util.Observable, {
    doClose: function() {
        if (!this.switchLink.hasClass('switchLinkHover')) {
            if (this._lockAnimation) return;
            this._lockAnimation = true;
        }

        this.fireEvent('beforeClose', this);
        this.switchContent.stopFx();
        this.switchContent.scaleHeight = this.switchContent.getHeight();
        this.switchContent.scale(undefined, 0,
            { easing: 'easeOut', duration: this.config.animation.duration, afterStyle: "display:none;",
                callback: function() {
                    this.fireEvent('closed', this);
                    this._lockAnimation = false;
                },
                scope: this
            }
        );
        this.switchLink.removeClass('switchLinkOpened');
    },

    doOpen: function() {
        if (!this.switchLink.hasClass('switchLinkHover')) {
            if (this._lockAnimation) return;
            this._lockAnimation = true;
        }

        this.fireEvent('beforeOpen', this);
        this.switchContent.stopFx();
        this.switchContent.setStyle('display', 'block');
        this.switchContent.scale(undefined, this.switchContent.scaleHeight,
            { easing: 'easeOut', duration: this.config.animation.duration, afterStyle: "display:block;height:auto;",
                callback: function() {
                    this.fireEvent('opened', this);
                    Kwf.callOnContentReady(this.el.dom, {newRender: false});
                    if (Ext.isIE6) {
                        this.switchContent.setWidth(this.switchContent.getWidth());
                    }
                    this._lockAnimation = false;
                },
                scope: this
            }
        );
        this.switchLink.addClass('switchLinkOpened');
    }
});