
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
    '<div class="lightboxBody">{body}</div',
    '<div Class="clear"></div>',
    '<div class="lightboxFooter">{footer}</div'
);

Vpc.Basic.ImageEnlarge.tplHeader = new Ext.XTemplate(
    '<a class="closeButton" href="#">',
        ''+trlVps('close')+' X',
    '</a>',
    '{fullSizeLink}'
);

Vpc.Basic.ImageEnlarge.tplBody = new Ext.XTemplate(
    '<img src="{values.image.src}" width="{values.image.width}" height="{values.image.height}" class="centerImage" />',
    '{nextImageBig}',
    '{previousImageBig}'
);
Vpc.Basic.ImageEnlarge.tplFooter = new Ext.XTemplate(
    '<div class="prevBtn">{previousImageButton}</div>',
    '<div class="title"><p class="title">{title}</p></div>',
    '<div class="nextBtn">{nextImageButton}</div>'
);

Vpc.Basic.ImageEnlarge.tplSwitchBig = new Ext.XTemplate(
    '<div class="switchBig {type}SwitchBig">',
        '<div class="lightboxContent">',
            '<p><tpl if="type==\'previous\'">« </tpl>{text}<tpl if="type==\'next\'"> »</tpl></p>',
            '<img width="185px" src="{src}" />',
        '</div>',
    '</div>'
);

Vpc.Basic.ImageEnlarge.tplSwitchButton = new Ext.XTemplate(
    '<a class="switchButton {type}SwitchButton" href="#">',
        '<tpl if="type==\'previous\'">« </tpl>{text}<tpl if="type==\'next\'"> »</tpl>',
    '</a>'
);

Vpc.Basic.ImageEnlarge.prototype =
{

    hide: function(e)
    {
        if (e) e.stopEvent();
        this.lightbox.applyStyles('display: none;');
        this.unmask();
    },

    show: function(linkEl)
    {
        this.mask();

        this.lightbox.applyStyles('display: block;');
        this.lightbox.center();

        var m = linkEl.dom.rel.match(/enlarge_([0-9]+)_([0-9]+)/);

        var data = {};

        data.title = linkEl.dom.title ? linkEl.dom.title : '&nbsp;';

        linkEl.query('> .vpsEnlargeTagData').each(function(i) {
            var name = i.className.replace('vpsEnlargeTagData', '').trim();
            data[name] = i.innerHTML;
        }, this);
        linkEl.prev('.vpsEnlargeTagData').query('> *').each(function(i) {
            if (i.className) {
                var name = i.className.trim();
                data[name] = i.innerHTML;
            }
        }, this);

        var options = linkEl.down(".options", true);
        if (options && options.value) {
            options = Ext.decode(options.value);
        } else {
            options = {};
        }
        if (options.fullSizeUrl) {
            data.fullSizeLink = '<a href="'+options.fullSizeUrl+'" class="fullSizeLink" title="'+trlVps('image in originalsize')+'" target="_blank"></a> ';
        } else {
            data.fullSizeLink = '';
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
            data.nextImageBig = tpls.tplSwitchBig.apply(data.nextImage);
        }
        if (data.previousImage) {
            data.previousImageButton = tpls.tplSwitchButton.apply(data.previousImage);
            data.previousImageBig = tpls.tplSwitchBig.apply(data.previousImage);
        }
        data.header = tpls.tplHeader.apply(data);
        data.footer = tpls.tplFooter.apply(data);
        data.body = tpls.tplBody.apply(data);

        tpls.tpl.overwrite(this.lightbox, data);

        var applyNexPreviousEvents = function(imageLink, type) {
            // preload next image
            var tmpNextImage = new Image();
            tmpNextImage.src = imageLink.dom.href;

            // overlay next button
            this.lightbox.query('.'+type+'SwitchBig').each(function(el) {
                el = Ext.get(el);
                el.on('click', function(e) {
                    this.lightbox.show(this.imageLink);
                }, {lightbox: this, imageLink: imageLink}, { stopEvent: true });
                el.on('mouseover', function(e) {
                    this.addClass('bigOver');
                }, el);
                el.on('mouseout', function(e) {
                    this.removeClass('bigOver');
                }, el);
            }, this);
            // next small button
            this.lightbox.query('.'+type+'SwitchButton').each(function(el) {
                el = Ext.fly(el);
                el.on('click', function(e) {
                    this.lightbox.show(this.imageLink);
                }, {lightbox: this, imageLink: imageLink}, { stopEvent: true });
            }, this);
        };

        if (linkEl.nextImage) {
            applyNexPreviousEvents.call(this, linkEl.nextImage, 'next');
        }
        if (linkEl.previousImage) {
            applyNexPreviousEvents.call(this, linkEl.previousImage, 'previous');
        }

        this.lightbox.query('.closeButton').each(function(el) {
            el = Ext.fly(el);
            el.on('click', this.hide, this, { stopEvent: true });
        }, this);

        this.lightbox.query('.lightboxBody img.centerImage').each(function(img) {
            img = Ext.fly(img);
            var imageTopMargin = (img.parent('.lightboxBody').getHeight() - img.getHeight()) / 2;
            if (imageTopMargin < 0) imageTopMargin = 0;
            img.setStyle('margin-top', imageTopMargin+'px');
        }, this);
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

        arrayPageSize = new Array(pageWidth,pageHeight,windowWidth,windowHeight) 
        return arrayPageSize;
    }
};
