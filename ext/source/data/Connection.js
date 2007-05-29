/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.data.Connection
 * The class encapsulates a connection to the page's originating domain, allowing requests to be made
 * either to a configured URL, or to a URL specified at request time.
 * <p>
 * Requests made by this class are asynchronous, and will return immediately, and no data from
 * the server will be available. To process the returned data, us a callback in the request options
 * object.
 * @constructor
 * @param config {Object} a configuration object.
 */
Ext.data.Connection = function(config){
    Ext.apply(this, config);
    this.addEvents({
        "beforerequest" : true,
        "requestcomplete" : true,
        "requestexception" : true
    });
    Ext.data.Connection.superclass.constructor.call(this);
};

Ext.extend(Ext.data.Connection, Ext.util.Observable, {
    /**
     * @cfg url {String} (Optional) The default URL to be used for requests to the server.
     */
    /**
     * @cfg extraParams {Object} (Optional) An object containing properties which are used as
     * extra parameters to each request made by this object.
     */
    /**
     * @cfg method {String} (Optional) The default HTTP method to be used for requests.
     */
    /**
     * @cfg timeout {Number} (Optional) The timeout in milliseconds to be used for requests. Defaults
     * to 30000.
     */
    timeout : 30000,
    
    /**
     * Sends an HTTP request to a remote server.
     * @param {Object} options. An object which may contain the following properties:<ul>
     * <li>url {String} (Optional) The URL to which to send the request. Defaults to configured URL</li>
     * <li>params {Object} (Optional) An object containing properties which are used as extra parameters to the request</li>
     * <li>method {String} (Optional) The HTTP method to use for the request. Defaults to the configured method, or
     * if no method was configured, "GET" if no parameters are being sent, and "POST" if parameters are being sent.</li>
     * <li>callback {Function} (Optional) The function to be called upon receipt of the HTTP response.
     * The callback is passed the following parameters:<ul>
     * <li>options {Object} The parameter to the request call.</li>
     * <li>success {Boolean} True if the request succeeded.</li>
     * <li>resopnse {Object} The XMLHttpRequest object containing the response data.</li>
     * </ul></li>
     * <li>scope {Object} (Optional) The scope in which to execute the callback: The "this" object
     * for the callback function. Defaults to the browser window.</li>
     * </ul>
     */
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

    /**
     * Determine whether this object has a request outstanding.
     * @return {Boolean} True if there is an outstanding request.
     */
    isLoading : function(){
        return this.transId ? true : false;  
    },

    /**
     * Aborts any outstanding request.
     */
    abort : function(){
        if(this.isLoading()){
            Ext.lib.Ajax.abort(this.transId);
        }
    },

    // private
    handleResponse : function(response){
        this.transId = false;
        var options = response.argument.options;
        this.fireEvent("requestcomplete", this, response, options);
        if(typeof options.callback == "function"){
            options.callback.call(options.scope||window, options, true, response);
        }
    },

    // private
    handleFailure : function(response, e){
        this.transId = false;
        var options = response.argument.options;
        this.fireEvent("requestexception", this, response, options, e);
        if(typeof options.callback == "function"){
            options.callback.call(options.scope||window, options, false, response);
        }
    }
});