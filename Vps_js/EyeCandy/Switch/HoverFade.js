Vps.onContentReady(function()
{
    var els = Ext.query('div.vpsSwitchHoverFade');
    els.each(function(el) {
        var hoverFade = new Vps.Switch.HoverFade({
            wrapper: el
        });
    });
});


Ext.namespace("Vps.Switch.HoverFade");

Vps.Switch.HoverFade = function(cfg) {
    this.duration = 0.6;
    this.easingFadeOut = 'easeOut';
    this.easingFadeIn = 'easeOut';
    this.fadeOutAfter = 800;
    this.fadeInAfter = 100;
    this.endOpacity = 1.0;

    if (cfg.duration) this.duration = cfg.duration;
    if (cfg.easingFadeOut) this.easingFadeOut = cfg.easingFadeOut;
    if (cfg.easingFadeIn) this.easingFadeIn = cfg.easingFadeIn;
    if (cfg.fadeOutAfter) this.fadeOutAfter = cfg.fadeOutAfter;
    if (cfg.fadeInAfter) this.fadeInAfter = cfg.fadeInAfter;
    if (cfg.endOpacity) this.endOpacity = cfg.endOpacity;

    this.switchLink = Ext.get(Ext.query('.switchLink', cfg.wrapper)[0]);
    this.switchContent = Ext.get(Ext.query('div.switchContent', cfg.wrapper)[0]);
    this.fadeWrapper = Ext.get(cfg.wrapper);

    this.switchContent.setStyle('display', 'none');

    if (this.switchLink && this.switchContent) {
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

Vps.Switch.HoverFade.prototype = {

    contentOver: false,
    linkOver: false,
    isVisible: false,

    fadeIn: function() {
        (function() {
            if (this.linkOver && !this.isVisible && !this.contentOver) {
                this.isVisible = true;
                this.switchContent.fadeIn({
                    endOpacity: this.endOpacity, easing: this.easingFadeIn, duration: this.duration, useDisplay: true
                });
            }
        }).defer(this.fadeInAfter, this);
    },

    fadeOut: function() {
        (function() {
            if (!this.contentOver && !this.linkOver) {
                this.isVisible = false;
                this.switchContent.fadeOut({
                    endOpacity: 0.0, easing: this.easingFadeOut, duration: this.duration, useDisplay: true
                });
            }
        }).defer(this.fadeOutAfter, this);
    }

};
