var onReady = require('kwf/on-ready');

onReady.onContentReady(function()
{
    var els = Ext2.query('.kwfRoundBoderBox');
    if (els.length) {
        throw 'Please correct the name "kwfRoundBoderBox" - there is a "R" missing in "Border"';
    }

    var setSizes = function(extEl) {

        var wd = extEl.getWidth() - extEl.down('.kwfTopLeft').getWidth() - extEl.down('.kwfTopRight').getWidth();
        extEl.down('.kwfTopCenter').setWidth(wd);

        var wd = extEl.getWidth() - extEl.down('.kwfBottomLeft').getWidth() - extEl.down('.kwfBottomRight').getWidth();
        extEl.down('.kwfBottomCenter').setWidth(wd);

        var ht = extEl.getHeight() - extEl.down('.kwfTopLeft').getHeight() - extEl.down('.kwfBottomLeft').getHeight();
        extEl.down('.kwfMiddleLeft').setHeight(ht);
        extEl.down('.kwfMiddleRight').setHeight(ht);

    };

    var els = Ext2.query('.kwfRoundBorderBox');
    Ext2.each(els, function(el) {
        var extEl = Ext2.get(el);
        if (extEl.child('.kwfMiddleCenterContent')) {
            setSizes(extEl);
            return;
        }
        var children = el.childNodes;

        // mit elementen direkt arbeiten, sonst gehen zB events die auf den
        // children drauf sind verloren
        var contentEl = document.createElement('div');
        contentEl.className = 'kwfMiddleCenterContent';
        while (children.length) {
            contentEl.appendChild(children[0]);
        };

        // durch obiges appendChild wurden die kinder bereits in den neuen
        // Knoten verschoben und "el" hat keinen inhalt mehr. deshalb können
        // wir direkt anfangen, ein element nach dem andren wieder einzufügen
        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfRoundBorder kwfTopLeft';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfRoundBorder kwfTopCenter';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfRoundBorder kwfTopRight';
        el.appendChild(tmpEl);

        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfRoundBorder kwfMiddleLeft';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfMiddleCenter';
        tmpEl.appendChild(contentEl);
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfRoundBorder kwfMiddleRight';
        el.appendChild(tmpEl);

        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfRoundBorder kwfBottomLeft';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfRoundBorder kwfBottomCenter';
        el.appendChild(tmpEl);
        var tmpEl = document.createElement('div');
        tmpEl.className = 'kwfRoundBorder kwfBottomRight';
        el.appendChild(tmpEl);

        setSizes(extEl);
    });
});
