/*
 * Ext JS Library 1.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.data.Connection = function(config){
    Ext.apply(this, config);
    this.events = {
        "beforerequest" : true,
        "requestcomplete" : true,
        "requestexception" : true
    };
    Ext.data.Connection.superclass.constructor.call(this);
};

Ext.extend(Ext.data.Connection, Ext.util.Observable, {
    timeout : 30000,
    request : function(options){
        if(this.fireEvent("beforerequest", this, options) !== false){
            var p = options.params;
            if(typeof p == "object"){
                p = Ext.urlEncode(Ext.apply(options.params, this.extraParams));
            }
            var cb = {
                success: this.handleResponse,
                failure: this.handleFailure,
                scope: this,
        		argument: {options: options},
        		timeout : this.timeout
            };
            var method = options.method||this.method||(p ? "POST" : "GET");
            var url = options.url || this.url;
            if(this.autoAbort !== false){
                this.abort();
            }
            if(method == 'GET' && p){
                url += (url.indexOf('?') != -1 ? '&' : '?') + p;
                p = '';
            }
            this.transId = Ext.lib.Ajax.request(method, url, cb, p);
        }else{
            if(typeof options.callback == "function"){
                options.callback.call(options.scope||window, options, null, null);
            }
        }
    },
    
    isLoading : function(){
        return this.transId ? true : false;  
    },
    
    abort : function(){
        if(this.isLoading()){
            Ext.lib.Ajax.abort(this.transId);
        }
    },
    
    handleResponse : function(response){
        this.transId = false;
        var options = response.argument.options;
        this.fireEvent("requestcomplete", this, response, options);
        if(typeof options.callback == "function"){
            options.callback.call(options.scope||window, options, true, response);
        }
    },
    
    handleFailure : function(response, e){
        this.transId = false;
        var options = response.argument.options;
        this.fireEvent("requestexception", this, response, options, e);
        if(typeof options.callback == "function"){
            options.callback.call(options.scope||window, options, false, response);
        }
    }
});