Kwf.onContentReady(function(readyEl) {
    var els = document.getElementsByTagName('a');
    for (var i=0; i<els.length; i++) {
        if (els[i].kwfLightbox) continue;
        var m = els[i].rel.match(/(^lightbox| lightbox)({.*?})?/);
        if (m) {
            var options = {};
            if (m[2]) options = Ext.decode(m[2]);
            var l;
            if (Kwf.EyeCandy.Lightbox.allByUrl[els[i].href]) {
                l = Kwf.EyeCandy.Lightbox.allByUrl[els[i].href];
            } else {
                l = new Kwf.EyeCandy.Lightbox.Lightbox(Ext.get(els[i]).dom.href, options);
            }
            els[i].kwfLightbox = l;
            Ext.EventManager.addListener(els[i], 'click', function(ev) {
                this.kwfLightbox.show({
                    clickTarget: Ext.get(this)
                });
                Kwf.Utils.HistoryState.currentState.lightbox = this.href;
                Kwf.Utils.HistoryState.pushState(document.title, this.href);
                ev.stopEvent();
            }, els[i], { stopEvent: true });
        }
    }

    Ext.query('.kwfLightbox').each(function(el) {
        if (el.kwfLightbox) return;
        var lightboxEl = Ext.get(el);
        var options = Ext.decode(lightboxEl.child('input.options').dom.value);
        var l = new Kwf.EyeCandy.Lightbox.Lightbox(window.location.href, options);
        Kwf.Utils.HistoryState.currentState.lightbox = window.location.href;
        Kwf.Utils.HistoryState.updateState();
        lightboxEl.enableDisplayMode('block');
        l.lightboxEl = lightboxEl;
        l.innerLightboxEl = lightboxEl.down('.kwfLightboxInner');
        l.fetched = true;
        l.initialize();
        l.closeHref = window.location.href.substr(0, window.location.href.lastIndexOf('/'));
        l.contentEl = l.innerLightboxEl.down('.kwfLightboxContent');
        l.style.afterCreateLightboxEl();
        l.style.onShow();
        l.style.onContentReady();
        el.kwfLightbox = l;
        Kwf.EyeCandy.Lightbox.currentOpen = l;
    });

    readyEl = Ext.get(readyEl);
    if (readyEl.isVisible() && Kwf.EyeCandy.Lightbox.currentOpen) {
        if (Kwf.EyeCandy.Lightbox.currentOpen.lightboxEl
            && Kwf.EyeCandy.Lightbox.currentOpen.lightboxEl.isVisible()
            && (Kwf.EyeCandy.Lightbox.currentOpen.innerLightboxEl.contains(readyEl)
            || readyEl.contains(Kwf.EyeCandy.Lightbox.currentOpen.innerLightboxEl))
        ) {
            Kwf.EyeCandy.Lightbox.currentOpen.style.onContentReady();
        }
    }
});

Kwf.Utils.HistoryState.on('popstate', function() {
    if (Kwf.EyeCandy.Lightbox.onlyCloseOnPopstate) {
        //onlyCloseOnPopstate is set in closeAndPushState
        //if multiple lightboxes are in history and we close current one we go back in history until none is open
        //so just close current one and don't show others (required to avoid flicker on closing)
        if (Kwf.EyeCandy.Lightbox.currentOpen) {
            Kwf.EyeCandy.Lightbox.currentOpen.close();
        }
        return;
    }
    var lightbox = Kwf.Utils.HistoryState.currentState.lightbox;
    if (lightbox) {
        if (!Kwf.EyeCandy.Lightbox.allByUrl[lightbox]) return;
        if (Kwf.EyeCandy.Lightbox.currentOpen != Kwf.EyeCandy.Lightbox.allByUrl[lightbox]) {
            Kwf.EyeCandy.Lightbox.allByUrl[lightbox].show();
        }
    } else {
        if (Kwf.EyeCandy.Lightbox.currentOpen) {
            Kwf.EyeCandy.Lightbox.currentOpen.close();
        }
    }
});

Ext.fly(window).on('resize', function(ev) {
    if (Kwf.EyeCandy.Lightbox.currentOpen) {
        Kwf.EyeCandy.Lightbox.currentOpen.style.onResizeWindow(ev);
    }
}, this);

Ext.ns('Kwf.EyeCandy.Lightbox');
Kwf.EyeCandy.Lightbox.currentOpen = null;
Kwf.EyeCandy.Lightbox.allByUrl = {};
Kwf.EyeCandy.Lightbox.Lightbox = function(href, options) {
    this.href = href;
    Kwf.EyeCandy.Lightbox.allByUrl[href] = this;
    this.options = options;
    if (options.style) {
        this.style = new Kwf.EyeCandy.Lightbox.Styles[options.style](this);
    } else {
        this.style = new Kwf.EyeCandy.Lightbox.Styles.CenterBox(this);
    }
};
Kwf.EyeCandy.Lightbox.Lightbox.prototype = {
    fetched: false,
    _blockOnContentReady: false,
    createLightboxEl: function()
    {
        if (this.lightboxEl) return;

        var lightbox = Ext.getBody().createChild({
            cls: 'kwfLightbox' + (this.options.style ? ' kwfLightbox'+this.options.style : ''),
            html: '<div class="kwfLightboxInner kwfLightboxLoading"><div class="loading"><div class="inner1"><div class="inner2">&nbsp;</div></div></div></div>'
        });
        lightbox.dom.kwfLightbox = this; //don't initialize again in onContentReady
        lightbox.enableDisplayMode('block');
        this.lightboxEl = lightbox;
        this.innerLightboxEl = lightbox.down('.kwfLightboxInner');

        var el = this.innerLightboxEl;

        if (this.options.width) {
            el.setWidth(parseInt(this.options.width) + el.getBorderWidth("lr") + el.getPadding("lr"));
        }
        if (this.options.height) {
            el.setHeight(parseInt(this.options.height) + el.getBorderWidth("tb") + el.getPadding("tb"));
        }
        this.style.afterCreateLightboxEl();
        this.lightboxEl.hide();
    },
    fetchContent: function()
    {
        if (this.fetched) return;
        this.fetched = true;

        Ext.Ajax.request({
            params: { url: this.href },
            url: Kwf.getKwcRenderUrl(),
            success: function(response, options) {
                this.contentEl = this.innerLightboxEl.createChild({
                    cls: 'kwfLightboxContent'
                });
                this.closeButtonEl = this.innerLightboxEl.createChild({
                    cls: 'closeButton',
                    tag: 'a',
                    href: '#'
                });

                this.style.updateContent(response.responseText);

                if (this.lightboxEl.isVisible()) this.contentEl.hide();

                var showContent = function() {
                    this.innerLightboxEl.removeClass('kwfLightboxLoading');
                    this.innerLightboxEl.child('.loading').remove();
                    if (this.lightboxEl.isVisible()) {
                        this.contentEl.fadeIn();
                    }
                    this._blockOnContentReady = true; //don't resize twice
                    Kwf.callOnContentReady(this.contentEl.dom, {newRender: true});
                    this._blockOnContentReady = false;
                    this.style.afterContentShown();
                    if (this.lightboxEl.isVisible()) {
                        this.preloadLinks();
                    }
                };
                var imagesToLoad = 0;
                this.contentEl.query('img.hideWhileLoading').each(function(imgEl) {
                    imagesToLoad++;
                    imgEl.onload = (function() {
                        imagesToLoad--;
                        if (imagesToLoad <= 0) showContent.call(this);
                    }).createDelegate(this);
                }, this);
                if (imagesToLoad == 0) showContent.call(this);
                this.initialize();
            },
            failure: function() {
                //fallback
                location.href = this.href;
            },
            scope: this
        });
    },
    show: function(options)
    {
        this.createLightboxEl();
        this.style.onShow(options);

        if (!this.closeHref) {
            if (Kwf.EyeCandy.Lightbox.currentOpen) {
                this.closeHref = Kwf.EyeCandy.Lightbox.currentOpen.closeHref;
            } else {
                this.closeHref = window.location.href;
            }
        }

        if (Kwf.EyeCandy.Lightbox.currentOpen) {
            var closeOptions = {};
            if (options && options.clickTarget) {
                closeOptions.showClickTarget = options.clickTarget;
            }
            Kwf.EyeCandy.Lightbox.currentOpen.close(closeOptions);
        }
        Kwf.EyeCandy.Lightbox.currentOpen = this;

        this.lightboxEl.addClass('kwfLightboxOpen');
        if (this.fetched) {
            if (!this.lightboxEl.isVisible()) {
                this.lightboxEl.fadeIn();
                this.preloadLinks();
                Kwf.callOnContentReady(this.innerLightboxEl.dom, {newRender: false});
            }
            this.style.afterContentShown();
        } else {
            this.lightboxEl.show();
            this.fetchContent();
        }
        this.style.afterShow(options);
        Kwf.Statistics.count(this.href);
    },
    close: function(options) {
        this.lightboxEl.hide();
        //so eg. flash component can remove object
        Kwf.callOnContentReady(this.lightboxEl, {newRender: false});
        this.lightboxEl.show();

        this.style.onClose(options);
        this.lightboxEl.removeClass('kwfLightboxOpen');
        Kwf.EyeCandy.Lightbox.currentOpen = null;
    },
    closeAndPushState: function() {
        if (Kwf.Utils.HistoryState.entries > 0) {
            Kwf.EyeCandy.Lightbox.onlyCloseOnPopstate = true; //required to avoid flicker on closing, see popstate handler
            var previousEntries = Kwf.Utils.HistoryState.entries;
            history.back();
            var closeLightbox = (function() {
                //didn't change yet, wait a bit longer
                if (previousEntries == Kwf.Utils.HistoryState.entries) {
                    closeLightbox.defer(10, this);
                    return;
                }
                //check if there is still a lightbox open
                //has to be defered because closing happens in 'popstate' event which is async in IE
                if (Kwf.Utils.HistoryState.currentState.lightbox) {
                    previousEntries = Kwf.Utils.HistoryState.entries;
                    history.back();
                    closeLightbox.defer(1, this);
                } else {
                    //last entry in history that had lightbox open
                    Kwf.EyeCandy.Lightbox.onlyCloseOnPopstate = false;
                }
            });
            closeLightbox.defer(1, this);
        } else {
            delete Kwf.Utils.HistoryState.currentState.lightbox;
            Kwf.Utils.HistoryState.replaceState(document.title, this.closeHref);
            //location.replace(this.closeHref);
            this.close();
        }
    },
    initialize: function()
    {
        var closeButtons = this.innerLightboxEl.select('.closeButton');
        closeButtons.each(function(el) {
            el.on('click', function(ev) {
                ev.stopEvent();
                this.closeAndPushState();
            }, this);
        }, this);
    },
    preloadLinks: function() {
        this.innerLightboxEl.query('a.preload').each(function(el) {
            if (el.kwfLightbox) el.kwfLightbox.preload();
        }, this);
    },
    preload: function() {
        this.createLightboxEl();
        this.fetchContent();
    }
};



Kwf.EyeCandy.Lightbox.Styles = {};
Kwf.EyeCandy.Lightbox.Styles.Abstract = function(lightbox) {
    this.lightbox = lightbox;
};
Kwf.EyeCandy.Lightbox.Styles.Abstract.masks = 0;
Kwf.EyeCandy.Lightbox.Styles.Abstract.prototype = {
    afterCreateLightboxEl: Ext.emptyFn,
    afterContentShown: Ext.emptyFn,
    updateContent: function(responseText) {
        this.lightbox.contentEl.update(responseText);
    },
    onShow: Ext.emptyFn,
    afterShow: Ext.emptyFn,
    onClose: Ext.emptyFn,
    afterClose: Ext.emptyFn,
    onContentReady: Ext.emptyFn,
    onResizeWindow: Ext.emptyFn,

    mask: function() {
        //calling mask multiple times in valid, unmask must be called exactly often
        Kwf.EyeCandy.Lightbox.Styles.Abstract.masks++;
        if (Kwf.EyeCandy.Lightbox.Styles.Abstract.masks > 1) return;
        Ext.getBody().addClass('kwfLightboxTheaterMode');
        var maskEl = Ext.getBody().mask();
        Ext.getBody().removeClass('x-masked');
        Ext.getBody().removeClass('x-masked-relative');
        maskEl.addClass('lightboxMask');
        maskEl.dom.style.height = '';
        maskEl.dom.style.width = '';

        //maskEl.setHeight(Math.max(Ext.lib.Dom.getViewHeight(), Ext.lib.Dom.getDocumentHeight()));

        maskEl.on('click', function() {
            this.lightbox.closeAndPushState();
        }, this);
    },
    unmask: function() {
        Kwf.EyeCandy.Lightbox.Styles.Abstract.masks--;
        if (Kwf.EyeCandy.Lightbox.Styles.Abstract.masks > 0) return;
        Ext.getBody()._mask.fadeOut({
            concurrent: true,
            callback: function() {
                Ext.getBody().removeClass('kwfLightboxTheaterMode');
                Ext.getBody()._mask.remove();
            },
            scope: this
        });
    }
};

Kwf.EyeCandy.Lightbox.Styles.CenterBox = Ext.extend(Kwf.EyeCandy.Lightbox.Styles.Abstract, {
    afterCreateLightboxEl: function() {
        this.lightbox.lightboxEl.on('click', function(ev) {
            if (ev.getTarget() == this.lightbox.lightboxEl.dom) {
                this.lightbox.closeAndPushState();
            }
        }, this);
        this._center(false);
    },
    afterContentShown: function() {
        this._center(false);
    },
    updateContent: function(responseText) {
        var isVisible = this.lightbox.lightboxEl.isVisible();
        this.lightbox.lightboxEl.show(); //to mesaure

        var originalSize = this.lightbox.innerLightboxEl.getSize();

        Kwf.EyeCandy.Lightbox.Styles.CenterBox.superclass.updateContent.apply(this, arguments);

        if (!this.lightbox.options.height) this.lightbox.innerLightboxEl.dom.style.height = '';
        if (!this.lightbox.options.width) this.lightbox.innerLightboxEl.dom.style.width = '';
        var newSize = this.lightbox.contentEl.getSize();
        newSize['height'] += this.lightbox.innerLightboxEl.getBorderWidth("tb")+this.lightbox.innerLightboxEl.getPadding("tb");
        newSize['width'] += this.lightbox.innerLightboxEl.getBorderWidth("lr")+this.lightbox.innerLightboxEl.getPadding("lr");

        if (isVisible) {
            this.lightbox.innerLightboxEl.setSize(newSize);
            if (this.lightbox.innerLightboxEl.getColor('backgroundColor')) {
                //animate size only if backgroundColor is set - else it doesn't make sense
                this._center(true);
                this.lightbox.innerLightboxEl.setSize(originalSize);
                this.lightbox.innerLightboxEl.setSize(newSize, null, true);
            } else {
                this._center(false);
            }
        } else {
            this.lightbox.innerLightboxEl.setSize(newSize);
            this._center(false);
            this.lightbox.lightboxEl.hide();
        }
    },
    onShow: function() {
        this.mask();
    },
    afterShow: function() {
        this._center();
    },
    onClose: function(options) {
        this.lightbox.lightboxEl.fadeOut({
            concurrent: true,
            callback: function() {
                this.afterClose();
            },
            scope: this
        });
        this.unmask();
    },
    _getCenterXy: function() {
        var xy = this.lightbox.innerLightboxEl.getAlignToXY(document, 'c-c');

        //if lightbox is larget than viewport don't position lightbox above, the user can only scroll down
        if (xy[0] < Ext.getBody().getScroll().left+20) xy[0] = Ext.getBody().getScroll().left+20;
        if (xy[1] < Ext.getBody().getScroll().top+20) xy[1] = Ext.getBody().getScroll().top+20;

        return xy;
    },
    _center: function(anim) {
        if (!this.lightbox.lightboxEl.isVisible(true)) return;
        this.lightbox.innerLightboxEl.setXY(this._getCenterXy(), anim);
    },

    onContentReady: function()
    {
        if (this.lightbox._blockOnContentReady) return;
        if (!this.lightbox.contentEl) return;

        //adjust size if height changed
        var newSize = this.lightbox.contentEl.getSize();
        newSize['height'] += this.lightbox.innerLightboxEl.getBorderWidth("tb")+this.lightbox.innerLightboxEl.getPadding("tb");
        newSize['width'] += this.lightbox.innerLightboxEl.getBorderWidth("lr")+this.lightbox.innerLightboxEl.getPadding("lr");
        if (this.lightbox.contentEl.child('> .kwfRoundBorderBox > .kwfMiddleCenter')) {
            newSize['height'] -= this.lightbox.contentEl.child('> .kwfRoundBorderBox > .kwfMiddleCenter').getPadding('tb');
        }
        var originalSize = this.lightbox.innerLightboxEl.getSize();
        this.lightbox.innerLightboxEl.setSize(newSize); //set to new size so centering works (no animation)
        var centerXy = this._getCenterXy();
        var xy = this.lightbox.innerLightboxEl.getXY();
        xy[0] = centerXy[0];
        if (centerXy[1] < xy[1]) xy[1] = centerXy[1]; //move up, but not down
        /*
        //animation to new position disabled, buggy
        this.lightbox.innerLightboxEl.setXY(xy, true);
        this.lightbox.innerLightboxEl.setSize(originalSize); //set back to previous size for animation
        this.lightbox.innerLightboxEl.setSize(newSize, null, true); //now animate to new size
        */

        //instead center unanimated
        this.lightbox.innerLightboxEl.setXY(xy);
    },

    onResizeWindow: function() {
        this._center(false);
    }
});
