var kwfExtend = require('kwf/commonjs/extend');

Kwf.EyeCandy.List.Plugins.ActiveListener.LargeContent = kwfExtend(Kwf.EyeCandy.List.Plugins.Abstract, {
    largeContentSelector: '.largeContent',
    largeContainerSelector: '.listSwitchLargeContent',
    transition: 'fade',
    //transitionConfig: {},
    init: function() {
        this.list.on('activeChanged', function(item) {
            this._activate(item);
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
                activeEl.fadeOut(Ext2.applyIf({
                    useDisplay: true
                }, this.transitionConfig));

                nextEl.dom.style.zIndex = 1;
                nextEl.fadeIn(Ext2.applyIf({
                    useDisplay: true
                }, this.transitionConfig));
            } else if (this.transition == 'slide') {
                activeEl.slideOut(
                    this.activeItem.listIndex < item.listIndex ? 'l' : 'r',
                    Ext2.applyIf({
                      remove: false,
                      useDisplay: true
                    }, this.transitionConfig)
                );
                nextEl.slideIn(
                    this.activeItem.listIndex < item.listIndex ? 'r' : 'l',
                    Ext2.applyIf({
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
