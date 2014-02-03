Kwf.Utils.StickyHeader = function(selector)
{
    Kwf.onJElementReady(selector, function(target) {

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
                if($(window).scrollTop() > $(target).height() && $(window).width() > 550) {
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
