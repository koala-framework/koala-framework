Kwf.onComponentEvent('favouritesChanged', function(change) {
    var element = Ext.getBody().child('.kwcBoxFavourites').child('.cnt');
    element.update(parseInt(element.dom.innerHTML) + change);
});
