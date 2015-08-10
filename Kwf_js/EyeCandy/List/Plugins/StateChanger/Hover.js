var kwfExtend = require('kwf/extend');

Kwf.EyeCandy.List.Plugins.StateChanger.Hover = kwfExtend(Kwf.EyeCandy.List.Plugins.Abstract, {
    state: 'large',
    init: function() {
        this.list.on('childMouseEnter', function(item) {
            item.pushState(this.state, this);
        }, this);
        this.list.on('childMouseLeave', function(item) {
            item.removeState(this.state, this);
        }, this);
    }
});
