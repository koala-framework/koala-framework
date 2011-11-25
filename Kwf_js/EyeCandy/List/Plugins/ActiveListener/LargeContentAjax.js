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

        this.largeContent[item.id] = this.largeContainer.createChild({
            html: '<div class="loading"><div class="inner1"><div class="inner2">&nbsp;</div></div></div>'
        });
        this.largeContent[item.id].enableDisplayMode('block');

        var url = '/kwf/util/kwc/render';
        if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;

        Ext.Ajax.request({
            params: { url: item.el.child('a').dom.href },
            url: url,
            success: function(response) {
                var contentEl = this.largeContent[item.id].createChild();
                contentEl.hide();
                contentEl.update(response.responseText);

                var showContent = function() {
                    this.largeContent[item.id].child('.loading').hide();
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
                        if (imagesToLoad <= 0) showContent.call(this)
                    }).createDelegate(this);
                }, this);

                Kwf.callOnContentReady();
            },
            scope: this
        });
    },
    _activate: function(item)
    {
        if (!this.activeItem) {
            //the first one activated must be already shown in largeContainer
            this.largeContent[item.id] = this.largeContainer.child('div');
            this.largeContent[item.id].enableDisplayMode('block');
            this.activeItem = item;
            return;
        }

        if (!this.largeContent[item.id]) {
            this._loadItem(item);
        }
        var activeEl = this.largeContent[this.activeItem.id];
        var nextEl = this.largeContent[item.id];

        if (this.transition == 'fade') {
            activeEl.dom.style.zIndex = 2;

            nextEl.dom.style.zIndex = 1;
            nextEl.show();

            activeEl.fadeOut(Ext.applyIf({
                useDisplay: true,
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
