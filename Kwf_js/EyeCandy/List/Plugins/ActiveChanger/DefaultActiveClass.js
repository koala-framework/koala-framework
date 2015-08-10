var kwfExtend = require('kwf/extend');

Kwf.EyeCandy.List.Plugins.ActiveChanger.DefaultActiveClass = kwfExtend(Kwf.EyeCandy.List.Plugins.Abstract, {
    render: function() {
        var found = false;
        this.list.items.forEach(function(item) {
            if (item.el.hasClass('defaultActive')) {
                item.el.removeClass('defaultActive');
                this.list.setActiveItem(item);
                found = true;
                return(false);
            }
        }, this);
        if (!found) {
            this.list.setActiveItem(this.list.getFirstItem());
        }
    }
});
