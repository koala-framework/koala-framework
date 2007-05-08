/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.data.SimpleStore
 * @extends Ext.data.Store
 * Smaller help class to make creating Stores from Array data easier.
 * @cfg {Number} id The index in the array that be used for record ids. Leave blank to auto generate ids.
 * @cfg {Array} fields An standard field array consisting of string name or object literal field definitions
 * @cfg {Array} data The multi-dimensional array of data
 * @constructor
 * @param {Object} config
 */
Ext.data.SimpleStore = function(config){
    Ext.data.SimpleStore.superclass.constructor.call(this, {
        reader: new Ext.data.ArrayReader({
                id: config.id
            },
            Ext.data.Record.create(config.fields)
        ),
        proxy : new Ext.data.MemoryProxy(config.data)
    });
    this.load();
};
Ext.extend(Ext.data.SimpleStore, Ext.data.Store);