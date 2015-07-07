var onReady = require('kwf/on-ready');

(function() {

    Kwf.namespace('Kwf.EyeCandy.Switch.Display');

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
        this.switchLink = this.el.find(this.config.link || '.switchLink');
        this.switchContainer = this.el.find(this.config.container || '.switchContent');
        this.boundEvent = this.config.hover ? 'hover' : 'click';
        this.activeTimeout = null;

        this.doOpen = function() {
            this.switchContainer
                .css('display', 'block')
                .css('height', '');
            onReady.callOnContentReady(this.el, {action: 'show'});
            this.switchContainer
                .css('display', 'none')

            if(this.config.fade) {
                this.switchContainer.fadeIn(this.config);
            } else {
                this.switchContainer.slideDown(this.config);
            }
            this.switchLink.addClass('switchLinkOpened');
        }

        this.doClose = function() {
            this.switchContainer.css('display', 'none');
            onReady.callOnContentReady(this.el, {action: 'hide'});
            this.switchContainer
                .css('display', 'block')

            if(this.config.fade) {
                this.switchContainer.fadeOut(this.config);
            } else {
                this.switchContainer.slideUp(this.config);
            }
            this.switchLink.removeClass('switchLinkOpened');
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
                    if(this.activeTimeout && event.type === 'mouseenter') {
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

        if (this.switchContainer.hasClass('active')) {
            this.doOpen();
        }

    }

    Kwf.EyeCandy.Switch.Display = function(elOrSelector, config) {
        if (typeof elOrSelector == 'string') {
            onReady.onRender(elOrSelector, function(el) {
                Kwf.EyeCandy.Switch.Display(el, config);
            }, { defer: true });
            onReady.onRender(elOrSelector, function(el, config) {
                if (!el.find(config.container || 'div.switchContent').hasClass('active')) {
                    el.find(config.container || 'div.switchContent').hide();
                }
            });
        } else {
            config = config || {};
            el = elOrSelector;
            if (!el.find(config.container || 'div.switchContent').hasClass('active')) {
                el.find(config.container || 'div.switchContent').hide();
            }
            el = elOrSelector.get(0);

            if(!el.switchDisplayObject) {
                el.switchDisplayObject = new switchDisplayCls(el, config);
            }
        };

    };

    Kwf.EyeCandy.Switch.Display('.kwfSwitchDisplay');
    Kwf.EyeCandy.Switch.Display('.kwfSwitchHoverFade', {
        fade: true,
        hover: true,
        duration: 200
    });

})();

