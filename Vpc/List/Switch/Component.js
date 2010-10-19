Ext.namespace('Vpc.ListSwitch');

Vpc.ListSwitch.View = function(componentWrapper) {
    this.componentWrapper = Ext.get(componentWrapper);
    this.previewElements = [];
    this.activePreviewLink = null;
    this.init();
};

Vpc.ListSwitch.View.prototype = {
    init: function() {
        if (this.componentWrapper.initDone) return;

        this.previousEl = this.componentWrapper.down('.listSwitchLargeWrapper a.listSwitchPrevious');
        this.nextEl = this.componentWrapper.down('.listSwitchLargeWrapper a.listSwitchNext');

        this.previousEl.on('click', this.showPrevious, this);
        this.nextEl.on('click', this.showNext, this);

        var switchItems = Ext.DomQuery.select(
            'div.listSwitchItem',
            this.componentWrapper.down('.listSwitchPreviewWrapper').dom
        );
        Ext.each(switchItems, function(si) {
            var previewLink = Ext.get(si).down('a.previewLink');
            previewLink.largeContent = Ext.get(si).down('.largeContent').dom;
            previewLink.switchIndex = this.previewElements.length;
            previewLink.on('click', function(ev, el, cfg) {
                this.setLarge(cfg.linkEl);
                ev.stopEvent();
            }, this, { linkEl: previewLink });
            this.previewElements.push(previewLink);
        }, this);

        if (this.previewElements.length && this.previewElements[0]) {
            this.setLarge(this.previewElements[0]);
        }

        this.componentWrapper.initDone = true;
    },

    setLarge: function(previewEl) {
        if (this.activePreviewLink)  {
            this.activePreviewLink.largeContent.style.display = 'none';
        } else {
            this.componentWrapper.down('.listSwitchLargeWrapper .listSwitchLargeContent').dom.innerHTML = '';
        }
        previewEl.largeContent.style.display = 'block';
        this.componentWrapper.down('.listSwitchLargeWrapper .listSwitchLargeContent').dom.appendChild(previewEl.largeContent);
        if (this.activePreviewLink) {
            this.activePreviewLink.removeClass('active');
        }
        this.activePreviewLink = previewEl;
        this.activePreviewLink.addClass('active');

        this.previousEl.setDisplayed(this.activePreviewLink.switchIndex == 0 ? false : true);
        this.nextEl.setDisplayed(
            this.activePreviewLink.switchIndex >= (this.previewElements.length -1) ? false : true
        );
    },

    showNext: function(ev) {
        if (this.previewElements[this.activePreviewLink.switchIndex+1]) {
            this.setLarge(this.previewElements[this.activePreviewLink.switchIndex+1]);
        }
        ev.stopEvent();
    },

    showPrevious: function(ev) {
        if (this.activePreviewLink.switchIndex >= 1) {
            this.setLarge(this.previewElements[this.activePreviewLink.switchIndex-1]);
        }
        ev.stopEvent();
    }
};

Vps.onContentReady(function() {
    var switches = Ext.DomQuery.select('div.vpsListSwitch');
    Ext.each(switches, function(sw) {
        var list = new Vpc.ListSwitch.View(sw);
    });
});

