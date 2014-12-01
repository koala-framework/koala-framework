Kwf.Auto.Filter.Text = function(config)
{
    Kwf.Auto.Filter.Text.superclass.constructor.call(this, config);

    this.textField = new Ext2.form.TriggerField({
        width:config.width,
        triggerClass:'x2-form-clear-trigger',
        onTriggerClick: this.clear.createDelegate(this)
    });
    this.paramName = config.paramName;
    if (config.minLength) this.minLength = config.minLength;
    this.textField.on('render', function() {
        // TODO:
        // event darf nicht "keypress" sein, da sonst zB backspace und del tasten
        // nicht funktionieren. Was jetzt noch das Problem ist: Was ist wenn man
        // per rechter Maustaste etwas einfügt? Man müsste sich merken was drin
        // steht und bei allen events prüfen ob was daherkommt...
        this.textField.getEl().on('keyup', function() {
            if (!this.minLength) {
                this.fireEvent('filter', this, this.getParams());
            } else if (this.textField.getValue().length == 0 || this.textField.getValue().length >= this.minLength) {
                this.fireEvent('filter', this, this.getParams());
            }
        }, this, {buffer: 500});
    }, this);
    this.toolbarItems.add(this.textField);
};

Ext2.extend(Kwf.Auto.Filter.Text, Kwf.Auto.Filter.Abstract, {
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
