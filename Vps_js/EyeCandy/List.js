Ext.namespace("Vps.EyeCandy");

Vps.EyeCandy.List = function(cfg) {
    Ext.apply(this, cfg);
    this._init();
};

Ext.extend(Vps.EyeCandy.List, Ext.util.Observable, {
    //el
    //plugins[]
    //states[]
    childSelector: '.listItem',
    _init: function() {
        this.addEvents({
            'childMouseEnter': true,
            'childMouseLeave': true,
            'childClick': true,
            'childStateChanged': true
        });
        Ext.applyIf(this, {
            plugins: [],
            states: [],
        });

        if (!this.el) throw 'el required';

        this.el = Ext.get(this.el);

        this.items = [];
        var items = Ext.query(this.childSelector, this.el);
        var idx = 0;
        items.forEach(function(el) {
            var item = new Vps.EyeCandy.List.Item({
                list: this,
                el: el,
                listIndex: idx
            });
            item.on('mouseEnter', function(item) {
                this.fireEvent('childMouseEnter', this, item);
            }, this);
            item.on('mouseLeave', function(item) {
                this.fireEvent('childMouseLeave', this, item);
            }, this);
            item.on('click', function(item) {
                this.fireEvent('childClick', this, item);
            }, this);
            item.on('stateChanged', function(item) {
                this.fireEvent('childStateChanged', this, item);
            }, this);
            idx += 1;
        }, this);

        this.plugins.each(function(p) {
            p.list = this;
            p.init();
        }, this);

        this.plugins.each(function(p) {
            p.render();
        }, this);
    },
    getItems: function() {
        return this.items;
    },
    getItem: function(idx) {
        var ret = null;
        this.items.each(function(i) {
            if (idx == i.listIndex) {
                ret = i;
                return true;
            }
        }, this);
        return ret;
    }
});
