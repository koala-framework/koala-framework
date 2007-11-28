Vps.Auto.TreePanel = Ext.extend(Vps.Auto.AbstractPanel,

    layout: 'fit

    initComponent : function
   
	    this.addEvent
	        'selectionchange
	        'editaction
            'addaction
	        'loade
	    

	    Ext.Ajax.request
            mask: tru
	        url: this.controllerUrl + '/jsonMeta
	        params: this.baseParam
	        success: this.onMetaChang
	        scope: th
	    }
        if (!this.actions) this.actions = {
        Vps.Auto.TreePanel.superclass.initComponent.call(this
    

    onMetaChange: function(response)
        meta = Ext.decode(response.responseText
        this.icons = meta.icon

        // Toolb
        if (meta.buttons.each == undefined) { // Abfrage nötig, falls keine Buttons geliefe
            var tbar = [
            for (var button in meta.buttons)
                tbar.add(this.getAction(button)
           
       

        // Tr
        baseParams = this.baseParams != undefined ? this.baseParams : {
        if (this.openedId != undefined) { baseParams.openedId = this.openedId;
        this.tree = new Ext.tree.TreePanel
            border      : fals
//            animate     : tru
            loader      : new Ext.tree.TreeLoader
                baseParams  : baseParam
                dataUrl     : this.controllerUrl + '/jsonDat
            }
            enableDD    : meta.enableD
            autoScroll: tru
            rootVisible : meta.rootVisibl
            tbar        : tb
        }

        this.tree.setRootNod
            new Ext.tree.AsyncTreeNode
                text: meta.rootTex
                id: '0
                allowDrag: fal
            
        

        this.tree.getSelectionModel().on('selectionchange', this.onSelectionchange, this
        this.tree.getSelectionModel().on('beforeselect', function(selModel, newNode, oldNode)
            return this.fireEvent('beforeselectionchange', newNode.attributes.id
        }, this
        this.tree.on('beforenodedrop', this.onMove, this
        this.tree.on('collapsenode', this.onCollapseNode, this
        this.tree.on('expandnode', this.onExpandNode, this

        this.tree.on('load', function(node)
            if (this.openedId == node.id)
                node.select(
           
            return tru
        }, this

        this.add(this.tree
        this.doLayout(

        if (meta.rootVisible)
            this.tree.getRootNode().ui.iconNode.style.backgroundImage = 'url(/assets/silkicons/' + meta.icons.root + '.png)
            this.tree.getRootNode().select(
       
        this.tree.getRootNode().expand(

        if (!this.editDialog && meta.editDialog)
            this.editDialog = meta.editDialo
       
        if (this.editDialog && !(this.editDialog instanceof Vps.Auto.Form.Window))
            this.editDialog = new Vps.Auto.Form.Window(meta.editDialog
       
        if (this.editDialog)
            this.editDialog.on('datachange', function(o)
                if (o.data.addedId != undefined)
                    id = o.data.addedI
                } else
                    id = this.tree.getSelectionModel().getSelectedNode().i
               
                this.onSave(id
            }, this
            this.tree.on('dblclick', function(grid, rowIndex)
                this.onEdit(
            }, this
       

        this.fireEvent('loaded', this.tree
    

    onEdit : function (o, e)
        if (this.editDialog != undefined)
            node = this.tree.getSelectionModel().getSelectedNode(
            this.editDialog.showEdit(node.id
        } else
            this.fireEvent('editaction', this.tree.getSelectionModel().getSelectedNode()
       
    

    onAdd: function (o, e)
        if (this.editDialog != undefined)
            this.editDialog.showAdd(
            this.editDialog.getAutoForm().applyBaseParams
                parent_id: this.tree.getSelectionModel().getSelectedNode().
            }
        } else
            this.fireEvent('addaction', this.tree.getSelectionModel().getSelectedNode()
       
    

    onSave : function (i
   
        Ext.Ajax.request
            url: this.controllerUrl + '/jsonNodeData
            params: { node: id 
            success: function(r)
                var response = Ext.decode(r.responseText).dat
                node = this.tree.getNodeById(response.id
                if (node == undefined)
                    if (response.data.parent_id == null) { response.data.parent_id = 0;
                    parentNode = this.tree.getNodeById(response.data.parent_id
                    if (parentNode.isLoaded())
                        response.uiProvider = eval(response.uiProvider
                        node = new Ext.tree.AsyncTreeNode(response
                        if (parentNode.firstChild)
                            parentNode.insertBefore(node, parentNode.firstChild
                        } else
                            parentNode.appendChild(node
                       
                        parentNode.expand(
                        this.tree.getSelectionModel().select(this.tree.getNodeById(response.id)
                    } else
                        parentNode.expand(
                   
                } else
                    node.setText(response.text
                    node.attributes.visible = response.visibl
                    this.setVisible(node
               
            
            scope: th
        
    

    onSelectionchange: function (selModel, node)
        if (node && node.id != 0)
            this.getAction('edit').enable(
            this.getAction('invisible').enable(
            this.getAction('delete').enable(
        } else
            this.getAction('edit').disable(
            this.getAction('invisible').disable(
            this.getAction('delete').disable(
       
        this.fireEvent('selectionchange', node
    

    onDelete: function (o, e)
        Ext.MessageBox.confirm('Löschen', 'Wollen Sie diesen Eintrag wirklich löschen:\n\n"' + this.tree.getSelectionModel().getSelectedNode().text + '"
            function  (button)
                if (button == 'yes')
                    Ext.Ajax.request
                        url: this.controllerUrl + '/jsonDelete
                        params:
                            id: this.tree.getSelectionModel().getSelectedNode().
                        
                        success: function(r)
                            response = Ext.decode(r.responseText
                            node = this.tree.getNodeById(response.id
                            if (node.nextSibling)
                                sibling = node.nextSiblin
                            } else if (node.previousSibling)
                                sibling = node.previousSiblin
                            } else if (node.parentNode)
                                sibling = node.parentNod
                           
                            this.tree.getSelectionModel().select(sibling
                            node.parentNode.removeChild(node
                        
                        scope: th
                    }
               
            
            th
        
    

    onMove : function(e
        Ext.Ajax.request
            url: this.controllerUrl + '/jsonMove
            params:
                source: e.dropNode.i
                target: e.target.i
                point: e.poi
            
            failure: function(r)
                this.tree.getRootNode().reload(
            
            scope: th
        
        return tru
    

    onCollapseNode : function(node)
        Ext.Ajax.request
            url: this.controllerUrl + '/jsonCollapse
            params: {id: node.i
        }
    

    onExpandNode : function(node)
        if (!node.attributes.children)
            Ext.Ajax.request
                url: this.controllerUrl + '/jsonExpand
                params: {id: node.i
            }
       
    

    onVisible : function (o, e)
        Ext.Ajax.request
            url: this.controllerUrl + '/jsonVisible
            params:
                id: this.tree.getSelectionModel().getSelectedNode().
            
            success: function(r)
                response = Ext.decode(r.responseText
                node = this.tree.getNodeById(response.id
                node.attributes.visible = response.visibl
                this.setVisible(node
            
            scope: th
        
    

    setVisible : function (node)
        if (node.attributes.visible)
            node.ui.iconNode.style.backgroundImage = 'url(/assets/silkicons/' + this.icons['default'] + '.png)
        } else
            node.ui.iconNode.style.backgroundImage = 'url(/assets/silkicons/' + this.icons['invisible'] + '.png)
       
    

    getAction : function(typ
   
        if (this.actions[type]) return this.actions[type

        if (type == 'delete')
            this.actions[type] = new Ext.Action
                text    : 'Delete
                handler : this.onDelet
                icon    : '/assets/silkicons/' + this.icons['delete'] + '.png
                cls     : 'x-btn-text-icon
                disabled: tru
                scope   : th
            }
        } else if (type == 'add')
            this.actions[type] = new Ext.Action
                text    : 'Add
                handler : this.onAd
                icon    : '/assets/silkicons/' + this.icons['add'] + '.png
                cls     : 'x-btn-text-icon
                scope   : th
            }
        } else if (type == 'edit')
            this.actions[type] = new Ext.Action
                text    : 'Edit
                handler : this.onEdi
                icon    : '/assets/silkicons/' + this.icons['edit'] + '.png
                cls     : 'x-btn-text-icon
                disabled: tru
                scope   : th
            }
        } else if (type == 'invisible')
            this.actions[type] = new Ext.Action
                text    : 'Toggle Visibility
                handler : this.onVisibl
                icon    : '/assets/silkicons/' + this.icons['invisible'] + '.png
                cls     : 'x-btn-text-icon
                disabled: tru
                scope   : th
            }
        } else if (type == 'reload')
            this.actions[type] = new Ext.Action
                text    : '
                handler : function () { this.tree.getRootNode().reload(); 
                icon    : '/assets/silkicons/bullet_star.png
                cls     : 'x-btn-icon
                scope   : th
            }
        } else if (type == 'expandAll')
            this.actions[type] = new Ext.Action
                text    : '
                handler : function () { this.tree.expandAll(); 
                icon    : '/assets/silkicons/bullet_add.png
                cls     : 'x-btn-icon
                scope   : th
            }
        } else if (type == 'collapseAll')
            this.actions[type] = new Ext.Action
                text    : '
                handler : function () { this.tree.collapseAll(); 
                icon    : '/assets/silkicons/bullet_delete.png
                cls     : 'x-btn-icon
                scope   : th
            }
        } else
            throw 'unknown action-type: ' + typ
       
        return this.actions[type
    
    getTree : function()
        return this.tre
    
    getSelectionModel : function()
        return this.getTree().getSelectionModel(
    
    getSelectedNode : function()
        return this.getSelectionModel().getSelectedNode(
    

    //für AbstractPan
    getSelectedId: function()
        var s = this.getSelectedNode(
        if (s) return s.i
        return nul
    

    //für AbstractPan
    selectId: function(id)
        if (id)
            var n = this.getTree().getNodeById(id
            if (n)
                n.select(
           
        } else
            this.getSelectionModel().clearSelections(
       
   


}

Vps.Auto.TreeNode = Ext.extend(Ext.tree.TreeNodeUI,
    initEvents : function(
        Vps.Auto.TreeNode.superclass.initEvents.call(this
        this.node.ui.iconNode.style.backgroundImage = 'url(/assets/silkicons/' + this.node.attributes.bIcon + '.png)
    
    onDblClick : function(e
        e.preventDefault(
        this.fireEvent("dblclick", this.node, e
   
}

