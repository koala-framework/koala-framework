/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.data.Connection=function(_1){Ext.apply(this,_1);this.addEvents({"beforerequest":true,"requestcomplete":true,"requestexception":true});Ext.data.Connection.superclass.constructor.call(this);};Ext.extend(Ext.data.Connection,Ext.util.Observable,{timeout:30000,request:function(_2){if(this.fireEvent("beforerequest",this,_2)!==false){var p=_2.params;if(typeof p=="object"){p=Ext.urlEncode(Ext.apply(_2.params,this.extraParams));}var cb={success:this.handleResponse,failure:this.handleFailure,scope:this,argument:{options:_2},timeout:this.timeout};var _5=_2.method||this.method||(p?"POST":"GET");var _6=_2.url||this.url;if(this.autoAbort!==false){this.abort();}if(_5=="GET"&&p){_6+=(_6.indexOf("?")!=-1?"&":"?")+p;p="";}this.transId=Ext.lib.Ajax.request(_5,_6,cb,p);}else{if(typeof _2.callback=="function"){_2.callback.call(_2.scope||window,_2,null,null);}}},isLoading:function(){return this.transId?true:false;},abort:function(){if(this.isLoading()){Ext.lib.Ajax.abort(this.transId);}},handleResponse:function(_7){this.transId=false;var _8=_7.argument.options;this.fireEvent("requestcomplete",this,_7,_8);if(typeof _8.callback=="function"){_8.callback.call(_8.scope||window,_8,true,_7);}},handleFailure:function(_9,e){this.transId=false;var _b=_9.argument.options;this.fireEvent("requestexception",this,_9,_b,e);if(typeof _b.callback=="function"){_b.callback.call(_b.scope||window,_b,false,_9);}}});
