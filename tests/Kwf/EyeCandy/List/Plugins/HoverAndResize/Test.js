Vps.onContentReady(function() {
    Ext.query('.testItemWrapper').forEach(function(list) {
        if (!list.vpsList) {
            list.vpsList = new Vps.EyeCandy.List({
                el: list,
                childSelector: '.testItem',
                defaultState: 'normal',
                plugins: [
                    new Vps.EyeCandy.List.Plugins.StateChanger.Hover({
                        state: 'large'
                    }),
                    new Vps.EyeCandy.List.Plugins.StateListener.Resize({
                        sizes: {
                            normal: { width: 100, height: 100 },
                            large: { width: 200, height: 200 }
                        }
                    })
                ]
            });
        }
    });
});
