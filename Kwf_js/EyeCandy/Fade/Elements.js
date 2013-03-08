Kwf.onContentReady(function()
{
    var fadeComponents = Ext.query('div.kwfFadeElements');
    Ext.each(fadeComponents, function(c) {
        var extWrapperEl = Ext.get(c);
        if (extWrapperEl.fadeElementsObject) return; // nur einmal initialisieren

        var fadeClass = Ext.query('.fadeClass', c)[0].value;
        var selector = Ext.query('.fadeSelector', c)[0].value;
        var config = Ext.query('.fadeConfig', c); // optional
        if (config && config[0]) {
            config = Ext.decode(config[0].value);
        } else {
            config = { };
        }
        var textSelector = Ext.query('.textSelector', c); // optional
        if (textSelector && textSelector[0]) {
            config.textSelector = textSelector[0].value;
        }

        config.selector = selector;
        config.selectorRoot = c;

        var cls = Kwf.Fade.Elements;
        if (fadeClass) {
            cls = eval(fadeClass);
            delete fadeClass;
        }

        extWrapperEl.fadeElementsObject = new cls(config);
        if (config.autoStart == undefined || config.autoStart) {
            extWrapperEl.fadeElementsObject.start();
        }
    });
});

Ext.namespace("Kwf.Fade");

Kwf.Fade.Elements = function(cfg) {
    this.selector = cfg.selector;

    this.animationType = 'fade';
    this.elementAccessDirect = false; // optional: displays direct acces links to each image
    this.elementAccessPlayPause = false; // optional: displayes play / pause button
    this.elementAccessLinks = false; // optional, deprecated: displays both of above
    this.elementAccessNextPrevious = false;
    this.selectorRoot = document;
    this.fadeDuration = 1.5;
    this.easingFadeOut = 'easeIn'; //TODO change names to fit every animation
    this.easingFadeIn = 'easeIn';
    this.fadeEvery = 7;
    this.startRandom = true;

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

    this._elementAccessLinkEls = [];

    this.fadeElements = Ext.query(this.selector, this.selectorRoot);

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
    Ext.each(this.fadeElements, function(e) {
        var ee = Ext.get(e);

        ee.addClass('kwfFadeElement');
        if (i != this.active) {
            ee.setStyle('display', 'none');
        } else {
            ee.setStyle('display', 'block');
        }
        i += 1;
    }, this);

    // wenn nur ein fade element existiert und im text element kein inhalt ist: ausblenden
    if (this.fadeElements.length == 1 && cfg.textSelector) {
        var dontShowEl = Ext.query(cfg.textSelector, this.selectorRoot);
        if (dontShowEl && dontShowEl[0]) {
            if (dontShowEl[0].innerHTML.replace(/\s/g, '') == '') {
                Ext.get(dontShowEl[0]).setDisplayed('none');
            }
        }
    }

    // create the element access link if needed
    if ((this.elementAccessDirect || this.elementAccessPlayPause || this.elementAccessNextPrevious) && i >= 1) {
        this._createElementAccessLinks(this.active);
    }
};

Kwf.Fade.Elements.prototype = {

    active: 0,
    next: 1,
    _firstFaded: false,
    _timeoutId: null,
    _playPause: 'play',
    _playPauseButton: null,
    _template: null,

    start: function() {
        if (this.fadeElements.length <= 1) return;
        this._timeoutId = this.doFade.defer(this._getDeferTime(), this);
    },

    doFade: function() {
        if (this.fadeElements.length <= 1) return;

        var activeEl = Ext.get(this.fadeElements[this.active]);
        if (!activeEl.isVisible(true)) {
            this._timeoutId = this.doFade.defer(this._getDeferTime(), this);
            return;
        }
        var nextEl = Ext.get(this.fadeElements[this.next]);
        if(this.animationType == 'slide') { //TODO implement different animation-types
            // order of slideIn and slideOut is important because else there is one
            // pixel margin between the leaving and comming element
            nextEl.slideIn('r', { endOpacity: 1.0, easing: this.easingFadeIn, duration: this.fadeDuration, useDisplay: true,
                callback: function () {
                    Kwf.fireComponentEvent('componentSlideIn', nextEl.parent(), nextEl);
                }
            });
            activeEl.slideOut('l', { endOpacity: .0, easing: this.easingFadeOut, duration: this.fadeDuration, useDisplay: true,
                callback: function() {
                    Kwf.fireComponentEvent('componentSlideOut', activeEl.parent(), activeEl);
                }
            });
        } else {
            activeEl.fadeOut({ endOpacity: .0, easing: this.easingFadeOut, duration: this.fadeDuration, useDisplay: true,
                callback: function() {
                    Kwf.fireComponentEvent('componentFadeOut', activeEl.parent(), activeEl);
                }
            });
            nextEl.fadeIn({ endOpacity: 1.0, easing: this.easingFadeIn, duration: this.fadeDuration, useDisplay: true,
                callback: function () {
                    Kwf.fireComponentEvent('componentFadeIn', nextEl.parent(), nextEl);
                }
            });
        }

        if (this.elementAccessDirect) {
            if (Ext.get(this._elementAccessLinkEls[this.active]).hasClass('elementAccessLinkActive')) {
                Ext.get(this._elementAccessLinkEls[this.active]).removeClass('elementAccessLinkActive');
            }
            Ext.get(this._elementAccessLinkEls[this.next]).addClass('elementAccessLinkActive');
        }

        this.active = this.next;
        this.next += 1;
        if (typeof this.fadeElements[this.next] == 'undefined') {
            this.next = 0;
        }

        this._timeoutId = this.doFade.defer(this._getDeferTime(), this);
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

    _createElementAccessLinks: function(activeLinkIndex) {
        // accessLinks and play / pause button if there are at least 2 images
        if (this.fadeElements.length >= 2) {
            var template = '';
            if (this.elementAccessDirect || this.elementAccessPlayPause) {
                template += '<ul class="elementAccessLinks">\n';
            }

            if (this.elementAccessDirect) {
                Ext.each(this.fadeElements, function(e) {
                    template += '<li>';
                        template += '<a class="elementAccessLink" href="#"></a>';
                    template += '</li>\n';
                }, this);
            }

            if (this.elementAccessPlayPause) {
                template += '<li>';
                    template += '<a class="elementAccessPlayPauseButton elementAccessPause" href="#">&nbsp;</a>';
                template += '</li>\n';
            }

            if (this.elementAccessDirect || this.elementAccessPlayPause) {
                template += '</ul>\n';
            }

            if (this.elementAccessNextPrevious) {
                template += '<a class="elementAccessPrevious" href="#"></a>\n';

                template += '<a class="elementAccessNext" href="#"></a>\n';
            }

            this.template = new Ext.XTemplate(template);
            this.template.append(this.selectorRoot);

            var elementAccessLinks = Ext.get(this.selectorRoot).select('a.elementAccessLink', true);
            if (elementAccessLinks) {
                this._elementAccessLinkEls = Ext.get(this.selectorRoot).query('a.elementAccessLink');
                var j = 0;
                elementAccessLinks.each(function(link) {
                    if (activeLinkIndex==j) link.addClass('elementAccessLinkActive');
                    link.on('click', function(ev, el, opt) {
                        ev.stopEvent();

                        if (this._timeoutId) {
                            window.clearTimeout(this._timeoutId);
                        }
                        this.next = opt.activateIdx;
                        this.doFade();
                        if (this.elementAccessPlayPause) this.pause();

                    }, this, { activateIdx: j });
                    j += 1;
                }, this);
            }

            var playPauseButton = Ext.get(this.selectorRoot).child('a.elementAccessPlayPauseButton');
            if (playPauseButton) {
                playPauseButton.on('click', function(ev, el, opt) {
                    ev.stopEvent();

                    if (this._playPause == 'play') {
                        this.pause();
                    } else if (this._playPause == 'pause') {
                        this.play();
                    }
                }, this);
            }

            var prevButton = Ext.get(this.selectorRoot).child('a.elementAccessPrevious');
            if (prevButton) {
                prevButton.on('click', function(ev, el, opt) {
                    ev.stopEvent();

                    if (this._timeoutId) window.clearTimeout(this._timeoutId);

                    var nextIdx = this.active - 1;
                    if (nextIdx < 0) nextIdx = this.fadeElements.length-1;

                    this.next = nextIdx;
                    this.doFade();
                    if (this.elementAccessPlayPause) this.pause();
                }, this);
            }

            var nextButton = Ext.get(this.selectorRoot).child('a.elementAccessNext');
            if (nextButton) {
                nextButton.on('click', function(ev, el, opt) {
                    ev.stopEvent();

                    if (this._timeoutId) window.clearTimeout(this._timeoutId);

                    var nextIdx = this.active + 1;
                    if (nextIdx >= this.fadeElements.length) nextIdx = 0;

                    this.next = nextIdx;
                    this.doFade();
                    if (this.elementAccessPlayPause) this.pause();
                }, this);
            }
        }
    }
};
