// @require ModernizrPrefixed

var $ = require('jQuery');
var onReady = require('kwf/on-ready');
var oneTransitionEnd = require('kwf/lightbox/helper/one-transition-end');

var AbstractStyle = function(lightbox) {
    this.lightbox = lightbox;
    this.init();
};
AbstractStyle.masks = 0;
AbstractStyle.prototype = {

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
        //calling mask multiple times is valid, unmask must be called exactly often
        var maskEl = this.lightbox.lightboxEl.find('.kwfUp-kwfLightboxMask');
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

module.exports = AbstractStyle;
