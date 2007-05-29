/*
 * Ext JS Library 1.0.1
 * Copyright(c) 2006-2007, Ext JS, LLC.
 * licensing@extjs.com
 * 
 * http://www.extjs.com/license
 */

/**
 * @class Ext.form.Field
 * @extends Ext.Component
 * Base class for form fields that provides default event handling, sizing, value handling and other functionality.
 * @constructor
 * Creates a new Field 
 * @param {Object} config Configuration options
 */
Ext.form.Field = function(config){
    Ext.form.Field.superclass.constructor.call(this, config);
    this.addEvents({
        /**
         * @event focus
         * Fires when this field receives input focus
	     * @param {Ext.form.Field} this
	     */
        focus : true,
        /**
         * @event blur
         * Fires when
	     * @param {Ext.form.Field} this
	     */
        blur : true,
        /**
         * @event specialkey
         * Fires when any key related to navigation (arrows, tab, enter, esc, etc.) is pressed.  You can check
         * {@link Ext.EventObject#getKey} to determine which key was pressed.
	     * @param {Ext.form.Field} this
	     * @param {Ext.EventObject} e The event object
	     */
        specialkey : true,
        /**
         * @event change
         * Fires just before the field blurs if the field value has changed
	     * @param {Ext.form.Field} this
	     * @param {Mixed} value The changed value
	     * @param {Mixed} value The original value
	     */
        change : true,
        /**
         * @event invalid
         * Fires after the field has been marked as invalid
	     * @param {Ext.form.Field} this
	     * @param {String} msg The validation message
	     */
        invalid : true,
        /**
         * @event valid
         * Fires after the field has been validated with no errors
	     * @param {Ext.form.Field} this
	     */
        valid : true
    });
};

Ext.extend(Ext.form.Field, Ext.Component,  {
    /**
     * @cfg {String} invalidClass The CSS class to use when marking a field invalid (defaults to "x-form-invalid")
     */
    invalidClass : "x-form-invalid",
    /**
     * @cfg {String} invalidText The error text to use when marking a field invalid and no message is provided (defaults to "The value in this field is invalid")
     */
    invalidText : "The value in this field is invalid",
    /**
     * @cfg {String} focusClass The CSS class to use when the field receives focus (defaults to "x-form-focus")
     */
    focusClass : "x-form-focus",
    /**
     * @cfg {String/Boolean} validationEvent The event that should initiate field validation. Set to false to disable
      automatic validation. (defaults to "keyup")
     */
    validationEvent : "keyup",
    /**
     * @cfg {String/Boolean} validateOnBlur Defaults to true.
     */
    validateOnBlur : true,
    /**
     * @cfg {Number} validationDelay The length of time in milliseconds after user input begins until validation is initiated (defaults to 250)
     */
    validationDelay : 250,
    /**
     * @cfg {String/Object} autoCreate A DomHelper element spec, or true for a default element spec (defaults to
     * {tag: "input", type: "text", size: "20", autocomplete: "off"})
     */
    defaultAutoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off"},
    /**
     * @cfg {String} fieldClass The default CSS class for the field (defaults to "x-form-field")
     */
    fieldClass: "x-form-field",
    /**
     * @cfg {String} msgTarget The location where error text should display.  Should be one of the following values (defaults to 'qtip'):
     *<pre>
Value         Description
-----------   ----------------------------------------------------------------------
qtip          Display a quick tip when the user hovers over the field
title         Display a default browser title attribute popup
under         Add a block div beneath the field containing the error text
side          Add an error icon to the right of the field with a popup on hover
[element id]  Add the error text directly to the innerHTML of the specified element
</pre>
     */
    msgTarget: 'qtip',
    /**
     * @cfg {String} msgFx <b>Experimental</b> The effect used when displaying a validation message under the field (defaults to 'normal').
     */
    msgFx : 'normal',

    /**
     * @cfg {String} inputType The type attribute for input fields - e.g. radio, text, password. (defaults to "text")
     */
    inputType : undefined,

    // private
    isFormField : true,

    // private
    hasFocus : false,

    /**
     * @cfg {Mixed} value A value to initialize this field with
     */
    value : undefined,


    /**
     * Returns the name attribute of the field if available
     * @return {String} name The field name
     */
    getName: function(){
         return this.rendered && this.el.dom.name ? this.el.dom.name : (this.hiddenName || '');
    },

    /**
     * Apply the behaviors of this component to an existing element. <b>This is used instead of render().</b>
     * @param {String/HTMLElement/Element} el The id of the node, a DOM Node or an existing Element
     * @return {Ext.form.Field} this
     */
    applyTo : function(target){
        this.target = target;
        this.el = Ext.get(target);
        this.render(this.el.dom.parentNode);
        return this;
    },

    // private
    onRender : function(ct, position){
        if(this.el){
            this.el = Ext.get(this.el);
            if(!this.target){
                ct.dom.appendChild(this.el.dom);
            }
        }else {
            var cfg = this.getAutoCreate();
            if(!cfg.name){
                cfg.name = this.name || this.id;
            }
            if(this.inputType){
                cfg.type = this.inputType;
            }
            if(this.tabIndex !== undefined){
                cfg.tabIndex = this.tabIndex;
            }
            this.el = ct.createChild(cfg, position);
        }
        var type = this.el.dom.type;
        if(type){
            if(type == 'password'){
                type = 'text';
            }
            this.el.addClass('x-form-'+type);
        }
        if(!this.customSize && (this.width || this.height)){
            this.setSize(this.width || "", this.height || "");
        }
        if(this.readOnly){
            this.el.dom.readOnly = true;
        }

        this.el.addClass([this.fieldClass, this.cls]);
        this.initValue();
    },

    // private
    initValue : function(){
        if(this.value !== undefined){
            this.setValue(this.value);
        }else if(this.el.dom.value.length > 0){
            this.setValue(this.el.dom.value);
        }
    },

    // private
    afterRender : function(){
        Ext.form.Field.superclass.afterRender.call(this);
        this.initEvents();
    },

    // private
    fireKey : function(e){
        if(e.isNavKeyPress()){
            this.fireEvent("specialkey", this, e);
        }
    },

    /**
     * Resets the current field value to the originally-loaded value and clears any validation messages
     */
    reset : function(){
        this.setValue(this.originalValue);
        this.clearInvalid();
    },

    // private
    initEvents : function(){
        this.el.on(Ext.isIE ? "keydown" : "keypress", this.fireKey,  this);
        this.el.on("focus", this.onFocus,  this);
        this.el.on("blur", this.onBlur,  this);

        // reference to original value for reset
        this.originalValue = this.getValue();
    },

    // private
    onFocus : function(){
        if(!Ext.isOpera){ // don't touch in Opera
            this.el.addClass(this.focusClass);
        }
        this.hasFocus = true;
        this.startValue = this.getValue();
        this.fireEvent("focus", this);
    },

    // private
    onBlur : function(){
        this.el.removeClass(this.focusClass);
        this.hasFocus = false;
        if(this.validationEvent !== false && this.validateOnBlur && this.validationEvent != "blur"){
            this.validate();
        }
        var v = this.getValue();
        if(v != this.startValue){
            this.fireEvent('change', this, v, this.startValue);
        }
        this.fireEvent("blur", this);
    },

    /**
     * Sets the height and width of the field
     * @param {Number} width The new field width in pixels
     * @param {Number} height The new field height in pixels
     */
    setSize : function(w, h){
        if(!this.rendered || !this.el){
            this.width = w;
            this.height = h;
            return;
        }
        if(w){
            w = this.adjustWidth(this.el.dom.tagName, w);
            this.el.setWidth(w);
        }
        if(h){
            this.el.setHeight(h);
        }
        var h = this.el.dom.offsetHeight; // force browser recalc
    },

    /**
     * Returns whether or not the field value is currently valid
     * @param {Boolean} preventMark True to disable marking the field invalid
     * @return {Boolean} True if the value is valid, else false
     */
    isValid : function(preventMark){
        if(this.disabled){
            return true;
        }
        var restore = this.preventMark;
        this.preventMark = preventMark === true;
        var v = this.validateValue(this.getRawValue());
        this.preventMark = restore;
        return v;
    },

    /**
     * Validates the field value
     * @return {Boolean} True if the value is valid, else false
     */
    validate : function(){
        if(this.disabled || this.validateValue(this.getRawValue())){
            this.clearInvalid();
            return true;
        }
        return false;
    },

    // private
    // Subclasses should provide the validation implementation by overriding this
    validateValue : function(value){
        return true;
    },

    /**
     * Mark this field as invalid
     * @param {String} msg The validation message
     */
    markInvalid : function(msg){
        if(!this.rendered || this.preventMark){ // not rendered
            return;
        }
        this.el.addClass(this.invalidClass);
        msg = msg || this.invalidText;
        switch(this.msgTarget){
            case 'qtip':
                this.el.dom.qtip = msg;
                this.el.dom.qclass = 'x-form-invalid-tip';
                break;
            case 'title':
                this.el.dom.title = msg;
                break;
            case 'under':
                if(!this.errorEl){
                    var elp = this.el.findParent('.x-form-element', 5, true);
                    this.errorEl = elp.createChild({cls:'x-form-invalid-msg'});
                    this.errorEl.setWidth(elp.getWidth(true)-20);
                }
                this.errorEl.update(msg);
                Ext.form.Field.msgFx[this.msgFx].show(this.errorEl, this);
                break;
            case 'side':
                if(!this.errorIcon){
                    var elp = this.el.findParent('.x-form-element', 5, true);
                    this.errorIcon = elp.createChild({cls:'x-form-invalid-icon'});
                }
                this.alignErrorIcon();
                this.errorIcon.dom.qtip = msg;
                this.errorIcon.dom.qclass = 'x-form-invalid-tip';
                this.errorIcon.show();
                break;
            default:
                var t = Ext.getDom(this.msgTarget);
                t.innerHTML = msg;
                t.style.display = this.msgDisplay;
                break;
        }
        this.fireEvent('invalid', this, msg);
    },

    // private
    alignErrorIcon : function(){
        this.errorIcon.alignTo(this.el, 'tl-tr', [2, 0]);
    },

    /**
     * Clear any invalid styles/messages for this field
     */
    clearInvalid : function(){
        if(!this.rendered || this.preventMark){ // not rendered
            return;
        }
        this.el.removeClass(this.invalidClass);
        switch(this.msgTarget){
            case 'qtip':
                this.el.dom.qtip = '';
                break;
            case 'title':
                this.el.dom.title = '';
                break;
            case 'under':
                if(this.errorEl){
                    Ext.form.Field.msgFx[this.msgFx].hide(this.errorEl, this);
                }
                break;
            case 'side':
                if(this.errorIcon){
                    this.errorIcon.dom.qtip = '';
                    this.errorIcon.hide();
                }
                break;
            default:
                var t = Ext.getDom(this.msgTarget);
                t.innerHTML = '';
                t.style.display = 'none';
                break;
        }
        this.fireEvent('valid', this);
    },

    /**
     * Returns the raw data value which may or may not be a valid, defined value.  To return a normalized value see {@link #getValue}.
     * @return {Mixed} value The field value
     */
    getRawValue : function(){
        return this.el.getValue();
    },

    /**
     * Returns the normalized data value (undefined or emptyText will be returned as '').  To return the raw value see {@link #getRawValue}.
     * @return {Mixed} value The field value
     */
    getValue : function(){
        var v = this.el.getValue();
        if(v == this.emptyText || v === undefined){
            v = '';
        }
        return v;
    },

    /**
     * Sets the underlying DOM field's value directly, bypassing validation.  To set the value with validation see {@link #setValue}.
     * @param {Mixed} value The value to set
     */
    setRawValue : function(v){
        return this.el.dom.value = v;
    },

    /**
     * Sets a data value into the field and validates it.  To set the value directly without validation see {@link #setRawValue}.
     * @param {Mixed} value The value to set
     */
    setValue : function(v){
        this.value = v;
        if(this.rendered){
            this.el.dom.value = v;
            this.validate();
        }
    },

    // private
    adjustWidth : function(tag, w){
        tag = tag.toLowerCase();
        if(typeof w == 'number' && Ext.isStrict && !Ext.isSafari){
            if(Ext.isIE && (tag == 'input' || tag == 'textarea')){
                if(tag == 'input'){
                    return w + 2;
                }
                if(tag = 'textarea'){
                    return w-2;
                }
            }else if(Ext.isGecko && tag == 'textarea'){
                return w-6;
            }else if(Ext.isOpera){
                if(tag == 'input'){
                    return w + 2;
                }
                if(tag = 'textarea'){
                    return w-2;
                }
            }
        }
        return w;
    }
});


// anything other than normal should be considered experimental
Ext.form.Field.msgFx = {
    normal : {
        show: function(msgEl, f){
            msgEl.setDisplayed('block');
        },

        hide : function(msgEl, f){
            msgEl.setDisplayed(false).update('');
        }
    },

    slide : {
        show: function(msgEl, f){
            msgEl.slideIn('t', {stopFx:true});
        },

        hide : function(msgEl, f){
            msgEl.slideOut('t', {stopFx:true,useDisplay:true});
        }
    },

    slideRight : {
        show: function(msgEl, f){
            msgEl.fixDisplay();
            msgEl.alignTo(f.el, 'tl-tr');
            msgEl.slideIn('l', {stopFx:true});
        },

        hide : function(msgEl, f){
            msgEl.slideOut('l', {stopFx:true,useDisplay:true});
        }
    }
};
