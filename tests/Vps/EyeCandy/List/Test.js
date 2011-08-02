Vps.onContentReady(function() {
    var lists = Ext.query('.testItemWrapper');
    lists.forEach(function(list) {
        if (!list.vpsList) {
            list.vpsList = new Vps.EyeCandy.List.Abstract(list, '.testItem');
            list.vpsList.on('childMouseEnter', function(list, item) {
                document.getElementById('result').innerHTML += 'childMouseEnter|cnt:'+list.items.length+'|idx:'+item.vpsListIndex+'---';
            });
            list.vpsList.on('childMouseLeave', function(list, item) {
                document.getElementById('result').innerHTML += 'childMouseLeave|cnt:'+list.items.length+'|idx:'+item.vpsListIndex+'---';
            });
            list.vpsList.on('childClick', function(list, item) {
                document.getElementById('result').innerHTML += 'childClick|cnt:'+list.items.length+'|idx:'+item.vpsListIndex+'---';
            });
        }
    });
});
