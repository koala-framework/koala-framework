Vps.Auto.AbstractPanel = Ext.extend(Ext.Panel,
{
    checkDirty: false,

    initComponent: function() {
        this.addEvents(
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
                this.bindings.each(function(b) {
                    b.item.enable();
                    var params = {};
                    params[b.queryParam] = id;
                    b.item.applyBaseParams(params);
                    b.item.load();
                }, this);
            } else {
                this.bindings.each(function(b) {
                    b.item.disable();
                }, this);
            }
        }, this);
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
            b.item.on('datachange', function() {
                this.reload();
            }, this, {buffer: 300});
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
    load: function(params) {
    },
    reload: function() {
    },
    getSelectedId: function() {
    },
    selectId: function(id) {
    },
    isDirty: function() {
        return false;
    }
});
