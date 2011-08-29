Ext.namespace("Vps.EyeCandy.List");

Vps.EyeCandy.List.Item = function(cfg) {
    Ext.apply(this, cfg);
    this._init();
};

Ext.extend(Vps.EyeCandy.List.Item, Ext.util.Observable, {
    //list
    //listIndex
    //el
    _init: function() {
        this.addEvents({
            'mouseEnter': true,
            'mouseLeave': true,
            'click': true
        });

        this.state = [];

        Vps.Event.on(this.el, 'mouseEnter', function() {
            this.fireEvent('mouseEnter', this);
        }, this);
        Vps.Event.on(this.el, 'mouseLeave', function() {
            this.fireEvent('mouseLeave', this);
        }, this);
        Ext.fly(this.el).on('click', function() {
            this.fireEvent('click', this);
        }, this);
    },
    getState: function()
    {
        if (!this.state.length) return null;
        return this.state[this.state.length-1];
    },
    pushState: function(state)
    {
        this.state.push(state);
        this.fireEvent('stateChanged', this);
    },
    popState: function(state)
    {
        var ret = this.state.pop(state);
        this.fireEvent('stateChanged', this);
        return ret;
    },
    getNextSibling: function()
    {
        return this.list.getItem(this.listIndex+1);
    },
    getPreviousSibling: function()
    {
        return this.list.getItem(this.listIndex-1);
    }
});
