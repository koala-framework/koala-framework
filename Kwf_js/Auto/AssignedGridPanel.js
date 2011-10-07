Kwf.Auto.AssignedGridPanel = Ext.extend(Kwf.Auto.GridPanel,
{
    initComponent: function() {
        this.actions.textAssign = new Ext.Action({
            text    : trlKwf('Assign by text input'),
            icon    : '/assets/silkicons/table_multiple.png',
            cls     : 'x-btn-text-icon',
            handler : this.onTextAssign,
            scope   : this
        });
        Kwf.Auto.AssignedGridPanel.superclass.initComponent.call(this);
    },

    onTextAssign : function()
    {
        var params = this.getBaseParams();

        Ext.MessageBox.show({
            title    : trlKwf('Assign by text input'),
            msg      : trlKwf('Please enter the text you wish to assign.')+'<br />'
                      +trlKwf('Seperate items by a new line.'),
            width    : 400,
            buttons  : Ext.MessageBox.OKCANCEL,
            multiline: true,
            fn       : function(btn, text) {
                if (btn == 'ok') {
                    params.assignText = text;
                    Ext.Ajax.request({
                        url: this.controllerUrl + '/json-text-assign',
                        params: params,
                        success: function(response, options, r) {
                            this.reload();
                            this.fireEvent('datachange', r);
                        },
                        scope: this
                    });
                }
            },
            scope: this
        });
    }
});
