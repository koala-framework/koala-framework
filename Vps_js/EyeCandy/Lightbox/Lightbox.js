Vps.onContentReady(function() {
    var els = document.getElementsByTagName('a');
    for (var i=0; i<els.length; i++) {
        if (els[i].vpsLightbox) continue;
        var m = els[i].rel.match(/(^lightbox| lightbox)({.*?})?/)
        if (m) {
            var options = {};
            if (m[2]) options = Ext.decode(m[2]);
            var l;
            if (Vps.EyeCandy.Lightbox.allByUrl[els[i].href]) {
                l = Vps.EyeCandy.Lightbox.allByUrl[els[i].href];
            } else {
                l = new Vps.EyeCandy.Lightbox.Lightbox(Ext.get(els[i]), options);
            }
            els[i].vpsLightbox = l;
            Ext.EventManager.addListener(els[i], 'click', function(ev) {
                this.show();
                ev.stopEvent();
            }, l, { stopEvent: true });
        }
    }

    Ext.query('.vpsLightbox').each(function(el) {
        if (el.vpsLightbox) return;
        var lightboxEl = Ext.get(el);
        var options = Ext.decode(lightboxEl.child('input.options').dom.value);
        var l = new Vps.EyeCandy.Lightbox.Lightbox(null, options);
        l.lightboxEl = lightboxEl;
        l.style.afterCreateLightboxEl();
        l.initialize();
        l.style.onShow();
        el.vpsLightbox = l;
        Vps.EyeCandy.Lightbox.currentOpen = l;
    });
});

Ext.ns('Vps.EyeCandy.Lightbox');
Vps.EyeCandy.Lightbox.currentOpen = null;
Vps.EyeCandy.Lightbox.allByUrl = {};
Vps.EyeCandy.Lightbox.Lightbox = function(linkEl, options) {
    this.linkEl = linkEl;
    if (linkEl) Vps.EyeCandy.Lightbox.allByUrl[linkEl.dom.href] = this;
    this.options = options;
    if (options.style) {
        this.style = new Vps.EyeCandy.Lightbox.Styles[options.style](this);
    } else {
        this.style = new Vps.EyeCandy.Lightbox.Styles.CenterBox(this);
    }
};
Vps.EyeCandy.Lightbox.Lightbox.prototype = {
    fetched: false,
    createLightboxEl: function()
    {
        if (this.lightboxEl) return;

        var lightbox = Ext.getBody().createChild({
            cls: 'vpsLightbox vpsLightbox'+this.options.style,
            html: '<div class="loading"></div>'
        });
        lightbox.dom.vpsLightbox = this; //don't initialize again in onContentReady
        lightbox.enableDisplayMode('block');
        if (this.options.width) {
            lightbox.setWidth(this.options.width + lightbox.getBorderWidth("lr") + lightbox.getPadding("lr"));
        }
        if (this.options.height) {
            lightbox.setHeight(this.options.height + lightbox.getBorderWidth("tb") + lightbox.getPadding("tb"));
        }
        this.lightboxEl = lightbox;
        this.style.afterCreateLightboxEl();
        lightbox.hide();
    },
    fetchContent: function()
    {
        if (this.fetched) return;
        this.fetched = true;

        var url = '/vps/util/vpc/render';
        if (Vps.Debug.rootFilename) url = Vps.Debug.rootFilename + url;
        Ext.Ajax.request({
            params: { url: this.linkEl.dom.href },
            url: url,
            success: function(response, options) {
                var contentEl = this.lightboxEl.createChild();
                if (this.lightboxEl.isVisible()) contentEl.hide();
                contentEl.update(response.responseText);
                var imagesToLoad = 0;
                contentEl.query('img').each(function(imgEl) {
                    imagesToLoad++;
                    imgEl.onload = (function() {
                        imagesToLoad--;
                        if (imagesToLoad <= 0) {
                            this.lightboxEl.child('.loading').hide();
                            if (this.lightboxEl.isVisible()) {
                                contentEl.fadeIn();
                                this.preloadLinks();
                            }
                        }
                    }).createDelegate(this);
                }, this);
                Vps.callOnContentReady();
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
        if (Vps.EyeCandy.Lightbox.currentOpen) {
            Vps.EyeCandy.Lightbox.currentOpen.close();
        }
        Vps.EyeCandy.Lightbox.currentOpen = this;

        this.createLightboxEl();
        this.style.onShow();
        this.lightboxEl.addClass('vpsLightboxOpen');
        if (this.fetched) {
            if (!this.lightboxEl.isVisible()) {
                this.lightboxEl.fadeIn();
                this.preloadLinks();
            }
        } else {
            this.lightboxEl.show();
            this.fetchContent();
        }
    },
    close: function() {
        this.style.onClose();
        this.lightboxEl.fadeOut();
        this.lightboxEl.removeClass('vpsLightboxOpen');
        Vps.EyeCandy.Lightbox.currentOpen = null;
    },
    initialize: function()
    {
        this.lightboxEl.child('.closeButton').on('click', function(ev) {
            this.close();
            ev.stopEvent();
        }, this);
    },
    preloadLinks: function() {
        this.lightboxEl.query('a.preload').each(function(el) {
            el.vpsLightbox.preload();
        }, this);
    },
    preload: function() {
        this.createLightboxEl();
        this.fetchContent();
    }
};



Vps.EyeCandy.Lightbox.Styles = {};
Vps.EyeCandy.Lightbox.Styles.Abstract = function(lightbox) {
    this.lightbox = lightbox;
};
Vps.EyeCandy.Lightbox.Styles.Abstract.prototype = {
    afterCreateLightboxEl: Ext.emptyFn,
    onShow: Ext.emptyFn,
    onClose: Ext.emptyFn,

    mask: function() {
        var maskEl = Ext.getBody().mask();
        Ext.getBody().removeClass('x-masked');
        maskEl.addClass('lightboxMask');

        maskEl.setHeight(Math.max(Ext.lib.Dom.getViewHeight(), Ext.lib.Dom.getDocumentHeight()));

        maskEl.on('click', function() {
            this.lightbox.close();
        }, this);
    },
    unmask: function() {
        Ext.getBody().unmask();
        Ext.getBody().removeClass('lightboxShowOverflow');
    }
};

Vps.EyeCandy.Lightbox.Styles.CenterBox = Ext.extend(Vps.EyeCandy.Lightbox.Styles.Abstract, {
    afterCreateLightboxEl: function() {
        this.lightbox.lightboxEl.center();
    },
    onShow: function() {
        this.mask();
    },
    onClose: function() {
        this.unmask();
    }
});
