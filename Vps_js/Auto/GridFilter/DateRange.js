Vps.Auto.GridFilter.DateRange = function(config)
{
    Vps.Auto.GridFilter.DateRange.superclass.constructor.call(this, config);
    this.fieldFrom = new Vps.Form.DateField({
        width: 80,
        value: config.from
    });


    this.toolbarItems.add(this.fieldFrom);
    this.toolbarItems.add(' - ');
    this.fieldTo = new Vps.Form.DateField({
        width: 80,
        value: config.to
    });
    this.toolbarItems.add(this.fieldTo);

    if (config.button) {
	    this.toolbarItems.add(new Ext.Button({
	        text: trl('Suchen'),
	        handler: function() {
	            this.fireEvent('filter', this, this.getParams());
	        },
	        scope: this
	    }));
	}

	this.fieldTo.on('menuhidden', reload, this);

	if (!config.button) {
	    this.fieldTo.on('render', function() {
	        this.fieldTo.getEl().on('keypress',reload, this, {buffer: 500});
	    }, this);

		this.fieldFrom.on('menuhidden', reload , this);
	    this.fieldFrom.on('render', function() {
	        this.fieldFrom.getEl().on('keypress', reload, this, {buffer: 500});
	    }, this);
	}

	function reload(){
		if (this.fieldFrom.isValid() && this.fieldTo.isValid()) {
			this.fireEvent('filter', this, this.getParams());
		}
	}


};



Ext.extend(Vps.Auto.GridFilter.DateRange, Vps.Auto.GridFilter.Abstract, {
    reset: function() {
        this.fieldFrom.reset();
        this.fieldTo.reset();
    },
    getParams: function() {
        var params = {};
        params[this.id+'_from'] = this.fieldFrom.getValue().format('Y-m-d');
        params[this.id+'_to'] = this.fieldTo.getValue().format('Y-m-d');
        return params;
    }
});
