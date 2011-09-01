Vps.EyeCandy.List.Plugins.StateListener.NextPreviousLinks = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    state: 'active',
    render: function() {
        this.list.el.createChild({
            tag: 'a',
            cls: 'listSwitchPrevious',
            href: '#'
        }).on('click', function(ev) {
            ev.stopEvent();
            this.onPrevious();
        }, this);
        this.list.el.createChild({
            tag: 'a',
            cls: 'listSwitchNext',
            href: '#'
        }).on('click', function(ev) {
            ev.stopEvent();
            this.onNext();
        }, this);
        this.list.on('childStateChanged', function(item) {
            if (item.getState() == this.state) {
                //links enabled/disabled wenn am ende/anfang
            }
        }, this);
    },
    onPrevious: function() {
        this.list.getItems().each(function(i) {
            if (i.getState() == this.state) {
                var next = this.list.getItem(i.listIndex+1);
                next.pushState('active', this);
            }
        }, this);
    },
    onNext: function() {
        this.list.getItems().each(function(i) {
            if (i.getState() == this.state) {
                var next = this.list.getItem(i.listIndex-1);
                next.pushState('active', this);
            }
        }, this);
    }
});
