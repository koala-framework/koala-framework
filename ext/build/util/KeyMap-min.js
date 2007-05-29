/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.KeyMap=function(el,_2,_3){this.el=Ext.get(el);this.eventName=_3||"keydown";this.bindings=[];if(_2 instanceof Array){for(var i=0,_5=_2.length;i<_5;i++){this.addBinding(_2[i]);}}else{this.addBinding(_2);}this.keyDownDelegate=Ext.EventManager.wrap(this.handleKeyDown,this,true);this.enable();};Ext.KeyMap.prototype={stopEvent:false,addBinding:function(_6){var _7=_6.key,_8=_6.shift,_9=_6.ctrl,_a=_6.alt,fn=_6.fn,_c=_6.scope;if(typeof _7=="string"){var ks=[];var _e=_7.toUpperCase();for(var j=0,len=_e.length;j<len;j++){ks.push(_e.charCodeAt(j));}_7=ks;}var _11=_7 instanceof Array;var _12=function(e){if((!_8||e.shiftKey)&&(!_9||e.ctrlKey)&&(!_a||e.altKey)){var k=e.getKey();if(_11){for(var i=0,len=_7.length;i<len;i++){if(_7[i]==k){if(this.stopEvent){e.stopEvent();}fn.call(_c||window,k,e);return;}}}else{if(k==_7){if(this.stopEvent){e.stopEvent();}fn.call(_c||window,k,e);}}}};this.bindings.push(_12);},handleKeyDown:function(e){if(this.enabled){var b=this.bindings;for(var i=0,len=b.length;i<len;i++){b[i].call(this,e);}}},isEnabled:function(){return this.enabled;},enable:function(){if(!this.enabled){this.el.on(this.eventName,this.keyDownDelegate);this.enabled=true;}},disable:function(){if(this.enabled){this.el.removeListener(this.eventName,this.keyDownDelegate);this.enabled=false;}}};
