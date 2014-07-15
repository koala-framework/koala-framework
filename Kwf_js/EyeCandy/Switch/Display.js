Kwf.onElementReady('div.kwfSwitchDisplay', function switchDisplay(el) {
    el = Ext2.get(el);
    // Attach Switch.Display-Object to dom because ext-element is still existent even though
    // it's no part of dom anymore when html is changed e.g. because of ajax-view
    if (!el.dom.switchDisplayObject) {
        el.dom.switchDisplayObject = new Kwf.Switch.Display(el);
    }
}, {defer: true});

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
    this.config = Ext2.apply(defaultConfig, config);

    this.el = el;
    this.switchLink = Ext2.get(Ext2.query('.switchLink', this.el.dom)[0]);
    if (!this.switchLink) this.switchLink = Ext2.get(Ext2.query('.switchLinkHover', this.el.dom)[0]);
    this.switchContent = Ext2.get(Ext2.query('.switchContent', this.el.dom)[0]);
    this.kwfSwitchCloseLink = Ext2.query('.switchCloseLink', this.el.dom);
    if (this.kwfSwitchCloseLink.length) {
        this.kwfSwitchCloseLink = Ext2.get(this.kwfSwitchCloseLink[0]);
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

    // if it is important or active, show on startup
    if (this.switchContent.child('.kwfImportant')
        || this.switchContent.hasClass('active')) {
        this.switchContent.setStyle('display', 'block');
        this.switchContent.setStyle('height', 'auto');
        this.switchLink.addClass('switchLinkOpened');
        if (Ext2.isIE6) {
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
            Ext2.EventManager.addListener(this.switchLink, 'click', function(e) {
                if (this.switchLink.hasClass('switchLinkOpened')) {
                    this.doClose();
                } else {
                    this.doOpen();
                }
            }, this, { stopEvent: true });
        }
    }

    if (this.kwfSwitchCloseLink) {
        Ext2.EventManager.addListener(this.kwfSwitchCloseLink, 'click', function(e) {
            this.doClose();
        }, this, { stopEvent: true });
    }
};

Ext2.extend(Kwf.Switch.Display, Ext2.util.Observable, {
    doClose: function() {
        if (this._state == 'closing' || this._state == 'closed') {
            return;
        }
        if (this._state == 'opening' && this.switchContent.scaleHeight) {
            this.switchContent.scale(undefined, this.switchContent.scaleHeight);
        }
        this._state = 'closing';

        this.fireEvent('beforeClose', this);
        this.switchContent.stopFx();
        if (!this.switchContent.scaleHeight) {
            this.switchContent.scaleHeight = this.switchContent.getHeight();
        }
        this.switchContent.scale(undefined, 0,
            { easing: 'easeOut', duration: this.config.animation.duration, afterStyle: "display:none;",
                callback: function() {
                    this.fireEvent('closed', this);
                    this._state = 'closed';
                },
                scope: this
            }
        );
        this.switchLink.removeClass('switchLinkOpened');
    },

    doOpen: function() {
        if (this._state == 'opening' || this._state == 'opened') {
            return;
        }
        if (this._state == 'closing') {
            this.switchContent.scale(undefined, 0);
        }
        this._state = 'opening';

        this.fireEvent('beforeOpen', this);
        this.switchContent.stopFx();
        this.switchContent.setStyle('display', 'block');
        Kwf.callOnContentReady(this.el.dom, {newRender: false});
        this.switchContent.scale(undefined, this.switchContent.scaleHeight,
            { easing: 'easeOut', duration: this.config.animation.duration, afterStyle: "display:block;height:auto;",
                callback: function() {
                    this.fireEvent('opened', this);
                    this._state = 'opened';
                    Kwf.callOnContentReady(this.el.dom, {newRender: false});
                    if (Ext2.isIE6) {
                        this.switchContent.setWidth(this.switchContent.getWidth());
                    }
                },
                scope: this
            }
        );
        this.switchLink.addClass('switchLinkOpened');
    }
});
