var kwfExtend = require('kwf/commonjs/extend');

Kwf.EyeCandy.List.Plugins.ActiveChanger.NextPreviousLinks = kwfExtend(Kwf.EyeCandy.List.Plugins.Abstract, {
    stopAtEnd: false,

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
                this.previousLink.addClass('listSwitchEnd');
            } else {
                this.previousLink.removeClass('listSwitchEnd');
            }
            if (item == this.list.getLastItem()) {
                this.nextLink.addClass('listSwitchEnd');
            } else {
                this.nextLink.removeClass('listSwitchEnd');
            }
        }, this);
    },
    onPrevious: function() {
        var item;
        if (this.list.getActiveItem() === this.list.getFirstItem() && !this.stopAtEnd) {
            item = this.list.getLastItem();
        } else {
            item = this.list.getItem(this.list.getActiveItem().listIndex-1);
        }

        if (item) {
            this.list.setActiveItem(item);
        }
    },
    onNext: function() {
        var item;
        if (this.list.getActiveItem() === this.list.getLastItem() && !this.stopAtEnd) {
            item = this.list.getFirstItem();
        } else {
            item = this.list.getItem(this.list.getActiveItem().listIndex+1);
        }

        if (item) {
            this.list.setActiveItem(item);
        }
    }
});
