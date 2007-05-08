/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.data.DataProxy
 * This class is an abstract base class for implementations which provide retrieval of
 * unformatted data objects.
 * <p>
 * DataProxy implementations are usually used in conjunction with an implementation of Ext.data.DataReader
 * (of the approriate type which knows how to parse the data object) to provide a block of Records
 * to an Ext.data.Store.
 * <p>
 * Custom implementations must implement the load method as described in
 * {@link Ext.data.HttpProxy#load}.
 */
Ext.data.DataProxy = function(){
    this.addEvents({
        /**
         * @event beforeload
         * Fires before a network request is made to retrieve a data object.
         * @param {Object} params The params parameter to the load function.
         */
        beforeload : true,
        /**
         * @event load
         * Fires before the load method's callback is called.
         * @param {Object} o The data object.
         * @param {Object} arg The callback argument object passed to the load function.
         */
        load : true,
        /**
         * @event loadexception
         * Fires if an Exception occurs during data retrieval.
         * @param {Object} o The data object.
         * @param {Object} arg The callback argument object passed to the load function.
         * @param {Object} e The Exception.
         */
        loadexception : true
    });
    Ext.data.DataProxy.superclass.constructor.call(this);
};

Ext.extend(Ext.data.DataProxy, Ext.util.Observable);