// um flackern zu unterbinden
document.write('<style type="text/css"> div.vpsSwitchDisplay div.switchContent { display: none; } </style>');

Vps.onContentReady(function() {
    var els = Ext.query('div.vpsSwitchDisplay');
    els.forEach(function(el) {
        if (!el.switchDisplayObject) {
            el = Ext.get(el);
            el.switchDisplayObject = new Vps.Switch.Display(el);
        }
    });
});

Vps.Switch.Display = function(el) {
    this.addEvents({
        'beforeOpen': true,
        'beforeClose': true,
        'opened': true,
        'closed': true
    });
    this._lockAnimation = false;

    this.el = el;
    this.switchLink = Ext.get(Ext.query('.switchLink', this.el.dom)[0]);
    if (!this.switchLink) this.switchLink = Ext.get(Ext.query('.switchLinkHover', this.el.dom)[0]);
    this.switchContent = Ext.get(Ext.query('.switchContent', this.el.dom)[0]);
    this.vpsSwitchCloseLink = Ext.query('.switchCloseLink', this.el.dom);
    if (this.vpsSwitchCloseLink.length) {
        this.vpsSwitchCloseLink = Ext.get(this.vpsSwitchCloseLink[0]);
    } else {
        this.vpsSwitchCloseLink = false;
    }

    // durch unterbinden von flackern (ganz oben) muss das auf block
    // gesetzt werden, damit die hoehe gemessen werden kann
    this.switchContent.setStyle('display', 'block');
    this.switchContent.scaleHeight = this.switchContent.getHeight();
    this.switchContent.setHeight(0);
    // und schnell wieder auf 'none' bevors wer merkt :)
    this.switchContent.setStyle('display', 'none');

    // if it is important, show on startup
    if (this.switchContent.child('.vpsImportant')) {
        this.switchContent.setStyle('display', 'block');
        this.switchContent.setStyle('height', 'auto');
        this.switchLink.addClass('switchLinkOpened');
        if (Ext.isIE6) {
            this.switchContent.setWidth(this.switchContent.getWidth());
        }
    }

    if (this.switchLink && this.switchContent) {
        if (this.switchLink.hasClass('switchLinkHover')) {
            Vps.Event.on(this.el.dom, 'mouseEnter', function() {
                this.doOpen();
            }, this);
            Vps.Event.on(this.el.dom, 'mouseLeave', function() {
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

    if (this.vpsSwitchCloseLink) {
        Ext.EventManager.addListener(this.vpsSwitchCloseLink, 'click', function(e) {
            this.doClose();
        }, this, { stopEvent: true });
    }
};

Ext.extend(Vps.Switch.Display, Ext.util.Observable, {
    doClose: function() {
        if (this._lockAnimation) return;
        this._lockAnimation = true;

        this.fireEvent('beforeClose', this);
        this.switchContent.scaleHeight = this.switchContent.getHeight();
        this.switchContent.scale(undefined, 0,
            { easing: 'easeOut', duration: .5, afterStyle: "display:none;",
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
        if (this._lockAnimation) return;
        this._lockAnimation = true;

        this.fireEvent('beforeOpen', this);
        this.switchContent.setStyle('display', 'block');
        this.switchContent.scale(undefined, this.switchContent.scaleHeight,
            { easing: 'easeOut', duration: .5, afterStyle: "display:block;height:auto;",
                callback: function() {
                    this.fireEvent('opened', this);
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