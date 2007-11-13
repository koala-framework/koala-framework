Vps.Auto.AssignedGridPanel = Ext.extend(Vps.Auto.GridPanel,
{

    textAssignActionUrl: null,

    initComponent: function()
    {
        Vps.Auto.AssignedGridPanel.superclass.initComponent.call(this);

        if (!this.textAssignActionUrl) {
            this.textAssignActionUrl = this.controllerUrl + '/jsonTextAssign';
        }

    },

    onMetaLoad : function(result)
    {
        Vps.Auto.AssignedGridPanel.superclass.onMetaLoad.call(this, result);

        if (this.metaData.buttons.textAssign) {
            this.getGrid().getTopToolbar().unshift(this.getAction('textAssign'), '-');
        }
    },

    getAction : function(type)
    {
        if (this.actions[type]) return this.actions[type];

        if (type == 'textAssign') {
            this.actions[type] = new Ext.Action({
                text    : 'Assign by text input',
                icon    : '/assets/silkicons/table_multiple.png',
                cls     : 'x-btn-text-icon',
                handler : this.onTextAssign,
                scope   : this
            });
        }

        return Vps.Auto.AssignedGridPanel.superclass.getAction.call(this, type);
    },

    onTextAssign : function()
    {
        var params = this.getBaseParams();

        Ext.MessageBox.show({
            title    : 'Assign by text input',
            msg      : 'Please enter the text you wish to assign.<br />'
                      +'Seperate items by a new line.',
            width    : 400,
            buttons  : Ext.MessageBox.OKCANCEL,
            multiline: true,
            fn       : function(btn, text) {
                if (btn == 'ok') {
                    params.assignText = text;
                    Ext.Ajax.request({
                        url: this.textAssignActionUrl,
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