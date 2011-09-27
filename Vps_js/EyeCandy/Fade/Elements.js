Vps.onContentReady(function()
{
    var fadeComponents = Ext.query('div.vpsFadeElements');
    Ext.each(fadeComponents, function(c) {
        var extWrapperEl = Ext.get(c);
        if (extWrapperEl.fadeElementsObject) return; // nur einmal initialisieren

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

        extWrapperEl.fadeElementsObject = new Vps.Fade.Elements(config);
        extWrapperEl.fadeElementsObject.start();
    });
});

Ext.namespace("Vps.Fade");

Vps.Fade.Elements = function(cfg) {
    this.selector = cfg.selector;

    this.elementAccessDirect = false; // optional: displays direct acces links to each image
    this.elementAccessPlayPause = false; // optional: displayes play / pause button
    this.elementAccessLinks = false; // optional, deprecated: displays both of above
    this.elementAccessNextPrevious = false;
    this.selectorRoot = document;
    this.fadeDuration = 1.5;
    this.easingFadeOut = 'easeIn';
    this.easingFadeIn = 'easeIn';
    this.fadeEvery = 7;
    this.startRandom = true;

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

        ee.addClass('vpsFadeElement');
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

Vps.Fade.Elements.prototype = {

    active: 0,
    next: 1,
    _firstFaded: false,
    _timeoutId: null,
    _elementAccessLinkEls: [ ],
    _playPause: 'play',
    _playPauseButton: null,

    start: function() {
        if (this.fadeElements.length <= 1) return;
        this._timeoutId = this.doFade.defer(this._getDeferTime(), this);
    },

    doFade: function() {
        if (this.fadeElements.length <= 1) return;

        var activeEl = Ext.get(this.fadeElements[this.active]);
        activeEl.fadeOut({ endOpacity: .0, easing: this.easingFadeOut, duration: this.fadeDuration, useDisplay: true });

        var nextEl = Ext.get(this.fadeElements[this.next]);
        nextEl.fadeIn({ endOpacity: 1.0, easing: this.easingFadeIn, duration: this.fadeDuration, useDisplay: true });

        if (this.elementAccessDirect) {
            if (this._elementAccessLinkEls[this.active].hasClass('elementAccessLinkActive')) {
                this._elementAccessLinkEls[this.active].removeClass('elementAccessLinkActive');
            }
            this._elementAccessLinkEls[this.next].addClass('elementAccessLinkActive');
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
            if (this.elementAccessDirect || this.elementAccessPlayPause) {
                var ul = Ext.get(this.selectorRoot).createChild({ tag: 'ul', cls: 'elementAccessLinks' });
            }

            if (this.elementAccessDirect) {
                var j = 0;
                Ext.each(this.fadeElements, function(e) {
                    var a = ul.createChild({ tag: 'li' })
                        .createChild({
                            tag: 'a',
                            cls: 'elementAccessLink'+(activeLinkIndex==j ? ' elementAccessLinkActive' : ''),
                            html: '',
                            href: '#'
                        });
                    a.on('click', function(ev, el, opt) {
                        ev.stopEvent();

                        if (this._timeoutId) {
                            window.clearTimeout(this._timeoutId);
                        }
                        this.next = opt.activateIdx;
                        this.doFade();
                        if (this.elementAccessPlayPause) this.pause();

                    }, this, { activateIdx: j });
                    this._elementAccessLinkEls.push(a);
                    j += 1;
                }, this);
            }

            if (this.elementAccessPlayPause) {
                this._playPauseButton = ul.createChild({ tag: 'li' })
                    .createChild({
                        tag: 'a',
                        cls: 'elementAccessPlayPauseButton elementAccessPause',
                        html: '&nbsp;',
                        href: '#'
                    });
                this._playPauseButton.on('click', function(ev, el, opt) {
                    ev.stopEvent();

                    if (this._playPause == 'play') {
                        this.pause();
                    } else if (this._playPause == 'pause') {
                        this.play();
                    }
                }, this);
            }

            if (this.elementAccessNextPrevious) {
                var prevButton = Ext.get(this.selectorRoot).createChild({
                    tag: 'a', cls: 'elementAccessPrevious', html: '', href: '#'
                });
                prevButton.on('click', function(ev, el, opt) {
                    ev.stopEvent();

                    if (this._timeoutId) window.clearTimeout(this._timeoutId);

                    var nextIdx = this.active - 1;
                    if (nextIdx < 0) nextIdx = this.fadeElements.length-1;

                    this.next = nextIdx;
                    this.doFade();
                    if (this.elementAccessPlayPause) this.pause();
                }, this);

                var nextButton = Ext.get(this.selectorRoot).createChild({
                    tag: 'a', cls: 'elementAccessNext', html: '', href: '#'
                });
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
