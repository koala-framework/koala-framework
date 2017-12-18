var AbstractStyle = require('kwf/commonjs/lightbox/style/abstract');
var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');
var kwfExtend = require('kwf/commonjs/extend');
var oneTransitionEnd = require('kwf/commonjs/element/one-transition-end');
var lightboxHelper = require('kwf/commonjs/lightbox/lightbox-helper');

var CenterBoxStyle = kwfExtend(AbstractStyle, {
    init: function()
    {
        this._previousWindowWidth = $(window).width();
        this._previousWindowHeight = $(window).height();
    },

    afterCreateLightboxEl: function() {
        this.lightbox.lightboxEl.click((function(ev) {
            if (this.lightbox.lightboxEl.is(ev.target)) {
                this.lightbox.closeAndPushState();
            }
        }).bind(this));

        this._resizeContent();
    },
    _resizeContent: function()
    {
        this._updateMobile();

        //if content is larger than window, resize accordingly
        var originalWidth = this.lightbox.innerLightboxEl.width();
        var originalHeight = this.lightbox.innerLightboxEl.height();
        var newWidth = originalWidth;
        var newHeight = originalHeight;

        var maxSize = this._getMaxContentSize();

        if (newWidth > maxSize.width) {
            var ratio = newHeight / newWidth;
            var offs = newWidth-maxSize.width;
            newWidth -= offs;
            if (this.lightbox.options.adaptHeight) newHeight -= offs*ratio;
        }
        if (this.lightbox.options.adaptHeight && newHeight > maxSize.height) {
            var ratio = newWidth / newHeight;
            var offs = newHeight-maxSize.height;
            newHeight -= offs;
            newWidth -= offs*ratio;
        }
        if (!this.lightbox.options.adaptHeight && newHeight > maxSize.height) {
            //delete originalHeight;
        }
        this.lightbox.innerLightboxEl.width(newWidth);
        this.lightbox.innerLightboxEl.height(newHeight);
        if (newWidth != originalWidth) {
            onReady.callOnContentReady(this.lightbox.innerLightboxEl, { action: 'widthChange' });
        }
    },
    afterContentShown: function() {

        //reset to initial size so lightbox can grow
        var initialSize = {
            width: null,
            height: null
        };
        if (this.lightbox.options.width) initialSize.width = this.lightbox.options.width;
        if (this.lightbox.options.height) initialSize.height = this.lightbox.options.height;
        this.lightbox.innerLightboxEl.css(initialSize);

        this._resizeContent();
    },
    _getOuterMargin: function()
    {
        var maxSize = this._getMaxContentSize(false);
        if (maxSize.width <= 650) {
            return 0;
        } else if (maxSize.width <= 1100) {
            return 20;
        } else if (maxSize.width <= 1600) {
            return 40;
        } else {
            return 60;
        }
    },
    _updateMobile: function()
    {
        if (this._getOuterMargin() == 0) {
            this.lightbox.lightboxEl.addClass('kwfUp-mobile');
        } else {
            this.lightbox.lightboxEl.removeClass('kwfUp-mobile');
        }
    },
    _getMaxContentSize: function(subtractOuterMargin) {
        this.lightbox.lightboxEl.css('overflow', 'hidden');
        var maxSize = {
            width: this.lightbox.lightboxEl.innerWidth(),
            height: this.lightbox.lightboxEl.innerHeight()
        };
        this.lightbox.lightboxEl.css('overflow', '');
        if (subtractOuterMargin !== false) {
            maxSize.width -= this._getOuterMargin()*2;
            maxSize.height -= this._getOuterMargin()*2;
        }
        maxSize.width -= parseInt(this.lightbox.innerLightboxEl.css('paddingLeft'))
                            + parseInt(this.lightbox.innerLightboxEl.css('paddingRight'));
        maxSize.height -= parseInt(this.lightbox.innerLightboxEl.css('paddingTop'))
                            + parseInt(this.lightbox.innerLightboxEl.css('paddingBottom'));

        return maxSize;
    },


    //http://stackoverflow.com/a/13382873
    _getScrollbarWidth: function() {
        var outer = document.createElement("div");
        outer.style.visibility = "hidden";
        outer.style.width = "200px";
        document.body.appendChild(outer);

        var widthNoScroll = outer.offsetWidth;
        outer.style.overflow = "scroll";

        var inner = document.createElement("div");
        inner.style.width = "100%";
        outer.appendChild(inner);

        var widthWithScroll = inner.offsetWidth;

        outer.parentNode.removeChild(outer);

        return widthNoScroll - widthWithScroll;
    },


    _getContentSize: function(dontDeleteHeight)
    {
        var newWidth = this.lightbox.contentEl.width();
        var newHeight = this.lightbox.contentEl.height();
        var maxSize = this._getMaxContentSize();
        if (newWidth > maxSize.width) newWidth = maxSize.width;

        if (this.lightbox.options.adaptHeight && newHeight > maxSize.height) {
            newHeight = maxSize.height;
        } else {
            if (!dontDeleteHeight) {
                newHeight = '';
            }
        }

        return {
            width: newWidth,
            height: newHeight
        };
    },

    //update the lightbox content which was loaded using ajax
    updateContent: function(responseText)
    {
        var isVisible = this.lightbox.lightboxEl.is(':visible');
        this.lightbox.lightboxEl.show(); //to mesaure

        var originalHeight = this.lightbox.innerLightboxEl.height();
        var originalWidth = this.lightbox.innerLightboxEl.width();

        //do the actual update + callOnContentReady
        CenterBoxStyle.superclass.updateContent.apply(this, arguments);

        if (!this.lightbox.options.height) this.lightbox.innerLightboxEl.css('height', '');
        if (!this.lightbox.options.width) this.lightbox.innerLightboxEl.css('width', '');
        if (isVisible) {
            var newSize = this._getContentSize(true);
            this.lightbox.innerLightboxEl.css(newSize);
            if (this.lightbox.innerLightboxEl.css('backgroundColor')) {
                //animate size only if backgroundColor is set - else it doesn't make sense
                this.lightbox.innerLightboxEl.width(originalWidth);
                this.lightbox.innerLightboxEl.height(originalHeight);
                var newSize = this._getContentSize();
                this.lightbox.innerLightboxEl.css(newSize);
            }
        } else {
            var newSize = this._getContentSize();
            this.lightbox.innerLightboxEl.css(newSize);
            this.lightbox.lightboxEl.hide();
        }
    },
    onShow: function() {
        this.mask();
        var scrollbarWidth = this._getScrollbarWidth();
        $('html').addClass('kwfUp-kwfLightboxActive');

        //Add margin on html to compensate missing scrollbar when lightbox is open
        //because of ugly flicker when you close the lightbox
        if (scrollbarWidth != 0) {
            $('html').css("margin-right", scrollbarWidth + "px");
        }
    },
    onClose: function(options) {
        var duration = this.lightbox.innerLightboxEl.css('transitionDuration');
        if (parseFloat(duration)>0) {
            $('body').addClass('kwfUp-kwfLightboxAnimate');
            oneTransitionEnd(this.lightbox.innerLightboxEl, function() {
                if (!lightboxHelper.currentOpen) {
                    $('html').removeClass('kwfUp-kwfLightboxActive');
                    $('html').css("margin-right", "");
                }
                $('body').removeClass('kwfUp-kwfLightboxAnimate');
                this.lightbox.lightboxEl.hide();
                this.afterClose();
            }, this);
        } else {
            $('html').removeClass('kwfUp-kwfLightboxActive');
            $('html').css("margin-right", "");
            this.lightbox.lightboxEl.hide();
            this.afterClose();
        }
        this.unmask();
    },

    //called if element *inside* lightbox did fire callOnContentReady
    //it might have changed it's height and we need to adapt the lightbox size
    onContentReady: function()
    {
        if (this.lightbox._blockOnContentReady) return;
        if (!this.lightbox.contentEl) return;
        this.initMask();
    },

    //the browser window resized, the lightbox content might be responsive - update lightbox size
    onResizeWindow: function(ev)
    {
        if ($(window).width() == this._previousWindowWidth && $(window).height() == this._previousWindowHeight) {
            return;
        }
        this._previousWindowWidth = $(window).width();
        this._previousWindowHeight = $(window).height();

        //reset to initial size so lightbox can grow
        var initialSize = {
            width: null,
            height: null
        };
        if (this.lightbox.options.width) initialSize.width = this.lightbox.options.width;
        if (this.lightbox.options.height) initialSize.height = this.lightbox.options.height;
        this.lightbox.innerLightboxEl.css(initialSize);

        this._resizeContent();
    }
});
module.exports = CenterBoxStyle;
