Kwf.Utils.StickyHeader = function(selector)
{
    Kwf.onJElementReady(selector, function(target) {
        var parents = $(target).parentsUntil('body');
        var fixedParent = $(parents).filter(function(i, parent) { 
            return $(parent).css('position') === 'fixed';
        })

        if(target.length && fixedParent.length) {

            $(fixedParent).addClass('kwfUtilsStickyHeader');

            function setCss(){
                if($(window).scrollTop() > $(target).height() && $(window).width() > 550) {
                    $(fixedParent).addClass('stick').css({'transform': 'translate(0, -'+$(target).height()+'px)'});
                } else {
                    $(fixedParent).removeClass('stick').css({'transform': 'translate(0, 0)'});
                }
            }

            $(window).on('scroll touchmove', function(event){
                setCss();
            })

            setCss();
        }
    })
}
