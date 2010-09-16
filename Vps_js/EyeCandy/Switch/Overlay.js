// um flackern zu unterbinden
document.write('<style type="text/css"> div.vpsSwitchOverlay div.switchContent { display: none; } </style>');

Vps.onContentReady(function() {
    var els = Ext.query('div.vpsSwitchOverlay');
    els.forEach(function(el) {
        el = Ext.get(el);
        el.switchOverlayObject = new Vps.Switch.Overlay(el);
    });
});

Vps.Switch.Overlay = function(el) {
    this.addEvents({
        'beforeOpen': true,
        'beforeClose': true,
        'opened': true,
        'closed': true
    });
    this._lockAnimation = false;

    this.el = el;
    this.switchLink = Ext.get(Ext.query('.switchLink', this.el.dom)[0]);
    this.switchContent = Ext.get(Ext.query('.switchContent', this.el.dom)[0]);

    // if it is important, show on startup
    if (this.switchContent.child('.vpsImportant')) {
        this.switchContent.setStyle('display', 'block');
        this.switchLink.addClass('switchLinkOpened');
    }

    if (this.switchLink && this.switchContent) {
        Ext.EventManager.addListener(this.switchLink, 'click', function(e) {
            if (this.switchLink.hasClass('switchLinkOpened')) {
                this.doClose();
            } else {
                this.doOpen();
            }
        }, this, { stopEvent: true });
    }
};

Ext.extend(Vps.Switch.Overlay, Ext.util.Observable, {
    doClose: function() {
        if (this._lockAnimation) return;
        this._lockAnimation = true;

        this.fireEvent('beforeClose', this);

        this.switchContent.fadeOut({
            endOpacity: .0,
            easing: 'easeIn',
            duration: .35,
            useDisplay: true,
            callback: function() {
                this.fireEvent('closed', this);
                this._lockAnimation = false;
            },
            scope: this
        });
        this.switchLink.removeClass('switchLinkOpened');
    },

    doOpen: function() {
        if (this._lockAnimation) return;
        this._lockAnimation = true;

        this.fireEvent('beforeOpen', this);

        this.switchContent.fadeIn({
            endOpacity: 1.0,
            easing: 'easeIn',
            duration: .35,
            useDisplay: true,
            callback: function() {
                this.fireEvent('opened', this);
                this._lockAnimation = false;
            },
            scope: this
        });
        this.switchLink.addClass('switchLinkOpened');
    }
});