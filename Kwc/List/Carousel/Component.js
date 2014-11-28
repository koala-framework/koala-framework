Ext.namespace("Kwc.List.Carousel");
Kwc.List.Carousel.Component = Ext.extend(Kwf.EyeCandy.List, {
    childSelector: '.listItem',
    _activeChangeLocked: false,
    _init: function() {
        this.plugins = [
            new Kwc.List.Carousel.Carousel({
                numberShown: 3,
                moveElementSelector: '.listWrapper'
            }),
            new Kwc.List.Carousel.NextPreviousLinks()
        ];

        Kwc.List.Carousel.Component.superclass._init.call(this);
    },
    getActiveChangeLocked: function() {
        return this._activeChangeLocked;
    },
    setActiveChangeLocked: function(value) {
        this._activeChangeLocked = value;
    }
});



Kwf.onElementReady('.kwcListCarousel', function(el, config){

    el.setStyle('max-width', config.contentWidth+'px');
    var wrapper = el.child('.listWrapper') ? el.child('.listWrapper') : el.child('.imageWrapper');

    function responsiveContent(el) {
        if(wrapper.hasClass('listWrapper')) {
            el.query('.listItem').each(function(listItem){
                listItem = Ext.get(listItem);
                if(el.getWidth() < (config.contentWidth-1)) {
                    el.removeClass('fullwidth');
                    var invertedWidth = el.getWidth() * -1;
                    wrapper.setLeft(invertedWidth);
                } else {
                    el.addClass('fullwidth');
                    wrapper.setLeft(-(config.contentWidth+40));
                }
                listItem.setWidth(el.getWidth());
            })

        } else if(wrapper.hasClass('imageWrapper')) {
            var imageItem = wrapper.child('.listItem');
            wrapper.setLeft(0);
            if(imageItem && imageItem.length){
                imageItem.setWidth(el.getWidth());
            }

        }
    }

    if(wrapper.hasClass('listWrapper')) {
        var stage = new Kwc.List.Carousel.Component({
            el: el
        });
    }

    responsiveContent(el);

    Kwf.Utils.ResponsiveEl('.kwcListCarousel', responsiveContent);

});
