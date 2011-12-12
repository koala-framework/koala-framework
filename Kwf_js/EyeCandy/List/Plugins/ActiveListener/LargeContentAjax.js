Kwf.EyeCandy.List.Plugins.ActiveListener.LargeContentAjax = Ext.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    init: function() {
        this.activeItem = null;
        this.list.on('activeChanged', function(item) {
            this._activate(item);
        }, this);

        this.largeContainer = this.list.el.child(this.largeContainerSelector);
        this.largeContent = {};
        this.fetchedItems = {};
    },

    _loadItem: function(item, options)
    {
        if (this.largeContent[item.id]) return; //already loaded/loading

        this.largeContent[item.id] = this.largeContainer.createChild({
            html: '<div class="loading"><div class="inner1"><div class="inner2">&nbsp;</div></div></div>'
        });
        this.largeContent[item.id].enableDisplayMode('block');
        if (!options.visible) {
            this.largeContent[item.id].hide();
        }

        var url = '/kwf/util/kwc/render';
        if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;

        Ext.Ajax.request({
            params: { url: item.el.child('a').dom.href },
            url: url,
            success: function(response) {
                var contentEl = this.largeContent[item.id].createChild();
                this.largeContent[item.id].setStyle('position', 'absolute');
                this.fetchedItems[item.id] = true;
                contentEl.update(response.responseText);

                if (this.largeContent[item.id].isVisible()) {

                    this.largeContainer.setHeight(this.largeContent[item.id].getHeight(), true);

                    var showContent = function() {
                        this.largeContent[item.id].child('.loading').remove();
                        contentEl.fadeIn();
                        Kwf.callOnContentReady(contentEl.dom, {newRender: true});
                        if (options && options.success) {
                            options.success.call(options.scope || this);
                        }
                    };

                    var imagesToLoad = 0;
                    contentEl.query('img').each(function(imgEl) {
                        imagesToLoad++;
                        imgEl.onload = (function() {
                            imagesToLoad--;
                            if (imagesToLoad <= 0) showContent.call(this);
                        }).createDelegate(this);
                    }, this);

                    contentEl.hide(); //after callOnContentReady else cufon won't work inside contentEl
                    if (imagesToLoad == 0) showContent.call(this);

                } else {
                    this.largeContent[item.id].child('.loading').remove();
                    this.largeContent[item.id].show();
                    this.largeContent[item.id].hide();
                    Kwf.callOnContentReady(this.largeContent[item.id].dom, {newRender: true});
                }
            },
            scope: this
        });

    },
    _activate: function(item)
    {
        var nextItem = this.list.getItem(item.listIndex+1);
        if (nextItem && !this.largeContent[nextItem.id]) {
            //preload
            this._loadItem.defer(1000, this, [nextItem, {visible: false}]);
        }

        if (!this.activeItem) {
            //the first one activated must be already shown in largeContainer
            this.largeContent[item.id] = this.largeContainer.child('div');
            this.largeContent[item.id].enableDisplayMode('block');
            this.largeContent[item.id].setStyle('position', 'absolute');
            this.largeContainer.setHeight(this.largeContent[item.id].getHeight());
            this.activeItem = item;
            return;
        }

        this._loadItem(item, {visible: true});

        var activeEl = this.largeContent[this.activeItem.id];
        var nextEl = this.largeContent[item.id];

        if (this.fetchedItems[item.id]) {
            nextEl.show();
            this.largeContainer.setHeight(nextEl.getHeight(), true);
            nextEl.hide();
        }

        if (this.transition == 'fade') {
            activeEl.dom.style.zIndex = 2;

            nextEl.dom.style.zIndex = 1;
            nextEl.show();

            activeEl.fadeOut(Ext.applyIf({
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

        Kwf.callOnContentReady(nextEl.dom, {newRender: false});

        this.activeItem = item;
    }
});
