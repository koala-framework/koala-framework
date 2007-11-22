Vps.Auto.AbstractPanel = Ext.extend(Ext.Panel,
{
    checkDirty: false,

    initComponent: function() {
        this.activeId = null;

        this.addEvents(
            /**
            * @event datachange
            * Daten wurden geändert, zB um grid neu zu laden
            * @param {Object} result from server
            */
            'datachange',
            'selectionchange',
            'beforeselectionchange'
        );
        var binds = this.bindings;
        this.bindings = new Ext.util.MixedCollection();
        if (binds) {
            this.addBinding.apply(this, binds);
        }
        this.on('selectionchange', function() {
            var id = this.getSelectedId();
            if (id) {
                this.activeId = id;
                this.bindings.each(function(b) {
                    b.item.enable();
                    if (b.item.getBaseParams()[b.queryParam] != this.activeId) {
                        var params = {};
                        params[b.queryParam] = this.activeId;
                        b.item.applyBaseParams(params);
                        b.item.load();
                    }
                }, this);
            }
        }, this, {buffer: 500});
        this.on('beforeselectionchange', function(id) {
            var ret = true;
            this.bindings.each(function(b) {
                if (!b.item.mabySubmit({
                    callback: function() {
                        b.item.reset();
                        this.selectId(id);
                    },
                    scope: this
                })) {
                    ret = false;
                    return false; //break each
                }
            }, this)
            return ret;
        }, this);

        Vps.Auto.AbstractPanel.superclass.initComponent.call(this);
    },
    addBinding: function() {
        for(var i = 0; i < arguments.length; i++){
            var b = arguments[i];
            if (b instanceof Vps.Auto.AbstractPanel) {
                b = {item: b};
            }
            if (!b.queryParam) b.queryParam = 'id';
            b.item.disable();
            this.bindings.add(b);

            b.item.on('datachange', function(result)
            {
                if (result && result.data && result.data.addedId) {

                    //nachdem ein neuer eintrag hinzugefügt wurde die anderen reloaden
                    this.activeId = result.data.addedId;

                    //neuladen und wenn geladen den neuen auswählen
                    this.reload({
                        callback: function() {
                            this.selectId(this.activeId);
                        },
                        scope: this
                    });

                    //die anderen auch neu laden
                    this.bindings.each(function(i) {
                        i.item.enable();
                        if (i.item.getBaseParams()[i.queryParam] != this.activeId) {
                            var params = {};
                            params[i.queryParam] = this.activeId;
                            i.item.applyBaseParams(params);
                            i.item.load();
                        }
                    }, this);
                } else {
                    this.reload();
                }
            }, this);

            if (b.item instanceof Vps.Auto.FormPanel) {
                b.item.on('addaction', function(form) {
                    this.activeId = 0;
                    this.selectId(0);
                    //bei addaction die anderen disablen
                    this.bindings.each(function(i) {
                        if (i.item != form) {
                            i.item.disable();
                        }
                    }, this);
                }, this);
            }
        }
    },
    removeBinding: function(autoPanel) {
        //todo
    },

    //deprecated
    mabySave : function(callback, callCallbackIfNotDirty)
    {
        var ret = this.mabySubmit(callback.callback, callback.scope || this);
        if(typeof callCallbackIfNotDirty == 'undefined') callCallbackIfNotDirty = true;
        if (ret && callCallbackIfNotDirty) {
            callback.callback.call(callback.scope);
        }
        return ret;
    },
    
    mabySubmit : function(cb, options)
    {
        if (this.checkDirty && this.isDirty()) {
            Ext.Msg.show({
            title:'Save',
            msg: 'Do you want to save the changes?',
            buttons: Ext.Msg.YESNOCANCEL,
            scope: this,
            fn: function(button) {
                if (button == 'yes') {
                    if (!options) options = {};
                    options.success = cb.callback;
                    options.scope = cb.scope;
                    this.submit(options);
                } else if (button == 'no') {
                    cb.callback.call(cb.scope || this);
                } else if (button == 'cancel') {
                    //nothing to do, action allread canceled
                }
            }});
            return false;
        }
        return true;
    },

    submit: function(options) {
    },
    reset: function() {
    },
    load: function(params, options) {
    },
    reload : function(options) {
        this.load(null, options);
    },
    getSelectedId: function() {
    },
    selectId: function(id) {
    },
    isDirty: function() {
        return false;
    },
    setBaseParams : function(baseParams) {
    },
    applyBaseParams : function(baseParams) {
    },
    getBaseParams : function() {
    }
});
