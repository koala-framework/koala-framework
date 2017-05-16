var onReady = require('kwf/commonjs/on-ready-ext2');

Ext2.namespace("Kwc.Legacy.List.Carousel");
Kwc.Legacy.List.Carousel.Carousel = Ext2.extend(Kwf.EyeCandy.List.Plugins.Abstract, {
    numberShown: 5,
    moveElementSelector: false, //if false list.el, else list.child(moveElementSelector)

    _currentListIndex: 0,
    init: function() {
        Ext2.applyIf(this, {
            animationConfig: { duration: 0.60, easing: 'easeBothStrong' }
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
        this.list.setActiveItem(this.list.items[0]);
        if (this.list.items.length > 2) {
            this.list.setActiveItem(this.list.items[1]);
            this._currentListIndex = 1;
        }
        
        this.list.on('activeChanged', function(item) {
            if (item.listIndex > this._currentListIndex) {
                this.onMoveNext();
            } else {
                this.onMovePrevious();
            }
        }, this);
    },
    onMoveNext: function() {
        // fade out first one
        this.list.setActiveChangeLocked(true);
        this.list.getItem(this.numberShown).el.show();

        onReady.callOnContentReady(this.moveElement, {newRender: false});

        var firstElWidth = this.list.getItem(0).getWidthIncludingMargin();

        this.list.el.addClass('carouselMoving');

        var cfg = Ext2.applyIf({
            callback: function() {
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

                this._currentListIndex = this.list.getActiveItem().listIndex;
                this.list.setActiveChangeLocked(false);

                this.list.el.removeClass('carouselMoving');
            },
            scope: this
        }, this.animationConfig);
        this.moveElement.move('left', firstElWidth, cfg);
    },
    onMovePrevious: function()
    {
        this.list.setActiveChangeLocked(true);
        // rechts rausgeschobenes element vorn dran
        this.list.getLastItem().el.insertBefore(this.list.getItem(0).el);
        this.list.items.unshift(this.list.items.pop());

        this.list.el.addClass('carouselMoving');

        //adapt listIndex property
        var idx = 0;
        this.list.items.each(function(i) {
            i.listIndex = idx++;
        }, this);

        // left von wrapper setzen
        this.moveElement.move('left', this.list.getItem(0).getWidthIncludingMargin(), false);

        // fade in first
        this.list.getItem(0).el.show();

        onReady.callOnContentReady(this.moveElement, {newRender: false});

        var cfg = Ext2.applyIf({
            callback: function() {
                this._currentListIndex = this.list.getActiveItem().listIndex;
                this.list.setActiveChangeLocked(false);

                this.list.el.removeClass('carouselMoving');
            },
            scope: this
        }, this.animationConfig);
        this.moveElement.move('right', this.list.getItem(0).getWidthIncludingMargin(), cfg);
    }

});
