/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.data.HttpProxy=function(_1){Ext.data.HttpProxy.superclass.constructor.call(this);this.conn=_1.events?_1:new Ext.data.Connection(_1);};Ext.extend(Ext.data.HttpProxy,Ext.data.DataProxy,{getConnection:function(){return this.conn;},load:function(_2,_3,_4,_5,_6){if(this.fireEvent("beforeload",this,_2)!==false){this.conn.request({params:_2||{},request:{callback:_4,scope:_5,arg:_6},reader:_3,callback:this.loadResponse,scope:this});}else{_4.call(_5||this,null,_6,false);}},loadResponse:function(o,_8,_9){if(!_8){this.fireEvent("loadexception",this,o,_9);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}var _a;try{_a=o.reader.read(_9);}catch(e){this.fireEvent("loadexception",this,o,_9,e);o.request.callback.call(o.request.scope,null,o.request.arg,false);return;}this.fireEvent("load",this,o,o.request.arg);o.request.callback.call(o.request.scope,_a,o.request.arg,true);},update:function(_b){},updateResponse:function(_c){}});
