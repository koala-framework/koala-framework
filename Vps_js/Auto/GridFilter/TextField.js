Vps.Auto.GridFilter.TextField = function(config)
{
    Vps.Auto.GridFilter.TextField.superclass.constructor.call(this, config);

    this.textField = new Ext.form.TextField({
        width: config.width
    });
    this.textField.on('render', function() {
        this.textField.getEl().on('keypress', function() {
            this.fireEvent('filter', this, this.getParams());
        }, this, {buffer: 500});
    }, this);
    this.toolbarItems.add(this.textField);
};

Ext.extend(Vps.Auto.GridFilter.TextField, Vps.Auto.GridFilter.Abstract, {
    reset: function() {
        this.textField.reset();
    },
    getParams: function() {
        return { query: this.textField.getValue() };
    },
    setValue: function(v) {
        this.textField.setValue(v);
        this.fireEvent('filter', this, this.getParams());
    }
});
