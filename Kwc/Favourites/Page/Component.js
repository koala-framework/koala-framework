var componentEvent = require('kwf/commonjs/component-event');

componentEvent.on('favouritesChanged', function(change) {
    $('.kwcFavouritesPageComponentFavouritesCount').each(function() {
        var element = $(this);
        element.html(parseInt(element.html()) + change);
    });
});
