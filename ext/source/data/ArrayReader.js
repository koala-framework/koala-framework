/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.data.ArrayReader
 * @extends Ext.data.DataReader
 * Data reader class to create an Array of Ext.data.Record objects from an Array.
 * Each element of that Array represents a row of data fields. The
 * fields are pulled into a Record object using as a subscript, the <em>mapping</em> property
 * of the field definition if it exists, or the field's ordinal position in the definition.
 * <p>
 * The code below lists all configuration options.
 * <pre><code>
   var RecordDef = Ext.data.Record.create([
       {name: 'name', mapping: 1},
       {name: 'occupation', mapping: 2},
   ]);
   var myReader = new Ext.data.ArrayReader({
       id: 0                     // The subscript within row Array that provides an ID for the Record (optional)
   }, RecordDef);
  </code></pre>
 * <p>
 * This would consume an Array like this:
 * <pre><code>
   [ [1, 'Bill', 'Gardener'], [2, 'Ben', 'Horticulturalist'] ]
  </code></pre>
 * @cfg {String} totalProperty Name of the property from which to retrieve the total number of records
 * in the dataset. This is only needed if the whole dataset is not passed in one go, but is being
 * paged from the remote server.
 * @cfg {String} id (optional) The subscript within row Array that provides an ID for the Record
 * @constructor
 * Create a new JsonReader
 * @param {Object} meta Metadata configuration options.
 * @param {Array/Ext.data.Record constructor} recordType Either an Array of field definition objects,
 * or an {@link Ext.data.Record} object created using {@link Ext.data.Record#create}.
 */
Ext.data.ArrayReader = function(meta, recordType){
    Ext.data.ArrayReader.superclass.constructor.call(this, meta, recordType);
};

Ext.extend(Ext.data.ArrayReader, Ext.data.JsonReader, {
    /**
     * Create a data block containing Ext.data.Records from an XML document.
     * @param {Object} o An object which contains an Array of row objects in the property specified
     * in the config as 'root, and optionally a property, specified in the config as 'totalProperty'
     * which contains the total size of the dataset.
     * @return {Object} data A data block which is used by an Ext.data.Store object as
     * a cache of Ext.data.Records.
     */
    readRecords : function(o){
        var sid = this.meta ? this.meta.id : null;
    	var recordType = this.recordType, fields = recordType.prototype.fields;
    	var records = [];
    	var root = o;
	    for(var i = 0; i < root.length; i++){
		    var n = root[i];
	        var values = {};
	        var id = ((sid || sid === 0) && n[sid] !== undefined && n[sid] !== "" ? n[sid] : null);
	        for(var j = 0, jlen = fields.length; j < jlen; j++){
                var f = fields.items[j];
                var k = f.mapping !== undefined && f.mapping !== null ? f.mapping : j;
                var v = n[k] !== undefined ? n[k] : f.defaultValue;
                v = f.convert(v);
                values[f.name] = v;
            }
	        var record = new recordType(values, id);
	        record.json = n;
	        records[records.length] = record;
	    }
	    return {
	        records : records,
	        totalRecords : records.length
	    };
    }
});