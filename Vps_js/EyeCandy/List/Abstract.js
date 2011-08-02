Ext.namespace("Vps.EyeCandy.List");

Vps.EyeCandy.List.Abstract = function(wrapperEl, childrenSelector) {
    this.wrapperEl = wrapperEl;
    this.items = Ext.query(childrenSelector, this.wrapperEl);
    this._init();
};

Ext.extend(Vps.EyeCandy.List.Abstract, Ext.util.Observable, {
    _init: function() {
        this.addEvents({
            'childMouseEnter': true,
            'childMouseLeave': true,
            'childClick': true
        });

        var idx = 0;
        this.items.forEach(function(item) {
            item.vpsListIndex = idx;
            Vps.Event.on(item, 'mouseEnter', function() {
                this.fireEvent('childMouseEnter', this, item);
            }, this);
            Vps.Event.on(item, 'mouseLeave', function() {
                this.fireEvent('childMouseLeave', this, item);
            }, this);
            Ext.get(item).on('click', function() {
                this.fireEvent('childClick', this, item);
            }, this);
            idx += 1;
        }, this);
    }
});
