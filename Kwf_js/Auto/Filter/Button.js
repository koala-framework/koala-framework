Kwf.Auto.Filter.Button = function(config)
{
    Kwf.Auto.Filter.Button.superclass.constructor.call(this, config);

    this.button = new Ext2.Button({
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

Ext2.extend(Kwf.Auto.Filter.Button, Kwf.Auto.Filter.Abstract, {
    reset: function() {
        this.button.toggle(false);
    },
    getParams: function(paramName) {
        var params = {};
        params[paramName] = this.button.pressed ? 1 : 0;
        return params;
    }
});
