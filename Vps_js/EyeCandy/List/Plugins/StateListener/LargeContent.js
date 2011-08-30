Vps.EyeCandy.List.Plugins.StateListener.LargeContent = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    activatedState: 'active',
    largeContentSelector: '.largeContent',
    largeContainerSelector: '.listSwitchLargeContent',
    //switchOptions: {}
    init: function() {
        this.list.on('childStateChanged', function(item) {
            if (item.getState() == this.activatedState) {
                this._activate(item);
            }
        }, this);

        this.largeContent = {};
        this.list.getItems().each(function(i) {
            this.largeContent[i.id] = i.el.child(this.largeContentSelector);
        }, this);
    },

    render: function() {
        this.largeContainer = this.list.el.child(this.largeContainerSelector);

        this.list.getFirstItem().pushState(this.activatedState, 'startup');
    },

    _activate: function(item)
    {
        if (this.activeItem) {
            this.largeContent[this.activeItem.id].hide();
        }

        this.largeContainer.appendChild(this.largeContent[item.id]);
        this.largeContent[item.id].show();

        this.activeItem = item;
    }
});
