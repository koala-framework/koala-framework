var onReady = require('kwf/commonjs/on-ready');

onReady.onRender('.kwcClass', function youtubePlayer(el) {
    var imageEl = el.find('.kwcBem__image');
    var youtubePlayer = el.find('.kwcBem__youtubePlayer');
    var outerYoutubeContainer = el.find('.kwcBem__outerYoutubeContainer');
    imageEl.click(function(ev) {
        outerYoutubeContainer.addClass('kwcBem__outerYoutubeContainer--youtubeActive');
        onReady.callOnContentReady(youtubePlayer, {action: 'show'});
    });
});
