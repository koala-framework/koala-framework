Ext.form.Field.prototype.getName = function()
    //http://extjs.com/forum/showthread.php?t=152
    return this.rendered && this.el.dom.name ? this.el.dom.name : (this.name || this.hiddenName || ''
