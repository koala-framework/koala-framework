var onReady = require('kwf/on-ready');
var $ = require('jQuery');
var componentEvent = require('kwf/component-event');
var fetchSessionToken = require('kwf/user/fetch-session-token');

(function() {
var kwcFavouritesComponentIds = [];
var kwcFavouritesInitialized = false;
onReady.onRender('.kwcClass', function(el, config) {
    kwcFavouritesComponentIds.push(config.componentId);

    if (!kwcFavouritesInitialized) {
        kwcFavouritesInitialized = true;
        setTimeout(function() {
            fetchSessionToken().done(function(sessionToken) {
                $.getJSON(config.controllerUrl + '/json-get-favourites', {
                    componentId: config.componentId,
                    kwfSessionToken: sessionToken,
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
            });
        }, 10);
    }

    var switchContent = el.find('.switchContent');
    el.find('div.switchLink > a').on('click.kwcFavourites', function(ev) {
        ev.preventDefault();
        el.toggleClass('isFavourite loading');
        el.hasClass('isFavourite') ? switchContent.html(config.deleteFavourite) : switchContent.html(config.saveFavourite);
        fetchSessionToken().done(function(sessionToken) {
            $.ajax({
                method: 'GET',
                url: config.controllerUrl+'/json-favourite',
                dataType: 'json',
                data: {
                    componentId: config.componentId,
                    kwfSessionToken: sessionToken,
                    is_favourite: el.hasClass('isFavourite') ? 1 : 0
                },
                error: function() {
                    el.toggleClass('isFavourite');
                },
                success: function() {
                    el.removeClass('loading');
                    var count = 0;
                    el.hasClass('isFavourite') ? count += 1 : count -= 1;
                    componentEvent.trigger('favouritesChanged', count, el);
                }
            });
        });
    });
}, this, {defer: true});
})();
