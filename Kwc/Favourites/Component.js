(function() {
var kwcFavouritesComponentIds = [];
var kwcFavouritesInitialized = false;
Kwf.onJElementReady('.kwcFavourites', function(el, config) {
    kwcFavouritesComponentIds.push(config.componentId);

    if (!kwcFavouritesInitialized) {
        kwcFavouritesInitialized = true;
        setTimeout(function() {
            $.getJSON(config.controllerUrl + '/json-get-favourites', {
                componentId: config.componentId,
                kwfSessionToken: Kwf.sessionToken,
                kwcFavouritesComponentIds: kwcFavouritesComponentIds
            }, function(data) {
                $.each(data.componentIds, function(index, value) {
                    var favEl = $('#' + value);
                    favEl.addClass('isFavourite');
                    favEl.find('.switchContent').html(config.deleteFavourite);
                });
                kwcFavouritesComponentIds = [];
                kwcFavouritesInitialized = false;
            });
        }, 10);
    }

    var switchContent = el.find('.switchContent');
    el.find('div.switchLink > a').on('click.kwcFavourites', function(ev) {
        ev.preventDefault();
        el.toggleClass('isFavourite loading');
        el.hasClass('isFavourite') ? switchContent.html(config.deleteFavourite) : switchContent.html(config.saveFavourite);
        $.ajax({
            method: 'GET',
            url: config.controllerUrl+'/json-favourite',
            dataType: 'json',
            data: {
                componentId: config.componentId,
                kwfSessionToken: Kwf.sessionToken,
                is_favourite: el.hasClass('isFavourite') ? 1 : 0
            },
            error: function() {
                el.toggleClass('isFavourite');
            },
            success: function() {
                el.removeClass('loading');
                var count = 0;
                el.hasClass('isFavourite') ? count += 1 : count -= 1;
                Kwf.fireComponentEvent('favouritesChanged', count, el);
            }
        });
    });
}, this, {defer: true});
})();
