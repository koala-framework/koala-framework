Vps.Auto.GridFilter.Date = function(config)
{
    Vps.Auto.GridFilter.Date.superclass.constructor.call(this, config);
    this.field = new Vps.Form.DateField({
        width: 80,
        value: config.value || ''
    });
    this.toolbarItems.add(this.field);
    this.field.on('menuhidden', function() {
        this.fireEvent('filter', this, this.getParams());
    }, this);
    this.field.on('render', function() {
        this.field.getEl().on('keypress', function() {
            this.fireEvent('filter', this, this.getParams());
        }, this, {buffer: 500});
    }, this);

};

Ext.extend(Vps.Auto.GridFilter.Date, Vps.Auto.GridFilter.Abstract, {
    reset: function() {
        this.field.reset();
    },
    getParams: function() {
        var params = {};
        if (this.field.getValue()) {
            params['query_'+this.id] = this.field.getValue().format('Y-m-d');
        } else {
            params['query_'+this.id] = null;
        }
        return params;
    }
});
