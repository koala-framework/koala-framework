Vps.Form.ComboBox = function(config)
{
    if(!config.store) throw "no store set";
    var store;
    if (config.store.data) {
        config.store = Ext.applyIf(config.store, {
            fields: ['id', 'name'],
            id: 'id'
        });
        store = new Ext.data.SimpleStore(config.store);
        config.mode = 'local';
    } else {
        if (config.store.reader) {
            if (config.store.reader.type && Ext.data[config.store.reader.type]) {
                var readerType = Ext.data[config.store.reader.type];
                delete config.store.reader.type;
            } else if (config.store.reader.type) {
                try {
                    var readerType = eval(config.store.reader.type);
                } catch(e) {
                    throw "invalid readerType: "+config.store.reader.type;
                }
                delete config.store.reader.type;
            } else {
                var readerType = Ext.data.JsonReader;
            }
            if (!config.store.reader.rows) throw "no rows defined, required if reader does not configure through meta data";
            var rows = config.store.reader.rows;
            delete config.store.reader.rows;
            var reader = new readerType(config.store.reader, rows);
        } else {
            var reader = new Ext.data.JsonReader(); //reader configuriert sich autom. durch meta-daten
        }
        if (config.store.proxy) {
            if (config.store.proxy.type && Ext.data[config.store.proxy.type]) {
                var proxyType = Ext.data[config.store.proxy.type];
                delete config.store.proxy.type;
            } else if (config.store.proxy.type) {
                try {
                    var proxyType = eval(config.store.proxy.type);
                } catch(e) {
                    throw "invalid proxyType: "+config.store.proxy.type;
                }
                delete config.store.proxy.type;
            } else {
                var proxyType = Ext.data.HttpProxy;
            }
            var proxy = new proxyType(config.store.proxy);
        } else if (config.store.data) {
            var proxy = new Ext.data.MemoryProxy(config.store.data);
        } else {
            var proxy = new Ext.data.HttpProxy(config.store);
        }
        if (config.store.type && Ext.data[config.store.type]) {
            store = new Ext.data[config.store.type]({
                proxy: proxy,
                reader: reader
            });
        } else if (config.store.type) {
            try {
                var storeType = eval(config.store.type)
            } catch(e) {
                throw "invalid storeType: "+config.store.type;
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
    delete config.store;

    config = Ext.applyIf(config, {
        store: store,
        displayField: 'name',
        valueField: 'id',
        triggerAction: 'all'
    });
    Vps.Form.ComboBox.superclass.constructor.call(this, config);
};
Ext.extend(Vps.Form.ComboBox, Ext.form.ComboBox,
{
});