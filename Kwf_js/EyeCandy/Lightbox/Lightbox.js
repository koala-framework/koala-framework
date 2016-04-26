// @require ModernizrPrefixed
var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var historyState = require('kwf/history-state');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');
var kwfExtend = require('kwf/extend');
var t = require('kwf/trl');
var injectAssets = require('kwf/inject-assets');

var statistics = require('kwf/statistics');
var currentOpen = null;
var escapeHandlerInstalled = false;
var allByUrl = {};
var onlyCloseOnPopstate;

$(document).on('click', 'a[data-kwc-lightbox]', function(event) {
    var el = event.currentTarget;
    var $el = $(el);
    var options = $el.data('kwc-lightbox');
    var href = $el.attr('href');
    if (options.lightboxUrl) {
        href = options.lightboxUrl; //ImagePage passes lightboxUrl as href points to img directly
    }
    if (allByUrl[href]) {
        l = allByUrl[href];
    } else {
        l = new Lightbox(href, options);
    }
    el.kwfLightbox = l;

    if (currentOpen && currentOpen.href == href) {
        //already open, ignore click
        event.preventDefault();
        return;
    }
    this.kwfLightbox.show({
        clickTarget: this
    });
    historyState.currentState.lightbox = href;
    historyState.pushState(document.title, href);

    event.preventDefault();
});

var oneTransitionEnd = function (el, callback, scope) {
    var transEndEventNames = {
        'WebkitTransition' : 'webkitTransitionEnd.kwfLightbox',
        'MozTransition'    : 'transitionend.kwfLightbox',
        'transition'       : 'transitionend.kwfLightbox'
    };
    var transitionType = Modernizr.prefixed('transition');
    if (!transitionType) return;

    var event = transEndEventNames[ transitionType ];
    if (transitionType != 'WebkitTransition') { // can be removed as soon as modernizr fixes https://github.com/Modernizr/Modernizr/issues/897
        event += ' ' +transEndEventNames['WebkitTransition'];
    }
    el.on(event, function() {
        el.off(event);
        callback.call(scope, arguments);
    });
};

onReady.onRender('.kwfUp-kwfLightbox', function lightboxEl(el) {
    //initialize lightbox that was not dynamically created (created by ContentSender/Lightbox)

    if (el[0].kwfLightbox) return;
    var options = jQuery.parseJSON(el.find('input.options').val());
    var l = new Lightbox(window.location.href, options);
    historyState.currentState.lightbox = window.location.href;
    historyState.updateState();
    l.lightboxEl = el;
    l.innerLightboxEl = el.find('.kwfUp-kwfLightboxInner');
    l.fetched = true;
    l.initialize();
    l.closeHref = window.location.href.substr(0, window.location.href.lastIndexOf('/'));
    l.contentEl = l.innerLightboxEl.find('.kwfUp-kwfLightboxContent');
    l.style.afterCreateLightboxEl();
    l.style.onContentReady();
    el[0].kwfLightbox = l;
    currentOpen = l;

    //Remove the kwfUp-kwfLightboxOpen class and get the transform matrix data
    //We need that info, for future open animations
    var transformName = Modernizr.prefixed('transform') || '';
    l.lightboxEl.hide();
    l.lightboxEl.removeClass('kwfUp-kwfLightboxOpen');
    l.lightboxEl.width(); //trigger layout
    l.lightboxEl.show();
    var matrix = l.innerLightboxEl.css(transformName);
    l.lightboxEl.hide();
    l.lightboxEl.addClass('kwfUp-kwfLightboxOpen');
    l.lightboxEl.width(); //trigger layout
    l.lightboxEl.show();
    var values = null;
    if (matrix) values = matrix.match(/-?[\d\.]+/g);
    if (values != null) {
        if (values[4] && values[4] == l.innerLightboxEl.outerWidth()) {
            //translatex
            l.innerLightboxEl.magicTransform = true;
            l.innerLightboxEl.magicTransformX = true;
        }
        if (values[5] && values[5] == l.innerLightboxEl.outerHeight()) {
            //translateY
            l.innerLightboxEl.magicTransform = true;
            l.innerLightboxEl.magicTransformY = true;
        }
    }
    //callOnContentReady so eg. ResponsiveEl can do it's job based on the new with of the lightbox
    onReady.callOnContentReady(l.contentEl, {action: 'show'});

    //lazy load parent content
    var mainContent = $('.kwfUp-kwfMainContent');
    if (mainContent.data('kwc-component-id')) {
        setTimeout(function() {
            $.ajax({
                url: getKwcRenderUrl(),
                data: { componentId: mainContent.data('kwc-component-id'), type: 'json' },
                dataType: 'json',
                context: this
            }).done(function(response) {
                injectAssets(response.assets);
                mainContent.html(response.content);
                onReady.callOnContentReady(mainContent, {action: 'render'});
            });
        }, 100);
    }

}, { priority: 10 }); //after ResponsiveEl so lightbox can adapt to responsive content

onReady.onContentReady(function lightboxContent(readyEl, options)
{
    if (!currentOpen) return;

    readyEl = $(readyEl);
    if (readyEl.is(':visible')) {
        //callOnContentReady was called for an element inside the lightbox, style can update the lightbox size
        if (currentOpen.lightboxEl
            && currentOpen.lightboxEl.is(':visible')
            && ($.contains(currentOpen.innerLightboxEl, readyEl)
            || $.contains(readyEl, currentOpen.innerLightboxEl))
        ) {
            currentOpen.style.onContentReady();
        }
    }
});

historyState.on('popstate', function() {
    if (onlyCloseOnPopstate) {
        //onlyCloseOnPopstate is set in closeAndPushState
        //if multiple lightboxes are in history and we close current one we go back in history until none is open
        //so just close current one and don't show others (required to avoid flicker on closing)
        if (currentOpen) {
            currentOpen.close();
        }
        return;
    }
    var lightbox = historyState.currentState.lightbox;
    if (lightbox) {
        if (!allByUrl[lightbox]) return;
        if (currentOpen != allByUrl[lightbox]) {
            allByUrl[lightbox].show();
        }
    } else {
        if (currentOpen) {
            currentOpen.close();
        }
    }
});

//if (!(Ext2.isMac && 'ontouchstart' in document.documentElement)) {
    var timer = 0;
    $(window).resize(function(ev) {
        clearTimeout(timer);
        timer = setTimeout(function(){
            if (currentOpen) {
                currentOpen.style.onResizeWindow(ev);
            }
        }, 100);
    });
/*
} else {
    //on iOS listen to orientationchange as resize event triggers randomly when scrolling
    $(window).on('orientationchange', function(ev) {
        if (currentOpen) {
            currentOpen.style.onResizeWindow(ev);
        }
    });
}
*/

var Lightbox = function(href, options) {
    this.href = href;
    allByUrl[href] = this;
    this.options = options;
    if (options.style) {
        this.style = new LightboxStyles[options.style](this);
    } else {
        this.style = new LightboxStyles.CenterBox(this);
    }
};
Lightbox.prototype = {
    fetched: false,
    _blockOnContentReady: false,
    createLightboxEl: function()
    {
        if (this.lightboxEl) return;

        var cls = 'kwfUp-kwfLightbox';
        if (this.options.style) cls += ' kwfUp-kwfLightbox'+this.options.style;
        if (this.options.cssClass) cls += ' '+this.options.cssClass;
        var lightbox = $(
            '<div class="'+cls+'">'+
                '<div class="kwfUp-kwfLightboxScrollOuter">'+
                    '<div class="kwfUp-kwfLightboxScroll">'+
                        '<div class="kwfUp-kwfLightboxBetween">'+
                            '<div class="kwfUp-kwfLightboxBetweenInner">'+
                                '<div class="kwfUp-kwfLightboxInner kwfUp-kwfLightboxLoading"><div class="kwfUp-loading"><div class="inner1"><div class="inner2">&nbsp;</div></div></div></div>'+
                            '</div>'+
                        '</div>'+
                    '</div>'+
                '</div>'+
            '</div>'
        );
        $(document.body).append(lightbox);
        lightbox[0].kwfLightbox = this; //don't initialize again in onContentReady

        this.lightboxEl = lightbox;
        this.innerLightboxEl = lightbox.find('.kwfUp-kwfLightboxInner');
        var el = this.innerLightboxEl;

        var transformName = Modernizr.prefixed('transform') || '';
        var matrix = el.css(transformName);
        var values = null;
        if (matrix) values = matrix.match(/-?[\d\.]+/g);
        if (values != null && values[4] && values[4] == el.outerWidth()) {
            //translatex
            this.innerLightboxEl.magicTransform = true;
            this.innerLightboxEl.magicTransformX = true;
            values[4] = ($(window).width()-el.outerWidth())/2 + el.outerWidth();
            var newMatrix = 'matrix('+values[0]+','+values[1]+','+values[2]+','+values[3]+','+values[4]+','+values[5]+')';
            this.innerLightboxEl.css(transformName, newMatrix);
        }
        if (values != null && values[5] && values[5] == el.outerWidth()) {
            //translateY
            this.innerLightboxEl.magicTransform = true;
            this.innerLightboxEl.magicTransformY = true;
            values[5] = ($(window).height()-el.outerHeight())/2 + el.outerHeight();
            var newMatrix = 'matrix('+values[0]+','+values[1]+','+values[2]+','+values[3]+','+values[4]+','+values[5]+')';
            this.innerLightboxEl.css(transformName, newMatrix);
        }
        //Thanks to the css-transforms specification we need to use this 0.001px fix
        //This is, because if the transform matrix is (0, 0, 0, 0, 0, 0) it is a non-invertible matrix
        //The spec says, that in this case the animation should fall back to a discrete animation ...
        //Link to the bug report: https://code.google.com/p/chromium/issues/detail?id=494914
        if (this.innerLightboxEl.magicTransform) {
            if (values[0] == 0) {
                this.innerLightboxEl.magicScale = true;
                values[0] = 0.001;
            }
            if (values[3] == 0) {
                this.innerLightboxEl.magicScale = true;
                values[3] = 0.001;
            }
            var newMatrix = 'matrix('+values[0]+','+values[1]+','+values[2]+','+values[3]+','+values[4]+','+values[5]+')';
            this.innerLightboxEl.css(transformName, newMatrix);
        }

        if (this.options.width) {
            el.width(parseInt(this.options.width) /*+ el.getBorderWidth("lr") + el.getPadding("lr")*/);
        }
        if (this.options.height) {
            el.height(parseInt(this.options.height) /*+ el.getBorderWidth("tb") + el.getPadding("tb")*/);
        }
        if (this.options.adaptHeight) {
            this.lightboxEl.addClass('kwfUp-adaptHeight');
        }
        this.style.afterCreateLightboxEl();
        this.lightboxEl.hide();
    },
    fetchContent: function()
    {
        if (this.fetched) return;
        this.fetched = true;

        $.ajax({
            url: getKwcRenderUrl(),
            data: { url: 'http://'+location.host+this.href, type: 'json' },
            dataType: 'json',
            context: this
        }).done(function(response) {

            injectAssets(response.assets);

            this.contentEl = $(
                '<div class="kwfUp-kwfLightboxContent"></div>'
            );
            this.closeButtonEl = $(
                '<a href="#" class="kwfUp-closeButton"><span class="kwfUp-innerCloseButton">'+t.trlKwf("Close")+'</span></a>'
            );
            var self = this;
            var appendContent = function() {
                self.innerLightboxEl.append(self.contentEl);
                self.innerLightboxEl.append(self.closeButtonEl);

                self.style.updateContent(response.content);

                if (self.lightboxEl.is(':visible')) {
                    self.contentEl.hide();
                }

                var showContent = function() {
                    self.innerLightboxEl.removeClass('kwfUp-kwfLightboxLoading');
                    self.innerLightboxEl.find('.kwfUp-loading').remove();
                    if (self.lightboxEl.is(':visible')) {
                        self.contentEl.show();
                    }
                    self.style.afterContentShown();
                    if (self.lightboxEl.is(':visible')) {
                        self.preloadLinks();
                    }
                };
                var imagesToLoad = 0;
                self.contentEl.find('img.kwfUp-hideWhileLoading').each(function() {
                    imagesToLoad++;
                    $(this).on('load', function() {
                        imagesToLoad--;
                        if (imagesToLoad <= 0) showContent.call(this);
                    });
                });
                if (imagesToLoad == 0) showContent.call(this);

                self.initialize();
            };
            //Check if the Lightbox is currently animating
            //If that's the case, wait till the animation is over and insert the content
            //Otherwise the animation could look bad, if the content changes the lightbox size
            if ($('body').hasClass('kwfUp-kwfLightboxAnimate'))  {
                oneTransitionEnd(this.innerLightboxEl, function() {
                    appendContent();
                }, this);
            } else {
                appendContent();
            }
        }).fail(function() {
            //fallback
            location.href = this.href;
        });
    },
    show: function(options)
    {
        $('html').addClass('kwfUp-kwfLightboxActive');
        this.createLightboxEl();
        this.style.onShow(options);

        if (!this.closeHref) {
            if (currentOpen) {
                this.closeHref = currentOpen.closeHref;
            } else {
                this.closeHref = window.location.href;
            }
        }

        if (currentOpen) {
            var closeOptions = {};
            if (options && options.clickTarget) {
                closeOptions.showClickTarget = options.clickTarget;
            }
            currentOpen.close(closeOptions);
        }
        currentOpen = this;
        this.showOptions = options;
        if (!this.fetched) {
            this.fetchContent();
        }
        if (!this.lightboxEl.is(':visible')) {
            this.lightboxEl.show();
            if (this.innerLightboxEl.magicTransform) {
                var transformName = Modernizr.prefixed('transform') || '';
                var matrix = this.innerLightboxEl.css(transformName);
                var values = matrix.match(/-?[\d\.]+/g);
                if (this.innerLightboxEl.magicScale) {
                    values[0] = 1;
                    values[3] = 1;
                }
                var newMatrix = 'matrix('+values[0]+','+values[1]+','+values[2]+','+values[3]+',0,0)';
                this.innerLightboxEl.css(transformName, newMatrix);
            }
            var transitionDurationName = Modernizr.prefixed('transitionDuration') || '';
            var duration = this.innerLightboxEl.css(transitionDurationName);
            if (parseFloat(duration)>0) {
                $('body').addClass('kwfUp-kwfLightboxAnimate');
                oneTransitionEnd(this.innerLightboxEl, function() {
                    $('body').removeClass('kwfUp-kwfLightboxAnimate');
                    onReady.callOnContentReady(this.lightboxEl, {action: 'show'});
                }, this);
            } else {
                onReady.callOnContentReady(this.lightboxEl, {action: 'show'});
            }
        }
        this.lightboxEl.addClass('kwfUp-kwfLightboxOpen');
        this.style.afterShow(options);

        statistics.trackView(this.href);
    },
    close: function(options) {
        $('html').removeClass('kwfUp-kwfLightboxActive');
        this.lightboxEl.hide();
        //so eg. flash component can remove object
        onReady.callOnContentReady(this.lightboxEl, {action: 'hide'});
        this.lightboxEl.show();

        this.style.onClose(options);
        this.lightboxEl.removeClass('kwfUp-kwfLightboxOpen');

        if (this.innerLightboxEl.magicTransform) {
            var transformName = Modernizr.prefixed('transform') || '';
            var matrix = this.innerLightboxEl.css(transformName);
            var values = matrix.match(/-?[\d\.]+/g);
            if (this.innerLightboxEl.magicTransformX) {
                values[4] = ($(window).width()-this.innerLightboxEl.outerWidth())/2 + this.innerLightboxEl.outerWidth();
            }
            if (this.innerLightboxEl.magicTransformY) {
                values[5] = ($(window).height()-this.innerLightboxEl.outerHeight())/2 + this.innerLightboxEl.outerHeight();
            }
            if (this.magicScale) {
                values[0] = 0.001;
                values[3] = 0.001;
            }
            var newMatrix = 'matrix('+values[0]+','+values[1]+','+values[2]+','+values[3]+','+values[4]+','+values[5]+')';
            this.innerLightboxEl.css(transformName, newMatrix);
        }
        currentOpen = null;
    },
    closeAndPushState: function() {
        if (historyState.entries > 0) {
            onlyCloseOnPopstate = true; //required to avoid flicker on closing, see popstate handler
            var previousEntries = historyState.entries;
            history.back();
            var closeLightbox = (function() {
                //didn't change yet, wait a bit longer
                if (previousEntries == historyState.entries) {
                    setTimeout(closeLightbox.bind(this), 10);
                    return;
                }
                //check if there is still a lightbox open
                //has to be defered because closing happens in 'popstate' event which is async in IE
                if (historyState.currentState.lightbox) {
                    previousEntries = historyState.entries;
                    history.back();
                    setTimeout(closeLightbox.bind(this), 1);
                } else {
                    //last entry in history that had lightbox open
                    onlyCloseOnPopstate = false;
                }
            });
            setTimeout(closeLightbox.bind(this), 1);
        } else {
            delete historyState.currentState.lightbox;
            historyState.replaceState(document.title, this.closeHref);
            //location.replace(this.closeHref);
            this.close();
        }
    },
    initialize: function()
    {
        var closeButtons = this.lightboxEl.find('.kwfUp-closeButton');

        if (!escapeHandlerInstalled) {
            escapeHandlerInstalled = true;
            $('body').keydown((function(e) {
                if (e.keyCode == 27 && currentOpen) {
                    currentOpen.closeAndPushState();
                }
            }));
        }

        closeButtons.click((function(ev) {
            ev.preventDefault();
            this.closeAndPushState();
        }).bind(this));
    },
    preloadLinks: function() {
        this.innerLightboxEl.find('a.kwfUp-preload').each(function() {
            if (this.kwfLightbox) this.kwfLightbox.preload();
        }, this);
    },
    preload: function() {
        this.createLightboxEl();
        this.fetchContent();
    }
};


var LightboxStyles = {};
LightboxStyles.Abstract = function(lightbox) {
    this.lightbox = lightbox;
    this.init();
};
LightboxStyles.Abstract.masks = 0;
LightboxStyles.Abstract.prototype = {

    init: function() {},
    afterCreateLightboxEl: function() {},
    afterContentShown: function() {},
    updateContent: function(responseText) {
        this.lightbox.contentEl.html(responseText);

        this._blockOnContentReady = true; //don't resize twice
        //callOnContentReady so eg. ResponsiveEl can do it's job which might change the height of contents
        onReady.callOnContentReady(this.lightbox.contentEl, {action: 'render'});
        this._blockOnContentReady = false;
    },
    onShow: function() {},
    afterShow: function() {},
    onClose: function() {},
    afterClose: function() {},
    onContentReady: function() {},
    onResizeWindow: function() {},

    initMask: function() {
        var maskEl = this.lightbox.lightboxEl.find('.kwfUp-kwfLightboxMask');
        maskEl.click(function(ev) {
            if ($(document.body).find('.kwfUp-kwfLightboxMask').is(ev.target)) {
                if (currentOpen) {
                    currentOpen.style.onMaskClick();
                }
            }
        });
    },
    mask: function() {
        //calling mask multiple times in valid, unmask must be called exactly often
        var maskEl = $(document.body).find('.kwfUp-kwfLightboxMask');
        if (maskEl.length) {
            maskEl.show();
            setTimeout(function(){
                maskEl.addClass('kwfUp-kwfLightboxMaskOpen');
            }, 0);
        } else {
            maskEl = $('<div class="kwfUp-kwfLightboxMask"></div>');
            var lightboxEl = this.lightbox.lightboxEl;
            lightboxEl.find('.kwfUp-kwfLightboxScrollOuter').append(maskEl);
            setTimeout(function(){
                lightboxEl.scrollTop(50000);
            }, 0);
            setTimeout(function(){
                maskEl.addClass('kwfUp-kwfLightboxMaskOpen');
            }, 0);
            this.initMask();
        }
    },
    unmask: function() {
        var lightboxMaskEl = this.lightbox.lightboxEl.find('.kwfUp-kwfLightboxMask');
        var transitionDurationName = Modernizr.prefixed('transitionDuration') || '';
        var duration = lightboxMaskEl.css(transitionDurationName);
        lightboxMaskEl.removeClass('kwfUp-kwfLightboxMaskOpen');
        if (parseFloat(duration)>0) {
            oneTransitionEnd(lightboxMaskEl, function() {
                lightboxMaskEl.hide();
            }, this);
        } else {
            lightboxMaskEl.hide();
        }
    },
    onMaskClick: function()
    {
        this.lightbox.closeAndPushState();
    }
};

LightboxStyles.CenterBox = kwfExtend(LightboxStyles.Abstract, {
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

        var maxSize = this._getMaxContentSize();

        if (originalWidth > maxSize.width) {
            var ratio = originalHeight / originalWidth;
            var offs = originalWidth-maxSize.width;
            originalWidth -= offs;
            if (this.lightbox.options.adaptHeight) originalHeight -= offs*ratio;
        }
        if (this.lightbox.options.adaptHeight && originalHeight > maxSize.height) {
            var ratio = originalWidth / originalHeight;
            var offs = originalHeight-maxSize.height;
            originalHeight -= offs;
            originalWidth -= offs*ratio;
        }
        if (!this.lightbox.options.adaptHeight && originalHeight > maxSize.height) {
            //delete originalHeight;
        }
        this.lightbox.innerLightboxEl.width(originalWidth);
        this.lightbox.innerLightboxEl.height(originalHeight);
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
        LightboxStyles.CenterBox.superclass.updateContent.apply(this, arguments);

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
    },
    onClose: function(options) {
        var transitionDurationName = Modernizr.prefixed('transitionDuration') || '';
        var duration = this.lightbox.innerLightboxEl.css(transitionDurationName);
        if (parseFloat(duration)>0) {
            $('body').addClass('kwfUp-kwfLightboxAnimate');
            oneTransitionEnd(this.lightbox.innerLightboxEl, function() {
                $('html').removeClass('kwfUp-kwfLightboxActive');
                $('body').removeClass('kwfUp-kwfLightboxAnimate');
                this.lightbox.lightboxEl.hide();
                this.afterClose();
            }, this);
        } else {
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
