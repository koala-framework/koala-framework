Vps.Component.Overview = Ext.extend(Vps.Auto.GridPanel,
    initComponent : function()
        Vps.Component.Overview.superclass.initComponent.call(this
        this.on('selectionchange', function()
            if (this.getSelected())
                this.getAction('createTpl').enable(
                this.getAction('createCss').enable(
            } else
                this.getAction('createTpl').disable(
                this.getAction('createCss').disable(
           
        }, this
    
    getAction : function(typ
   
        if (this.actions[type]) return this.actions[type

        if (type == 'createTpl')
            this.actions[type] = new Ext.Action
                text    : 'create tpl
                icon    : '/assets/silkicons/page_copy.png
                cls     : 'x-btn-text-icon
                disabled: tru
                handler : this.onCreate.createDelegate(thi
                                [ 'tpl' ]
                scope   : th
            }
        } else if (type == 'createCss')
            this.actions[type] = new Ext.Action
                text    : 'create css
                icon    : '/assets/silkicons/page_copy.png
                cls     : 'x-btn-text-icon
                disabled: tru
                handler : this.onCreate.createDelegate(thi
                                [ 'css' ]
                scope: th
            }
        } else if (type == 'addComponent')
            this.actions[type] = new Ext.Action
                text    : 'add component
                icon    : '/assets/silkicons/brick_add.png
                cls     : 'x-btn-text-icon
                handler : this.onAddComponen
                scope: th
            }
       
        return Vps.Component.Overview.superclass.getAction.call(this, type
    

    onCreate : function(createTyp
   
        if (!this.getSelected()) retur
        Ext.getBody().mask('Copying...'
        Ext.Ajax.request
            url: this.controllerUrl+'/jsonCreate
            params: { type: createType, class: this.getSelected().data.class 
            success: function(a, b, r)
                this.reload(
                Ext.Msg.alert('create '+createTyp
                              "File successfully created: "+r.path
            
            scope: thi
            callback: function()
                Ext.getBody().unmask(
           
        }
    
    onAddComponent : function
   
        var data = [
        this.metaData.components.each(function(c)
            data.push([c, c]
        }, this
        var component = new Vps.Form.ComboBox
            fieldLabel: 'Component
            editable: fals
            triggerAction: 'all
            forceSelection: tru
            allowBlank: fals
            width: 30
            store:
                data: da
           
        }
        var name = new Ext.form.TextField
            width: 30
            fieldLabel: 'Name
            allowBlank: fal
        }
        component.on('select', function(cmb, record, index)
            var r = this.getStore().getAt(0
            if (r)
                var m = r.data.class.match(/^(Vpc_[A-Za-z0-9]+_)/
                name.setValue(record.data.id.replace('Vpc_', m[1])
           
        }, this
        var dlg = new Ext.Window
            title: 'Add Component
            width: 45
            modal: tru
            items: 
                xtype: 'form
                plain: tru
                baseCls: 'x-plain
                bodyStyle: 'padding: 10px
                items: [component, nam
            }
            buttons: 
                text: 'OK
                handler: function()
                    dlg.close(
                    Ext.getBody().mask('Creating...'
                    Ext.Ajax.request
                        url: this.controllerUrl+'/jsonAddComponent
                        params:
                            class: component.getValue(
                            name: name.getValue
                        
                        success: function(a,b,r)
                                Ext.Msg.alert('Add Component
                                        "File successfully created: "+r.path
                        
                        callback: function()
                            Ext.getBody().unmask(
                        
                        scope: th
                    }
                
                scope: th
            }
                text: 'Cancel
                handler: function()
                    dlg.close(
               
            
        }
        dlg.show(
   
}
