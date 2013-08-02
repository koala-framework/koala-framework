Kwf.onComponentEvent('favouritesChanged', function(change) {
    Ext.select('.kwcFavouritesPageComponentFavouritesCount').each(function(element) {
        element.update(parseInt(element.dom.innerHTML) + change);
    }, this);
});
