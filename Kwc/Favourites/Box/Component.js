Kwf.onComponentEvent('favouritesChanged', function(change) {
    var element = Ext.getBody().child('.kwcFavouritesBox').child('.cnt');
    element.update(parseInt(element.dom.innerHTML) + change);
});
