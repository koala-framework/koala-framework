Kwf.onComponentEvent('favouritesChanged', function(change) {
    var element = Ext.getBody().child('.kwcFavouritesPageComponentFavouritesCount');
    element.update(parseInt(element.dom.innerHTML) + change);
});
