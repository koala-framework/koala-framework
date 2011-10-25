Kwf.Form.ColorPicker = Ext.extend(Ext.form.TriggerField,
{
    triggerClass : 'x-form-search-trigger',
    readOnly : true,
    width : 200,
    onTriggerClick : function(){
        win = new Kwf.ColorPicker.Window({
                modal: true,
                title: trlKwf('Select your Position'),
                width:535,
                height:500,
                shadow:true,
                closeAction: 'hide'
            });
//             debugger;
        win.show();
        win.focus();
    }
});
Ext.reg('colorpickerfield', Kwf.ColorPicker.Window);
