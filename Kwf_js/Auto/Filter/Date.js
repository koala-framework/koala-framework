Vps.Auto.Filter.Date = function(config)
{
    Vps.Auto.Filter.Date.superclass.constructor.call(this, config);
    this.field = new Vps.Form.DateField({
        width: 80,
        value: config.value || ''
    });
    this.toolbarItems.add(this.field);
    this.field.on('menuhidden', function() {
        this.fireEvent('filter', this, this.getParams(config.paramName));
    }, this);
    this.field.on('render', function() {
        this.field.getEl().on('keypress', function() {
            this.fireEvent('filter', this, this.getParams(config.paramName));
        }, this, {buffer: 500});
    }, this);

};

Ext.extend(Vps.Auto.Filter.Date, Vps.Auto.Filter.Abstract, {
    reset: function() {
        this.field.reset();
    },
    getParams: function(paramName) {
        var params = {};
        if (this.field.getValue()) {
            params[paramName] = this.field.getValue().format('Y-m-d');
        } else {
            params[paramName] = null;
        }
        return params;
    }
});
