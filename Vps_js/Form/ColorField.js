/**
 * Based on code found at http://extjs.com/forum/showthread.php?t=5106
 * 
 * Modified by Merijn Schering <mschering@intermesh.nl>
 * 
 * Changes: 
 *  -Handles value better. Uses value config property as start value. 
 *  -Removed changed trigger image because it didn't handle state.
 *     -Added colors config property so you can overide the default color palette * 
 * 
 * @class Vps.Form.ColorField
 * @extends Ext.form.TriggerField
 * Provides a very simple color form field with a ColorMenu dropdown.
 * Values are stored as a six-character hex value without the '#'.
 * I.e. 'ffffff'
 * @constructor
 * Create a new ColorField
 * <br />Example:
 * <pre><code>
var cf = new Ext.form.ColorField({
    fieldLabel: 'Color',
    hiddenName:'pref_sales'
});
</code></pre>
 * @param {Object} config
 */


Vps.Form.ColorField =  Ext.extend(function(config){
    
    this.menu = new Ext.menu.ColorMenu();
    this.menu.palette.on('select', this.handleSelect, this );

    this.menu.on(Ext.apply({}, this.menuListeners, {
        scope:this
    }));
    
    if(config.colors)
    {
        this.menu.palette.colors=config.colors;
    }
  
    Vps.Form.ColorField.superclass.constructor.call(this, config);
  
},Ext.form.TriggerField,  {

    /**
       * @cfg {String} triggerClass
       * An additional CSS class used to style the trigger button.  The trigger will always get the
       * class 'x-form-trigger' and triggerClass will be <b>appended</b> if specified (defaults to 'x-form-color-trigger'
       * which displays a color icon).
    * */
    triggerClass : 'x-form-color-trigger',
    
    /**
     * @cfg {String/Object} autoCreate
     * A DomHelper element spec, or true for a default element spec (defaults to
     * {tag: "input", type: "text", size: "10", autocomplete: "off"})
     */
    // private
    defaultAutoCreate : {tag: "input", type: "text", size: "1", autocomplete: "off", maxlength:"6"},
    
    /**
     * @cfg {String} lengthText
     * A string to be displayed when the length of the input field is
     * not 3 or 6, i.e. 'fff' or 'ffccff'.
     */
    lengthText: trlVps("Color hex values must be either 3 or 6 characters."),
    
    //text to use if blank and allowBlank is false
    blankText: trlVps("Must have a hexidecimal value in the format ABCDEF."),
    
    /**
     * @cfg {String} color
     * A string hex value to be used as the default color.  Defaults
     * to 'FFFFFF' (white).
     */
    //defaultColor: 'FFFFFF',
    
    maskRe: /[a-f0-9]/i,
    // These regexes limit input and validation to hex values
    regex: /[a-f0-9]/i,

    width: 50,

    initComponent: function() {
        this.addEvents('select');
        Vps.Form.ColorField.superclass.initComponent.call(this);
    },

    alignHelpAndComment: function() {
        Vps.Form.ColorField.superclass.alignHelpAndComment.apply(this, arguments);
        if (this.colorPreview) {
            this.colorPreview.alignTo(this.getEl(), 'tr', [25, 0]);
        }
    },

    onRender: function(ct, position) {
        Vps.Form.ColorField.superclass.onRender.apply(this, arguments);
        this.colorPreview = this.wrap.createChild({
            tag: 'div',
            cls: 'vps-form-color-preview',
            style: 'left: '+(this.width+10+15)+'px'
        });
        this.setColor(this.value);
    },
    
    // private
    validateValue : function(value){
        if(value.length<1) {
            this.setColor(this.defaultColor);
            if(!this.allowBlank) {
                this.markInvalid(String.format(this.blankText, value));
                return false
            }
            return true;
        }
        if(value.length!=3 && value.length!=6 ) {
            this.markInvalid(String.format(this.lengthText, value));
            return false;
        }
        this.setColor(value);
        return true;
    },

    // private
    validateBlur : function(){
        return !this.menu || !this.menu.isVisible();
    },
    
    // Manually apply the invalid line image since the background
    // was previously cleared so the color would show through.
    markInvalid : function( msg ) {
        Vps.Form.ColorField.superclass.markInvalid.call(this, msg);
        this.colorPreview.setStyle({
            'background-image': 'url(/assets/ext/resources/images/default/grid/invalid_line.gif)'
        });
    },

  /**
   * Returns the current color value of the color field
   * @return {String} value The hexidecimal color value
  
  getValue : function(){
        return this.curValue || this.defaultValue || "FFFFFF";
  }, */

  /**
   * Sets the value of the color field.  Format as hex value 'FFFFFF'
   * without the '#'.
   * @param {String} hex The color value
   */
  setValue : function(hex){
        Vps.Form.ColorField.superclass.setValue.call(this, hex);
        this.setColor(hex);
  },
    
    /**
     * Sets the current color and changes the background.
     * Does *not* change the value of the field.
     * @param {String} hex The color value.
     */
    setColor : function(hex) {
        if (hex) {
            this.colorPreview.dom.style.backgroundColor = '#' + hex;
        } else {
            this.colorPreview.dom.style.backgroundColor = 'transparent';
        }
        this.colorPreview.dom.style.backgroundImage = 'none';
    },

  // private
  menuListeners : {
      select: function(m, d){
          this.setValue(d);
      },
      show : function(){ // retain focus styling
          this.onFocus();
      },
      hide : function(){
          this.focus();
          var ml = this.menuListeners;
          this.menu.un("select", ml.select,  this);
          this.menu.un("show", ml.show,  this);
          this.menu.un("hide", ml.hide,  this);
      }
  },
    
    //private
    handleSelect : function(palette, selColor) {
        this.setValue(selColor);
        this.fireEvent('select', this, selColor);
    },

  // private
  // Implements the default empty TriggerField.onTriggerClick function to display the ColorPicker
  onTriggerClick : function(){
      if(this.disabled){
          return;
      }
      
      this.menu.show(this.el, "tl-bl?");
  }
});

Ext.reg('colorfield', Vps.Form.ColorField);
