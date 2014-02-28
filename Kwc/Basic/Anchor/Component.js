Kwf.onContentReady(function(el){
    if(window.location.hash) {
        var target = Ext.get(window.location.hash.replace('#', ''));
        if(target) {
            window.scrollTo(0, target.getTop());
        }
    }
}, this, {priority: 50});