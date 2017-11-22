var onReady = require('kwf/commonjs/on-ready');
var $ = require('jquery');

    var switchDisplayCls = function(el, config) {

        this.config = {
            duration: 500,
            easing: 'swing',
            hover: false,
            fade: false,
            fadeInDelay: 200,
            fadeOutDelay: 200
        };

        $.extend(this.config, config);

        this.el = $(el);
        this.switchLink = this.el.find(this.config.link || '.kwfUp-switchLink:first');
        this.switchContainer = this.el.find(this.config.container || '.kwfUp-switchContent:first');
        this.boundEvent = this.config.hover ? 'hover' : 'click';
        this.activeTimeout = null;

        this.doOpen = function() {
            this.switchContainer
                .css('display', 'block')
                .css('height', '');
            onReady.callOnContentReady(this.el, {action: 'show'});
            this.switchContainer
                .css('display', 'none')

            if (this.config.fade) {
                this.switchContainer.fadeIn(this.config);
            } else {
                this.switchContainer.slideDown(this.config);
            }
            this.switchLink.addClass('kwfUp-switchLinkOpened');
        }

        this.doClose = function() {
            this.switchContainer.css('display', 'none');
            onReady.callOnContentReady(this.el, {action: 'hide'});
            this.switchContainer
                .css('display', 'block')

            if (this.config.fade) {
                this.switchContainer.fadeOut(this.config);
            } else {
                this.switchContainer.slideUp(this.config);
            }
            this.switchLink.removeClass('kwfUp-switchLinkOpened');
        }

        this.switchLink[this.boundEvent]($.proxy(function(){
            var event = arguments[0];
            if (this.switchContainer.is(':hidden')) {
                if (this.config.fade) {
                    if (this.activeTimeout && event.type === 'mouseleave') {
                        clearTimeout(this.activeTimeout);
                        this.activeTimeout = null;
                        return;
                    }
                    this.activeTimeout = setTimeout($.proxy(function() {
                        this.activeTimeout = null;
                        this.doOpen();
                    }, this), this.config.fadeInDelay);
                } else {
                    this.doOpen();
                }
            } else {
                if (this.config.fade) {
                    if (this.activeTimeout && event.type === 'mouseenter') {
                        clearTimeout(this.activeTimeout);
                        this.activeTimeout = null;
                        return;
                    }
                    this.activeTimeout = setTimeout($.proxy(function(){
                        this.activeTimeout = null;
                        this.doClose();
                    }, this), this.config.fadeOutDelay);
                } else {
                    this.doClose();
                }
            }
            return false;
        }, this));

        if (this.switchContainer.hasClass('kwfUp-active')) {
            this.doOpen();
        }

    }

    var SwitchDisplay = function(elOrSelector, config) {
        if (typeof elOrSelector == 'string') {
            onReady.onRender(elOrSelector, function(el) {
                SwitchDisplay(el, config);
            }, {
                defer: true,
                priority: -1 // initialize before tabs
            });
            onReady.onRender(elOrSelector, function(el, config) {
                if (!el.find(config.container || 'div.kwfUp-switchContent:first').hasClass('kwfUp-active')) {
                    el.find(config.container || 'div.kwfUp-switchContent:first').hide();
                }
            });
        } else {
            config = config || {};
            el = elOrSelector;
            if (!el.find(config.container || 'div.kwfUp-switchContent:first').hasClass('kwfUp-active')) {
                el.find(config.container || 'div.kwfUp-switchContent:first').hide();
            }
            el = elOrSelector.get(0);

            if (!el.switchDisplayObject) {
                el.switchDisplayObject = new switchDisplayCls(el, config);
            }
        };

    };

    SwitchDisplay('.kwfUp-kwfSwitchDisplay');
    SwitchDisplay('.kwfUp-kwfSwitchHoverFade', {
        fade: true,
        hover: true,
        duration: 200
    });
