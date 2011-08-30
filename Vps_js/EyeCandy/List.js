Ext.namespace("Vps.EyeCandy");

Vps.EyeCandy.List = function(cfg) {
    Ext.apply(this, cfg);
    this._init();
};

Ext.extend(Vps.EyeCandy.List, Ext.util.Observable, {
    //el
    //plugins[]
    //states[]
    defaultState: 'normal',
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
        var items = this.el.query(this.childSelector);
        var idx = 0;
        items.forEach(function(el) {
            var item = new Vps.EyeCandy.List.Item({
                list: this,
                el: Ext.get(el),
                listIndex: idx
            });
            item.on('mouseEnter', function(item) {
                this.fireEvent('childMouseEnter', item);
            }, this);
            item.on('mouseLeave', function(item) {
                this.fireEvent('childMouseLeave', item);
            }, this);
            item.on('click', function(item) {
                this.fireEvent('childClick', item);
            }, this);
            item.on('stateChanged', function(item) {
                this.fireEvent('childStateChanged', item);
            }, this);
            this.items.push(item);
            idx += 1;
        }, this);

        if (this.defaultState) {
            this.items.each(function(i) {
                i.pushState(this.defaultState, 'startup');
            }, this);
        }

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
        return this.items[idx];
        /*
        var ret = null;
        this.items.each(function(i) {
            if (idx == i.listIndex) {
                ret = i;
                return true;
            }
        }, this);
        return ret;
        */
    },
    getLastItem: function() {
        if (!this.items.length) return null;
        return this.items[this.items.length-1];
    },
    getFirstItem: function() {
        return this.items[0];
    }
});
