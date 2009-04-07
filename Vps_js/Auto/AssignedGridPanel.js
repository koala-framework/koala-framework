Vps.Auto.AssignedGridPanel = Ext.extend(Vps.Auto.GridPanel,
{
    initComponent: function() {
        this.actions.textAssign = new Ext.Action({
            text    : trlVps('Assign by text input'),
            icon    : '/assets/silkicons/table_multiple.png',
            cls     : 'x-btn-text-icon',
            handler : this.onTextAssign,
            scope   : this
        });
        Vps.Auto.AssignedGridPanel.superclass.initComponent.call(this);
    },

    onTextAssign : function()
    {
        var params = this.getBaseParams();

        Ext.MessageBox.show({
            title    : trlVps('Assign by text input'),
            msg      : trlVps('Please enter the text you wish to assign.')+'<br />'
                      +trlVps('Seperate items by a new line.'),
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
