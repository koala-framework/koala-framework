Vps.Auto.AssignedGridPanel = Ext.extend(Vps.Auto.GridPane


    getAction : function(typ
   
        if (this.actions[type]) return this.actions[type

        if (type == 'textAssign')
            this.actions[type] = new Ext.Action
                text    : 'Assign by text input
                icon    : '/assets/silkicons/table_multiple.png
                cls     : 'x-btn-text-icon
                handler : this.onTextAssig
                scope   : th
            }
       

        return Vps.Auto.AssignedGridPanel.superclass.getAction.call(this, type
    

    onTextAssign : function
   
        var params = this.getBaseParams(

        Ext.MessageBox.show
            title    : 'Assign by text input
            msg      : 'Please enter the text you wish to assign.<br /
                      +'Seperate items by a new line.
            width    : 40
            buttons  : Ext.MessageBox.OKCANCE
            multiline: tru
            fn       : function(btn, text)
                if (btn == 'ok')
                    params.assignText = tex
                    Ext.Ajax.request
                        url: this.controllerUrl + '/jsonTextAssign
                        params: param
                        success: function(response, options, r)
                            this.reload(
                            this.fireEvent('datachange', r
                        
                        scope: th
                    }
               
            
            scope: th
        }
   

}