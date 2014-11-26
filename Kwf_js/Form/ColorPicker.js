Kwf.Form.ColorPicker = Ext2.extend(Ext2.form.TriggerField,
{
    triggerClass : 'x2-form-search-trigger',
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
Ext2.reg('colorpickerfield', Kwf.ColorPicker.Window);
