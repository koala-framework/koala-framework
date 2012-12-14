Kwf.onElementReady('.kwcFulltextSearchBox', function(el, config) {
    new Kwc.FulltextSearch.Box.Component(el, config);
});

Ext.ns('Kwc.FulltextSearch.Box');
Kwc.FulltextSearch.Box.Component = function(el, config) {
    this.el = el;
    this.config = config;

    if (location.protocol+'//'+location.host+location.pathname == config.searchUrl) {
        //we are already on search page; nothing to do
        return;
    }

    this.searchForm = Kwc.Form.findForm(el);

    this.searchForm.on('fieldChange', function(f) {
        this.loadAndShowSearch();
    }, this, { buffer: 500 });

    this.searchForm.on('beforeSubmit', function(f) {
        this.loadAndShowSearch();
        return false;
    }, this);

    Kwf.Utils.HistoryState.currentState.searchVisible = false;
    Kwf.Utils.HistoryState.on('popstate', function() {
        if (Kwf.Utils.HistoryState.currentState.searchVisible) {
            this.showSearch();
        } else {
            this.hideSearch();
        }
    }, this);
};

Kwc.FulltextSearch.Box.Component.prototype =
{
    loadAndShowSearch: function()
    {
        if (Kwf.Utils.HistoryState.currentState.searchVisible) return;

        if (this.previousSearchFormValues == Ext.encode(this.searchForm.getValues())) {
            //don't show search if value didn't change
            return;
        }

        if (this.searchMainContent) {
            //search already loaded, just show it
            this.showSearch();
            return;
        }

        var url = '/kwf/util/kwc/render';
        if (Kwf.Debug.rootFilename) url = Kwf.Debug.rootFilename + url;

        var params = this.searchForm.getValuesIncludingPost();
        params.url = this.config.searchUrl;
        Ext.Ajax.request({
            params: params,
            url: url,
            success: function(response, options) {
                this.previousMainContent = Ext.select('.kwfMainContent').first();
                this.previousMainContent.enableDisplayMode('block');
                this.previousMainContent.hide();
                this.searchMainContent = this.el.createChild({
                    cls: 'kwfMainContent',
                    tag: 'div',
                    style: 'width: ' + this.previousMainContent.getStyle('width'),
                    html: response.responseText
                }, this.previousMainContent);
                this.searchMainContent.enableDisplayMode('block');
                Kwf.callOnContentReady(this.searchMainContent);

                Kwf.Utils.HistoryState.currentState.searchVisible = true;
                var ajaxViewEl = this.searchMainContent.child('.kwcDirectoriesListViewAjax');
                if (ajaxViewEl && ajaxViewEl.kwcViewAjax) {
                    ajaxViewEl.kwcViewAjax.pushSearchFormHistoryState();
                }
            },
            scope: this
        });
    },
    hideSearch: function() {
        if (this.searchMainContent) {
            this.searchMainContent.hide();
            this.previousMainContent.show();
            this.previousSearchFormValues = Ext.encode(this.searchForm.getValues());
        }
    },
    showSearch: function() {
        this.searchMainContent.show();
        this.previousMainContent.hide();
    }
};
