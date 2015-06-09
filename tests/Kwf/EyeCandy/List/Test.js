Kwf.onContentReady(function() {
    var lists = Ext2.query('.testItemWrapper');
    lists.forEach(function(list) {
        if (!list.kwfList) {
            list.kwfList = new Kwf.EyeCandy.List({el: list, childSelector: '.testItem'});
            list.kwfList.on('childMouseEnter', function(item) {
                document.getElementById('result').innerHTML += 'childMouseEnter|cnt:'+item.list.items.length+'|idx:'+item.listIndex+'---';
            });
            list.kwfList.on('childMouseLeave', function(item) {
                document.getElementById('result').innerHTML += 'childMouseLeave|cnt:'+item.list.items.length+'|idx:'+item.listIndex+'---';
            });
            list.kwfList.on('childClick', function(item) {
                document.getElementById('result').innerHTML += 'childClick|cnt:'+item.list.items.length+'|idx:'+item.listIndex+'---';
            });
        }
    });
});
