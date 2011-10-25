Kwf.onContentReady(function() {
    Ext.query('.testItemWrapper').forEach(function(list) {
        if (!list.kwfList) {
            list.kwfList = new Kwf.EyeCandy.List({
                el: list,
                childSelector: '.testItem',
                plugins: [
                    new Kwf.EyeCandy.List.Plugins.Carousel({
                        numberShown: 3
                    })
                ]
            });
        }
    });
});
