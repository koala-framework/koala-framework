Vps.onContentReady(function() {
    Ext.query('.testItemWrapper').forEach(function(list) {
        if (!list.vpsList) {
            list.vpsList = new Vps.EyeCandy.List({
                el: list,
                childSelector: '.testItem',
                plugins: [
                    new Vps.EyeCandy.List.Plugins.Carousel({
                        numberShown: 3
                    })
                ]
            });
        }
    });
});
