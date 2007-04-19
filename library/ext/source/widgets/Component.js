/*
 * Ext JS Library 1.0
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.ComponentMgr
 * Provides a common registry of all components on a page so that they can be easily accessed by component id.
 * @singleton
 */
Ext.ComponentMgr = function(){
    var all = new Ext.util.MixedCollection();

    return {
        // private
        register : function(c){
            all.add(c);
        },

        // private
        unregister : function(c){
            all.remove(c);
        },

        /**
         * Returns a component by id
         * @param {String} id The component id
         */
        get : function(id){
            return all.get(id);
        },

        /**
         * Registers a function that will be called when a specified component is added to ComponentMgr
         * @param {String} id The component id
         * @param {Funtction} fn The callback function
         * @param {Object} scope The scope of the callback
         */
        onAvailable : function(id, fn, scope){
            all.on("add", function(index, o){
                if(o.id == id){
                    fn.call(scope || o, o);
                    all.un("add", fn, scope);
                }
            });
        }
    };
}();

/**
 * @class Ext.Component
 * @extends Ext.util.Observable
 * Base class for all Ext form controls that provides a common set of events and functionality shared by all components.
 * @constructor
 * @param {Ext.Element/String/Object} config The configuration options.  If an element is passed, it is set as the internal
 * element and its id used as the component id.  If a string is passed, it is assumed to be the id of an existing element
 * and is used as the component id.  Otherwise, it is assumed to be a standard config object and is applied to the component.
 */
Ext.Component = function(config){
    config = config || {};
    if(config.tagName || config.dom || typeof config == "string"){ // element object
        config = {el: config, id: config.id || config};
    }
    Ext.apply(this, config);
    this.addEvents({
        /**
         * @event disable
         * Fires after the component is disabled
	     * @param {Ext.Component} this
	     */
        disable : true,
        /**
         * @event enable
         * Fires after the component is enabled
	     * @param {Ext.Component} this
	     */
        enable : true,
        /**
         * @event beforeshow
         * Fires before the component is shown
	     * @param {Ext.Component} this
	     */
        beforeshow : true,
        /**
         * @event show
         * Fires after the component is shown
	     * @param {Ext.Component} this
	     */
        show : true,
        /**
         * @event beforehide
         * Fires before the component is hidden
	     * @param {Ext.Component} this
	     */
        beforehide : true,
        /**
         * @event hide
         * Fires after the component is hidden
	     * @param {Ext.Component} this
	     */
        hide : true,
        /**
         * @event beforerender
         * Fires before the component is rendered
	     * @param {Ext.Component} this
	     */
        beforerender : true,
        /**
         * @event render
         * Fires after the component is rendered
	     * @param {Ext.Component} this
	     */
        render : true,
        /**
         * @event beforedestroy
         * Fires before the component is destroyed
	     * @param {Ext.Component} this
	     */
        beforedestroy : true,
        /**
         * @event destroy
         * Fires after the component is destroyed
	     * @param {Ext.Component} this
	     */
        destroy : true
    });
    if(!this.id){
        this.id = "ext-comp-" + (++Ext.Component.AUTO_ID);
    }
    Ext.ComponentMgr.register(this);
    Ext.Component.superclass.constructor.call(this);
};

// private
Ext.Component.AUTO_ID = 1000;

Ext.extend(Ext.Component, Ext.util.Observable, {
    /**
     * true if this component is hidden. Read-only.
     */
    hidden : false,
    /**
     * true if this component is disabled. Read-only.
     */
    disabled : false,
    /**
     * CSS class added to the component when it is disabled.
     */
    disabledClass : "x-item-disabled",
    /**
     * true if this component has been rendered. Read-only.
     */
    rendered : false,

    // private
    ctype : "Ext.Component",

    // private
    actionMode : "el",

    // private
    getActionEl : function(){
        return this[this.actionMode];
    },

    /**
     * If this is a lazy rendering component, render it to it's container element
     * @param {String/HTMLElement/Element} container The element this component should be rendered into
     */
    render : function(container){
        if(!this.rendered && this.fireEvent("beforerender", this) !== false){
            this.container = Ext.get(container);
            this.rendered = true;
            this.onRender(this.container);
            if(this.cls){
                this.el.addClass(this.cls);
                delete this.cls;
            }
            this.fireEvent("render", this);
            if(this.hidden){
                this.hide();
            }
            if(this.disabled){
                this.disable();
            }
            this.afterRender(this.container);
        }
    },

    // private
    // default function is not really useful
    onRender : function(ct){
        this.el = Ext.get(this.el);
        ct.dom.appendChild(this.el.dom);
    },

    // private
    getAutoCreate : function(){
        var cfg = typeof this.autoCreate == "object" ?
                      this.autoCreate : Ext.apply({}, this.defaultAutoCreate);
        if(this.id && !cfg.id){
            cfg.id = this.id;
        }
        return cfg;
    },

    // private
    afterRender : Ext.emptyFn,

    // private
    destroy : function(){
        if(this.fireEvent("beforedestroy", this) !== false){
            this.purgeListeners();
            if(this.rendered){
                this.el.removeAllListeners();
                this.el.remove();
                if(this.actionMode == "container"){
                    this.container.remove();
                }
            }
            this.onDestroy();
            Ext.ComponentMgr.unregister(this);
            this.fireEvent("destroy", this);
        }
    },

    onDestroy : function(){

    },
    
    /**
     * Returns the underlying {@link Ext.Element}
     * @return {Ext.Element} The element
     */
    getEl : function(){
        return this.el;
    },

    /**
     * Try to focus this component
     * @param {Boolean} selectText True to also select the text in this component (if applicable)
     */
    focus : function(selectText){
        if(this.rendered){
            this.el.focus();
            if(selectText === true){
                this.el.dom.select();
            }
        }
    },

    // private
    blur : function(){
        if(this.rendered){
            this.el.blur();
        }
    },

    /**
     * Disable this component
     */
    disable : function(){
        if(this.rendered){
            this.getActionEl().addClass(this.disabledClass);
            this.el.dom.disabled = true;
        }
        this.disabled = true;
        this.fireEvent("disable", this);
    },

    /**
     * Enable this component
     */
    enable : function(){
        if(this.rendered){
            this.getActionEl().removeClass(this.disabledClass);
            this.el.dom.disabled = false;
        }
        this.disabled = false;
        this.fireEvent("enable", this);
    },

    /**
     * Convenience function for setting disabled/enabled by boolean
     * @param {Boolean} disabled
     */
    setDisabled : function(disabled){
        this[disabled ? "disable" : "enable"]();
    },

    /**
     * Show this component
     */
    show: function(){
        if(this.fireEvent("beforeshow", this) !== false){
            this.hidden = false;
            if(this.rendered){
                this.onShow();
            }
            this.fireEvent("show", this);
        }
    },

    // private
    onShow : function(){
        var st = this.getActionEl().dom.style;
        st.display = "";
        st.visibility = "visible";
    },

    /**
     * Hide this component
     */
    hide: function(){
        if(this.fireEvent("beforehide", this) !== false){
            this.hidden = true;
            if(this.rendered){
                this.onHide();
            }
            this.fireEvent("hide", this);
        }
    },

    // private
    onHide : function(){
        this.getActionEl().dom.style.display = "none";
    },

    /**
     * Convenience function to hide or show this component by boolean
     * @param {Boolean} visible True to show, false to hide
     */
    setVisible: function(visible){
        if(visible) {
            this.show();
        }else{
            this.hide();
        }
    }
});