
Kwf.onContentReady(function() {
    var searchBoxes = Ext2.DomQuery.select('.kwcBoxSearch');
    Ext2.each(searchBoxes, function(searchBox) {
        var els = {
            searchField  : Ext2.get(Ext2.DomQuery.select('.searchField', searchBox)[0]),
            searchResult : Ext2.get(Ext2.DomQuery.select('.searchResult', searchBox)[0]),
            ajaxUrl      : Ext2.get(Ext2.DomQuery.select('.ajaxUrl', searchBox)[0]),
            submitParam  : Ext2.get(Ext2.DomQuery.select('.submitParam', searchBox)[0]),
            searchSettings: Ext2.get(Ext2.DomQuery.select('.searchSettings', searchBox)[0])
        };
        if (els.searchSettings) {
            var searchSettings = Ext2.decode(els.searchSettings.getValue());
        }
        var aligning = 'tl-bl';
        if (searchSettings && searchSettings.searchResultBoxAlign) {
            aligning = searchSettings.searchResultBoxAlign;
        }
        els.searchResult.alignTo(els.searchField, aligning);
        els.searchResult.hide();

        els.searchField.on('keyup', function() {
            if (this.searchField.getValue().length < 2) return;
            this.searchField.rel;
            var mgr = this.searchResult.getUpdater();
            mgr.abort();
            var params = {};
            params[this.submitParam.dom.name] = this.submitParam.getValue();
            params[this.submitParam.dom.name+'-post'] = 'post';
            params[this.searchField.dom.name] = this.searchField.getValue();
            mgr.update({
                url: this.ajaxUrl.getValue(),
                params: params,
                callback: function(el) {
                    Kwf.callOnContentReady(el.dom, {newRender: true});
                    el.show();
                }
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
            if (searchSettings && searchSettings.searchResultBoxFade) {
                els.searchResult.fadeIn({ duration: .35, useDisplay: true });
            } else {
                els.searchResult.show();
            }
            focused = true;
        });

        els.searchField.on('blur', function() {
            if (!mouseover) {
                if (searchSettings && searchSettings.searchResultBoxFade) {
                    if (els.searchResult.isVisible()) {
                        els.searchResult.fadeOut({duration: .35, useDisplay: true});
                    }
                } else {
                    els.searchResult.hide();
                }
            }
            focused = false;
        });
        Ext2.get(document.body).on('click', function() {
            if (!mouseover && !focused) {
                if (searchSettings && searchSettings.searchResultBoxFade) {
                    if (els.searchResult.isVisible()) {
                        els.searchResult.fadeOut({duration: .35, useDisplay: true});
                    }
                } else {
                    els.searchResult.hide();
                }
            }
        });
    });
});
