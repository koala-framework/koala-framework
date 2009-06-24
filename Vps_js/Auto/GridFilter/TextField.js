Vps.Auto.GridFilter.TextField = function(config)
{
    Vps.Auto.GridFilter.TextField.superclass.constructor.call(this, config);

    this.textField = new Ext.form.TextField({
        width: config.width
    });
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

Ext.extend(Vps.Auto.GridFilter.TextField, Vps.Auto.GridFilter.Abstract, {
    reset: function() {
        this.textField.reset();
    },
    getParams: function() {
        var key = 'query';
        if (this.id != 'text') key = 'query_'+this.id;

        var params = {};
        params[key] = this.textField.getValue();
        return params;
    },
    setValue: function(v) {
        this.textField.setValue(v);
        this.fireEvent('filter', this, this.getParams());
    }
});
