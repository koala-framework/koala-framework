var onReady = require('kwf/on-ready');

Kwf.Utils.StickyHeader = function(selector, minWidth)
{
    onReady.onRender(selector, function(target) {

        minWidth = minWidth || 550;

        var parents = $(target).parentsUntil('body');
        var fixedParent = $(parents).filter(function(i, parent) {
            return $(parent).css('position') === 'fixed';
        })

        fixedParent = ($(fixedParent).length > 1) ? fixedParent[0] : fixedParent;

        if (target.length && fixedParent.length) {
            var cssStyle = {
                'position': 'relative',
                'top' :  -$(fixedParent).height()
            }

            $(document).find('.kwcBasicAnchor').css(cssStyle);
            $(fixedParent).addClass('kwfUtilsStickyHeader');

            function setCss(){
                if($(window).scrollTop() > $(target).height() && $(window).width() > minWidth) {
                    $(fixedParent).addClass('stick');
                } else {
                    $(fixedParent).removeClass('stick');
                }
            }

            $(window).on('scroll touchmove', function(event){
                setCss();
            })

            setCss();
        }
    })
}
