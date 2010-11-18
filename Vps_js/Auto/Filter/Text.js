Vps.Auto.Filter.Text = function(config)
{
    Vps.Auto.Filter.Text.superclass.constructor.call(this, config);

    this.textField = new Ext.form.TriggerField({
        width:config.width,
        triggerClass:'x-form-clear-trigger',
        onTriggerClick: this.clear.createDelegate(this)
    });
    this.paramName = config.paramName;
    this.textField.on('render', function() {
        // TODO:
        // event darf nicht "keypress" sein, da sonst zB backspace und del tasten
        // nicht funktionieren. Was jetzt noch das Problem ist: Was ist wenn man
        // per rechter Maustaste etwas einfügt? Man müsste sich merken was drin
        // steht und bei allen events prüfen ob was daherkommt...
        this.textField.getEl().on('keyup', function() {
            this.fireEvent('filter', this, this.getParams());
        }, this, {buffer: 500});
    }, this);
    this.toolbarItems.add(this.textField);
};

Ext.extend(Vps.Auto.Filter.Text, Vps.Auto.Filter.Abstract, {
	clear: function()
	{
        if (this.textField.getValue()) {
            this.textField.setValue('');
            this.fireEvent('filter', this, this.getParams());
        }
	},
	
    reset: function() {
        this.textField.reset();
    },
    getParams: function() {
        var params = {};
        params[this.paramName] = this.textField.getValue();
        return params;
    },
    setValue: function(v) {
        this.textField.setValue(v);
        this.fireEvent('filter', this, this.getParams());
    }
});
