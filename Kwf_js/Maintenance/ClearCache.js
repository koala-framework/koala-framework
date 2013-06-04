Ext.ns('Kwf.Maintenance');
Kwf.Maintenance.ClearCache = Ext.extend(Ext.Panel, {
    border: false,
    initComponent: function() {
        this.layout = 'border';
        
        var typeCheckboxes = [];
        this.typeNames.each(function(t) {
            var b = new Kwf.Form.Checkbox({
                name: t,
                boxLabel: t
            });
            b.setValue(true);
            typeCheckboxes.push(b);
        }, this);
        this.items = [
            {
                items: typeCheckboxes,
                layout: 'form',
                region: 'center',
                buttons: [{
                    text: trlKwf('Clear Cache'),
                    handler: function() {
                        var types = [];
                        typeCheckboxes.each(function(t) {
                            if (t.getValue()) {
                                types.push(t.name);
                            }
                        }, this);
                        Kwf.Utils.BackgroundProcess.request({
                            url: '/kwf/maintenance/clear-cache/json-clear-cache',
                            progress: true,
                            params: {
                                type: types.join(',')
                            },
                            success: function(response, options, r) {
                                if (r.message) {
                                    var msg = r.message.replace("\n", "<br />");
                                    Ext.Msg.alert(trlKwf('Clear Cache'), msg);
                                }
                            },
                            scope: this
                        });
                    },
                    scope: this
                }]
            }
        ];

        Kwf.Maintenance.ClearCache.superclass.initComponent.call(this);
    }
});
Ext.reg('kwf.maintenance.clearCache', Kwf.Maintenance.ClearCache);
