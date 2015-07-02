var onReady = require('kwf/on-ready');
var historyState = require('kwf/history-state');

Kwf.namespace('Kwf.EyeCandy.Lightbox');

$(document).on('click', 'a[data-kwc-lightbox]', function(event) {
    var el = event.currentTarget;
    var $el = $(el);
    var options = $el.data('kwc-lightbox');
    if (Kwf.EyeCandy.Lightbox.allByUrl[$el.attr('href')]) {
        l = Kwf.EyeCandy.Lightbox.allByUrl[$el.attr('href')];
    } else {
        l = new Kwf.EyeCandy.Lightbox.Lightbox($el.attr('href'), options);
    }
    el.kwfLightbox = l;

    if (Kwf.EyeCandy.Lightbox.currentOpen &&
        Kwf.EyeCandy.Lightbox.currentOpen.href == $el.attr('href')
    ) {
        //already open, ignore click
        event.preventDefault();
        return;
    }
    this.kwfLightbox.show({
        clickTarget: this
    });
    historyState.currentState.lightbox = this.href;
    historyState.pushState(document.title, this.href);

    event.preventDefault();
});

onReady.onRender('.kwfLightbox', function lightboxEl(el) {
    //initialize lightbox that was not dynamically created (created by ContentSender/Lightbox)
    if (el[0].kwfLightbox) return;
    var options = jQuery.parseJSON(el.find('input.options').val());
    var l = new Kwf.EyeCandy.Lightbox.Lightbox(window.location.href, options);
    historyState.currentState.lightbox = window.location.href;
    historyState.updateState();
    l.lightboxEl = el;
    l.innerLightboxEl = el.find('.kwfLightboxInner');
    l.fetched = true;
    l.initialize();
    l.closeHref = window.location.href.substr(0, window.location.href.lastIndexOf('/'));
    l.contentEl = l.innerLightboxEl.find('.kwfLightboxContent');
    l.style.afterCreateLightboxEl();
    l.style.onShow();
    l.style.onContentReady();
    el[0].kwfLightbox = l;
    Kwf.EyeCandy.Lightbox.currentOpen = l;

    //callOnContentReady so eg. ResponsiveEl can do it's job based on the new with of the lightbox
    Kwf.callOnContentReady(l.contentEl, {action: 'show'});
}, { priority: 10 }); //after ResponsiveEl so lightbox can adapt to responsive content

onReady.onContentReady(function lightboxContent(readyEl, options)
{
    if (!Kwf.EyeCandy.Lightbox.currentOpen) return;

    readyEl = $(readyEl);
    if (readyEl.is(':visible')) {
        //callOnContentReady was called for an element inside the lightbox, style can update the lightbox size
        if (Kwf.EyeCandy.Lightbox.currentOpen.lightboxEl
            && Kwf.EyeCandy.Lightbox.currentOpen.lightboxEl.is(':visible')
            && ($.contains(Kwf.EyeCandy.Lightbox.currentOpen.innerLightboxEl, readyEl)
            || $.contains(readyEl, Kwf.EyeCandy.Lightbox.currentOpen.innerLightboxEl))
        ) {
            Kwf.EyeCandy.Lightbox.currentOpen.style.onContentReady();
        }
    }
});

historyState.on('popstate', function() {
    if (Kwf.EyeCandy.Lightbox.onlyCloseOnPopstate) {
        //onlyCloseOnPopstate is set in closeAndPushState
        //if multiple lightboxes are in history and we close current one we go back in history until none is open
        //so just close current one and don't show others (required to avoid flicker on closing)
        if (Kwf.EyeCandy.Lightbox.currentOpen) {
            Kwf.EyeCandy.Lightbox.currentOpen.close();
        }
        return;
    }
    var lightbox = historyState.currentState.lightbox;
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

/* TODO commonjs
if (!(Ext2.isMac && 'ontouchstart' in document.documentElement)) {
    var timer = 0;
    $(window).resize(function(ev) {
        clearTimeout(timer);
        timer = setTimeout(function(){
            if (Kwf.EyeCandy.Lightbox.currentOpen) {
                Kwf.EyeCandy.Lightbox.currentOpen.style.onResizeWindow(ev);
            }
        }, 100);
    });

} else {
*/
    //on iOS listen to orientationchange as resize event triggers randomly when scrolling
    $(window).on('orientationchange', function(ev) {
        if (Kwf.EyeCandy.Lightbox.currentOpen) {
            Kwf.EyeCandy.Lightbox.currentOpen.style.onResizeWindow(ev);
        }
    });
// }

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

        var cls = 'kwfLightbox';
        if (this.options.style) cls += ' kwfLightbox'+this.options.style;
        if (this.options.cssClass) cls += ' '+this.options.cssClass;
        var lightbox = $(
            '<div class="'+cls+'">'+
                '<div class="kwfLightboxInner kwfLightboxLoading"><div class="loading"><div class="inner1"><div class="inner2">&nbsp;</div></div></div></div>'+
            '</div>'
        );
        $(document.body).append(lightbox);
        lightbox[0].kwfLightbox = this; //don't initialize again in onContentReady

        this.lightboxEl = lightbox;
        this.innerLightboxEl = lightbox.find('.kwfLightboxInner');
        var el = this.innerLightboxEl;

        if (this.options.width) {
            el.width(parseInt(this.options.width) /*+ el.getBorderWidth("lr") + el.getPadding("lr")*/);
        }
        if (this.options.height) {
            el.height(parseInt(this.options.height) /*+ el.getBorderWidth("tb") + el.getPadding("tb")*/);
        }
        if (this.options.adaptHeight) {
            this.lightboxEl.addClass('adaptHeight');
        }
        this.style.afterCreateLightboxEl();
        this.lightboxEl.hide();
    },
    fetchContent: function()
    {
        if (this.fetched) return;
        this.fetched = true;

        $.ajax({
            url: Kwf.getKwcRenderUrl(),
            data: { url: 'http://'+location.host+this.href },
            dataType: 'html',
            context: this
        }).done(function(responseText) {
            this.contentEl = $(
                '<div class="kwfLightboxContent"></div>'
            );
            this.closeButtonEl = $(
                '<a href="#" class="closeButton"></a>'
            );
            this.innerLightboxEl.append(this.contentEl);
            this.innerLightboxEl.append(this.closeButtonEl);

            this.style.updateContent(responseText);

            if (this.lightboxEl.is(':visible')) {
                this.contentEl.hide();
            }

            var self = this;
            var showContent = function() {
                self.innerLightboxEl.removeClass('kwfLightboxLoading');
                self.innerLightboxEl.find('.loading').remove();
                if (self.lightboxEl.is(':visible')) {
                    self.contentEl.fadeIn();
                }
                self.style.afterContentShown();
                if (self.lightboxEl.is(':visible')) {
                    self.preloadLinks();
                }
            };
            var imagesToLoad = 0;
            this.contentEl.find('img.hideWhileLoading').each(function() {
                imagesToLoad++;
                $(this).on('load', function() {
                    imagesToLoad--;
                    if (imagesToLoad <= 0) showContent.call(this);
                });
            });
            if (imagesToLoad == 0) showContent.call(this);

            this.initialize();
        }).fail(function() {
            //fallback
            location.href = this.href;
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

        this.showOptions = options;

        this.lightboxEl.addClass('kwfLightboxOpen');
        if (this.fetched) {
            if (!this.lightboxEl.is(':visible')) {
                this.lightboxEl.show();
                Kwf.callOnContentReady(this.lightboxEl, {action: 'show'});
                this.style.afterContentShown();
                this.preloadLinks();
            }
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
        Kwf.callOnContentReady(this.lightboxEl, {action: 'hide'});
        this.lightboxEl.show();

        this.style.onClose(options);
        this.lightboxEl.removeClass('kwfLightboxOpen');
        Kwf.EyeCandy.Lightbox.currentOpen = null;
    },
    closeAndPushState: function() {
        if (historyState.entries > 0) {
            Kwf.EyeCandy.Lightbox.onlyCloseOnPopstate = true; //required to avoid flicker on closing, see popstate handler
            var previousEntries = historyState.entries;
            history.back();
            var closeLightbox = (function() {
                //didn't change yet, wait a bit longer
                if (previousEntries == historyState.entries) {
                    closeLightbox.defer(10, this);
                    return;
                }
                //check if there is still a lightbox open
                //has to be defered because closing happens in 'popstate' event which is async in IE
                if (historyState.currentState.lightbox) {
                    previousEntries = historyState.entries;
                    history.back();
                    closeLightbox.defer(1, this);
                } else {
                    //last entry in history that had lightbox open
                    Kwf.EyeCandy.Lightbox.onlyCloseOnPopstate = false;
                }
            });
            closeLightbox.defer(1, this);
        } else {
            delete historyState.currentState.lightbox;
            historyState.replaceState(document.title, this.closeHref);
            //location.replace(this.closeHref);
            this.close();
        }
    },
    initialize: function()
    {
        var closeButtons = this.innerLightboxEl.find('.closeButton');
        closeButtons.click((function(ev) {
            ev.preventDefault();
            this.closeAndPushState();
        }).bind(this));
    },
    preloadLinks: function() {
        this.innerLightboxEl.find('a.preload').each(function() {
            if (this.kwfLightbox) this.kwfLightbox.preload();
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
    this.init();
};
Kwf.EyeCandy.Lightbox.Styles.Abstract.masks = 0;
Kwf.EyeCandy.Lightbox.Styles.Abstract.prototype = {
    init: function() {},
    afterCreateLightboxEl: function() {},
    afterContentShown: function() {},
    updateContent: function(responseText) {
        this.lightbox.contentEl.html(responseText);

        this._blockOnContentReady = true; //don't resize twice
        //callOnContentReady so eg. ResponsiveEl can do it's job which might change the height of contents
        Kwf.callOnContentReady(this.lightbox.contentEl, {action: 'render'});
        this._blockOnContentReady = false;
    },
    onShow: function() {},
    afterShow: function() {},
    onClose: function() {},
    afterClose: function() {},
    onContentReady: function() {},
    onResizeWindow: function() {},

    mask: function() {
        //calling mask multiple times in valid, unmask must be called exactly often
        Kwf.EyeCandy.Lightbox.Styles.Abstract.masks++;
        if (Kwf.EyeCandy.Lightbox.Styles.Abstract.masks > 1) return;
        $(document.body).addClass('kwfLightboxTheaterMode');
        var maskEl = $(document.body).find('.lightboxMask');
        if (maskEl.length) {
            maskEl.show();
        } else {
            maskEl = $(document.body).append('<div class="lightboxMask"></div>');
            maskEl.click(function(ev) {
                if ($(document.body).find('.lightboxMask').is(ev.target)) {
                    if (Kwf.EyeCandy.Lightbox.currentOpen) {
                        Kwf.EyeCandy.Lightbox.currentOpen.style.onMaskClick();
                    }
                }
            });
        }
    },
    unmask: function() {
        Kwf.EyeCandy.Lightbox.Styles.Abstract.masks--;
        if (Kwf.EyeCandy.Lightbox.Styles.Abstract.masks > 0) return;
        $(document.body).find('.lightboxMask').fadeOut({
            complete: function() {
                $(document.body).removeClass('kwfLightboxTheaterMode');
                $(document.body).find('.lightboxMask').hide();
            }
        });
    },
    onMaskClick: function()
    {
        this.lightbox.closeAndPushState();
    }
};

Kwf.EyeCandy.Lightbox.Styles.CenterBox = Kwf.extend(Kwf.EyeCandy.Lightbox.Styles.Abstract, {
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
        this._center(false);
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
        this._center(false);
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
            this.lightbox.lightboxEl.addClass('mobile');
        } else {
            this.lightbox.lightboxEl.removeClass('mobile');
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
        Kwf.EyeCandy.Lightbox.Styles.CenterBox.superclass.updateContent.apply(this, arguments);

        if (!this.lightbox.options.height) this.lightbox.innerLightboxEl.css('height', '');
        if (!this.lightbox.options.width) this.lightbox.innerLightboxEl.css('width', '');
        if (isVisible) {
            var newSize = this._getContentSize(true);
            this.lightbox.innerLightboxEl.css(newSize);
            if (this.lightbox.innerLightboxEl.css('backgroundColor')) {
                //animate size only if backgroundColor is set - else it doesn't make sense
                this._center(true);
                this.lightbox.innerLightboxEl.width(originalWidth);
                this.lightbox.innerLightboxEl.height(originalHeight);
                this.lightbox.innerLightboxEl.animate(newSize, {
                    complete: (function() {
                        var newSize = this._getContentSize();
                        this.lightbox.innerLightboxEl.css(newSize);
                    }).bind(this)
                });
            } else {
                this._center(false);
            }
        } else {
            var newSize = this._getContentSize();
            this.lightbox.innerLightboxEl.css(newSize);
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
            complete: (function() {
                this.afterClose();
            }).bind(this)
        });
        this.unmask();
    },
    _getCenterXy: function() {
        var winSize = this._getMaxContentSize(false);
        var xy = {
            left: (winSize.width - this.lightbox.innerLightboxEl.width()) / 2 + $(window).scrollLeft(),
            top: (winSize.height - this.lightbox.innerLightboxEl.height()) / 2 + $(window).scrollTop()
        };

        //if lightbox is larget than viewport don't position lightbox above, the user can only scroll down
        var m = this._getOuterMargin();
        if (xy.left < $(window).scrollLeft()+m) xy.left = $(window).scrollLeft()+m;
        if (xy.top < $(window).scrollTop()+m) xy.top = $(window).scrollTop()+m;
        xy.left = Math.floor(xy.left);
        xy.top = Math.floor(xy.top);

        return xy;
    },
    _center: function(anim) {
        if (!this.lightbox.lightboxEl.is(':visible')) return;
        this.lightbox.innerLightboxEl[anim ? 'animate' : 'css'](this._getCenterXy());
    },

    //called if element *inside* lightbox did fire callOnContentReady
    //it might have changed it's height and we need to adapt the lightbox size
    onContentReady: function()
    {
        if (this.lightbox._blockOnContentReady) return;
        if (!this.lightbox.contentEl) return;

        //adjust size if height changed
        var newSize = this._getContentSize();
        var originalHeight = this.lightbox.innerLightboxEl.height();
        var originalWidth = this.lightbox.innerLightboxEl.width();
        this.lightbox.innerLightboxEl.css(newSize); //set to new size so centering works (no animation)
        var centerXy = this._getCenterXy();

        var position = this.lightbox.innerLightboxEl.position();
        position.left = centerXy.left;
        if (centerXy.top < position.top) position.top = centerXy.top; //move up, but not down
        /*
        //animation to new position disabled, buggy
        this.lightbox.innerLightboxEl.setXY(xy, true);
        this.lightbox.innerLightboxEl.width(originalWidth); //set back to previous size for animation
        this.lightbox.innerLightboxEl.height(originalHeight);
        this.lightbox.innerLightboxEl.setSize(newSize, null, true); //now animate to new size
        */

        //instead center unanimated
        this.lightbox.innerLightboxEl.css(position);
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
