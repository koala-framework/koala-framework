Kwf.EyeCandy.List.Plugins.ActiveListener.LargeContentAjax = Ext.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this.activeItem = null;
        this.list.on('activeChanged', function(item) {
            this._activate(item);
        }, this);

        this.largeContainer = this.list.el.child(this.largeContainerSelector);
        this.largeContent = {};
    },

    _loadItem: function(item, options)
    {
        if (this.largeContent[item.id]) return; //already loaded/loading

        this.largeContent[item.id] = this.largeContainer.createChild();
        this.largeContent[item.id].enableDisplayMode('block');
        this.largeContent[item.id].hide();

        var url = '/kwf/util/kwc/render';
        if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;
        Ext.Ajax.request({
            params: { url: item.el.child('a').dom.href },
            url: url,
            success: function(response) {
                var contentEl = this.largeContent[item.id].createChild();
                contentEl.update(response.responseText);
                if (options && options.success) {
                    options.success.call(options.scope || this);
                }
            },
            failure: function() {
                //fallback
                location.href = item.el.child('a').dom.href;
            },
            scope: this
        });
    },
    _preload: function()
    {
        this.list.items.each(function(i) {
            if (!this.largeContent[i.id]) {
                this._loadItem(i, {
                    success: function() {
                        this._preload.defer(100, this);
                    },
                    scope: this
                });
                return(false);
            }
        }, this);
    },
    _activate: function(item)
    {
        if (!this.activeItem) {
            //the first one activated must be already shown in largeContainer
            this.largeContent[item.id] = this.largeContainer.child('div');
            this.largeContent[item.id].enableDisplayMode('block');
            this.activeItem = item;

            this._preload();
            return;
        }

        this._loadItem(item);
        var activeEl = this.largeContent[this.activeItem.id];
        var nextEl = this.largeContent[item.id];

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

        this.activeItem = item;
    }
});
