Vps.EyeCandy.List.Plugins.ActiveChanger.NextPreviousLinks = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    init: function() {

        this.previousLink = this.list.el.createChild({
            tag: 'a',
            cls: 'listSwitchPrevious',
            href: '#'
        });
        this.previousLink.on('click', function(ev) {
            ev.stopEvent();
            this.onPrevious();
        }, this);

        this.nextLink = this.list.el.createChild({
            tag: 'a',
            cls: 'listSwitchNext',
            href: '#'
        });
        this.nextLink.on('click', function(ev) {
            ev.stopEvent();
            this.onNext();
        }, this);

        this.list.on('activeChanged', function(item) {
            if (item == this.list.getFirstItem()) {
                this.previousLink.addClass('listSwitchInactive');
            } else {
                this.previousLink.removeClass('listSwitchInactive');
            }
            if (item == this.list.getLastItem()) {
                this.nextLink.addClass('listSwitchInactive');
            } else {
                this.nextLink.removeClass('listSwitchInactive');
            }
        }, this);
    },
    onPrevious: function() {
        var item = this.list.getItem(this.list.getActiveItem().listIndex-1);
        if (item) this.list.setActiveItem(item);
    },
    onNext: function() {
        var item = this.list.getItem(this.list.getActiveItem().listIndex+1);
        if (item) this.list.setActiveItem(item);
    }
});
