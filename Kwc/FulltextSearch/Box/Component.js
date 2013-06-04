Kwf.onElementReady('.kwcFulltextSearchBox', function(el, config) {
    new Kwc.FulltextSearch.Box.Component(el, config);
}, this, {
    priority: 0 //call *after* initializing kwcForm to have access to searchForm
});

Ext.ns('Kwc.FulltextSearch.Box');
Kwc.FulltextSearch.Box.Component = function(el, config) {
    this.el = el;
    this.config = config;

    if (this.config.hideSubmit) {
        this.el.child('.submitWrapper').enableDisplayMode();
        this.el.child('.submitWrapper').hide();
    }

    Kwf.Utils.HistoryState.on('popstate', function() {
        if (Kwf.Utils.HistoryState.currentState.searchVisible) {
            if (this.searchMainContent) {
                this.showSearch();
            } else {
                this.searchForm.setValues(Kwf.Utils.HistoryState.currentState.searchBoxValues);
                this.loadSearch();
            }
        } else {
            if (!this.previousMainContent) {
                //we didn't load the search using ajax, so we don't have a main content to show -> reload
                location.href = location.href;
            } else {
                this.hideSearch();
            }
        }
    }, this);

    this.searchForm = Kwc.Form.findForm(el);

    if (location.protocol+'//'+location.host+location.pathname == config.searchUrl) {
        //we are already on search page; nothing to do
        this.searchMainContent = Ext.select('.kwfMainContent').first();
        Kwf.Utils.HistoryState.currentState.searchBoxValues = this.searchForm.getValues();
        Kwf.Utils.HistoryState.currentState.searchVisible = true;
        Kwf.Utils.HistoryState.updateState();
        return;
    }
    this.previousSearchFormValues = this.searchForm.getValues();

    this.searchForm.on('fieldChange', function(f) {
        if (f instanceof Kwf.FrontendForm.Field && f.getValue().length < 3) return; //minimum length
        this.doSearch();
    }, this, { buffer: 500 });

    this.searchForm.on('beforeSubmit', function(f) {
        this.doSearch();
        if (!this.config.useLiveSearch) { // fires event to submit value
            return true;
        } else {
            return false;
        }
    }, this);

    Kwf.Utils.HistoryState.currentState.searchVisible = false;
    this.previousMainContent = Ext.select('.kwfMainContent').first();
};

Kwc.FulltextSearch.Box.Component.prototype =
{
    doSearch: function()
    {
        if (!this.config.useLiveSearch) {
            return;
        }

        Kwf.Utils.HistoryState.currentState.searchBoxValues = this.searchForm.getValues();

        if (Kwf.Utils.HistoryState.currentState.searchVisible) return;

        var values = this.searchForm.getValues();
        var diffFound = false;
        for(var i in values) {
            if (values[i] != this.previousSearchFormValues[i]) {
                diffFound = true;
                break;
            }
        }

        if (!diffFound) {
            //don't show search if value didn't change
            return;
        }

        if (this.searchMainContent) {
            //search already loaded, just show it
            this.showSearch();
            return;
        }


        this.loadSearch({
            success: function() {
                Kwf.Utils.HistoryState.currentState.searchVisible = true;
                var ajaxViewEl = this.searchMainContent.child('.kwcDirectoriesListViewAjax');
                if (ajaxViewEl && ajaxViewEl.kwcViewAjax) {
                    ajaxViewEl.kwcViewAjax.pushSearchFormHistoryState();
                }
            },
            scope: this
        });
    },

    loadSearch: function(params)
    {
        this.previousMainContent = Ext.select('.kwfMainContent').first();
        this.previousMainContent.enableDisplayMode('block');
        this.previousMainContent.hide();
        this.loadingContent = this.el.createChild({
            cls: 'kwfMainContent loadingContent',
            tag: 'div',
            style: 'width: ' + this.previousMainContent.getStyle('width'),
            html: '<div class="loading"></div>'
        }, this.previousMainContent);
        this.loadingContent.enableDisplayMode('block');

        var requestParams = this.searchForm.getValuesIncludingPost();
        requestParams.url = this.config.searchUrl;
        Ext.Ajax.request({
            params: requestParams,
            url: Kwf.getKwcRenderUrl(),
            success: function(response, options) {
                this.loadingContent.remove();
                this.searchMainContent = this.el.createChild({
                    cls: 'kwfMainContent',
                    tag: 'div',
                    style: 'width: ' + this.previousMainContent.getStyle('width'),
                    html: response.responseText
                }, this.previousMainContent);
                this.searchMainContent.enableDisplayMode('block');
                Kwf.callOnContentReady(this.searchMainContent);

                if (params && params.success) params.success.call(params.scope || this);
            },
            scope: this
        });
    },

    hideSearch: function() {
        if (this.searchMainContent) {
            this.searchMainContent.hide();
            this.previousMainContent.show();
            this.previousSearchFormValues = this.searchForm.getValues();
        }
    },
    showSearch: function() {
        this.searchMainContent.show();
        if (this.previousMainContent) this.previousMainContent.hide();
    }
};
