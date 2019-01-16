var $ = require('jquery');
var onReady = require('kwf/commonjs/on-ready');
var oneTransitionEnd = require('kwf/commonjs/element/one-transition-end');
var lightboxHelper = require('kwf/commonjs/lightbox/lightbox-helper');

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
    onShow: function() {
        $('html').addClass('kwfUp-kwfLightboxActive');
    },
    afterShow: function() {},
    onClose: function() {
        $('html').removeClass('kwfUp-kwfLightboxActive');
    },
    afterClose: function() {},
    onContentReady: function() {},
    onResizeWindow: function() {},

    initMask: function() {
        var maskEl = this.lightbox.lightboxEl.find('.kwfUp-kwfLightboxMask');
        maskEl.click(function(ev) {
            if ($(document.body).find('.kwfUp-kwfLightboxMask').is(ev.target)) {
                if (lightboxHelper.currentOpen) {
                    lightboxHelper.currentOpen.style.onMaskClick();
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
        var duration = lightboxMaskEl.css('transitionDuration');
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
