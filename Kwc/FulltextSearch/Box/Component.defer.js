/*
var $ = require('jQuery');
var _ = require('underscore');
var onReady = require('kwf/on-ready');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');
var historyState = require('kwf/history-state');
var findForm = require('kwf/frontend-form/find-form');
var Field = require('kwf/frontend-form/field/field');

var FulltextSearchBoxComponent = function(el, config) {
    this.el = el;
    this.config = config;

    if (this.config.hideSubmit) {
        this.el.find('.kwfUp-submitWrapper').hide();
    }


    if (this.config.useLiveSearch) {
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
                    if (!this.previousMainContent) {
                        //we didn't load the search using ajax, so we don't have a main content to show -> reload
                        location.href = location.href;
                    } else {
                        this.hideSearch();
                    }
                }
            }
        }, this);
    }

    this.searchForm = findForm(el.find('.kwfUp-kwcForm'));

    if (location.pathname === config.searchUrl) {
        //we are already on search page; nothing to do
        this.searchMainContent = $('.kwfUp-kwfMainContent').first();
        historyState.currentState.searchBoxValues = this.searchForm.getValues();
        historyState.currentState.searchVisible = true;
        historyState.updateState();
        return;
    }
    this.previousSearchFormValues = this.searchForm.getValues();

    this.searchForm.on('fieldChange', _.debounce(function(ev, f) {
        if (f instanceof Field && f.getValue().length < this.config.minSearchTermLength) {
            return; //minimum length
        }
        this.doSearch();
    }, 500), this);

    this.searchForm.on('beforeSubmit', function(f) {
        this.doSearch();
        if (!this.config.useLiveSearch) { // fires event to submit value
            return true;
        } else {
            return false;
        }
    }, this);

    historyState.currentState.searchVisible = false;
    this.previousMainContent = $('.kwfUp-kwfMainContent').first();

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
            if (values[i] !== this.previousSearchFormValues[i]) {
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
                var ajaxViewEl = this.searchMainContent.find('.kwfUp-kwcDirectoriesListViewAjax');
                if (ajaxViewEl && ajaxViewEl.kwcViewAjax) {
                    ajaxViewEl.kwcViewAjax.pushSearchFormHistoryState();
                }
            },
            scope: this
        });
    },

    loadSearch: function(params)
    {
        this.previousMainContent = $('.kwfUp-kwfMainContent').first();
        this.previousMainContent.hide();

        this.loadingContent = $('<main>', {
            class: 'kwfUp-kwfMainContent kwfUp-loadingContent',
            style: 'width: ' + this.previousMainContent.width(),
            html: '<div class="kwfUp-loading"></div>'
        }).insertBefore(this.previousMainContent);

        var requestParams = this.searchForm.getValuesIncludingPost();
        requestParams.url = location.protocol+'//'+location.host+this.config.searchUrl;

        $.ajax({
            url: getKwcRenderUrl(),
            data: requestParams
        }).then((function (data) {
            this.loadingContent.remove();
            this.searchMainContent = $('<main>',{
                class: 'kwfUp-kwfMainContent',
                style: 'width: ' + this.previousMainContent.width(),
                html: data
            }).insertBefore(this.previousMainContent);
            onReady.callOnContentReady(this.searchMainContent, {newRender: true});

            if (params && params.success) params.success.call(params.scope || this);
        }).bind(this));
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
