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

                    var previousHeight = this.largeContainer.getHeight();
                    var newHeight = this._getLargeContentHeight(item);
                    this.largeContainer.setHeight(newHeight); //set to new height, not animated
                    Kwf.callOnContentReady(contentEl.dom, {newRender: true});
                    contentEl.hide(); //hide after callOnContentReady, will be faded in after images loaded
                    this.largeContainer.setHeight(previousHeight, false); //set back to previous height, not animated
                    this.largeContainer.setHeight(newHeight, true); //animate to new height

                    var showContent = function() {
                        this.largeContent[item.id].child('.loading').remove();
                        contentEl.fadeIn();
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

    _getLargeContentHeight: function(item) {
        var height = this.largeContent[item.id].getHeight();
        if (this.largeContainer.getStyle('margin-top') && this.largeContainer.getStyle('margin-top').substr(-2)=='px') {
            height += parseInt(this.largeContainer.getStyle('margin-top'));
        }
        if (this.largeContainer.getStyle('margin-bottom') && this.largeContainer.getStyle('margin-bottom').substr(-2)=='px') {
            height += parseInt(this.largeContainer.getStyle('margin-bottom'));
        }
        return height;
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
            this.largeContainer.setHeight(this._getLargeContentHeight(item));
            this.activeItem = item;
            return;
        }

        this._loadItem(item, {visible: true});

        var activeEl = this.largeContent[this.activeItem.id];
        var nextEl = this.largeContent[item.id];

        if (this.fetchedItems[item.id]) {
            nextEl.show();
            var oldHeight = this.largeContainer.getHeight();
            var newHeight = this._getLargeContentHeight(item);
            this.largeContainer.setHeight(newHeight); //set new height without animation
            Kwf.callOnContentReady(nextEl.dom, {newRender: false});
            this.largeContainer.setHeight(oldHeight); //set previous height without animation
            this.largeContainer.setHeight(newHeight, true); //and now animate to new height
            nextEl.hide();
        }

        if (this.transition == 'fade') {
            activeEl.dom.style.zIndex = 1;
            nextEl.dom.style.zIndex = 2;

            activeEl.stopFx();
            nextEl.stopFx();
            nextEl.fadeIn(Ext.applyIf({
                callback: function() {
                    activeEl.hide();
                },
                scope: this
            }, this.transitionConfig));
        } else if (this.transition == 'slide') {
            activeEl.slideOut(
                this.activeItem.listIndex < item.listIndex ? 'l' : 'r',
                Ext.applyIf({
                    remove: false,
                }, this.transitionConfig)
            );
            nextEl.slideIn(
                this.activeItem.listIndex < item.listIndex ? 'r' : 'l',
                Ext.applyIf({
                    remove: false,
                }, this.transitionConfig)
            );
        } else {
            //default, no fx; switch hard
            activeEl.hide();
            nextEl.show();
        }

        Kwf.Statistics.count(item.el.child('a').dom.href);

        this.activeItem = item;
    }
});
