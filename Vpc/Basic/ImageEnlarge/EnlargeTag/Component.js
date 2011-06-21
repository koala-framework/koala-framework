
Vps.onContentReady(function() {
    var lightbox = new Vpc.Basic.ImageEnlarge();
    var els = document.getElementsByTagName('a');
    for (var i=0; i<els.length; i++) {
        if (els[i].rel.match(/enlarge_[0-9]+_[0-9]+/)) {
            Ext.EventManager.addListener(els[i], 'click', function(e) {
                lightbox.show(Ext.get(this), e);
                e.stopEvent();
            }, els[i], { stopEvent: true });
        }
        if (els[i].className.match(/vpcEnlargeTag/)) {
            Ext.DomHelper.append(els[i],
                { tag: 'span', cls: 'webZoom' }
            );
        }
    }
});


Ext.namespace("Vpc.Basic");

Vpc.Basic.ImageEnlarge = function()
{
    this.lightbox = Ext.get(
        Ext.DomHelper.append(Ext.getBody(),
            { tag: 'div', cls: 'lightbox webLightbox webStandard' }
        )
    );
};

Vpc.Basic.ImageEnlarge.tpl = new Ext.XTemplate(
    '<div class="lightboxHeader">{header}</div>',
    '<div class="lightboxBody">{body}</div>',
    '<div class="lightboxFooter">{footer}</div>'
);

Vpc.Basic.ImageEnlarge.tplHeader = new Ext.XTemplate(
    '<a class="closeButton" href="#">',
        '<img src="/assets/vps/Vpc/Basic/ImageEnlarge/EnlargeTag/close.png" width="42" height="42" alt="" />',
    '</a>'
);

Vpc.Basic.ImageEnlarge.tplBody = new Ext.XTemplate(
    '<div class="prevBtn">{previousImageButton}</div>',
    '<div class="nextBtn">{nextImageButton}</div>',
    /* alter Style, wird noch unterstützt, ersetzt durch die zwei folgenden Zeilen
    '<img src="{values.image.src}" width="{values.image.width}" height="{values.image.height}" class="centerImage" />'
     */
    '<div class="loading"><img src="/assets/vps/Vpc/Basic/ImageEnlarge/EnlargeTag/loading.gif" width="66" height="66" class="preloadImage" /></div>',
    '<div class="image" style="width:{values.image.width}px; height:{values.image.height}px"><img class="centerImage" /></div>'
    /*
    */
);

Vpc.Basic.ImageEnlarge.tplFooter = new Ext.XTemplate(
    '<tpl if="imageCaption"><p class="imageCaption<tpl if="title">Title</tpl>"><strong>{imageCaption}</strong></p></tpl>',
    '<tpl if="title"><p class="title">{title}</p></tpl>',
    '<tpl if="fullSizeLink"><p class="fullSizeLink">{fullSizeLink}</p></tpl>'
);

Vpc.Basic.ImageEnlarge.tplSwitchButton = new Ext.XTemplate(
    '<a class="switchButton {type}SwitchButton" href="#">&nbsp;</a>'
);

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
