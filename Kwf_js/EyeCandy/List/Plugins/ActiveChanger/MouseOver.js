var kwfExtend = require('kwf/extend');

Kwf.EyeCandy.List.Plugins.ActiveChanger.MouseOver = kwfExtend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this.list.on('childMouseEnter', function(item, ev) {
            this.list.setActiveItem(item);
        }, this);
    }
});
