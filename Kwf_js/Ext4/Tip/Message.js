Ext4.define('Kwf.Ext4.Tip.Message', {
    requires: ['Ext.tip.Tip'],
    singleton: true,
    showMessage: function(msg)
    {
        var tip = Ext4.create('Ext.tip.Tip', {
            renderTo: Ext4.getBody(),
            html: msg
        });
        tip.showBy(Ext4.getBody(), 't', [0, 10]);
        (function() {
            tip.el.fadeOut({
                callback: function() {
                    tip.destroy();
                }
            });
        }).defer(2000);
    }
});
