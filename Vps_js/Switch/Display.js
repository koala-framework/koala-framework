Vps.onContentReady(function()
{
    var els = Ext.query('div.vpsSwitchDisplay');
    els.forEach(function(el) {
        var switchLink = Ext.get(Ext.query('a.switchLink', el)[0]);
        var switchContent = Ext.get(Ext.query('div.switchContent', el)[0]);
        el = Ext.get(el);

        switchContent.scaleHeight = switchContent.getHeight();
        switchContent.setHeight(0);

        if (switchLink && switchContent) {
            var scopeObj = {
                switchLink    : switchLink,
                switchContent : switchContent,
                wrapperEl     : el
            };

            Ext.EventManager.addListener(switchLink, 'click', function(e) {
                if (this.switchLink.hasClass('switchLinkOpened')) {
                    this.switchContent.scaleHeight = this.switchContent.getHeight();
                    this.switchContent.scale(undefined, 0,
                        { easing: 'easeOut', duration: .5 }
                    );
                    this.switchLink.removeClass('switchLinkOpened');
                } else {
                    this.switchContent.scale(undefined, this.switchContent.scaleHeight,
                        { easing: 'easeOut', duration: .5, afterStyle: "height:auto;" }
                    );
                    this.switchLink.addClass('switchLinkOpened');
                }
            }, scopeObj, { stopEvent: true });
        }
    });

});
