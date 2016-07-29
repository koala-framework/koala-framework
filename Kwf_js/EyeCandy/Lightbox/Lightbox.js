// @require ModernizrPrefixed
var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var historyState = require('kwf/history-state');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');
var t = require('kwf/trl');
var injectAssets = require('kwf/inject-assets');
var oneTransitionEnd = require('kwf/lightbox/helper/one-transition-end');
var StylesRegistry = require('kwf/lightbox/styles-registry');
StylesRegistry.register('CenterBox', require('kwf/lightbox/style/center-box'));

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
    if (allByUrl[href] && !options.alwaysReload) {
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

onReady.onRender('.kwfUp-kwfLightbox', function lightboxEl(el) {
    //initialize lightbox that was not dynamically created (created by ContentSender/Lightbox)

    if (el[0].kwfLightbox) return;
    var options = $.parseJSON(el.find('input.options').val());
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
    if (!options.style) options.style = 'CenterBox';
    this.style = new StylesRegistry.styles[options.style](this);
};
Lightbox.prototype = {
    fetched: false,
    _blockOnContentReady: false,
    _isClosing: false,
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
        this._isClosing = false;

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
                    $('html').addClass('kwfUp-kwfLightboxAnimationEnd');
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
        $('html').removeClass('kwfUp-kwfLightboxAnimationEnd');
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
        if (this._isClosing) return; //prevent double-click on close button
        this._isClosing = true;
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
