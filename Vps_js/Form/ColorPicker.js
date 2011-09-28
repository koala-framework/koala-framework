Vps.Form.ColorPicker = Ext.extend(Ext.form.TriggerField,
{
    triggerClass : 'x-form-search-trigger',
    readOnly : true,
    width : 200,
    onTriggerClick : function(){
        win = new Vps.ColorPicker.Window({
                modal: true,
                title: trlVps('Select your Position'),
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
Ext.reg('colorpickerfield', Vps.ColorPicker.Window);
