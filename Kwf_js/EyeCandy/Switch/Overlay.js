// um flackern zu unterbinden
document.write('<style type="text/css"> .kwfSwitchOverlay .switchContent { display: none; } </style>');

Kwf.onContentReady(function() {
    var els = Ext2.query('.kwfSwitchOverlay');
    els.forEach(function(el) {
        if (!el.switchOverlayObject) {
            el = Ext2.get(el);
            el.switchOverlayObject = new Kwf.Switch.Overlay(el);
        }
    });
});

Kwf.Switch.Overlay = function(el) {
    this.addEvents({
        'beforeOpen': true,
        'beforeClose': true,
        'opened': true,
        'closed': true
    });
    this._lockAnimation = false;

    this.el = el;
    this.switchLinks = Ext2.query('.switchLink', this.el.dom);
    this.switchContent = Ext2.get(Ext2.query('.switchContent', this.el.dom)[0]);

    // if it is important, show on startup
    if (this.switchContent.child('.kwfImportant')) {
        this.switchContent.setStyle('display', 'block');
        this.switchLinks.each(function(sl) {
        	Ext2.get(sl).addClass('switchLinkOpened');	
        }, this);
    }

    if (this.switchLinks.length && this.switchContent) {
    	this.switchLinks.each(function(sl) {
	        Ext2.EventManager.addListener(sl, 'click', function(e) {
	        	this.switchLinks.each(function(sl) {
		            if (Ext2.get(sl).hasClass('switchLinkOpened')) {
		                this.doClose();
		            } else {
		                this.doOpen();
		            }
	        	}, this);
	        }, this, { stopEvent: true });
    	}, this);
    }
};

Ext2.extend(Kwf.Switch.Overlay, Ext2.util.Observable, {
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
        this.switchLinks.each(function(sl) {
        	Ext2.get(sl).removeClass('switchLinkOpened');
        }, this);
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
        this.switchLinks.each(function(sl) {
        	Ext2.get(sl).addClass('switchLinkOpened');
        }, this);
    }
});