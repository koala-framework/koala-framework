/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.tree.TreeLoader
 */
Ext.tree.TreeLoader = function(config){
    this.baseParams = {};
    this.requestMethod = "POST";
    Ext.apply(this, config);
    
    this.addEvents({
        "beforeload" : true,
        "load" : true,
        "loadexception" : true
    });
};

Ext.extend(Ext.tree.TreeLoader, Ext.util.Observable, {
    uiProviders : {},
    clearOnLoad : true,
    load : function(node, callback){
        if(this.clearOnLoad){
            while(node.firstChild){
                node.removeChild(node.firstChild);
            }
        }
        if(node.attributes.children){ // preloaded json children
            var cs = node.attributes.children;
            for(var i = 0, len = cs.length; i < len; i++){
                node.appendChild(this.createNode(cs[i]));
            }
            if(typeof callback == "function"){
                callback();
            }
        }else if(this.dataUrl){
            this.requestData(node, callback);
        }
    },
    
    getParams: function(node){
        var buf = [], bp = this.baseParams;
        for(var key in bp){
            if(typeof bp[key] != "function"){
                buf.push(encodeURIComponent(key), "=", encodeURIComponent(bp[key]), "&");
            }
        }
        buf.push("node=", encodeURIComponent(node.id));
        return buf.join("");
    },
    
    requestData : function(node, callback){
        if(this.fireEvent("beforeload", this, node, callback) !== false){
            var params = this.getParams(node);
            var cb = {
                success: this.handleResponse,
                failure: this.handleFailure,
                scope: this,
        		argument: {callback: callback, node: node}
            };
            this.transId = Ext.lib.Ajax.request(this.requestMethod, this.dataUrl, cb, params);
        }else{
            // if the load is cancelled, make sure we notify 
            // the node that we are done
            if(typeof callback == "function"){
                callback();
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

    /**
    * Override this function for custom TreeNode node implementation
    */
    createNode : function(attr){
        if(this.applyLoader !== false){
            attr.loader = this;
        }
        if(typeof attr.uiProvider == 'string'){
           attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
        }
        return(attr.leaf ?
                        new Ext.tree.TreeNode(attr) : 
                        new Ext.tree.AsyncTreeNode(attr));  
    },
    
    processResponse : function(response, node, callback){
        var json = response.responseText;
        try {
            var o = eval("("+json+")");
	        for(var i = 0, len = o.length; i < len; i++){
                var n = this.createNode(o[i]);
                if(n){
                    node.appendChild(n); 
                }
	        }
	        if(typeof callback == "function"){
                callback(this, node);
            }
        }catch(e){
            this.handleFailure(response);
        }
    },
    
    handleResponse : function(response){
        this.transId = false;
        var a = response.argument;
        this.processResponse(response, a.node, a.callback);
        this.fireEvent("load", this, a.node, response);
    },
    
    handleFailure : function(response){
        this.transId = false;
        var a = response.argument;
        this.fireEvent("loadexception", this, a.node, response);
        if(typeof a.callback == "function"){
            a.callback(this, a.node);
        }
    }
});