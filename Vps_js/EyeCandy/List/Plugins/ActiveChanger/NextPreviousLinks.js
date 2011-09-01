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
                this.previousLink.hide();
            } else {
                this.previousLink.show();
            }
            if (item == this.list.getLastItem()) {
                this.nextLink.hide();
            } else {
                this.nextLink.show();
            }
        }, this);
    },
    onPrevious: function() {
        this.list.setActiveItem(this.list.getItem(this.list.getActiveItem().listIndex-1));
    },
    onNext: function() {
        this.list.setActiveItem(this.list.getItem(this.list.getActiveItem().listIndex+1));
    }
});
