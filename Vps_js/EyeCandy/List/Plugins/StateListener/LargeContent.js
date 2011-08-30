Vps.EyeCandy.List.Plugins.StateListener.LargeContent = Ext.extend(Vps.EyeCandy.List.Plugins.Abstract, {
    activatedState: 'active',
    largeContentSelector: '.largeContent',
    largeContainerSelector: '.listSwitchLargeContent',
    transition: 'fade',
    //transitionConfig: {},
    init: function() {
        this.list.on('childStateChanged', function(item) {
            if (item.getState() == this.activatedState) {
                this._activate(item);
            }
        }, this);

        this.largeContainer = this.list.el.child(this.largeContainerSelector);
        this.largeContainer.select('*').each(function(i) { i.remove(); }, this);

        this.largeContent = {};
        this.list.getItems().each(function(i) {
            var largeContent = i.el.child(this.largeContentSelector);
            this.largeContainer.appendChild(largeContent);
            largeContent.enableDisplayMode('block');
            largeContent.hide();
            this.largeContent[i.id] = largeContent;
        }, this);
    },

    render: function() {
        this.list.getFirstItem().pushState(this.activatedState, 'startup');
    },

    _activate: function(item)
    {
        var activeEl = null;
        if (this.activeItem) activeEl = this.largeContent[this.activeItem.id];
        var nextEl = this.largeContent[item.id];

        if (!activeEl) {
            //first, no fx
            nextEl.show();
        } else {
            if (this.transition == 'fade') {
                activeEl.dom.style.zIndex = 2;
                activeEl.fadeOut(Ext.applyIf({
                    useDisplay: true
                }, this.transitionConfig));

                nextEl.dom.style.zIndex = 1;
                nextEl.fadeIn(Ext.applyIf({
                    useDisplay: true
                }, this.transitionConfig));
            } else if (this.transition == 'slide') {
                activeEl.slideOut(
                    this.activeItem.listIndex < item.listIndex ? 'l' : 'r',
                    Ext.applyIf({
                      remove: false,
                      useDisplay: true
                    }, this.transitionConfig)
                );
                nextEl.slideIn(
                    this.activeItem.listIndex < item.listIndex ? 'r' : 'l',
                    Ext.applyIf({
                      remove: false,
                      useDisplay: true
                    }, this.transitionConfig)
                );
            } else {
                //default, no fx; switch hard
                activeEl.hide();
                nextEl.show();
            }
        }

        this.activeItem = item;
    }
});
