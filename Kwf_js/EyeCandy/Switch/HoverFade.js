// deprecated, please don't use me
Kwf.onElementReady('.kwfSwitchHoverFade', function(el) {
    var hoverFade = new Kwf.Switch.HoverFade({
        wrapper: el.dom
    });
}, this, {defer: true});

Ext.namespace("Kwf.Switch.HoverFade");

Kwf.Switch.HoverFade = function(cfg) {
    this.duration = 0.2;
    this.easingFadeOut = 'easeOut';
    this.easingFadeIn = 'easeOut';
    this.fadeOutAfter = 200;
    this.fadeInAfter = 100;
    this.endOpacity = 1.0;

    if (cfg.duration) this.duration = cfg.duration;
    if (cfg.easingFadeOut) this.easingFadeOut = cfg.easingFadeOut;
    if (cfg.easingFadeIn) this.easingFadeIn = cfg.easingFadeIn;
    if (cfg.fadeOutAfter) this.fadeOutAfter = cfg.fadeOutAfter;
    if (cfg.fadeInAfter) this.fadeInAfter = cfg.fadeInAfter;
    if (cfg.endOpacity) this.endOpacity = cfg.endOpacity;

    this.switchLink = Ext.get(Ext.query('.switchLink', cfg.wrapper)[0]);
    this.switchContent = Ext.get(Ext.query('.switchContent', cfg.wrapper)[0]);
    this.fadeWrapper = Ext.get(cfg.wrapper);

    if (this.switchLink && this.switchContent) {
        this.switchContent.setStyle('display', 'none');

        this.switchLink.on('mouseover', function(e) {
            this.linkOver = true;
            this.fadeIn();
        }, this);

        this.switchLink.on('mouseout', function(e) {
            this.linkOver = false;
            this.fadeOut();
        }, this);

        this.switchContent.on('mouseover', function(e) {
            this.contentOver = true;
        }, this);

        this.switchContent.on('mouseout', function(e) {
            this.contentOver = false;
            this.fadeOut();
        }, this);
    }
};

Kwf.Switch.HoverFade.prototype = {

    contentOver: false,
    linkOver: false,
    isVisible: false,

    fadeIn: function() {
        (function() {
            if (this.linkOver && !this.isVisible && !this.contentOver) {
                this.isVisible = true;
                this.switchContent.stopFx().fadeIn({
                    endOpacity: this.endOpacity, easing: this.easingFadeIn, duration: this.duration, useDisplay: true
                });
            }
        }).defer(this.fadeInAfter, this);
    },

    fadeOut: function() {
        (function() {
            if (!this.contentOver && !this.linkOver) {
                this.isVisible = false;
                this.switchContent.stopFx().fadeOut({
                    endOpacity: 0.0, easing: this.easingFadeOut, duration: this.duration, useDisplay: true
                });
            }
        }).defer(this.fadeOutAfter, this);
    }

};
