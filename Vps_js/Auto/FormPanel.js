Vps.Auto.FormPanel = Ext.extend(Vps.Auto.AbstractPanel,
    autoload: tru
    autoScroll: true, //um scrollbars zu bekomm
    border: fals
    formConfig: {
    maskDisabled: tru

    initComponent: function
   
        this.actions = {

        this.addEvent
            'loadform
            'deleteaction
            'addaction
            'renderfor
        

        Vps.Auto.FormPanel.superclass.initComponent.call(this

        if (!this.formConfig) this.formConfig = {
        Ext.applyIf(this.formConfig,
            baseParams       : {
            trackResetOnLoad : tru
            maskDisabled     : fal
        }

        if (this.autoload)
            this.loadForm(this.controllerUrl
       
    

    loadForm : function(controllerUr
   
        this.controllerUrl = controllerUr
        this.formConfig.url = controllerUrl + '/jsonSave

        Ext.Ajax.request
            mask: tru
            url: this.controllerUrl+'/jsonLoad
            params: Ext.apply({ meta: true }, this.baseParams
            success: function(response, options, r)
                var result = Ext.decode(response.responseText
                this.onMetaChange(result.meta
                if (result.data)
                    Ext.apply(this.getForm().baseParams, this.baseParams
                    this.fireEvent('loadform', this.getForm()
                    this.getForm().clearInvalid(
                    this.getForm().setValues(result.data
               
            
            scope: th
        }
    

    onMetaChange : function(met
   
        Ext.applyIf(meta.form, this.formConfig

        if (this.baseCls) meta.form.baseCls = this.baseCls; //use the sa

        for (var i in this.actions)
            if (!meta.permissions[i])
                this.getAction(i).hide(
           
       

        if (meta.buttons && typeof meta.form.tbar == 'undefined')
            for (var b in meta.buttons)
                if (!meta.form.tbar) meta.form.tbar = [
                meta.form.tbar.push(this.getAction(b)
           
       
        if (this.formPanel != undefined)
            this.remove(this.formPanel, true
       
        this.formPanel = new Ext.FormPanel(meta.form
        this.formPanel.on('render', function()
            this.fireEvent('renderform', this.getForm()
        }, this
        this.add(this.formPanel
        this.doLayout(
        this.getForm().baseParams = {
    

    isDirty : function()
        return this.getForm().isDirty(
    

    getAction : function(typ
   
        if (this.actions[type]) return this.actions[type

        if (type == 'save')
            this.actions[type] = new Ext.Action
                text    : 'Save
                icon    : '/assets/silkicons/table_save.png
                cls     : 'x-btn-text-icon
                handler : function()
                    this.onSave(
                
                scope   : th
            }
        } else if (type == 'delete')
            this.actions[type] = new Ext.Action
                text    : 'Delete
                icon    : '/assets/silkicons/table_delete.png
                cls     : 'x-btn-text-icon
                handler : this.onDelet
                scope   : th
            }
        } else if (type == 'add')
            this.actions[type] = new Ext.Action
                text    : 'New Entry
                icon    : '/assets/silkicons/table_add.png
                cls     : 'x-btn-text-icon
                handler : this.onAd
                scope   : th
            }
        } else
            throw 'unknown action-type: ' + typ
       
        return this.actions[type
    

    load : function(params, options)

        //es kann auch direkt die id übergeben werd
        if (params && typeof params != 'object') params = { id: params 

        Ext.apply(this.getForm().baseParams, params

        if (!options) options = {
        this.getForm().clearValues(
        this.getForm().clearInvalid(
        this.getForm().waitMsgTarget = this.e
        this.enable(
        this.getForm().load(Ext.applyIf(options,
            url: this.controllerUrl+'/jsonLoad
            waitMsg: 'Loading...
            success: function(form, action)
                if (this.actions['delete']) this.actions['delete'].enable(
                this.fireEvent('loadform', this.getForm()
            
            scope: th
        })
    

    //für AbstractPan
    reset : function()
        this.getForm().reset(
    

    //deprecat
    onSubmit : function(options, successCallback)
        if (!options) options = {
        options.success = successCallback.callbac
        this.submit(options, successCallback
    

    //priva
    onSave : function()
        this.submit(
    

    //für AbstractPan
    submit: function(option
   
        this.getAction('save').disable(
        if (!options) options = {

        var cb =
            success: options.succes
            failure: options.failur
            callback: options.callbac
            scope: options.scope || th
        

        this.getForm().waitMsgTarget = this.e
        this.getForm().submit(Ext.apply(options,
            url: this.controllerUrl+'/jsonSave
            waitMsg: 'saving...
            success: function()
                this.onSubmitSuccess.apply(this, arguments
                if (cb.success)
                    cb.success.apply(cb.scope, argument
               
            
            failure: function()
                this.onSubmitFailure.apply(this, arguments
                if (cb.failure)
                    cb.failure.apply(cb.scope, argument
               
            
            callback: function()
                if (cb.callback)
                    cb.callback.apply(cb.scope, argument
               
            
            scope: th
        })
    
    onSubmitFailure: function(form, action)
        if(action.failureType == Ext.form.Action.CLIENT_INVALID)
            Ext.Msg.alert('Speichern', 'Es konnte nicht gespeichert werden, bitte alle Felder korrekt ausfüllen.'
       
        this.getAction('save').enable(
    

    onSubmitSuccess: function(form, action)
        this.getForm().resetDirty(
        this.fireEvent('datachange', action.result

        var reEnableSubmitButton = function()
            this.getAction('save').enable(
        
        reEnableSubmitButton.defer(1000, this

        if(action.result && action.result.data && action.result.data.addedId)
            this.getForm().baseParams.id = action.result.data.addedI
            this.getAction('delete').enable(
            this.getAction('save').enable(
       
        if (this.getForm().loadAfterSave)
            //bei file-upload neu lad
            this.reload(
       
    
    onDelete : function()
        Ext.Msg.show
        title:'löschen?
        msg: 'Möchten Sie diesen Eintrag wirklich löschen?
        buttons: Ext.Msg.YESN
        scope: thi
        fn: function(button)
            if (button == 'yes')

                Ext.Ajax.request
                        url: this.controllerUrl+'/jsonDelete
                        params: {id: this.getForm().baseParams.id
                        success: function(response, options, r)
                            this.fireEvent('datachange', r
                            this.getForm().clearValues(
                            this.getForm().clearInvalid(
                            this.disable(
                            this.fireEvent('deleteaction', this
                        
                        scope: th
                    }
           
       
        }
    
    onAdd : function()
        this.mabySave
            callback: function()
                this.enable(
                if (this.deleteButton) this.deleteButton.disable(
                this.getAction('delete').disable(
                this.applyBaseParams({id: 0}
                this.getForm().setDefaultValues(
                this.getForm().clearInvalid(
                this.fireEvent('addaction', this
            
            scope: th
        }
    
    findField: function(id)
        return this.getForm().findField(id
    
    getForm : function()
        return this.getFormPanel().getForm(
    
    getFormPanel : function()
        return this.formPane
    
    setBaseParams : function(baseParams)
        if (this.getForm())
            this.getForm().baseParams = baseParam
       
    
    applyBaseParams : function(baseParams)
        if (this.getForm())
            Ext.apply(this.getForm().baseParams, baseParams
       
    
    getBaseParams : function()
        return this.getForm().baseParam
   
}

Ext.reg('autoform', Vps.Auto.FormPanel
