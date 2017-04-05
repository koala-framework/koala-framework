var kwfExtend = require('kwf/commonjs/extend');

Kwf.EyeCandy.List.Plugins.ActiveChanger.Click = kwfExtend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this.list.on('childClick', function(item, ev) {
            ev.stopEvent();
            this.list.setActiveItem(item);
        }, this);
    }
});
