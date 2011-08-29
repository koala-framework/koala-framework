Vps.onContentReady(function() {
    var lists = Ext.query('.testItemWrapper');
    lists.forEach(function(list) {
        if (!list.vpsList) {
            list.vpsList = new Vps.EyeCandy.List({el: list, childSelector: '.testItem'});
            list.vpsList.on('childMouseEnter', function(item) {
                document.getElementById('result').innerHTML += 'childMouseEnter|cnt:'+item.list.items.length+'|idx:'+item.listIndex+'---';
            });
            list.vpsList.on('childMouseLeave', function(item) {
                document.getElementById('result').innerHTML += 'childMouseLeave|cnt:'+item.list.items.length+'|idx:'+item.listIndex+'---';
            });
            list.vpsList.on('childClick', function(item) {
                document.getElementById('result').innerHTML += 'childClick|cnt:'+item.list.items.length+'|idx:'+item.listIndex+'---';
            });
        }
    });
});
