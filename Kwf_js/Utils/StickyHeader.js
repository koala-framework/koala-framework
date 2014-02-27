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
                if($(fixedParent).css('position') === 'absolute') return false;

                if($(window).scrollTop() > $(target).height() && $(window).width() > 550) {
                    $(fixedParent).addClass('stick');
                } else {
                    $(fixedParent).removeClass('stick');
                }
            }

            function compareWindowHeight(){
                if($(fixedParent).height()*4 > $(window).height()) {
                    $(fixedParent).css({position: 'absolute'});
                } else {
                    $(fixedParent).css({position: 'fixed'});
                }
            }

            $(window).on('scroll touchmove', function(event){
                setCss();
            })

            $(window).on('resize', function(e){
                compareWindowHeight();
            })

            compareWindowHeight();
            setCss();
        }
    })
}
