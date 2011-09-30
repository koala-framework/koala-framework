Vps.onContentReady(function() {
    var els = document.getElementsByTagName('a');
    for (var i=0; i<els.length; i++) {
        if (els[i].vpsLightboxInitalized) continue;
        var m = els[i].rel.match(/(^lightbox| lightbox)({.*?})?/)
        if (m) {
            var options = {};
            if (m[2]) options = Ext.decode(m[2]);
            var l = new Vps.EyeCandy.Lightbox.Lightbox(Ext.get(els[i]), options);
            Ext.EventManager.addListener(els[i], 'click', function(ev) {
                this.show();
                ev.stopEvent();
            }, l, { stopEvent: true });
            els[i].vpsLightboxInitalized = true;
        }
    }
    Ext.query('.vpsLightbox').each(function(el) {
        if (el.vpsLightboxInitalized) return;
        el.vpsLightboxInitalized = true;
        var l = new Vps.EyeCandy.Lightbox.Lightbox(null, {});
        l.lightboxEl = Ext.get(el);
        l.initialize();
        l.style.onShow();
    });
});

Ext.ns('Vps.EyeCandy.Lightbox');
Vps.EyeCandy.Lightbox.currentOpen = null;
Vps.EyeCandy.Lightbox.Lightbox = function(linkEl, options) {
    this.linkEl = linkEl;
    this.options = options;
    if (options.style) {
        this.style = new Vps.EyeCandy.Lightbox.Styles[options.style](this);
    } else {
        this.style = new Vps.EyeCandy.Lightbox.Styles.CenterBox(this);
    }
};
Vps.EyeCandy.Lightbox.Lightbox.prototype = {
    show: function() {
        if (Vps.EyeCandy.Lightbox.currentOpen) {
            Vps.EyeCandy.Lightbox.currentOpen.close();
        }
        Vps.EyeCandy.Lightbox.currentOpen = this;
        var lightbox = Ext.getBody().createChild({
            cls: 'vpsLightbox vpsLightbox'+this.options.style,
            html: '<div class="loading"></div>'
        });
        if (this.options.width) {
            lightbox.setWidth(this.options.width + lightbox.getBorderWidth("lr") + lightbox.getPadding("lr"));
        }
        if (this.options.height) {
            lightbox.setHeight(this.options.height + lightbox.getBorderWidth("tb") + lightbox.getPadding("tb"));
        }
        lightbox.child('.loading').show();
        lightbox.center();
        this.lightboxEl = lightbox;

        this.style.beforeFetch();

        var url = '/vps/util/vpc/render';
        if (Vps.Debug.rootFilename) url = Vps.Debug.rootFilename + url;
        Ext.Ajax.request({
            params: { url: this.linkEl.dom.href },
            url: url,
            success: function(response, options) {
                var contentEl = this.lightboxEl.createChild();
                contentEl.hide();
                contentEl.update(response.responseText);
                var imagesToLoad = 0;
                contentEl.query('img').each(function(imgEl) {
                    imagesToLoad++;
                    imgEl.onload = (function() {
                        imagesToLoad--;
                        if (imagesToLoad <= 0) {
                            this.lightboxEl.child('.loading').hide();
                            contentEl.fadeIn();
                        }
                    }).createDelegate(this);
                }, this);
                Vps.callOnContentReady();
                this.initialize();
            },
            failure: function() {
                //fallback
                location.href = el.dom.href;
            },
            scope: this
        });
    },
    close: function() {
        this.style.onClose();
        this.lightboxEl.remove();
        delete this.lightboxEl;
        Vps.EyeCandy.Lightbox.currentOpen = null;
    },
    initialize: function()
    {
        this.style.onInitialize();
        this.lightboxEl.child('.closeButton').on('click', function(ev) {
            this.close();
            ev.stopEvent();
        }, this);
    }
};



Vps.EyeCandy.Lightbox.Styles = {};
Vps.EyeCandy.Lightbox.Styles.Abstract = function(lightbox) {
    this.lightbox = lightbox;
};
Vps.EyeCandy.Lightbox.Styles.Abstract.prototype = {
    beforeFetch: Ext.emptyFn,
    onShow: Ext.emptyFn,
    onClose: Ext.emptyFn,
    onInitialize: Ext.emptyFn,

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
    beforeFetch: function() {
        this.lightbox.lightboxEl.center();
        this.mask();
    },
    onShow: function() {
        this.lightbox.lightboxEl.center();
        this.mask();
    },
    onInitialize: function() {
        this.lightbox.lightboxEl.center();
        this.mask();
    },
    onClose: function() {
        this.unmask();
    }
});

/*
Vpc.Basic.ImageEnlarge.prototype =
{

    hide: function(e)
    {
        if (e) e.stopEvent();
        this.lightbox.applyStyles('display: none;');
        this.unmask();
    },

    // May be overwritten
    alignBox: function() {
        this.lightbox.center();
    },

    show: function(linkEl)
    {
        this.mask();

        this.lightbox.applyStyles('display: block;');

        var m = linkEl.dom.rel.match(/enlarge_([0-9]+)_([0-9]+)/);

        var data = {};

        data.title = linkEl.dom.title ? linkEl.dom.title : false;

        linkEl.query('> .vpsEnlargeTagData').each(function(i) {
            var name = i.className.replace('vpsEnlargeTagData', '').trim();
            data[name] = i.innerHTML;
        }, this);
        var dataEl = linkEl.prev('.vpsEnlargeTagData');
        if (dataEl) {
            dataEl.query('> *').each(function(i) {
                if (i.className) {
                    var name = i.className.trim();
                    data[name] = i.innerHTML;
                }
            }, this);
        }

        var options = linkEl.down(".options", true);
        if (options && options.value) {
            options = Ext.decode(options.value);
        } else {
            options = {};
        }

        for (var i in options) {
            if (i == 'title') {
                if (options.title && !data.title) {
                    data.title = options.title;
                }
            } else if (i == 'fullSizeUrl') {
                if (options.fullSizeUrl) {
                    data.fullSizeLink = '<a href="'+options.fullSizeUrl+'" class="fullSizeLink" title="'+trlVps('Download original image')+'">'+trlVps('Download original image')+'</a> ';
                } else {
                    data.fullSizeLink = '';
                }
            } else {
                data[i] = options[i];
            }
        }

        data.image = {
            src: linkEl.dom.href,
            width: parseInt(m[1]),
            height: parseInt(m[2])
        };
        if (linkEl.nextImage) {
            data.nextImage = {
                src: linkEl.nextImage.child('img').dom.src,
                title: linkEl.nextImage.dom.title,
                type: 'next',
                text: trlVps('next')
            };
        }
        if (linkEl.previousImage) {
            data.previousImage = {
                src: linkEl.previousImage.child('img').dom.src,
                title: linkEl.previousImage.dom.title,
                type: 'previous',
                text: trlVps('previous')
            };
        }

        var tpls = Vpc.Basic.ImageEnlarge;
        if (data.nextImage) {
            data.nextImageButton = tpls.tplSwitchButton.apply(data.nextImage);
        } else {
            data.nextImageButton = data.showInactiveSwitchLinks ? '<img class="nextImgBtn" src="/assets/vps/Vpc/Basic/ImageEnlarge/EnlargeTag/nextInactive.png" width="44" height="50" alt="" />' : '&nbsp;';
        }
        if (data.previousImage) {
            data.previousImageButton = tpls.tplSwitchButton.apply(data.previousImage);
        } else {
            data.previousImageButton = data.showInactiveSwitchLinks ? '<img class="previousImgBtn" src="/assets/vps/Vpc/Basic/ImageEnlarge/EnlargeTag/previousInactive.png" width="44" height="50" alt="" />' : '&nbsp;';
        }

        if (!data.title) data.title = '';
        if (!data.imageCaption) data.imageCaption = '';
        if (!data.fullSizeLink) data.fullSizeLink = '';

        data.header = tpls.tplHeader.apply(data);
        data.body = tpls.tplBody.apply(data);
        data.footer = tpls.tplFooter.apply(data);

        tpls.tpl.overwrite(this.lightbox, data);

        var image = new Image();
        image.onload = (function(){
            if (this.lightbox.child('.image')) {
                this.lightbox.child('.loading').hide();
                this.lightbox.child('.centerImage').dom.src = linkEl.dom.href;
                this.lightbox.child('.image').fadeIn();
            }
        }).createDelegate(this);
        image.src = linkEl.dom.href;

        this.lightbox.child('.lightboxFooter').setWidth(m[1]);

        var applyNextPreviousEvents = function(imageLink, type) {
            // preload next image
            var tmpNextImage = new Image();
            tmpNextImage.src = imageLink.dom.href;
            // next small button
            this.lightbox.query('.'+type+'SwitchButton').each(function(el) {
                el = Ext.fly(el);
                if (type == 'next') {
                    el.setStyle('background-position', 'right '+Math.floor(m[2]*0.2)+'px');
                } else {
                    el.setStyle('background-position', 'left '+Math.floor(m[2]*0.2)+'px');
                }

                el.on('click', function(e) {
                    if (this.lightbox.lightbox.child('.image')) {
                        this.lightbox.lightbox.child('.image').fadeOut({
                            callback: this.lightbox.show(this.imageLink),
                            scope: this
                        });
                    } else {
                        this.lightbox.show(this.imageLink);
                    }
                }, {lightbox: this, imageLink: imageLink}, { stopEvent: true });
            }, this);
        };

        if (linkEl.nextImage) {
            applyNextPreviousEvents.call(this, linkEl.nextImage, 'next');
        }
        if (linkEl.previousImage) {
            applyNextPreviousEvents.call(this, linkEl.previousImage, 'previous');
        }

        this.lightbox.query('.closeButton').each(function(el) {
            el = Ext.fly(el);
            el.on('click', this.hide, this, { stopEvent: true });
        }, this);

        if (Vps.Basic.LinkTag.Extern.processLinks) {
            Vps.Basic.LinkTag.Extern.processLinks(this.lightbox.dom);
        }

        this.alignBox();
    },

    mask: function()
    {
        var maskEl = Ext.getBody().mask();
        Ext.getBody().removeClass('x-masked');
        maskEl.addClass('lightboxMask');
        maskEl.applyStyles('height:'+this.getPageSize()[1]+'px;');
        maskEl.on('click', this.hide, this);
    },

    unmask: function()
    {
        Ext.getBody().unmask();
        Ext.getBody().removeClass('lightboxShowOverflow');
    },

    getPageSize: function()
    {
        var xScroll, yScroll;

        if (window.innerHeight && window.scrollMaxY) {
            xScroll = window.innerWidth + window.scrollMaxX;
            yScroll = window.innerHeight + window.scrollMaxY;
        } else if (document.body.scrollHeight > document.body.offsetHeight){ // all but Explorer Mac
            xScroll = document.body.scrollWidth;
            yScroll = document.body.scrollHeight;
        } else { // Explorer Mac...would also work in Explorer 6 Strict, Mozilla and Safari
            xScroll = document.body.offsetWidth;
            yScroll = document.body.offsetHeight;
        }

        var windowWidth, windowHeight;

        if (self.innerHeight) { // all except Explorer
            if(document.documentElement.clientWidth){
                windowWidth = document.documentElement.clientWidth;
            } else {
                windowWidth = self.innerWidth;
            }
            windowHeight = self.innerHeight;
        } else if (document.documentElement && document.documentElement.clientHeight) { // Explorer 6 Strict Mode
            windowWidth = document.documentElement.clientWidth;
            windowHeight = document.documentElement.clientHeight;
        } else if (document.body) { // other Explorers
            windowWidth = document.body.clientWidth;
            windowHeight = document.body.clientHeight;
        }

        // for small pages with total height less then height of the viewport
        if(yScroll < windowHeight){
            pageHeight = windowHeight;
        } else {
            pageHeight = yScroll;
        }

        // for small pages with total width less then width of the viewport
        if(xScroll < windowWidth){
            pageWidth = xScroll;
        } else {
            pageWidth = windowWidth;
        }

        arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight);
        return arrayPageSize;
    }
};
*/