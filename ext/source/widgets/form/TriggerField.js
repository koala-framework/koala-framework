/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.form.TriggerField
 * @extends Ext.form.TextField
 * Provides a convenient wrapper for TextFields that adds a clickable trigger button (looks like a combobox by default).
 * The trigger has no default action, so you must assign a function to implement the trigger click handler by
 * overriding {@link #onTriggerClick}. You can create a TriggerField directly, as it renders exactly like a combobox
 * for which you can provide a custom implementation.  For example:
 * <pre><code>
var trigger = new Ext.form.TriggerField();
trigger.onTriggerClick = myTriggerFn;
trigger.applyTo('my-field');
</code></pre>
 *
 * However, in general you will most likely want to use TriggerField as the base class for a reusable component.
 * {@link Ext.form.DateField} and {@link Ext.form.ComboBox} are perfect examples of this.
 * @cfg {String} triggerClass An additional CSS class used to style the trigger button.  The trigger will always get the
 * class 'x-form-trigger' by default and triggerClass will be <b>appended</b> if specified.
 * @constructor
 * Create a new TriggerField.
 * @param {Object} config Configuration options (valid {@Ext.form.TextField} config options will also be applied
 * to the base TextField)
 */
Ext.form.TriggerField = function(config){
    Ext.form.TriggerField.superclass.constructor.call(this, config);
    this.mimicing = false;
    this.on('disable', this.disableWrapper, this);
    this.on('enable', this.enableWrapper, this);
};

Ext.extend(Ext.form.TriggerField, Ext.form.TextField,  {
    /**
     * @cfg {String/Object} autoCreate A DomHelper element spec, or true for a default element spec (defaults to
     * {tag: "input", type: "text", size: "16", autocomplete: "off"})
     */

    // private
    defaultAutoCreate : {tag: "input", type: "text", size: "16", autocomplete: "off"},
    /**
     * @cfg {Boolean} hideTrigger True to hide the trigger element and display only the base text field (defaults to false)
     */
    hideTrigger:false,

    /** @cfg {Boolean} grow @hide */
    /** @cfg {Number} growMin @hide */
    /** @cfg {Number} growMax @hide */

    /**
     * @hide 
     * @method
     */
    autoSize: Ext.emptyFn,

    monitorTab : true,

    // private
    customSize : true,

    // private
    setSize : function(w, h){
        if(!this.wrap){
            this.width = w;
            this.height = h;
            return;
        }
        if(w){
            var wrapWidth = w;
            w = w - this.trigger.getWidth();
            Ext.form.TriggerField.superclass.setSize.call(this, w, h);
            this.wrap.setWidth(wrapWidth);
            if(this.onResize){
                this.onResize(wrapWidth, h);
            }
        }else{
            Ext.form.TriggerField.superclass.setSize.call(this, w, h);
            this.wrap.setWidth(this.el.getWidth()+this.trigger.getWidth());
        }
    },

    // private
    alignErrorIcon : function(){
        this.errorIcon.alignTo(this.wrap, 'tl-tr', [2, 0]);
    },

    // private
    onRender : function(ct, position){
        Ext.form.TriggerField.superclass.onRender.call(this, ct, position);
        this.wrap = this.el.wrap({cls: "x-form-field-wrap"});
        this.trigger = this.wrap.createChild({
            tag: "img", src: Ext.BLANK_IMAGE_URL, cls: "x-form-trigger "+this.triggerClass});
        this.trigger.on("click", this.onTriggerClick, this, {preventDefault:true});
        this.trigger.addClassOnOver('x-form-trigger-over');
        this.trigger.addClassOnClick('x-form-trigger-click');
        if(this.hideTrigger){
            this.trigger.setDisplayed(false);
        }
        this.setSize(this.width||'', this.height||'');
    },

    onDestroy : function(){
        if(this.trigger){
            this.trigger.removeAllListeners();
            this.trigger.remove();
        }
        if(this.wrap){
            this.wrap.remove();
        }
        Ext.form.TriggerField.superclass.onDestroy.call(this);
    },

    // private
    onFocus : function(){
        Ext.form.TriggerField.superclass.onFocus.call(this);
        if(!this.mimicing){
            this.mimicing = true;
            Ext.get(Ext.isIE ? document.body : document).on("mousedown", this.mimicBlur, this);
            if(this.monitorTab){
                this.el.on("keydown", this.checkTab, this);
            }
        }
    },

    // private
    checkTab : function(e){
        if(e.getKey() == e.TAB){
            this.triggerBlur();
        }
    },

    // private
    onBlur : function(){
        // do nothing
    },

    // private
    mimicBlur : function(e, t){
        if(!this.wrap.contains(t) && this.validateBlur()){
            this.triggerBlur();
        }
    },

    // private
    triggerBlur : function(){
        this.mimicing = false;
        Ext.get(Ext.isIE ? document.body : document).un("mousedown", this.mimicBlur);
        if(this.monitorTab){
            this.el.un("keydown", this.checkTab, this);
        }
        Ext.form.TriggerField.superclass.onBlur.call(this);
    },

    // private
    // This should be overriden by any subclass that needs to check whether or not the field can be blurred.
    validateBlur : function(e, t){
        return true;
    },

    // private
    disableWrapper : function(){
        if(this.wrap){
            this.wrap.addClass('x-item-disabled');
        }
    },

    // private
    enableWrapper : function(){
        if(this.wrap){
            this.wrap.removeClass('x-item-disabled');
        }
    },

    // private
    onShow : function(){
        if(this.wrap){
            this.wrap.dom.style.display = '';
            this.wrap.dom.style.visibility = 'visible';
        }
    },

    // private
    onHide : function(){
        this.wrap.dom.style.display = 'none';
    },

    /**
     * The function that should handle the trigger's click event.  This method does nothing by default until overridden
     * by a handler implementation.
     * @method
     * @param {EventObject} e
     */
    onTriggerClick : Ext.emptyFn
});