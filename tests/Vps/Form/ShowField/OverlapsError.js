Ext.namespace('Vps.Test');

Vps.Test.OverlapsError = Ext.extend(Ext.Panel, {
    initComponent: function()
    {
        Vps.Debug.displayErrors = true;
        this.buttons = [];
        this.buttons.push(
            new Ext.Button({
            text:'testA',
            handler : function(){
                var win = new Vps.Auto.Form.Window({
                    controllerUrl: '/vps/test/vps_form_show-field_value-overlaps-error-form'
                });
                win.showAdd();
                win.on('addaction', function(form, data) {
                    var fn = function() {
                        win.findField('firstname').setValue('vorname');
                        win.findField('lastname').setValue('nachname');
                    };
                    fn.defer(300);

                });
            },
            scope: this
        }));
        Vps.Test.OverlapsError.superclass.initComponent.call(this);
    }
});


