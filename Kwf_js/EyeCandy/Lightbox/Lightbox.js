Kwf.onContentReady(function() {
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
                l = new Kwf.EyeCandy.Lightbox.Lightbox(Ext.get(els[i]), options);
            }
            els[i].kwfLightbox = l;
            Ext.EventManager.addListener(els[i], 'click', function(ev) {
                this.show();
                ev.stopEvent();
            }, l, { stopEvent: true });
        }
    }

    Ext.query('.kwfLightbox').each(function(el) {
        if (el.kwfLightbox) return;
        var lightboxEl = Ext.get(el);
        var options = Ext.decode(lightboxEl.child('input.options').dom.value);
        var l = new Kwf.EyeCandy.Lightbox.Lightbox(null, options);
        lightboxEl.enableDisplayMode('block');
        l.lightboxEl = lightboxEl;
        l.innerLightboxEl = lightboxEl.down('.kwfLightboxInner');
        l.initialize();
        l.style.afterCreateLightboxEl();
        l.style.onShow();
        el.kwfLightbox = l;
        Kwf.EyeCandy.Lightbox.currentOpen = l;
    });
});

Ext.ns('Kwf.EyeCandy.Lightbox');
Kwf.EyeCandy.Lightbox.currentOpen = null;
Kwf.EyeCandy.Lightbox.allByUrl = {};
Kwf.EyeCandy.Lightbox.Lightbox = function(linkEl, options) {
    this.linkEl = linkEl;
    if (linkEl) Kwf.EyeCandy.Lightbox.allByUrl[linkEl.dom.href] = this;
    this.options = options;
    if (options.style) {
        this.style = new Kwf.EyeCandy.Lightbox.Styles[options.style](this);
    } else {
        this.style = new Kwf.EyeCandy.Lightbox.Styles.CenterBox(this);
    }
};
Kwf.EyeCandy.Lightbox.Lightbox.prototype = {
    fetched: false,
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
            el.setWidth(this.options.width + el.getBorderWidth("lr") + el.getPadding("lr"));
        }
        if (this.options.height) {
            el.setHeight(this.options.height + el.getBorderWidth("tb") + el.getPadding("tb"));
        }
        this.style.afterCreateLightboxEl();
        this.lightboxEl.hide();
    },
    fetchContent: function()
    {
        if (this.fetched) return;
        this.fetched = true;

        var url = '/kwf/util/kwc/render';
        if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;
        Ext.Ajax.request({
            params: { url: this.linkEl.dom.href },
            url: url,
            success: function(response, options) {
                var contentEl = this.innerLightboxEl.createChild();

                this.style.updateContent(contentEl, response.responseText);

                if (this.lightboxEl.isVisible()) contentEl.hide();

                var showContent = function() {
                    this.innerLightboxEl.removeClass('kwfLightboxLoading');
                    this.innerLightboxEl.child('.loading').remove();
                    if (this.lightboxEl.isVisible()) {
                        contentEl.fadeIn();
                    } else {
                        contentEl.show();
                    }
                    Kwf.callOnContentReady(contentEl.dom, {newRender: true});
                    this.style.afterContentShown();
                    if (this.lightboxEl.isVisible()) {
                        this.preloadLinks();
                    }
                };
                var imagesToLoad = 0;
                contentEl.query('img').each(function(imgEl) {
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
                location.href = this.linkEl.dom.href;
            },
            scope: this
        });
    },
    show: function()
    {
        this.createLightboxEl();
        this.style.onShow();

        if (Kwf.EyeCandy.Lightbox.currentOpen) {
            Kwf.EyeCandy.Lightbox.currentOpen.close();
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
    },
    close: function() {
        this.style.onClose();
        this.lightboxEl.fadeOut({
            concurrent: true,
            callback: function() {
                this.style.afterClose();
            },
            scope: this
        });
        this.lightboxEl.removeClass('kwfLightboxOpen');
        Kwf.EyeCandy.Lightbox.currentOpen = null;
    },
    initialize: function()
    {
        var closeButton = this.innerLightboxEl.child('.closeButton');
        if (closeButton) {
            closeButton.on('click', function(ev) {
                this.close();
                ev.stopEvent();
            }, this);
        }
        this.lightboxEl.on('click', function(ev) {
            if (ev.getTarget() == this.lightboxEl.dom) {
                this.close();
            }
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
    updateContent: function(contentEl, responseText) {
        contentEl.update(responseText);
    },
    onShow: Ext.emptyFn,
    onClose: Ext.emptyFn,
    afterClose: Ext.emptyFn,

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
            this.lightbox.close();
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
        this._center();
    },
    afterContentShown: function() {
        this._center();
    },
    updateContent: function(contentEl, responseText) {
        var isVisible = this.lightbox.lightboxEl.isVisible();

        this.lightbox.lightboxEl.show(); //to mesaure

        var originalSize = this.lightbox.innerLightboxEl.getSize();

        Kwf.EyeCandy.Lightbox.Styles.CenterBox.superclass.updateContent.apply(this, arguments);

        var newSize = this.lightbox.innerLightboxEl.getSize();
        newSize['height'] += this.lightbox.innerLightboxEl.getBorderWidth("tb")+this.lightbox.innerLightboxEl.getPadding("tb");
        newSize['width'] += this.lightbox.innerLightboxEl.getBorderWidth("lr")+this.lightbox.innerLightboxEl.getPadding("lr");

        if (isVisible) {
            this._center(true);
            if (this.lightbox.innerLightboxEl.getColor('backgroundColor')) {
                //animate size only if backgroundColor is set - else it doesn't make sense
                this.lightbox.innerLightboxEl.setSize(originalSize);
                this.lightbox.innerLightboxEl.setSize(newSize, null, true);
            } else {
                this.lightbox.innerLightboxEl.setSize(newSize);
            }
        } else {
            this._center(false);
            this.lightbox.innerLightboxEl.setSize(newSize);
            this.lightbox.lightboxEl.hide();
        }
    },
    onShow: function() {
        this.mask();
    },
    onClose: function() {
        this.unmask();
    },
    _getCenterXy: function() {
        var xy = this.lightbox.innerLightboxEl.getAlignToXY(document, 'c-c');
        if (xy[1] < 20) xy[1] = 20;
        return xy;
    },
    _center: function(anim) {
        this.lightbox.innerLightboxEl.setXY(this._getCenterXy(), anim);
    }
});
