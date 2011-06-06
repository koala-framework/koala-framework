Ext.namespace('Vpc.ListSwitch');

Vpc.ListSwitch.View = function(componentWrapper) {
    this.componentWrapper = Ext.get(componentWrapper);
    this.previewElements = [];
    this.activePreviewLink = null;
    this.switchOptions = null;
    this.init();
};

Vpc.ListSwitch.View.prototype = {
    init: function() {
        if (this.componentWrapper.initDone) return;

        var opts = this.componentWrapper.down(".options", true);
        if (!opts) {
            throw 'Options nicht gefunden! Seit vps 1.10 gibt es bei listSwitch ein hidden-input ".options". Wahrscheinlich wurde das .tpl überschrieben und die Zeile muss reinkopiert werden.';
        }
        this.switchOptions = Ext.decode(opts.value);

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
            this.componentWrapper.down('.listSwitchLargeWrapper .listSwitchLargeContent').dom.innerHTML = '';
            this.setLarge(this.previewElements[0]);
        }

        if (!this.switchOptions.hideArrowsAtEnds && this.previewElements.length > 1) {
            this.previousEl.setDisplayed('block');
            this.nextEl.setDisplayed('block');
        }

        this.componentWrapper.initDone = true;
    },

    setLarge: function(previewEl) {
        var largeContent = this.componentWrapper.down('.listSwitchLargeWrapper .listSwitchLargeContent').dom;
        var found = false;
        for (var i = 0; i < largeContent.childNodes.length; i++) {
            if (largeContent.childNodes[i] == previewEl.largeContent) {
                found = true;
                break;
            }
        }

        if (!found) {
            largeContent.appendChild(previewEl.largeContent);
        }

        if (!this.switchOptions.transition) { // no transition, switch hard
            if (this.activePreviewLink) {
                this.activePreviewLink.largeContent.style.display = 'none';
            }
            previewEl.largeContent.style.display = 'block';
        } else if (this.switchOptions.transition.type == 'fade') {
            if (this.activePreviewLink) {
                this.activePreviewLink.largeContent.style.zIndex = 2;
                var activeEl = Ext.get(this.activePreviewLink.largeContent);
                activeEl.fadeOut({ endOpacity: .0, easing: this.switchOptions.transition.easingOut, duration: this.switchOptions.transition.duration, useDisplay: true });

                previewEl.largeContent.style.zIndex = 1;
                var nextEl = Ext.get(previewEl.largeContent);
                nextEl.fadeIn({ endOpacity: 1.0, easing: this.switchOptions.transition.easingIn, duration: this.switchOptions.transition.duration, useDisplay: true });
            } else {
                previewEl.largeContent.style.display = 'block';
            }
        } else if (this.switchOptions.transition.type == 'slide') {
            if (this.activePreviewLink) {
                var activeEl = Ext.get(this.activePreviewLink.largeContent);
                activeEl.slideOut(
                    this.activePreviewLink.switchIndex < previewEl.switchIndex ? 'l' : 'r',
                    { easing: this.switchOptions.transition.easingOut,
                      duration: this.switchOptions.transition.duration,
                      remove: false,
                      useDisplay: true
                    }
                );

                var nextEl = Ext.get(previewEl.largeContent);
                nextEl.slideIn(
                    this.activePreviewLink.switchIndex < previewEl.switchIndex ? 'r' : 'l',
                    { easing: this.switchOptions.transition.easingIn,
                      duration: this.switchOptions.transition.duration,
                      remove: false,
                      useDisplay: true
                    }
                );
            } else {
                previewEl.largeContent.style.display = 'block';
            }
        }

        // preview link classes and set active preview link
        if (this.activePreviewLink) {
            this.activePreviewLink.removeClass('active');
        }
        this.activePreviewLink = previewEl;
        this.activePreviewLink.addClass('active');

        // pfeile ein / ausblenden
        if (this.switchOptions.hideArrowsAtEnds) {
            this.previousEl.setDisplayed(this.activePreviewLink.switchIndex == 0 ? false : 'block');
            this.nextEl.setDisplayed(
                this.activePreviewLink.switchIndex >= (this.previewElements.length -1) ? false : 'block'
            );
        }
    },

    showNext: function(ev) {
        if (this.previewElements[this.activePreviewLink.switchIndex+1]) {
            this.setLarge(this.previewElements[this.activePreviewLink.switchIndex+1]);
        } else {
            this.setLarge(this.previewElements[0]);
        }
        ev.stopEvent();
    },

    showPrevious: function(ev) {
        if (this.activePreviewLink.switchIndex >= 1) {
            this.setLarge(this.previewElements[this.activePreviewLink.switchIndex-1]);
        } else {
            this.setLarge(this.previewElements[this.previewElements.length-1]);
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

