Vps.Form.ComboBox = Ext.extend(Ext.form.ComboBox,
{
    initComponent : function()
    {
        this.addEvents({
            changevalue : true
        });
        if(!this.store) {
            throw "no store set";
        }
        var store;
        if (this.store.data) {
            this.store = Ext.applyIf(this.store, {
                fields: ['id', 'name'],
                id: 'id'
            });
            store = new Ext.data.SimpleStore(this.store);
            this.mode = 'local';
        } else {
            if (this.store.reader) {
                if (this.store.reader.type && Ext.data[this.store.reader.type]) {
                    var readerType = Ext.data[this.store.reader.type];
                    delete this.store.reader.type;
                } else if (this.store.reader.type) {
                    try {
                        var readerType = eval(this.store.reader.type);
                    } catch(e) {
                        throw "invalid readerType: "+this.store.reader.type;
                    }
                    delete this.store.reader.type;
                } else {
                    var readerType = Ext.data.JsonReader;
                }
                if (!this.store.reader.rows) throw "no rows defined, required if reader does not thisure through meta data";
                var rows = this.store.reader.rows;
                delete this.store.reader.rows;
                var reader = new readerType(this.store.reader, rows);
            } else {
                var reader = new Ext.data.JsonReader(); //reader thisuriert sich autom. durch meta-daten
            }
            if (this.store.proxy) {
                if (this.store.proxy.type && Ext.data[this.store.proxy.type]) {
                    var proxyType = Ext.data[this.store.proxy.type];
                    delete this.store.proxy.type;
                } else if (this.store.proxy.type) {
                    try {
                        var proxyType = eval(this.store.proxy.type);
                    } catch(e) {
                        throw "invalid proxyType: "+this.store.proxy.type;
                    }
                    delete this.store.proxy.type;
                } else {
                    var proxyType = Ext.data.HttpProxy;
                }
                var proxy = new proxyType(this.store.proxy);
            } else if (this.store.data) {
                var proxy = new Ext.data.MemoryProxy(this.store.data);
            } else {
                var proxy = new Ext.data.HttpProxy(this.store);
            }
            if (this.store.type && Ext.data[this.store.type]) {
                store = new Ext.data[this.store.type]({
                    proxy: proxy,
                    reader: reader
                });
            } else if (this.store.type) {
                try {
                    var storeType = eval(this.store.type)
                } catch(e) {
                    throw "invalid storeType: "+this.store.type;
                }
                store = new storeType({
                    proxy: proxy,
                    reader: reader
                });
            } else {
                store = new Ext.data.Store({
                    proxy: proxy,
                    reader: reader
                });
            }
        }
        delete this.store;

        Ext.applyIf(this, {
            store: store,
            displayField: 'name',
            valueField: 'id'
        });

        if (this.addDialog) {
            var d = Vps.Auto.Form.Window;
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

        this.store.on('load', function() {
            this.addNoSelection();
        }, this);
        if (this.store.recordType) {
            this.addNoSelection();
        }
        if (this.showNoSelection) {
            this.allowBlank = false;
        }

        Vps.Form.ComboBox.superclass.initComponent.call(this);
    },
    addNoSelection : function() {
        if (this.showNoSelection && this.store.find('id', '') == -1) {
            var data = {};
            data[this.displayField] = '(no selection)';
            data[this.valueField] = null;
            this.store.insert(0, new this.store.recordType(data));
        }
    },
    setValue : function(v)
    {
        if (this.store.proxy && this.valueField) {
            //wenn proxy vorhanden können daten nachgeladen werden
            //also loading anzeigen (siehe setValue)
            this.valueNotFoundText = this.loadingText;
        } else {
            this.valueNotFoundText = '';
        }
        Vps.Form.ComboBox.superclass.setValue.apply(this, arguments);
        if (this.valueField
                && !this.findRecord(this.valueField, v) //record nicht gefunden
                && this.store.proxy) { //proxy vorhanden (dh. daten können nachgeladen werden)
            this.store.baseParams[this.queryParam] = v;
            this.store.load({
                params: this.getParams(v),
                callback: function(r, options, success) {
                    if (success && this.findRecord(this.valueField, this.value)) {
                        this.setValue(this.value);
                    }
                },
                scope: this
            });
        }
        this.fireEvent('changevalue', this.value);
    },
    onRender : function(ct, position)
    {
        Vps.Form.ComboBox.superclass.onRender.call(this, ct, position);
        if (this.addDialog) {
            var c = this.el.up('div.x-form-field-wrap').insertSibling({style: 'float: right'}, 'before');
            var button = new Ext.Button({
                renderTo: c,
                text: this.addDialog.text || 'add new entry',
                handler: function() {
                    this.addDialog.showAdd();
                },
                scope: this
            });
        }
    }
});
Ext.reg('combobox', Vps.Form.ComboBox);
