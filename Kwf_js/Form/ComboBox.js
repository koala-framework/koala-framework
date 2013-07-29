Kwf.Form.ComboBox = Ext.extend(Ext.form.ComboBox,
{
    displayField: 'name',
    valueField: 'id',

    initComponent : function()
    {
        this.addEvents({
            changevalue : true
        });
        if(!this.store) {
            throw "no store set";
        }
        if (!(this.store instanceof Ext.data.Store)) {
            //store klonen (um nicht this.initalConfig.store zu ändern)
            var store = {};
            for (var i in this.store) {
                store[i] = this.store[i];
            }
            delete this.store;
            if (store.data) {
                store = Ext.applyIf(store, {
                    fields: ['id', 'name'],
                    id: 'id'
                });
                this.store = new Ext.data.SimpleStore(store);
                this.displayField = store.fields[1];
                this.mode = 'local';
            } else {
                if (store.reader) {
                    if (store.reader.type && Ext.data[store.reader.type]) {
                        var readerType = Ext.data[store.reader.type];
                        delete store.reader.type;
                    } else if (store.reader.type) {
                        try {
                            var readerType = eval(store.reader.type);
                        } catch(e) {
                            throw "invalid readerType: "+store.reader.type;
                        }
                        delete store.reader.type;
                    } else {
                        var readerType = Ext.data.JsonReader;
                    }
                    if (!store.reader.rows) throw "no rows defined, required if reader doesn't this through meta data";
                    var rows = store.reader.rows;
                    delete store.reader.rows;
                    var reader = new readerType(store.reader, rows);
                } else {
                    var reader = new Ext.data.JsonReader(); //reader thisuriert sich autom. durch meta-daten
                }
                if (store.proxy) {
                    if (store.proxy.type && Ext.data[store.proxy.type]) {
                        var proxyType = Ext.data[store.proxy.type];
                        delete store.proxy.type;
                    } else if (store.proxy.type) {
                        try {
                            var proxyType = eval(store.proxy.type);
                        } catch(e) {
                            throw "invalid proxyType: "+store.proxy.type;
                        }
                        delete store.proxy.type;
                    } else {
                        var proxyType = Ext.data.HttpProxy;
                    }
                    var proxy = new proxyType(store.proxy);
                } else if (store.data) {
                    var proxy = new Ext.data.MemoryProxy(store.data);
                } else {
                    var proxy = new Ext.data.HttpProxy(store);
                }
                var storeConfig = {
                    proxy: proxy,
                    reader: reader
                };
                Ext.apply(storeConfig, this.storeConfig);
                if (typeof storeConfig.remoteSort == 'undefined') {
                    storeConfig.remoteSort = proxy instanceof Ext.data.HttpProxy;
                }
                if (store.type && Ext.data[store.type]) {
                    this.store = new Ext.data[store.type](storeConfig);
                } else if (store.type) {
                    try {
                        var storeType = eval(store.type);
                    } catch(e) {
                        throw "invalid storeType: "+store.type;
                    }
                    this.store = new storeType(storeConfig);
                } else {
                    this.store = new Ext.data.Store(storeConfig);
                }
            }
        }
        if (this.baseParams) {
            this.store.baseParams = this.baseParams;
        }

        if (this.addDialog) {
            var d = Kwf.Auto.Form.Window;
            if (this.addDialog.type) {
                try {
                    d = eval(this.addDialog.type);
                } catch (e) {
                    throw new Error("Invalid addDialog \'"+this.addDialog.type+"': "+e);
                }
            }
            this.addDialog = new d(this.addDialog);
            this.addDialog.on('datachange', function(result) {
                if (result.data.addedId) {
                    //neuen Eintrag auswählen
                    this.setValue(result.data.addedId);
                }
            }, this);
        }

        if (this.showNoSelection) {
            if (!this.emptyText) {
                this.emptyText = '('+trlKwf('no selection')+')';
            }
        }
        this.store.on('load', function() {
            this.addNoSelection();
        }, this);
        if (this.store.recordType) {
            this.addNoSelection();
        }

        Kwf.Form.ComboBox.superclass.initComponent.call(this);
    },

    initList : function(){
        if (!this.listWidth) {
            //fixt bug wenn combobox in einem tab ist
            //ext verwendet this.wrap.getWidth() was ja eingentlich korrekt ist
            //das funktioniert aber im FF da nicht
            this.listWidth = this.el.getWidth()+this.trigger.getWidth();
        }
        Kwf.Form.ComboBox.superclass.initList.call(this);
    },

    addNoSelection : function() {
        if (this.showNoSelection && this.store.find('id', '') == -1) {
            var data = {};
            data[this.displayField] = this.emptyText;
            data[this.valueField] = null;
            for (var i = 0; i < this.store.fields.keys.length; i++) {
                if (this.store.fields.keys[i] != this.displayField
                    && this.store.fields.keys[i] != this.valueField
                ) {
                    data[this.store.fields.keys[i]] = null;
                }
            }
            this.store.insert(0, new this.store.recordType(data));
        }
    },
    onLoad : function(store, records, options) {
        if (!options || !options.blockOnLoad) { //don't call onLoad when loading text to display for setValue because this would expand() if the field has focus
            Kwf.Form.ComboBox.superclass.onLoad.apply(this, arguments);
        }
    },
    setValue : function(v)
    {
        if (v === '') v = null;
        if (v == this.emptyText) v = null;
        if (v && this.store.proxy && this.valueField && this.mode == 'remote') {
            //wenn proxy vorhanden können daten nachgeladen werden
            //also loading anzeigen (siehe setValue)
            this.valueNotFoundText = this.loadingText;
        } else {
            this.valueNotFoundText = '';
        }
        Kwf.Form.ComboBox.superclass.setValue.apply(this, arguments);
        if (v && this.valueField
                && !this.findRecord(this.valueField, v) //record nicht gefunden
                && this.mode == 'remote'
                && this.store.proxy //proxy vorhanden (dh. daten können nachgeladen werden)
                ) {
            delete this.lastQuery;
            this.store.baseParams[this.queryParam] = this.valueField+':'+v;
            this.store.load({
                blockOnLoad: true,
                params: this.getParams(v),
                callback: function(r, options, success) {
                    if (success) {
                        if (this.findRecord(this.valueField, this.value)) {
                            this.setValue(this.value);
                        } else {
                            this.setValue(null);
                        }
                    }
                },
                scope: this
            });
        }
        this.fireEvent('changevalue', this.value, this);
    },

    getParams : function(q){
        var ret = Kwf.Form.ComboBox.superclass.getParams.call(this, q);
        ret.current_value = this.getValue();
        return ret;
    },

    onRender : function(ct, position)
    {
        Kwf.Form.ComboBox.superclass.onRender.call(this, ct, position);
        if (this.addDialog) {
            var c = this.el.up('div.x-form-field-wrap').insertSibling({style: 'float: right'}, 'before');
            var button = new Ext.Button({
                renderTo: c,
                text: this.addDialog.text || trlKwf('add new entry'),
                handler: function() {
                    this.addDialog.showAdd();
                },
                scope: this
            });
        }
    },
    setFormBaseParams: function(params) {
        Ext.apply(this.store.baseParams, params);
    },


    /*
    Workaround für folgendes Problem:
    - ComboBox onBeforeLoad ersetzt den DataView html code durch einen eigenen Loading
    - this.view.all (da werden die gerenderten dom knoten gemerkt) wird aber nicht geleert
    - dadurch sind da elemente drinnen die in der luft hängen
    - wenn nun im onLoad this.select() aufgerufen wird, wird ein scrollChildIntoView für das Element
      das in der luft hängt aufgerufen
    - und das gibt einen JS-Error im IE
     */
    onBeforeLoad: function()
    {
        Kwf.Form.ComboBox.superclass.onBeforeLoad.call(this);
        if(!this.hasFocus){
            return;
        }
        this.view.all.clear();
    }


});
Ext.reg('combobox', Kwf.Form.ComboBox);
