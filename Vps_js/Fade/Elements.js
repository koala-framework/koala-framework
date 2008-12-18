Ext.namespace("Vps.Fade");

Vps.Fade.Elements = function(cfg) {
    this.selector = cfg.selector;
    this.selectorRoot = document;
    this.duration = 2.5;
    this.easingFadeOut = 'easeIn';
    this.easingFadeIn = 'easeIn';
    this.fadeEvery = 6000;

    if (cfg.selectorRoot) this.selectorRoot = cfg.selectorRoot;
    if (cfg.duration) this.duration = cfg.duration;
    if (cfg.easingFadeOut) this.easingFadeOut = cfg.easingFadeOut;
    if (cfg.easingFadeIn) this.easingFadeIn = cfg.easingFadeIn;
    if (cfg.fadeEvery) this.fadeEvery = cfg.fadeEvery;

    this.fadeElements = Ext.query(this.selector, this.selectorRoot);
    var i = 0;
    Ext.each(this.fadeElements, function(e) {
        var ee = Ext.get(e);
        ee.addClass('fadeElement');
        if (i >= 1) {
            ee.setStyle('display', 'none');
        }
        i += 1;
    });
};

Vps.Fade.Elements.prototype = {

    active: 0,

    start: function() {
        if (this.fadeElements.length <= 1) return;
        this.doFade.defer(this.fadeEvery, this);
    },

    doFade: function() {
        if (this.fadeElements.length <= 1) return;

        var activeEl = Ext.get(this.fadeElements[this.active]);
        activeEl.fadeOut({ endOpacity: .0, easing: this.easingFadeOut, duration: this.duration, useDisplay: true });

        if (typeof this.fadeElements[this.active+1] == 'undefined') {
            this.active = 0;
        } else {
            this.active += 1;
        }

        var nextEl = Ext.get(this.fadeElements[this.active]);
        nextEl.fadeIn({ endOpacity: 1.0, easing: this.easingFadeIn, duration: this.duration, useDisplay: true });


        this.doFade.defer(this.fadeEvery, this);
    }
};

Vps.onContentReady(function()
{
    var fadeComponents = Ext.query('div.vpcFadeElements');
    Ext.each(fadeComponents, function(c) {
        var selector = Ext.query('.fadeSelector', c)[0].value;
        var fade = new Vps.Fade.Elements({
            selector: selector,
            selectorRoot: c
        });
        fade.start();
    });
});