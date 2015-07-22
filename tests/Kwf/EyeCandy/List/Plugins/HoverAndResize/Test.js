var onReady = require('kwf/on-ready');

onReady.onContentReady(function() {
    Ext2.query('.testItemWrapper').forEach(function(list) {
        if (!list.kwfList) {
            list.kwfList = new Kwf.EyeCandy.List({
                el: list,
                childSelector: '.testItem',
                defaultState: 'normal',
                plugins: [
                    new Kwf.EyeCandy.List.Plugins.StateChanger.Hover({
                        state: 'large'
                    }),
                    new Kwf.EyeCandy.List.Plugins.StateListener.Resize({
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
