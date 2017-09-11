var onReady = require('kwf/commonjs/on-ready');
var $ = require('jquery');
var componentEvent = require('kwf/commonjs/component-event');

(function() {
var kwcFavouritesComponentIds = [];
var kwcFavouritesInitialized = false;
onReady.onRender('.kwcClass', function(el, config) {
    kwcFavouritesComponentIds.push(config.componentId);

    if (!kwcFavouritesInitialized) {
        kwcFavouritesInitialized = true;
        setTimeout(function() {
            $.getJSON(config.controllerUrl + '/json-get-favourites', {
                componentId: config.componentId,
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
}, this, {defer: true});
})();
