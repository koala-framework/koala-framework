Kwf.EyeCandy.List.Plugins.Carousel = Ext.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    numberShown: 5,
    //animationConfig: { duration: 0.25 },
    //fadeAnimationConfig: { duration: 0.25 }, //optional, by default animationConfig will be used
    useFade: true, //if elements will be faded in/out in addition to the moving
    moveElementSelector: false, //if false list.el, else list.child(moveElementSelector)

    _moveActive: false,
    init: function() {
        Ext.applyIf(this, {
            animationConfig: { duration: 0.25 }
        });
        if (!this.fadeAnimationConfig) {
            this.fadeAnimationConfig = this.animationConfig;
        }
        this.animationConfig.concurrent = true;
        this.fadeAnimationConfig.concurrent = true;

        this.moveElement = this.list.el;
        if (this.moveElementSelector) {
            this.moveElement = this.moveElement.child(this.moveElementSelector);
        }

        if (this.list.items.length > this.numberShown) {
            for(var i=this.numberShown; i<this.list.getItems().length; ++i) {
                this.list.getItem(i).el.hide();
            }
        }
    },
    render: function() {
        if (this.list.items.length > this.numberShown) {
            this.list.el.createChild({
                tag: 'a',
                cls: 'carouselPrevious',
                href: '#'
            }).on('click', function(ev) {
                ev.stopEvent();
                this.onMovePrevious();
            }, this);
            this.list.el.createChild({
                tag: 'a',
                cls: 'carouselNext',
                href: '#'
            }).on('click', function(ev) {
                ev.stopEvent();
                this.onMoveNext();
            }, this);
        }
    },

    onMoveNext: function() {
        if (this._moveActive) return;
        this._moveActive = true;

        // fade out first one
        if (this.useFade) {
            var cfg = Ext.applyIf({
                endOpacity: 0.01
            }, this.fadeAnimationConfig);
            this.list.getItem(0).el.fadeOut(cfg);
        }

        // fade in new one
        if (this.useFade) {
            var cfg = Ext.applyIf({
                endOpacity: 0.99
            }, this.fadeAnimationConfig);
            this.list.getItem(this.numberShown).el.fadeIn(cfg);
        } else {
            this.list.getItem(this.numberShown).el.show();
        }

        var firstElWidth = this.list.getItem(0).getWidthIncludingMargin();

        var cfg = Ext.applyIf({
            callback: function() {

                if (this.useFade) {
                    //fully hide & show (as we have endOpacity)
                    this.list.getItem(0).el.hide();
                    this.list.getItem(this.numberShown).el.show();
                }

                // push moved left element to back
                this.list.getItem(0).el.insertAfter(this.list.getLastItem().el); //move element
                this.list.items.push(this.list.items.shift()); //adapt array

                //adapt listIndex property
                var idx = 0;
                this.list.items.each(function(i) {
                    i.listIndex = idx++;
                }, this);

                // left von wrapper wieder setzen
                this.moveElement.move('right', firstElWidth, false);

                this._moveActive = false;
            },
            scope: this
        }, this.animationConfig);
        this.moveElement.move('left', firstElWidth, cfg);
    },

    onMovePrevious: function()
    {
        if (this._moveActive) return;
        this._moveActive = true;

        // rechts rausgeschobenes element vorn dran
        this.list.getLastItem().el.insertBefore(this.list.getItem(0).el);
        this.list.items.unshift(this.list.items.pop());

        //adapt listIndex property
        var idx = 0;
        this.list.items.each(function(i) {
            i.listIndex = idx++;
        }, this);

        // left von wrapper setzen
        this.moveElement.move('left', this.list.getItem(0).getWidthIncludingMargin(), false);

        // fade in first
        if (this.useFade) {
            var cfg = Ext.applyIf({
                useDisplay: false,
                endOpacity: 0.99
            }, this.fadeAnimationConfig);
            this.list.getItem(0).el.fadeIn(cfg);
        } else {
            this.list.getItem(0).el.show();
        }

        // fade out last
        if (this.useFade) {
            var cfg = Ext.applyIf({
                useDisplay: false,
                endOpacity: 0.01
            }, this.fadeAnimationConfig);
            this.list.getItem(this.numberShown).el.fadeOut(cfg);
        }

        var cfg = Ext.applyIf({
            callback: function() {
                this._moveActive = false;
                if (this.useFade) {
                    //fully hide & show (as we have endOpacity)
                    this.list.getItem(this.numberShown).el.hide();
                    this.list.getItem(0).el.show();
                }
            },
            scope: this
        }, this.animationConfig);
        this.moveElement.move('right', this.list.getItem(0).getWidthIncludingMargin(), cfg);
    }

});
