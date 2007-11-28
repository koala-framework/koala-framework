Vps.Auto.GridPanel = Ext.extend(Vps.Auto.AbstractPane

    controllerUrl: '
    //autoload: tru
    layout: 'fit

    initComponent : function
   
        this.actions = {

        if (!this.gridConfig) this.gridConfig = { plugins: [] 
//         if(this.autoload)
        //todo: wos bosiat bei !autolo

            if (!this.controllerUrl)
                throw 'No controllerUrl specified for AutoGrid.
           
            Ext.Ajax.request
                mask: tru
                url: this.controllerUrl+'/jsonData
                params: Ext.apply({ meta: true }, this.baseParams
                success: function(response, options, r)
                    var result = Ext.decode(response.responseText
                    this.onMetaLoad(result
                
                scope: th
            }
//        

        this.addEvent
            'rendergrid
            'beforerendergrid
            'deletero
        

        Vps.Auto.GridPanel.superclass.initComponent.call(this
    

    onMetaLoad : function(resul
   
        var meta = result.metaDat
        this.metaData = met

        if (!this.store)
            var remoteSort = fals
            if (meta.paging) remoteSort = tru
            var storeConfig =
                proxy: new Ext.data.HttpProxy({ url: this.controllerUrl + '/jsonData' }
                reader: new Ext.data.JsonReader
                    totalProperty: meta.totalPropert
                    root: meta.roo
                    id: meta.i
                    sucessProperty: meta.successPropert
                    fields: meta.fiel
                }
                remoteSort: remoteSor
                sortInfo: meta.sortIn
            
            if (meta.grouping)
                var storeType = Ext.data.GroupingStor
                storeConfig.groupField = meta.grouping.groupFiel
                delete meta.grouping.groupFiel
            } else
                var storeType = Ext.data.Stor
           
            if (this.baseParams) storeConfig.baseParams = this.baseParam
            this.store = new storeType(storeConfig

       

        this.store.newRecords = []; //hier werden neue records gespeichert die nicht dirty si
        this.store.on('update', function(store, record, operation)
            if (operation == Ext.data.Record.EDIT)
                this.getAction('save').enable(
           
        }, this
        this.store.on('add', function(store, records, index)
            this.getAction('save').enable(
        }, this

        this.store.on('loadexception', function(proxy, o, response, e)
            throw e; //re-thr
        }, this

        var gridConfig = Ext.applyIf(this.gridConfig,
            store: this.stor
            selModel: new Ext.grid.RowSelectionModel({singleSelect:true}
            clicksToEdit: 
            border: fals
            loadMask: tru
            plugins: [
            tbar: [
            listeners: { scope: this
        }

        this.relayEvents(this.store, ['load']
        this.relayEvents(gridConfig.selModel, ['selectionchange', 'rowselect', 'beforerowselect']

        gridConfig.selModel.on('rowselect', function(selData, gridRow, currentRow)
            this.getAction('delete').enable(
        }, this

        gridConfig.selModel.on('beforerowselect', function(selModel, rowIndex, keepExisting, record)
            return this.fireEvent('beforeselectionchange', record.id
        }, this

        if (meta.grouping)
            gridConfig.view = new Ext.grid.GroupingView(Ext.applyIf(meta.grouping,
                forceFit: tr
            })

            if (!meta.grouping.noGroupSummary)
                var found = fals
                gridConfig.plugins.each(function(p)
                    if (p instanceof Ext.grid.GroupSummary)
                        found = tru
                        return fals
                   
                }
                if (!found)
                    gridConfig.plugins.push(new Ext.grid.GroupSummary()
               
           
        } else
            gridConfig.view = new Ext.grid.GridView
                forceFit: tr
            }
       

        this.comboBoxes = [

        var config = [
        if (Ext.grid.CheckboxSelectionModel && this.gridConfig.selModel instanceof Ext.grid.CheckboxSelectionModel)
            config.push(this.gridConfig.selModel
       
        for (var i=0; i<meta.columns.length; i++)
            var column = meta.columns[i
            if (!column.header) continu

            if (column.editor && column.editor.xtype == 'checkbox')
                delete column.edito
                if (column.renderer) delete column.rendere
                column = new Ext.grid.CheckColumn(column
                gridConfig.plugins.push(column
            } else if (column.editor)
                Ext.applyIf(column.editor, { msgTarget: 'qtip' }

                column.editor = new Ext.grid.GridEditor(Ext.ComponentMgr.create(column.editor, 'textfield')
                var field = column.editor.fiel
                if(field instanceof Ext.form.ComboBox)
                    this.comboBoxes.push
                        field: fiel
                        column: colu
                    }
               
           

            if (typeof column.renderer == 'function')
                //do nothi
            } else if (Ext.util.Format[column.renderer])
                column.renderer = Ext.util.Format[column.renderer
            } else if (column.renderer)
                try
                    column.renderer = eval(column.renderer
                } catch(e)
                    throw "invalid renderer: "+column.rendere
               
            } else if (column.showDataIndex)
                column.renderer = Ext.util.Format.showField(column.showDataIndex
           

            if (column.defaultValue) delete column.defaultValu
            if (column.dateFormat) delete column.dateForma
            if (typeof column.sortable == 'undefined') column.sortable = meta.sortabl
            config.push(column
       
        gridConfig.colModel = new Ext.grid.ColumnModel(config

        this.gridConfig.listeners.validateedit = function(e)
            this.comboBoxes.each(function(box)
                if(e.field == box.column.dataIndex && box.column.showDataIndex)
                    e.record.data[box.column.showDataIndex] = box.field.getRawValue(
               
            }, this
        


        //editDialog kann entweder von config übergeben werden oder von meta-daten komm
        if (!this.editDialog && meta.editDialog)
            this.editDialog = meta.editDialo
       
        if (this.editDialog && !(this.editDialog instanceof Ext.Window))
            this.editDialog = new Vps.Auto.Form.Window(meta.editDialog
       
        if (this.editDialog)
            this.editDialog.on('datachange', function(r)
                this.reload(
                //r nicht durchschleifen - weil das probleme verursacht we
                //das grid zB an einem Tree gebunden i
                this.fireEvent('datachange'
            }, this

            if (this.editDialog.allowEdit !== false)
                this.on('rowdblclick', function(grid, rowIndex)
                    this.editDialog.showEdit(this.store.getAt(rowIndex).id
                }, this
           
       

        for (var i in this.actions)
            if (i == 'add' && this.editDialog) continue; //add-button anzeigen auch wenn keine permissions da die add-permissions im dialog sein müss
            if (!meta.permissions[i])
                this.getAction(i).hide(
           
       
        /* * Für 
        var ddrow = new Ext.dd.DropTarget(this.grid.container,
            ddGroup : 'GridDD
            copy:fals
            notifyDrop : function(dd, e, data
                var sm=data.grid.getSelectionModel(
                var rows=sm.getSelections(
                ds = data.grid.getDataSource(

                var cindex=dd.getDragData(e).rowInde
                for (i = 0; i < rows.length; i++)
                    rowData=ds.getById(rows[i].id
                    if(!this.copy)
                        ds.remove(ds.getById(rows[i].id)
                        ds.insert(cindex,rowData
                   
                
            
            scope:th
        }
        

        if (meta.paging)
            if (typeof meta.paging == 'object')
                var 
                if (meta.paging.type && Vps.PagingToolbar[meta.paging.type])
                    this.pagingType = meta.paging.typ
                    t = Vps.PagingToolbar[meta.paging.type
                } else if(meta.paging.type)
                    try
                        t = eval(meta.paging.type
                    } catch(e)
                        throw "invalid paging-toolbar: "+meta.paging.typ
                   
                    this.pagingType = meta.paging.typ
                } else
                    this.pagingType = 'Ext.PagingToolbar
                    t = Ext.PagingToolba
               
                delete meta.paging.typ
                var pagingConfig = meta.pagin
                pagingConfig.store = this.stor
                gridConfig.bbar = new t(pagingConfig
            } else
                this.pagingType = 'Ext.PagingToolbar
                gridConfig.bbar = new Ext.PagingToolbar
                        store: this.stor
                        pageSize: meta.pagin
                        displayInfo: tr
                    }
           
        } else
            this.pagingType = fals
       

        if (meta.buttons.reload)
            gridConfig.tbar.add(this.getAction('reload')
            delete meta.buttons.reloa
       
        if (meta.buttons.save)
            gridConfig.tbar.add(this.getAction('save')
            gridConfig.tbar.add('-'
            delete meta.buttons.sav
       
        if (meta.buttons.add)
            gridConfig.tbar.add(this.getAction('add')
            delete meta.buttons.ad
       
        if (meta.buttons['delete'])
            gridConfig.tbar.add(this.getAction('delete')
            delete meta.buttons['delete'
       
        for (var i in meta.buttons)
            gridConfig.tbar.add(this.getAction(i)
       

        var filtersEmpty = tru
        for (var i in meta.filters) filtersEmpty = false; //durch was einfacheres ersetzen 
        if (!filtersEmpty)
            if(gridConfig.tbar.length > 0)
                gridConfig.tbar.add('-'
           
            gridConfig.tbar.add('Filter:'
       
        if (meta.filters.text && typeof(meta.filters.text) != 'object')
            meta.filters.text = { type: 'TextField' 
       
        this.filters = new Ext.util.MixedCollection(
        var first = tru
        for(var filter in meta.filters)
            var f = meta.filters[filte
            if (!Vps.Auto.GridFilter[f.type])
                throw "Unknown filter.type: "+f.typ
           
            var type = Vps.Auto.GridFilter[f.type
            delete f.typ
            f.id = filte
            f = new type(f
            if (!first) gridConfig.tbar.add('  '
            console.log(f.getToolbarItem()
            f.getToolbarItem().each(function(i)
                gridConfig.tbar.add(i
            }
            this.filters.add(f
            f.on('filter', function(f, params)
                this.applyBaseParams(params
                this.load(
            }, this
            first = fals
       

        //wenn toolbar leer und keine tbar über config gesetzt dann nicht erstell
        if (gridConfig.tbar.length == 0 && (!this.initialConfig.gridConfig 
                                            !this.initialConfig.gridConfig.tbar))
            delete gridConfig.tba
       

        this.grid = new Ext.grid.EditorGridPanel(gridConfig

        this.fireEvent('beforerendergrid', this.grid

        this.add(this.grid
        this.doLayout(

        this.fireEvent('rendergrid', this.grid

        this.relayEvents(this.grid, ['rowdblclick']

        if (result.rows)
            this.store.loadData(result
       
    

    getAction : function(typ
   
        if (this.actions[type]) return this.actions[type

        if (type == 'reload')
            this.actions[type] = new Ext.Action
                text    : '
                handler : this.reloa
                icon    : '/assets/silkicons/bullet_star.png
                cls     : 'x-btn-icon
                scope   : th
            }
        } else if (type == 'save')
            this.actions[type] = new Ext.Action
                text    : 'Save
                icon    : '/assets/silkicons/table_save.png
                cls     : 'x-btn-text-icon
                disabled: true, //?? passt de
                handler : this.onSav
                scope   : th
            }
        } else if (type == 'add')
            this.actions[type] = new Ext.Action
                text    : 'Add
                icon    : '/assets/silkicons/table_add.png
                cls     : 'x-btn-text-icon
                handler : this.onAd
                scope: th
            }
        } else if (type == 'delete')
            this.actions[type] = new Ext.Action
                text    : 'Delete
                icon    : '/assets/silkicons/table_delete.png
                cls     : 'x-btn-text-icon
                disabled: tru
                handler : this.onDelet
                scope: th
            }
        } else if (type == 'pdf')
            this.actions[type] = new Ext.Action
                text    : 'Drucken
                icon    : '/assets/silkicons/printer.png
                cls     : 'x-btn-text-icon
                handler : this.onPd
                scope: th
            }
        } else if (type == 'csv')
            this.actions[type] = new Ext.Action
                text    : 'CSV Export
                icon    : '/assets/silkicons/page_code.png
                cls     : 'x-btn-text-icon
                handler : this.onCs
                scope: th
            }
        } else if (type == 'xls')
            this.actions[type] = new Ext.Action
                text    : 'Excel Export
                icon    : '/assets/silkicons/page_excel.png
                cls     : 'x-btn-text-icon
                handler : this.onXl
                scope: th
            }
        } else
            throw 'unknown action-type: ' + typ
       
        return this.actions[type
    

    //protected, zum überschreiben in unterklassen um zusäztliche daten zu speiche
    getSaveParams : function
   
        var data = [
        var modified = this.store.getModifiedRecords(
        if (!modified.length) return {
        //geänderte recor
        modified.each(function(r)
            this.store.newRecords.remove(r); //nur einmal speiche
            data.push(r.data
        }, this

        //neue, ungeänderte recor
        this.store.newRecords.each(function(r)
            data.push(r.data
        }, this

        this.el.mask('Saving...'

        var params = this.getBaseParams() || {
        params.data = Ext.util.JSON.encode(data
        return param
    
    onSave : function
   
        this.submit(
    
    submit : function(option
   
        if (!options) options = {

        if (arguments[1]) options.params = arguments[1]; //backwards compatibili

        this.getAction('save').disable(
        var params = this.getSaveParams(
        if (options.params) Ext.apply(params, options.params

        if (params == {}) retur

        var cb =
            success: options.succes
            failure: options.failulr
            callback: options.callbac
            scope: options.scope || th
        

        Ext.Ajax.request
            url: this.controllerUrl+'/jsonSave
            params: param
            success: function(response, options, r)
                this.reload(
                this.fireEvent('datachange', r
                if (cb.success)
                    cb.success.apply(cb.scope, argument
               
            
            failure: function()
                this.getAction('save').enable(
                if (cb.failure)
                    cb.failure.apply(cb.scope, argument
               
            
            callback: function()
                this.el.unmask(
                if (cb.callback)
                    cb.callback.apply(cb.scope, argument
               
            
            scope  : th
        }
    

    onAdd : function
   
        if (this.editDialog)
            this.editDialog.showAdd(
        } else
            var data = {
            for(var i=0; i<this.store.recordType.prototype.fields.items.length; i++)
                data[this.store.recordType.prototype.fields.items[i].name] = this.store.recordType.prototype.fields.items[i].defaultValu
           
            var record = new this.store.recordType(data

            this.getGrid().stopEditing(
            this.store.insert(0, record
            this.store.newRecords.push(record

            for(var i=0; i<this.getGrid().getColumnModel().getColumnCount(); i++)
                if(!this.getGrid().getColumnModel().isHidden(i) && this.getGrid().getColumnModel().isCellEditable(i, 0))
                    this.getGrid().startEditing(0, i
                    brea
               
           
       
    

    onDelete : function()
        Ext.Msg.show
            title:'Löschen
            msg: 'Do you really wish to remove this entry / these entries?
            buttons: Ext.Msg.YESN
            scope: thi
            fn: function(button)
                if (button == 'yes')
                    var selectedRows = this.getGrid().getSelectionModel().getSelections(
                    if (selectedRows.length == 0) retur

                    var ids = [
                    var params = {
                    selectedRows.each(function(selectedRo
                   
                        if (selectedRow.data.id == 0)
                            this.store.remove(selectedRow
                        } else
                            if (!params[this.store.reader.meta.id])
                                params[this.store.reader.meta.id] = '
                            } else
                                params[this.store.reader.meta.id] += ';
                           
                            params[this.store.reader.meta.id] += selectedRow.i
                       
                    }, this

                    if (params[this.store.reader.meta.id])
                        this.el.mask('Deleting...'
                        Ext.Ajax.request
                            url: this.controllerUrl+'/jsonDelete
                            params: param
                            success: function(response, options, r)
                                this.reload(
                                this.getAction('delete').disable(
                                this.fireEvent('deleterow', this.grid
                                this.fireEvent('datachange', r
                            
                            failure: function()
                                this.getAction('delete').enable(
                            
                            callback: function()
                                this.el.unmask(
                            
                            scope : th
                        }
                   
               
           
        }
    
    onPdf : function
   
        window.open(this.controllerUrl+'/pdf?'+Ext.urlEncode(this.getStore().baseParams)
    
    onCsv : function
   
        window.open(this.controllerUrl+'/csv?'+Ext.urlEncode(this.getStore().baseParams)
    
    onXls : function
   
        window.open(this.controllerUrl+'/xls?'+Ext.urlEncode(this.getStore().baseParams)
    
    getSelected: function()
        return this.getSelectionModel().getSelected(
    

    //für AbstractPan
    getSelectedId: function()
        var s = this.getSelected(
        if (s) return s.i
        return nul
    
    clearSelections: function()
        this.getGrid().getSelectionModel().clearSelections(
    
    selectRow: function(row)
        this.getSelectionModel().selectRow(row
    

    //für AbstractPan
    selectId: function(id)
        if (id)
            var r = this.getStore().getById(id
            if (r)
                this.getSelectionModel().selectRecords([r]
           
        } else
            this.getSelectionModel().clearSelections(
       
    

    //für AbstractPan
    reset: function()
        this.getStore().modified = [
        this.store.newRecords = [
    

    reload: function(options)
        this.store.reload(options
        this.store.commitChanges(
    
    load : function(params)
        if (!params) params = {
        if (this.pagingType && this.pagingType != 'Date' && !params.start)
            params.start = 
       
        this.getStore().load({ params: params }
    

    getGrid : function()
        return this.gri
    
    getSelectionModel : function()
        return this.getGrid().getSelectionModel(
    
    getColumnModel : function()
        return this.getGrid().getColumnModel(
    
    getStore : function()
        return this.stor
    
    getEditDialog : function()
        return this.editDialo
    
    getBaseParams : function()
        return this.getStore().baseParam
    
    setBaseParams : function(baseParams)
        if (this.editDialog)
            this.editDialog.getAutoForm().setBaseParams(baseParams
       
        this.getStore().baseParams = baseParam
    
    applyBaseParams : function(baseParams)
        if (this.editDialog)
            this.editDialog.getAutoForm().applyBaseParams(baseParams
       
        Ext.apply(this.getStore().baseParams, baseParams
    
    resetFilters: function()
        this.filters.each(function(f)
            f.reset(
            this.applyBaseParams(f.getParams()
        }, this
    
    isDirty: function()
        if (this.store.getModifiedRecords().length || this.store.newRecords.legth)
            return tru
        } else
            return fals
       
   
}

Ext.reg('autogrid', Vps.Auto.GridPanel
