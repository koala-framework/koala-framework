/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

Ext.util.Format=function(){var _1=/^\s+|\s+$/g;return {ellipsis:function(_2,_3){if(_2&&_2.length>_3){return _2.substr(0,_3-3)+"...";}return _2;},undef:function(_4){return typeof _4!="undefined"?_4:"";},htmlEncode:function(_5){return !_5?_5:String(_5).replace(/&/g,"&amp;").replace(/>/g,"&gt;").replace(/</g,"&lt;").replace(/"/g,"&quot;");},trim:function(_6){return String(_6).replace(_1,"");},substr:function(_7,_8,_9){return String(_7).substr(_8,_9);},lowercase:function(_a){return String(_a).toLowerCase();},uppercase:function(_b){return String(_b).toUpperCase();},capitalize:function(_c){return !_c?_c:_c.charAt(0).toUpperCase()+_c.substr(1).toLowerCase();},call:function(_d,fn){if(arguments.length>2){var _f=Array.prototype.slice.call(arguments,2);_f.unshift(_d);return eval(fn).apply(window,_f);}else{return eval(fn).call(window,_d);}},usMoney:function(v){v=(Math.round((v-0)*100))/100;v=(v==Math.floor(v))?v+".00":((v*10==Math.floor(v*10))?v+"0":v);return "$"+v;},date:function(v,_12){if(!v){return "";}if(!(v instanceof Date)){v=new Date(Date.parse(v));}return v.dateFormat(_12||"m/d/Y");},dateRenderer:function(_13){return function(v){return Ext.util.Format.date(v,_13);};},stripTagsRE:/<\/?[^>]+>/gi,stripTags:function(v){return !v?v:String(v).replace(this.stripTagsRE,"");}};}();
