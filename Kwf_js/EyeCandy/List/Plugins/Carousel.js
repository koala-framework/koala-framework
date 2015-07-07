var onReady = require('kwf/on-ready');

Kwf.EyeCandy.List.Plugins.Carousel = Ext2.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    //animationConfig: { duration: 0.25 },
    //fadeAnimationConfig: { duration: 0.25 }, //optional, by default animationConfig will be used
    useFade: true, //if elements will be faded in/out in addition to the moving
    moveElementSelector: false, //if false list.el, else list.child(moveElementSelector)
    getItemWidth: null, // Set function to get List Item Width dynamically (e.g. getItemWidth: function(listWidth) { ... })

    _moveActive: false,
    init: function() {
        var firstChild = this.list.items.length > 0 ? this.list.items[0] : null;
        if (!firstChild) return false;

        var numberShown = Math.floor(this.list.el.getWidth()/firstChild.el.getWidth());

        Ext2.applyIf(this, {
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

        Ext2.fly(window).on('resize', function() {
            this.updateButtons();
        }, this, { buffer: 101 }); //buffer 101 to get called after ResponsiveEl
    },
    render: function() {
        this.list.el.createChild({
            tag: 'a',
            cls: 'carouselPrevious',
            href: '#'
        }).on('click', function(ev) {
            if (this.list.el.child('a.carouselPrevious').hasClass('deactivated')) return;
            ev.stopEvent();
            this.onMovePrevious();
        }, this);
        this.list.el.createChild({
            tag: 'a',
            cls: 'carouselNext',
            href: '#'
        }).on('click', function(ev) {
            if (this.list.el.child('a.carouselNext').hasClass('deactivated')) return;
            ev.stopEvent();
            this.onMoveNext();
        }, this);

        this.updateButtons();
    },
    updateButtons: function() {
        var listWidth = Kwf.Utils.Element.getCachedWidth(this.list.el);
        var numberShown = 1;
        var itemWidth;

        if (this.getItemWidth) {
            itemWidth = this.getItemWidth.call(this, listWidth);
            numberShown = Math.floor(this.list.el.getWidth()/itemWidth);
        } else if (this.numberShown > 0) {
            numberShown = this.numberShown;
            itemWidth = listWidth / numberShown;
        }

        for (var i=0; i<this.list.getItems().length; i++) {
            if (itemWidth) this.list.getItem(i).el.setStyle('width', itemWidth+'px');
            this.list.getItem(i).el.show();
        }
        for (var i=numberShown; i<this.list.getItems().length; i++) {
            this.list.getItem(i).el.hide();
        }

        if (this.list.items.length > numberShown) {
            this.list.el.child('a.carouselPrevious').removeClass('deactivated');
            this.list.el.child('a.carouselNext').removeClass('deactivated');
        } else {
            this.list.el.child('a.carouselPrevious').addClass('deactivated');
            this.list.el.child('a.carouselNext').addClass('deactivated');
        }
    },
    onMoveNext: function() {
        if (this._moveActive) return;
        this._moveActive = true;

        var numberShown = Math.floor(this.list.el.getWidth()/this.list.items[0].el.getWidth());

        // fade out first one
        if (this.useFade) {
            var cfg = Ext2.applyIf({
                endOpacity: 0.01
            }, this.fadeAnimationConfig);
            this.list.getItem(0).el.fadeOut(cfg);
        }

        // fade in new one
        if (this.useFade) {
            var cfg = Ext2.applyIf({
                endOpacity: 0.99
            }, this.fadeAnimationConfig);
            this.list.getItem(numberShown).el.fadeIn(cfg);
        } else {
            this.list.getItem(numberShown).el.show();
        }

        onReady.callOnContentReady(this.list.getItem(numberShown).el, {action: 'show'});

        var firstElWidth = this.list.getItem(0).getWidthIncludingMargin();

        var cfg = Ext2.applyIf({
            callback: function() {
                if (this.useFade) {
                    //fully hide & show (as we have endOpacity)
                    this.list.getItem(0).el.hide();
                    this.list.getItem(numberShown).el.show();
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
                this.afterAnimation('next');
            },
            scope: this
        }, this.animationConfig);
        this.moveElement.move('left', firstElWidth, cfg);
    },
    onMovePrevious: function() {

        if (this._moveActive) return;
        this._moveActive = true;

        var numberShown = Math.floor(this.list.el.getWidth()/this.list.items[0].el.getWidth());

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
            var cfg = Ext2.applyIf({
                useDisplay: false,
                endOpacity: 0.99
            }, this.fadeAnimationConfig);
            this.list.getItem(0).el.fadeIn(cfg);
        } else {
            this.list.getItem(0).el.show();
        }
        onReady.callOnContentReady(this.list.getItem(0).el, {action: 'show'});

        // fade out last
        if (this.useFade) {
            var cfg = Ext2.applyIf({
                useDisplay: false,
                endOpacity: 0.01
            }, this.fadeAnimationConfig);
            this.list.getItem(numberShown).el.fadeOut(cfg);
        }

        var cfg = Ext2.applyIf({
            callback: function() {
                this._moveActive = false;
                if (this.useFade) {
                    //fully hide & show (as we have endOpacity)
                    this.list.getItem(numberShown).el.hide();
                    this.list.getItem(0).el.show();
                }
                this.afterAnimation('previous');
            },
            scope: this
        }, this.animationConfig);
        this.moveElement.move('right', this.list.getItem(0).getWidthIncludingMargin(), cfg);
    },

    afterAnimation: function(direction)
    {
    }

});
