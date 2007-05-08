/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.data.HttpProxy
 * An implementation of Ext.data.DataProxy that reads a data object from an Ext.data.Connection object
 * configured to reference a certain URL.
 * <p>
 * <em>Note that this class cannot be used to retrieve data from a domain other than the domain
 * from which the running page was served.
 * <p>
 * For cross-domain access to remote data, use an Ext.data.ScriptTagProxy.
 * </em>
 * <p>
 * Be aware that to enable the browser to parse an XML document, the server <strong>must</strong> set
 * the Content-Type header to "text/xml".
 * @constructor
 * @param {Object} conn An Ext.data.Connection object referencing the URL from which the data object
 * is to be read, or a configuration object for an Ext.data.Connection.
 */
Ext.data.HttpProxy = function(conn){
    Ext.data.HttpProxy.superclass.constructor.call(this);
    // is conn a conn config or a real conn?
    this.conn = conn.events ? conn : new Ext.data.Connection(conn);
};

Ext.extend(Ext.data.HttpProxy, Ext.data.DataProxy, {
    // private
    getConnection : function(){
        return this.conn;
    },

    /**
     * Load data from the configured Ext.data.Connection, read the data object into
     * a block of Ext.data.Records using the passed Ext.data.DataReader implementation, and
     * process that block using the passed callback.
     * @param {Object} params An object containing properties which are to be used as HTTP parameters
     * for the request to the remote server.
     * @param {Ext.data.DataReader) reader The Reader object which converts the data
     * object into a block of Ext.data.Records.
     * @param {Function} callback The function into which to pass the block of Ext.data.Records.
     * The function must be passed <ul>
     * <li>The Record block object</li>
     * <li>The "arg" argument from the load function</li>
     * <li>A boolean success indicator</li>
     * </ul>
     * @param {Object} scope The scope in which to call the callback
     * @param {Object} arg An optional argument which is passed to the callback as its second parameter.
     */
    load : function(params, reader, callback, scope, arg){
        if(this.fireEvent("beforeload", this, params) !== false){
            this.conn.request({
                params : params || {}, 
                request: {
                    callback : callback,
                    scope : scope,
                    arg : arg
                },
                reader: reader,
                callback : this.loadResponse,
                scope: this
            });
        }else{
            callback.call(scope||this, null, arg, false);
        }
    },
    
    // private
    loadResponse : function(o, success, response){
        if(!success){
            this.fireEvent("loadexception", this, o, response);
            o.request.callback.call(o.request.scope, null, o.request.arg, false);
            return;
        }
        var result;
        try {
            result = o.reader.read(response);
        }catch(e){
            this.fireEvent("loadexception", this, o, response, e);
            o.request.callback.call(o.request.scope, null, o.request.arg, false);
            return;
        }
        this.fireEvent("load", this, o, o.request.arg);
        o.request.callback.call(o.request.scope, result, o.request.arg, true);
    },
    
    // private
    update : function(dataSet){
        
    },
    
    // private
    updateResponse : function(dataSet){
        
    }
});