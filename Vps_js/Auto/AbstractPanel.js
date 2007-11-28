Vps.Auto.AbstractPanel = Ext.extend(Ext.Pane

    checkDirty: fals

    initComponent: function()
        this.activeId = nul

        this.addEvent
            /
            * @event datachan
            * Daten wurden geändert, zB um grid neu zu lad
            * @param {Object} result from serv
            
            'datachange
            'selectionchange
            'beforeselectionchang
        
        var binds = this.binding
        this.bindings = new Ext.util.MixedCollection(
        if (binds)
            this.addBinding.apply(this, binds
       
        this.on('selectionchange', function()
            var id = this.getSelectedId(
            if (id)
                this.activeId = i
                this.bindings.each(function(b)
                    b.item.enable(
                    if (b.item.getBaseParams()[b.queryParam] != this.activeId)
                        var params = {
                        params[b.queryParam] = this.activeI
                        b.item.applyBaseParams(params
                        b.item.load(
                   
                }, this
           
        }, this, {buffer: 500}
        this.on('beforeselectionchange', function(id)
            var ret = tru
            this.bindings.each(function(b)
                if (!b.item.mabySubmit
                    callback: function()
                        b.item.reset(
                        this.selectId(id
                    
                    scope: th
                }))
                    ret = fals
                    return false; //break ea
               
            }, thi
            return re
        }, this

        Vps.Auto.AbstractPanel.superclass.initComponent.call(this
    
    addBinding: function()
        for(var i = 0; i < arguments.length; i++
            var b = arguments[i
            if (b instanceof Vps.Auto.AbstractPanel)
                b = {item: b
           
            if (!b.queryParam) b.queryParam = 'id
            b.item.disable(
            this.bindings.add(b

            b.item.on('datachange', function(resul
           
                if (result && result.data && result.data.addedId)

                    //nachdem ein neuer eintrag hinzugefügt wurde die anderen reload
                    this.activeId = result.data.addedI

                    //neuladen und wenn geladen den neuen auswähl
                    this.reload
                        callback: function()
                            this.selectId(this.activeId
                        
                        scope: th
                    }

                    //die anderen auch neu lad
                    this.bindings.each(function(i)
                        i.item.enable(
                        if (i.item.getBaseParams()[i.queryParam] != this.activeId)
                            var params = {
                            params[i.queryParam] = this.activeI
                            i.item.applyBaseParams(params
                            i.item.load(
                       
                    }, this
                } else
                    this.reload(
               
            }, this

            if (b.item instanceof Vps.Auto.FormPanel)
                b.item.on('addaction', function(form)
                    this.activeId = 
                    this.selectId(0
                    //bei addaction die anderen disabl
                    this.bindings.each(function(i)
                        if (i.item != form)
                            i.item.disable(
                       
                    }, this
                }, this

                b.item.on('deleteaction', function(form)
                    this.activeId = nul
                    this.selectId(null
                    //wenn gelöscht alle anderen disabl
                    this.bindings.each(function(i)
                        i.item.disable(
                    }, this
                }, this
           
       
    
    removeBinding: function(autoPanel)
        //to
    

    //deprecat
    mabySave : function(callback, callCallbackIfNotDirt
   
        var ret = this.mabySubmit(callback.callback, callback.scope || this
        if(typeof callCallbackIfNotDirty == 'undefined') callCallbackIfNotDirty = tru
        if (ret && callCallbackIfNotDirty)
            callback.callback.call(callback.scope
       
        return re
    
  
    mabySubmit : function(cb, option
   
        if (this.checkDirty && this.isDirty())
            Ext.Msg.show
            title:'Save
            msg: 'Do you want to save the changes?
            buttons: Ext.Msg.YESNOCANCE
            scope: thi
            fn: function(button)
                if (button == 'yes')
                    if (!options) options = {
                    options.success = cb.callbac
                    options.scope = cb.scop
                    this.submit(options
                } else if (button == 'no')
                    cb.callback.call(cb.scope || this
                } else if (button == 'cancel')
                    //nothing to do, action allread cancel
               
            }}
            return fals
       
        return tru
    

    submit: function(options)
    
    reset: function()
    
    load: function(params, options)
    
    reload : function(options)
        this.load(null, options
    
    getSelectedId: function()
    
    selectId: function(id)
    
    isDirty: function()
        return fals
    
    setBaseParams : function(baseParams)
    
    applyBaseParams : function(baseParams)
    
    getBaseParams : function()
   
}
