var onReady = require('kwf/commonjs/on-ready');

onReady.onContentReady(function() {
    Ext2.query('.testItemWrapper').forEach(function(list) {
        if (!list.kwfList) {
            list.kwfList = new Kwf.EyeCandy.List({
                el: list,
                childSelector: '.testItem',
                plugins: [
                    new Kwf.EyeCandy.List.Plugins.StateChanger.Hover({
                        state: 'large'
                    })
                ]
            });
            list.kwfList.on('childStateChanged', function(item) {
                document.getElementById('result').innerHTML += 'childStateChanged|idx:'+item.listIndex+'|state:'+item.getState()+'---';
            });
        }
    });
});
