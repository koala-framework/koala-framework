Vps.onContentReady(function() {
    Ext.query('.testItemWrapper').forEach(function(list) {
        if (!list.vpsList) {
            list.vpsList = new Vps.EyeCandy.List({
                el: list,
                childSelector: '.testItem',
                plugins: [
                    new Vps.EyeCandy.List.Plugins.StateChanger.HoverOthers({
                        skipItems: 1,
                        state: 'tiny'
                    })
                ]
            });
            list.vpsList.on('childStateChanged', function(item) {
                document.getElementById('result').innerHTML += 'childStateChanged|idx:'+item.listIndex+'|state:'+item.getState()+'---';
            });
        }
    });
});
