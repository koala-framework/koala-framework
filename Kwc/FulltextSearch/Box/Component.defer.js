//TODO: convert to jquery
/*
var onReady = require('kwf/on-ready-ext2');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');
var historyState = require('kwf/history-state');
var findForm = require('kwf/frontend-form/find-form');
var kwfNs = require('kwf/namespace');

var FulltextSearchBoxComponent = function(el, config) {
    this.el = el;
    this.config = config;

    if (this.config.hideSubmit) {
        this.el.child('.submitWrapper').enableDisplayMode();
        this.el.child('.submitWrapper').hide();
    }

    historyState.on('popstate', function() {
        if (historyState.currentState.searchVisible) {
            if (this.searchMainContent) {
                this.showSearch();
            } else {
                this.searchForm.setValues(historyState.currentState.searchBoxValues);
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

    this.searchForm = findForm(el.dom);

    if (location.pathname == config.searchUrl) {
        //we are already on search page; nothing to do
        this.searchMainContent = Ext2.select('.kwfMainContent').first();
        historyState.currentState.searchBoxValues = this.searchForm.getValues();
        historyState.currentState.searchVisible = true;
        historyState.updateState();
        return;
    }
    this.previousSearchFormValues = this.searchForm.getValues();

    this.searchForm.on('fieldChange', function(f) {
        if (f instanceof Kwf.FrontendForm.Field && f.getValue().length < this.config.minSearchTermLength) {
            return; //minimum length
        }
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

    historyState.currentState.searchVisible = false;
    this.previousMainContent = Ext2.select('.kwfMainContent').first();
};

FulltextSearchBoxComponent.prototype =
{
    doSearch: function()
    {
        if (!this.config.useLiveSearch) {
            return;
        }

        historyState.currentState.searchBoxValues = this.searchForm.getValues();

        if (historyState.currentState.searchVisible) return;

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
                historyState.currentState.searchVisible = true;
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
        this.previousMainContent = Ext2.select('.kwfMainContent').first();
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
        requestParams.url = location.protocol+'//'+location.host+this.config.searchUrl;
        Ext2.Ajax.request({
            params: requestParams,
            url: getKwcRenderUrl(),
            success: function(response, options) {
                this.loadingContent.remove();
                this.searchMainContent = this.el.createChild({
                    cls: 'kwfMainContent',
                    tag: 'div',
                    style: 'width: ' + this.previousMainContent.getStyle('width'),
                    html: response.responseText
                }, this.previousMainContent);
                this.searchMainContent.enableDisplayMode('block');
                onReady.callOnContentReady(this.searchMainContent, {newRender: true});

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



onReady.onRender('.kwcClass', function fulltextSearchBox(el, config) {
    new FulltextSearchBoxComponent(el, config);
}, {
    priority: 0, //call *after* initializing kwcForm to have access to searchForm
    defer: true
});


*/
