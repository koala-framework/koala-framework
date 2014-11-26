Ext2.namespace("Kwf.EyeCandy");

Kwf.onElementShow('.kwfEyeCandyList', function eyeCandyList(el) {
    var opts = Ext2.fly(el).down('.options', true);
    if (opts) {
        opts = Ext2.decode(opts.value);
        var cls = Kwf.EyeCandy.List;
        if (opts['class']) {
            cls = eval(opts['class']);
            delete opts['class'];
        }
        opts.el = el;
        el.list = new cls(opts);
    }
}, { defer: true });

Kwf.EyeCandy.List = function(cfg) {
    Ext2.apply(this, cfg);
    this._init();
};

Ext2.extend(Kwf.EyeCandy.List, Ext2.util.Observable, {
    //el
    //plugins[]
    //states[]
    defaultState: 'normal',
    activeState: 'active',
    childSelector: '> .listItem',

    _lockChangeActive: false,
    _init: function() {
        this.addEvents({
            'childMouseEnter': true,
            'childMouseLeave': true,
            'childClick': true,
            'childStateChanged': true,
            'activeChanged': true
        });
        Ext2.applyIf(this, {
            plugins: [],
            states: []
        });

        if (!this.el) throw 'el required';

        this.el = Ext2.get(this.el);

        this.items = [];
        var items = this.el.query(this.childSelector);
        var idx = 0;
        items.forEach(function(el) {

            var item = new Kwf.EyeCandy.List.Item({
                list: this,
                id: Ext2.id(el),
                el: Ext2.get(el),
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

            var display = item.el.getStyle('display');
            if (!display) display = 'block';
            item.el.enableDisplayMode(display);

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
        if (this._lockChangeActive) return;
        var previousItem = this.activeItem;
        this.activeItem = item;
        if (previousItem != this.activeItem) {
            this.fireEvent('activeChanged', this.activeItem);
            if (previousItem) {
                previousItem.removeState(this.activeState, 'active');
                previousItem.el.removeClass('active');
            }
            if (this.activeItem) {
                this.activeItem.pushState(this.activeState, 'active');
                this.activeItem.el.addClass('active');
            }
        }
    },
    getActiveItem: function() {
        return this.activeItem;
    },

    /**
     * Locks changing active item, if locked setActiveItem won't change the active item
     *
     * Can be used to lock during animations.
     */
    setLockChangeActive: function(i) {
        this._lockChangeActive = i;
    }
});
