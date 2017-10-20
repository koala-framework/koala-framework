Ext2.namespace('Kwf.Test');

Kwf.Test.OverlapsError = Ext2.extend(Ext2.Panel, {
    initComponent: function()
    {
        Kwf.Debug.displayErrors = true;
        this.buttons = [];
        this.buttons.push(
            new Ext2.Button({
            text:'testA',
            handler : function(){
                var win = new Kwf.Auto.Form.Window({
                    controllerUrl: KWF_BASE_URL+'/kwf/test/kwf_form_show-field_value-overlaps-error-form'
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
        Kwf.Test.OverlapsError.superclass.initComponent.call(this);
    }
});


