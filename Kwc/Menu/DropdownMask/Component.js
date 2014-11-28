Kwf.onJElementReady('.kwcMenuDropdownMask', function(target, config) {
    var mask = $('<div class="kwcMenuDropdownMaskMask"></div>');
    var maskClone = $(mask).clone();
    var subMask;

    $(config.maskParent).prepend($(maskClone));

    if(config.maskParent !== 'body') {
        $(maskClone).addClass('notBody');
    }

    var parents = $(target).parentsUntil('body');
    var fixedParent = $(parents).filter(function(i, parent) {
      return $(parent).css('position') === 'fixed';
    })

    function setCss() {
        var headerHeight = $(fixedParent).height();
        var maskHeight = $(fixedParent).height()-($(target).offset().top+$(target).height());
        var maskOffset = $(target).offset().top+$(target).height();
        $(subMask).css({height: maskHeight, top: maskOffset, 'z-index': 1});
    }

    if (fixedParent && fixedParent.length) {
        subMask = $(mask).clone();
        $(fixedParent).prepend(subMask);
        setCss();
        $(window).resize(function(e) {
            setCss();
        })
    }

    $('.kwcMenuDropdownMask > ul > li').on('mouseenter', function(e){
        if ($(e.currentTarget).hasClass('hasSubmenu'))
            $('.kwcMenuDropdownMaskMask').addClass('visible')
    })

    $('.kwcMenuDropdownMask > ul > li').on('mouseleave', function(e){
        if ($(e.currentTarget).hasClass('hasSubmenu'))
            $('.kwcMenuDropdownMaskMask').removeClass('visible')
    })
});

