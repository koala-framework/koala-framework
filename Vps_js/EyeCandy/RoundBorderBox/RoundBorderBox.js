Vps.onContentReady(function()
{
    var els = Ext.query('.vpsRoundBoderBox');
    Ext.each(els, function(el) {
        el.innerHTML = '<div class="vpsRoundBorder vpsTopLeft"></div><div class="vpsRoundBorder vpsTopCenter"></div><div class="vpsRoundBorder vpsTopRight"></div>'
            +'<div class="vpsRoundBorder vpsMiddleLeft"></div><div class="vpsMiddleCenter"><div class="vpsMiddleCenterContent">'+el.innerHTML+'</div></div><div class="vpsRoundBorder vpsMiddleRight"></div>'
            +'<div class="vpsRoundBorder vpsBottomLeft"></div><div class="vpsRoundBorder vpsBottomCenter"></div><div class="vpsRoundBorder vpsBottomRight"></div>';

        var extEl = Ext.get(el);

        var wd = extEl.getWidth() - extEl.down('.vpsTopLeft').getWidth() - extEl.down('.vpsTopRight').getWidth();
        extEl.down('.vpsTopCenter').setWidth(wd);

        var wd = extEl.getWidth() - extEl.down('.vpsBottomLeft').getWidth() - extEl.down('.vpsBottomRight').getWidth();
        extEl.down('.vpsBottomCenter').setWidth(wd);

        var ht = extEl.getHeight() - extEl.down('.vpsTopLeft').getHeight() - extEl.down('.vpsBottomLeft').getHeight();
        extEl.down('.vpsMiddleLeft').setHeight(ht);
        extEl.down('.vpsMiddleRight').setHeight(ht);
    });
});