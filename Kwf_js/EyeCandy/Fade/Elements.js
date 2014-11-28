Kwf.onJElementReady('div.kwfFadeElements', function fadeElements(element) {
    var fadeClass = element.find('.fadeClass');
    var selector = element.find('.fadeSelector')[0].value;
    var config = element.find('.fadeConfig'); // optional
    if (config && config[0]) {
        config = $.parseJSON(config[0].value);
    } else {
        config = { };
    }
    var textSelector = element.find('.textSelector'); // optional
    if (textSelector && textSelector[0]) {
        config.textSelector = textSelector[0].value;
    }

    config.selector = selector;
    config.selectorRoot = element;

    var cls = Kwf.Fade.Elements;
    if (fadeClass.length) {
        cls = eval(fadeClass[0].value);
        delete fadeClass;
    }

    element = element.get(0);
    element.fadeElementsObject = new cls(config);
    element.fadeElementsObject.start();
});


Kwf.onJElementWidthChange('div.kwfFadeElements', function fadeElementsWidthChange(element) {
    element = element.get(0);
    if (element.fadeElementsObject) {
        element.fadeElementsObject.calculateMaxHeight();
    }
}, {priority: 10}); /* after ResponsiveEl */


if (!Kwf.Fade) Kwf.Fade = {};

Kwf.Fade.Elements = function(cfg) {
    this.selector = cfg.selector;

    this.animationType = 'fade';
    this.elementAccessDirect = false; // optional: displays direct acces links to each image
    this.elementAccessPlayPause = false; // optional: displayes play / pause button
    this.elementAccessLinks = false; // optional, deprecated: displays both of above
    this.elementAccessNextPrevious = false;
    this.selectorRoot = document;
    this.fadeDuration = 1.5;
    this.easingFadeOut = 'ease'; //TODO change names to fit every animation
    this.easingFadeIn = 'ease';
    this.fadeEvery = 7;
    this.startRandom = true;
    this.autoStart = true;
    this.direction = cfg.direction;

    if (typeof cfg.template != 'undefined') this._template = cfg.template;
    if (typeof cfg.animationType != 'undefined') this.animationType = cfg.animationType;
    if (typeof cfg.elementAccessPlayPause != 'undefined') this.elementAccessPlayPause = cfg.elementAccessPlayPause;
    if (typeof cfg.elementAccessDirect != 'undefined') this.elementAccessDirect = cfg.elementAccessDirect;
    if (typeof cfg.elementAccessLinks != 'undefined' && cfg.elementAccessLinks) {
        this.elementAccessPlayPause = cfg.elementAccessLinks;
        this.elementAccessDirect = cfg.elementAccessLinks;
    }
    if (typeof cfg.elementAccessNextPrevious != 'undefined') this.elementAccessNextPrevious = cfg.elementAccessNextPrevious;
    if (typeof cfg.selectorRoot != 'undefined') this.selectorRoot = cfg.selectorRoot;
    if (typeof cfg.fadeDuration != 'undefined') this.fadeDuration = cfg.fadeDuration;
    if (typeof cfg.easingFadeOut != 'undefined') this.easingFadeOut = cfg.easingFadeOut;
    if (typeof cfg.easingFadeIn != 'undefined') this.easingFadeIn = cfg.easingFadeIn;
    if (typeof cfg.fadeEvery != 'undefined') this.fadeEvery = cfg.fadeEvery;
    if (typeof cfg.startRandom != 'undefined') this.startRandom = cfg.startRandom;
    if (typeof cfg.autoStart != 'undefined') this.autoStart = cfg.autoStart;

    this._elementAccessLinkEls = [];

    this.fadeElements = $(this.selectorRoot).find(this.selector);

    $(this.selectorRoot).append('<div class="outerComponents"></div>');
    $(this.selectorRoot).children('.outerComponents').append('<div class="components"></div>');
    $(this.selectorRoot).find('.components').append(this.fadeElements);

    if (this.startRandom) {
        this.active = Math.floor(Math.random() * this.fadeElements.length);
        if (this.active >= this.fadeElements.length) {
            this.active = this.fadeElements.length - 1;
        }

        this.next = this.active + 1;
        if (this.next >= this.fadeElements.length) {
            this.next = 0;
        }
    }

    var i = 0;

    this.fadeElements.each($.proxy(function(index, e) {
        var ee = $(e);

        ee.addClass('kwfFadeElement');
        if(this.animationType == 'slide') {
            if (i != this.active) {
                ee.css('display', 'none');
            } else {
                ee.css('display', 'block');
            }
        } else {
            if (i != this.active) {
                ee.css('opacity', 0);
            } else {
                ee.css({
                    opacity: 1,
                    zIndex: 10
                });
            }
            ee.css({
                display: 'block',
                position: 'absolute'
            });
        }
        i += 1;
    }, this));

    // wenn nur ein fade element existiert und im text element kein inhalt ist: ausblenden
    if (this.fadeElements.length == 1 && cfg.textSelector) {
        var dontShowEl = $(this.selectorRoot).find(cfg.textSelector);
        if (dontShowEl && dontShowEl[0]) {
            if (dontShowEl[0].innerHTML.replace(/\s/g, '') == '') {
                $(dontShowEl[0]).css('display', 'none');
            }
        }
    }

    // create the element access link if needed
    if ((this.elementAccessDirect || this.elementAccessPlayPause || this.elementAccessNextPrevious) && i >= 1) {
        this._createElementAccessLinks();
    }
};

Kwf.Fade.Elements.prototype = {

    active: 0,
    next: 1,
    _components: false,
    _firstFaded: false,
    _timeoutId: null,
    _playPause: 'play',
    _playPauseButton: null,
    _template: null,
    _isAnimating: false,

    start: function() {
        this._components = $(this.selectorRoot).find('.components');
        this.calculateMaxHeight();
        if (this.fadeElements.length <= 1) return;
        if (this.autoStart) {
            this._timeoutId = setTimeout($.proxy(this.doFade, this), this._getDeferTime());
        }
    },

    doFade: function(direction) {
        if (this.fadeElements.length <= 1 || this._isAnimating) return;

        this._isAnimating = true;
        var activeEl = $(this.fadeElements[this.active]);
        if (!activeEl.is(':visible')) {
            this._timeoutId = setTimeout($.proxy(this.doFade, this), this._getDeferTime());
            this._isAnimating = false;
            return;
        }
        var nextEl = $(this.fadeElements[this.next]);
        if (activeEl[0] == nextEl[0]) {
            this._isAnimating = false;
            return;
        }

        nextEl.stop(true, false);
        activeEl.stop(true, false);
        if(this.animationType == 'slide') { //TODO implement different animation-types
            // set default direction
            var width = this._components.width();
            var height = this._components.height();
            var left = width;
            var top = height;

            var dir = 'r';
            if (this.direction) { // get direction if set
                dir = this.direction.substring(0,1);
            }
            if (direction) {
                dir = direction.substring(0,1);
            }
            // determine opposite direction depending on given direction
            if (dir == 'r') {
                left *= -1;
            } else if (dir == 'l') {
                width *= -1;
            } else if (dir == 't') {
                height *= -1;
            } else if (dir == 'b') {
                top *= -1;
            }

            if (dir == 'l' || dir == 'r') {
                if (nextEl.position().left != 0) left = nextEl.position().left;
                $(nextEl).show().css({
                    left: left,
                    zIndex: 10
                });
                Kwf.callOnContentReady(nextEl.get(0), {action: 'show'});
                if ($.support.transition || $.support.transform) {
                    this._components.transition({ x: width }, this.fadeDuration * 1000, this.easingFadeIn, $.proxy(function() {
                        this._components.css({ x: 0 });
                        nextEl.css('left', '0px');
                        activeEl.hide().css({
                            left: 0,
                            zIndex: 0
                        });
                        this._isAnimating = false;

                        Kwf.fireComponentEvent('componentSlideOut', Ext.get(activeEl.parent().get(0)), Ext.get(activeEl.get(0)));
                        Kwf.fireComponentEvent('componentSlideIn', Ext.get(nextEl.parent().get(0)), Ext.get(nextEl.get(0)));
                    }, this));
                } else {
                    activeEl.animate({
                        left: '+='+width
                    }, this.fadeDuration * 1000, this.easingFadeIn, function() {
                        $(this).hide().css({
                            left: 0,
                            zIndex: 0
                        });
                        Kwf.fireComponentEvent('componentSlideOut', Ext.get($(this).parent().get(0)), Ext.get(this));
                    });
                    nextEl.animate({
                        left: 0
                    }, this.fadeDuration * 1000, this.easingFadeIn, $.proxy(function() {
                        nextEl.css('left', '0px');
                        this._isAnimating = false;
                        Kwf.fireComponentEvent('componentSlideIn', Ext.get(nextEl.parent().get(0)), Ext.get(nextEl.get(0)));
                    }, this));
                }
            } else if (dir == 't' || dir == 'b') {
                if (nextEl.position().top != 0) top = nextEl.position().top;
                $(nextEl).show().css({
                    top: top,
                    zIndex: 10
                });
                Kwf.callOnContentReady(nextEl.get(0), {action: 'show'});
                if ($.support.transition || $.support.transform) {
                    this._components.transition({ y: height }, this.fadeDuration * 1000, this.easingFadeIn, $.proxy(function() {
                        this._components.css({ y: 0 });
                        nextEl.css('top', '0px');
                        activeEl.hide().css({
                            top: 0,
                            zIndex: 0
                        });
                        this._isAnimating = false;

                        Kwf.fireComponentEvent('componentSlideOut', Ext.get(activeEl.parent().get(0)), Ext.get(activeEl.get(0)));
                        Kwf.fireComponentEvent('componentSlideIn', Ext.get(nextEl.parent().get(0)), Ext.get(nextEl.get(0)));
                    }, this));
                } else {
                    activeEl.animate({
                        top: '+='+height
                    }, this.fadeDuration * 1000, this.easingFadeOut, function() {
                        $(this).hide().css({
                            top: 0,
                            zIndex: 0
                        });
                        Kwf.fireComponentEvent('componentSlideOut', Ext.get($(this).parent().get(0)), Ext.get(this));
                    });
                    nextEl.animate({
                        top: '+='+height
                    }, this.fadeDuration * 1000, this.easingFadeIn, $.proxy(function() {
                        nextEl.css('top', '0px');
                        this._isAnimating = false;
                        Kwf.fireComponentEvent('componentSlideIn', Ext.get(nextEl.parent().get(0)), Ext.get(nextEl.get(0)));
                    }, this));
                }
            }
        } else {
            nextEl.css({
                zIndex: 11,
                opacity: 0
            });
            if ($.support.transition || $.support.transform) {
                activeEl.transition({ opacity: 0 }, this.fadeDuration * 500, this.easingFadeOut, $.proxy(function() {
                    Kwf.fireComponentEvent('componentFadeOut', Ext.get(activeEl.parent().get(0)), Ext.get(activeEl.get(0)));
                }, this));
                nextEl.transition({ opacity: 1 }, this.fadeDuration * 1000, this.easingFadeIn, $.proxy(function() {
                    nextEl.css({zIndex: 10});
                    activeEl.css({zIndex: 0});
                    this._isAnimating = false;
                    Kwf.fireComponentEvent('componentFadeIn', Ext.get(nextEl.parent().get(0)), Ext.get(nextEl.get(0)));
                }, this));
            } else {
                activeEl.fadeTo(this.fadeDuration * 500, 0, this.easingFadeOut, $.proxy(function() {
                    Kwf.fireComponentEvent('componentFadeOut', Ext.get(activeEl.parent().get(0)), Ext.get(activeEl.get(0)));
                }, this));
                nextEl.fadeTo(this.fadeDuration * 1000, 1, this.easingFadeIn, $.proxy(function() {
                    nextEl.css({zIndex: 10});
                    activeEl.css({zIndex: 0});
                    this._isAnimating = false;
                    Kwf.fireComponentEvent('componentFadeIn', Ext.get(nextEl.parent().get(0)), Ext.get(nextEl.get(0)));
                }, this));
            }
        }

        if (this.elementAccessDirect) {
            if ($(this._elementAccessLinkEls[this.active]).hasClass('elementAccessLinkActive')) {
                $(this._elementAccessLinkEls[this.active]).removeClass('elementAccessLinkActive');
            }
            $(this._elementAccessLinkEls[this.next]).addClass('elementAccessLinkActive');
        }

        this.active = this.next;
        this.next += 1;
        if (typeof this.fadeElements[this.next] == 'undefined') {
            this.next = 0;
        }

        this._timeoutId = setTimeout($.proxy(this.doFade, this), this._getDeferTime());
    },

    /**
     * Calculates the max height of the fadeElements and sets this to the _components container
     * is useful for responsive webs
     **/
    calculateMaxHeight: function() {
        var maxHeight = 0;
        this.fadeElements.each($.proxy(function(index, el) {
            if ($(el).height() > maxHeight) {
                maxHeight = $(el).height();
            }
        }, this));
        this._components.css('height', maxHeight);
    },

    pause: function() {
        if (this._timeoutId) window.clearTimeout(this._timeoutId);
        if (this._playPauseButton) {
            this._playPauseButton.removeClass('elementAccessPause');
            this._playPauseButton.addClass('elementAccessPlay');
        }
        this._playPause = 'pause';
    },

    play: function() {
        this.doFade();
        if (this._playPauseButton) {
            this._playPauseButton.removeClass('elementAccessPlay');
            this._playPauseButton.addClass('elementAccessPause');
        }
        this._playPause = 'play';
    },

    _getDeferTime: function() {
        if (!this._firstFaded) {
            this._firstFaded = true;
            return Math.ceil(this.fadeEvery * 1000) - Math.ceil(this.fadeDuration * 1000);
        } else {
            return Math.ceil(this.fadeEvery * 1000);
        }
    },

    _createElementAccessLinks: function() {
        // accessLinks and play / pause button if there are at least 2 images
        if (this.fadeElements.length >= 2) {
            var template = '';
            if (this.elementAccessDirect || this.elementAccessPlayPause) {
                template += '<ul class="elementAccessLinks">';
            }

            if (this.elementAccessDirect) {
                template += '{{#elementAccessLinks}}' +
                    '<li>' +
                        '<a class="elementAccessLink" href="#"></a>' +
                    '</li>' +
                '{{/elementAccessLinks}}';
            }

            if (this.elementAccessPlayPause) {
                template += '<li>' +
                    '<a class="elementAccessPlayPauseButton elementAccessPause" href="#">&nbsp;</a>' +
                '</li>';
            }

            if (this.elementAccessDirect || this.elementAccessPlayPause) {
                template += '</ul>';
            }

            if (this.elementAccessNextPrevious) {
                template += '<a class="elementAccessPrevious" href="#"></a>' +
                '<a class="elementAccessNext" href="#"></a>';
            }

            if (!this._template) {
                this._template = template;
            }

            var data = {
                elementAccessLinks: []
            };

            this.fadeElements.each($.proxy(function(index, e) {
                data['elementAccessLinks'].push({
                    link: index + 1
                });
            }, this));
            var output = Mustache.render(this._template, data);
            $(this.selectorRoot).append(output);

            var elementAccessLinks = $(this.selectorRoot).find('a.elementAccessLink');
            if (elementAccessLinks.length) {
                this._elementAccessLinkEls = elementAccessLinks;
                this._elementAccessLinkEls.each($.proxy(function(index, e) {
                    if (this.active==index) $(e).addClass('elementAccessLinkActive');
                    $(e).click($.proxy(function(ev) {
                        ev.preventDefault();
                        if (this._timeoutId) {
                            window.clearTimeout(this._timeoutId);
                        }
                        this.next = index;
                        this.doFade();
                        if (this.elementAccessPlayPause) this.pause();
                    }, this));
                }, this));
            }

            this._playPauseButton = $(this.selectorRoot).find('a.elementAccessPlayPauseButton');
            if (this._playPauseButton.length) {
                this._playPauseButton.click($.proxy(function(ev) {
                    ev.preventDefault();

                    if (this._playPause == 'play') {
                        this.pause();
                    } else if (this._playPause == 'pause') {
                        this.play();
                    }
                }, this));
            }

            var prevButton = $(this.selectorRoot).find('a.elementAccessPrevious');
            if (prevButton.length) {
                prevButton.click($.proxy(function(ev) {
                    ev.preventDefault();

                    if (this._timeoutId) window.clearTimeout(this._timeoutId);

                    var nextIdx = this.active - 1;
                    if (nextIdx < 0) nextIdx = this.fadeElements.length-1;

                    this.next = nextIdx;
                    this.doFade('right');
                    if (this.elementAccessPlayPause) this.pause();
                }, this));
            }

            var nextButton = $(this.selectorRoot).find('a.elementAccessNext');
            if (nextButton.length) {
                nextButton.click($.proxy(function(ev) {
                    ev.preventDefault();

                    if (this._timeoutId) window.clearTimeout(this._timeoutId);

                    var nextIdx = this.active + 1;
                    if (nextIdx >= this.fadeElements.length) nextIdx = 0;

                    this.next = nextIdx;
                    this.doFade('left');
                    if (this.elementAccessPlayPause) this.pause();
                }, this));
            }
        }
    }
};
