Kwf.onContentReady(function() {
    if (Ext2.isIE6) return;

    var getElementText = function(element) {
        if(typeof element == "string")
            return element;
        else if(typeof element == "undefined")
            return element;

        var text = "";
        var kids = element.childNodes;
        for(var i=0;i<kids.length;i++)
        {
            if (kids[i].nodeName.toLowerCase()=='br') {
                text += '<br />';
            }
            if(kids[i].nodeType == 1) {
                text += getElementText(kids[i]);
            } else if(kids[i].nodeType == 3) {
                text += kids[i].nodeValue;
            }
        }
        return text;
    };
    var selectors = Kwf.Headline.selectors;
    selectors.each(function(selector) {
        var elements = Ext2.DomQuery.select(selector);
        elements.each(function(element) {
            var element = new Ext2.Element(element);
            var text = getElementText(element.dom);
            element.dom.innerHTML = '<img src="/media/headline?selector='
                                        +encodeURIComponent(selector)+
                                    '&text='+encodeURIComponent(text)+
                                    '&assetsType='+encodeURIComponent(Kwf.Headline.assetsType)+
                                    '" />';
        });
    });
});
