Vps.onContentReady(function()
{
    var els = Ext.query('div.vpsSwitchHoverFade');
    els.forEach(function(el) {
        var switchLink = Ext.get(Ext.query('a.switchLink', el)[0]);
        var switchContent = Ext.get(Ext.query('div.switchContent', el)[0]);
        el = Ext.get(el);

        switchContent.setStyle('display', 'none');

        if (switchLink && switchContent) {
            var scopeObj = {
                switchLink    : switchLink,
                switchContent : switchContent,
                wrapperEl     : el
            };

            Ext.EventManager.addListener(el, 'mouseover', function(e) {
                this.switchContent.fadeIn({ endOpacity: .95, easing: 'easeOut', duration: .5, useDisplay: true });
            }, scopeObj, { stopEvent: true });

            Ext.EventManager.addListener(el, 'mouseout', function(e) {
                this.switchContent.fadeOut({ endOpacity: 0.0, easing: 'easeOut', duration: .5, useDisplay: true });
            }, scopeObj, { stopEvent: true });
        }
    });

});
