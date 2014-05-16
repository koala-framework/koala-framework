Kwf.onComponentEvent('favouritesChanged', function(change) {
    $('.kwcFavouritesPageComponentFavouritesCount').each(function() {
        var element = $(this);
        element.html(parseInt(element.html()) + change);
    }, this);
});
