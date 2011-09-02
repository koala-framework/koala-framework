Ext.namespace("Vps.EyeCandy");

Vps.onContentReady(function() {
    Ext.query('.vpsEyeCandyList').forEach(function(el) {
        if (!el.list) {
            var opts = Ext.fly(el).down('.options', true);
            if (opts) {
                opts = Ext.decode(opts.value);
                var cls = Vps.EyeCandy.List;
                if (opts.class) {
                    cls = eval(opts.class);
                    delete opts.class;
                }
                opts.el = el;
                el.list = new cls(opts);
            }
        }
    }, this);
});

Vps.EyeCandy.List = function(cfg) {
    Ext.apply(this, cfg);
    this._init();
};

Ext.extend(Vps.EyeCandy.List, Ext.util.Observable, {
    //el
    //plugins[]
    //states[]
    defaultState: 'normal',
    activeState: 'active',
    childSelector: '.listItem',
    _init: function() {
        this.addEvents({
            'childMouseEnter': true,
            'childMouseLeave': true,
            'childClick': true,
            'childStateChanged': true,
            'activeChanged': true
        });
        Ext.applyIf(this, {
            plugins: [],
            states: []
        });

        if (!this.el) throw 'el required';

        this.el = Ext.get(this.el);

        this.items = [];
        var items = this.el.query(this.childSelector);
        var idx = 0;
        items.forEach(function(el) {

            var item = new Vps.EyeCandy.List.Item({
                list: this,
                id: Ext.id(el),
                el: Ext.get(el),
                listIndex: idx
            });
            item.on('mouseEnter', function(item) {
                this.fireEvent('childMouseEnter', item);
            }, this);
            item.on('mouseLeave', function(item) {
                this.fireEvent('childMouseLeave', item);
            }, this);
            item.on('click', function(item, ev) {
                this.fireEvent('childClick', item, ev);
            }, this);
            item.on('stateChanged', function(item) {
                /*
                var msg = '';
                this.items.each(function(i) {
                    msg += i.getState()+' ';
                }, this);
                console.log(msg);
                */
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
    },

    setActiveItem: function(item) {
        var previousItem = this.activeItem;
        this.activeItem = item;
        if (previousItem != this.activeItem) {
            this.fireEvent('activeChanged', this.activeItem);
            if (previousItem) {
                previousItem.removeState(this.activeState, 'active');
            }
            this.activeItem.pushState(this.activeState, 'active');
        }
    },
    getActiveItem: function() {
        return this.activeItem;
    }
});
