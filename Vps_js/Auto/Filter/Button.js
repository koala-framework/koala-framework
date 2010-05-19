Vps.Auto.Filter.Button = function(config)
{
    Vps.Auto.Filter.Button.superclass.constructor.call(this, config);

    this.button = new Ext.Button({
        icon: config.icon,
        text: config.text,
        cls: config.cls,
		pressed: config.pressed,
		tooltip: config.tooltip,
        enableToggle: true
    });
    this.button.on('toggle', function() {
        this.fireEvent('filter', this, this.getParams(config.paramName));
    }, this);
    this.toolbarItems.add(this.button);
};

Ext.extend(Vps.Auto.Filter.Button, Vps.Auto.Filter.Abstract, {
    reset: function() {
        this.button.toggle(false);
    },
    getParams: function(paramName) {
        var params = {};
        params[paramName] = this.button.pressed ? 1 : 0;
        return params;
    }
});
