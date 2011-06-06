Vps.onContentReady(function()
{
    var els = Ext.query('.vpsRoundBoderBox');
    if (els.length) {
        throw 'Please correct the name "vpsRoundBoderBox" - there is a "R" missing in "Border"';
    }

    var els = Ext.query('.vpsRoundBorderBox');
    Ext.each(els, function(el) {
        var extEl = Ext.get(el);
        if (extEl.child('.vpsMiddleCenterContent')) return;
        var children = el.childNodes;

        // mit elementen direkt arbeiten, sonst gehen zB events die auf den
        // children drauf sind verloren
        var contentEl = document.createElement('div');
        contentEl.className = 'vpsMiddleCenterContent';
        while (children.length) {
            contentEl.appendChild(children[0]);
        };

        // durch obiges appendChild wurden die kinder bereits in den neuen
        // Knoten verschoben und "el" hat keinen inhalt mehr. deshalb können
        // wir direkt anfangen, ein element nach dem andren wieder einzufügen
        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsRoundBorder vpsTopLeft';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsRoundBorder vpsTopCenter';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsRoundBorder vpsTopRight';
        el.appendChild(tmpEl);

        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsRoundBorder vpsMiddleLeft';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsMiddleCenter';
        tmpEl.appendChild(contentEl);
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsRoundBorder vpsMiddleRight';
        el.appendChild(tmpEl);

        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsRoundBorder vpsBottomLeft';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsRoundBorder vpsBottomCenter';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'vpsRoundBorder vpsBottomRight';
        el.appendChild(tmpEl);


        var wd = extEl.getWidth() - extEl.down('.vpsTopLeft').getWidth() - extEl.down('.vpsTopRight').getWidth();
        extEl.down('.vpsTopCenter').setWidth(wd);

        var wd = extEl.getWidth() - extEl.down('.vpsBottomLeft').getWidth() - extEl.down('.vpsBottomRight').getWidth();
        extEl.down('.vpsBottomCenter').setWidth(wd);

        var ht = extEl.getHeight() - extEl.down('.vpsTopLeft').getHeight() - extEl.down('.vpsBottomLeft').getHeight();
        extEl.down('.vpsMiddleLeft').setHeight(ht);
        extEl.down('.vpsMiddleRight').setHeight(ht);

    });
});