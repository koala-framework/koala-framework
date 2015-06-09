Kwf.Auto.AssignedGridPanel = Ext2.extend(Kwf.Auto.GridPanel,
{
    initComponent: function() {
        this.actions.textAssign = new Ext2.Action({
            text    : trlKwf('Assign by text input'),
            icon    : '/assets/silkicons/table_multiple.png',
            cls     : 'x2-btn-text-icon',
            handler : this.onTextAssign,
            scope   : this
        });
        Kwf.Auto.AssignedGridPanel.superclass.initComponent.call(this);
    },

    onTextAssign : function()
    {
        var params = this.getBaseParams();

        Ext2.MessageBox.show({
            title    : trlKwf('Assign by text input'),
            msg      : trlKwf('Please enter the text you wish to assign.')+'<br />'
                      +trlKwf('Seperate items by a new line.'),
            width    : 400,
            buttons  : Ext2.MessageBox.OKCANCEL,
            multiline: true,
            fn       : function(btn, text) {
                if (btn == 'ok') {
                    params.assignText = text;
                    Ext2.Ajax.request({
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
