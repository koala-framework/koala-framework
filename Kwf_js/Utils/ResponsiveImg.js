Kwf.Utils.ResponsiveImg = function(selector)
{
    Kwf.onContentReady(function(readyEl) {
        Ext.get(readyEl).select(selector, true).each(function(el) {
            if (!el.dom.getAttribute('width') || !el.dom.getAttribute('height')) {
                return;
            }
            if (Ext.isIE8 || Ext.isIE7 || Ext.isIE6) return; //those suckers don't support responsiveness or dpr2, so all below is not needed

            //img tags that set width/height: auto in css don't have size until they are loaded
            //move the size attribute into inline style with respecting aspect ratio
            //also always set width as style for dpr2 images as width: auto would display them in double size
            el.dom.style.width = el.dom.getAttribute('width')+'px';
            if (!el.dom.complete) {
                el.dom.style.height = el.dom.getAttribute('height')+'px';
                if (el.getWidth() < el.dom.getAttribute('width')) {
                    //image is responsive, adapt height accordingly
                    var ratio = el.dom.getAttribute('height') / el.dom.getAttribute('width');
                    el.dom.style.height = (ratio * el.getWidth())+'px';
                }
                el.on('load', function() {
                    //once the img is loaded remove the style again and let css with: auto do it's work
                    //required to be able to react to browser window change
                    //this.style.width = ''; //don't remove width as that would break with dpr2 images
                    this.style.height = '';
                }, el.dom);
            }
        }, this);
    }, this, { priority: -1 });
};
