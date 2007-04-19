/*
 * Ext JS Library 1.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */


//Note: not currently implemented.

Ext.Container = function(config){
    Ext.Container.superclass.constructor.call(this, config);
    this.items = new Ext.util.MixedCollection(false, this.getComponentId);
};

Ext.extend(Ext.Container, Ext.Component, {
    getComponentId : function(comp){
        return comp.id;
    },

    add : Ext.emptyFn,

    remove : Ext.emptyFn,

    insert : Ext.emptyFn
});