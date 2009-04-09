
Vps.onContentReady(function() {

    var searchBoxes = Ext.DomQuery.select('.vpcBoxSearch');
    Ext.each(searchBoxes, function(searchBox) {
        var els = {
            searchField  : Ext.get(Ext.DomQuery.select('.searchField', searchBox)[0]),
            searchResult : Ext.get(Ext.DomQuery.select('.searchResult', searchBox)[0]),
            ajaxUrl      : Ext.get(Ext.DomQuery.select('.ajaxUrl', searchBox)[0]),
            submitParam  : Ext.get(Ext.DomQuery.select('.submitParam', searchBox)[0])
        };
        els.searchResult.alignTo(els.searchField, 'tl-bl');
        els.searchResult.hide();

        els.searchField.on('keyup', function() {
            if (this.searchField.getValue().length < 2) return;
            this.searchField.rel;
            var mgr = this.searchResult.getUpdater();
            var params = {}
            params[this.submitParam.dom.name] = this.submitParam.getValue();
            params[this.searchField.dom.name] = this.searchField.getValue();
            mgr.update({
                url: this.ajaxUrl.getValue(),
                params: params
            });
        }, els, { buffer: 250 });

        // ein- / ausblenden der result-box
        var mouseover = false;
        var focused = false;

        els.searchResult.on('mouseover', function() {
            mouseover = true;
        });
        els.searchResult.on('mouseout', function() {
            mouseover = false;
        });

        els.searchField.on('focus', function() {
            els.searchResult.show();
            focused = true;
        });
        els.searchField.on('blur', function() {
            if (!mouseover) els.searchResult.hide();
            focused = false;
        });
        Ext.get(document.body).on('click', function() {
            if (!mouseover && !focused) els.searchResult.hide();
        });
    });
});
