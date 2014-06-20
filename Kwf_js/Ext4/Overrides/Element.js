// Workaround from http://www.sencha.com/forum/showthread.php?266651-4.2.1-GA-ToolTip-cut-off-on-Mac-Firefox
// Fixed in Ext 4.2.2
Ext4.define('Kwf.Ext4.Overrides.Element', {
    override: 'Ext.dom.Element',

    getWidth: function (contentWidth, preciseWidth) {
        var me = this,
            dom = me.dom,
            hidden = me.isStyle('display', 'none'),
            rect, width, floating;

        if (hidden) {
            return 0;
        }


        // Gecko will in some cases report an offsetWidth that is actually less than the width of the
        // text contents, because it measures fonts with sub-pixel precision but rounds the calculated
        // value down. Using getBoundingClientRect instead of offsetWidth allows us to get the precise
        // subpixel measurements so we can force them to always be rounded up. See
        // https://bugzilla.mozilla.org/show_bug.cgi?id=458617
        // Rounding up ensures that the width includes the full width of the text contents.
        if (Ext4.supports.BoundingClientRect) {
            rect = dom.getBoundingClientRect();
            // IE9 is the only browser that supports getBoundingClientRect() and
            // uses a filter to rotate the element vertically.  When a filter
            // is used to rotate the element, the getHeight/getWidth functions
            // are not inverted (see setVertical).
            width = (me.vertical && !Ext4.isIE9 && !Ext4.supports.RotatedBoundingClientRect) ?
                (rect.bottom - rect.top) : (rect.right - rect.left);
            width = preciseWidth ? width : Math.ceil(width);
        } else {
            width = dom.offsetWidth;
        }

        // IE9/10 Direct2D dimension rounding bug: https://sencha.jira.com/browse/EXTJSIV-603
        // there is no need make adjustments for this bug when the element is vertically
        // rotated because the width of a vertical element is its rotated height
        if (Ext4.supports.Direct2DBug && !me.vertical) {
            // get the fractional portion of the sub-pixel precision width of the element's text contents
            floating = me.adjustDirect2DDimension('width');
            if (preciseWidth) {
                width += floating;
            }
            // IE9 also measures fonts with sub-pixel precision, but unlike Gecko, instead of rounding the offsetWidth down,
            // it rounds to the nearest integer. This means that in order to ensure that the width includes the full
            // width of the text contents we need to increment the width by 1 only if the fractional portion is less than 0.5
            else if (floating > 0 && floating < 0.5) {
                width++;
            }
        }

        if (contentWidth) {
            width -= me.getBorderWidth("lr") + me.getPadding("lr");
        }

        return (width < 0) ? 0 : width;
    }
});
