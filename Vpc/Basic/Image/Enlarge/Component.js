
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

        var m = linkEl.dom.rel.match(/enlarge_([0-9]+)_([0-9]+)_?(.*)/);

        var imgWidth = parseInt(m[1]);
        var imgHeight = parseInt(m[2]);
        var fullSizePath = false;
        if (m[3]) {
            fullSizePath = m[3];
        }

        // head
        var hdHtml = '';
        if (fullSizePath) hdHtml += '<a href="'+fullSizePath+'" class="fullSizeLink" title="Bild in Originalgröße" target="_blank"></a> ';
        hdHtml += linkEl.dom.title ? linkEl.dom.title : '&nbsp;';
        var hd = Ext.DomHelper.overwrite(this.lightbox,
            { tag: 'div', cls:'lightboxHd', html: hdHtml }
        );

        // head - close
        var closeLink = Ext.get(Ext.DomHelper.insertFirst(hd,
            { tag: 'a', cls: 'lightboxClose', href: '#', children: [
                { tag: 'img', src: '/assets/vps/images/spacer.gif', alt: 'close' }
            ]}
        ));

        // body
        var bd = Ext.get(Ext.DomHelper.append(this.lightbox,
            { tag: 'div', cls: 'lightboxBd' }
        ));

        // body - image
        var imageTopMargin = (parseInt(bd.getStyle('height')) - imgHeight) / 2;
        if (imageTopMargin < 0) imageTopMargin = 0;

        var img = Ext.get(Ext.DomHelper.append(bd, {
            tag    : 'img',
            src    : linkEl.dom.href,
            alt    : '',
            width  : imgWidth,
            height : imgHeight,
            style  : 'margin-top: '+imageTopMargin+'px;'
        }));

        // foot
        var ft = Ext.DomHelper.append(this.lightbox,
            { tag: 'div', cls: 'lightboxFt' }
        );
        if (linkEl.nextImage) {
            // overlay next button
            var bigNext = Ext.get(Ext.DomHelper.append(bd, {
                tag: 'div', cls: 'lightboxNextBig'
            }));
            var tmpTitle = linkEl.nextImage.dom.title;
            if (tmpTitle.length > 18) {
                tmpTitle = tmpTitle.substr(0, 15)+'...';
            }
            Ext.DomHelper.append(bigNext, {
                tag: 'div', cls: 'lightboxNextBigContent', children: [
                    { tag: 'img', cls: 'lightboxNextBigButton', src: '/assets/vps/images/spacer.gif', alt: 'next' },
                    { tag: 'br' },
                    { tag: 'img', src: linkEl.nextImage.child('img').dom.src, alt: '' },
                    { tag: 'div', html: tmpTitle }
                ]
            });
            bigNext.on('click', function(e) {
                this.show(linkEl.nextImage);
            }, this, { stopEvent: true });
            bigNext.on('mouseover', function(e) {
                this.addClass('bigOver');
            }, bigPrevious);
            bigNext.on('mouseout', function(e) {
                this.removeClass('bigOver');
            }, bigPrevious);

            // small next button
            var nextButton = Ext.get(Ext.DomHelper.append(ft,
                { tag: 'a', cls:'lightboxNext', href:'#', children: [
                    { tag: 'img', src: '/assets/vps/images/spacer.gif', alt: 'next' }
                ]}
            ));
            nextButton.on('click', function(e) {
                this.show(linkEl.nextImage);
            }, this, { stopEvent: true });

            // preload next image
            var tmpNextImage = new Image();
            tmpNextImage.src = linkEl.nextImage.dom.href;
        }
        if (linkEl.previousImage) {
            // overlay previous button
            var bigPrevious = Ext.get(Ext.DomHelper.append(bd, {
                tag: 'div', cls: 'lightboxPreviousBig'
            }));
            var tmpTitle = linkEl.previousImage.dom.title;
            if (tmpTitle.length > 18) {
                tmpTitle = tmpTitle.substr(0, 15)+'...';
            }
            Ext.DomHelper.append(bigPrevious, {
                tag: 'div', cls: 'lightboxPreviousBigContent', children: [
                    { tag: 'img', cls: 'lightboxPreviousBigButton', src: '/assets/vps/images/spacer.gif', alt: 'previous' },
                    { tag: 'br' },
                    { tag: 'img', src: linkEl.previousImage.child('img').dom.src, alt: '' },
                    { tag: 'div', html: tmpTitle }
                ]
            });
            bigPrevious.on('click', function(e) {
                this.show(linkEl.previousImage);
            }, this, { stopEvent: true });
            bigPrevious.on('mouseover', function(e) {
                this.addClass('bigOver');
            }, bigPrevious);
            bigPrevious.on('mouseout', function(e) {
                this.removeClass('bigOver');
            }, bigPrevious);

            // previous small button
            var prevButton = Ext.get(Ext.DomHelper.append(ft,
                { tag: 'a', cls:'lightboxPrevious', href:'#', children: [
                    { tag: 'img', src: '/assets/vps/images/spacer.gif', alt: 'previous' }
                ]}
            ));
            prevButton.on('click', function(e) {
                this.show(linkEl.previousImage);
            }, this, { stopEvent: true });

            // preload previous image
            var tmpPreviousImage = new Image();
            tmpPreviousImage.src = linkEl.previousImage.dom.href;

        }

        closeLink.on('click', this.hide, this, { stopEvent: true });
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
