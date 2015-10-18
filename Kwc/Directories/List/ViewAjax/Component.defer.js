//TODO: convert to jquery
/*
var onReady = require('kwf/on-ready');
var $ = require('jQuery');
var historyState = require('kwf/history-state');
var getKwcRenderUrl = require('kwf/get-kwc-render-url');
var statistics = require('kwf/statistics');
var formRegistry = require('kwf/frontend-form/form-registry');

(function() {

var uniqueIdCnt = 0;
function getUniqueIdForFilterLink(el) {
    if (!el.id) {
        uniqueIdCnt++;
        el.id = 'filterLink'+uniqueIdCnt;
    }
    return el.id;
}

$(document).on('click', 'a', function(event) {
    var a = $(event.currentTarget);

    if (a.data('kwfViewAjaxInitDone')) return; //ignore back link

    if (a.data('kwc-view-ajax-filter')) {
        var config = a.data('kwc-view-ajax-filter');

        var view = Kwc.Directories.List.ViewAjax.byDirectoryViewComponentId[config.viewComponentId];
        if (!view) return;
        view.loadView({
            filterComponentId: config.componentId
        });
        if (view._getState().viewFilter != config.componentId) {
            view._getState().viewFilter = config.componentId;
            view._getState().menuLinkId = getUniqueIdForFilterLink(this);
            historyState.pushState(document.title, this.href);
        }

        $('a[kwc-view-ajax-filter]').each(function() {
            var config = $(this).data('kwc-view-ajax-filter');
            if (config.viewComponentId == view.componentId) {
                $(this).removeClass('current');
            }
        });
        $(this).addClass('current');

        event.preventDefault();

    }

});

onReady.onRender('.kwcClass', function viewAjax(el, config) {
    config.el = el.find('.viewContainer')[0];
    el.find('.kwcDirectoriesListViewAjaxPaging').remove(); //remove paging, we will do endless scrolling instead
    el[0].kwcViewAjax = new Kwc.Directories.List.ViewAjax(config);

    var linkToTop = $('<div class="linkToTop"></div>');
    el.append(linkToTop);
    linkToTop.click(function() {
        window.scrollTo(0, 0);
    });

    $(window).on('scroll', function() {
        var scrollHeight = $(window).scrollTop();
        if (scrollHeight >= 1700) {
            el.addClass('scrolledDown'); //will display linkToTop
        } else {
            el.removeClass('scrolledDown');
        }
    });

}, {
    priority: 0, //call *after* initializing kwcForm to have access to searchForm
    defer: true
});


//if there is no viewAjax that can handle the changed state reload current page
//this can happen if a reload has been between state navigations
historyState.on('popstate', function() {
    if (historyState.currentState.viewAjax) {
        var found = false;
        for (var componentId in historyState.currentState.viewAjax) {
            if (Kwc.Directories.List.ViewAjax.byComponentId[componentId]) {
                found = true;
            }
        }
        if (!found) {
            location.href = location.href;
        }
    }
}, this);


Kwf.namespace('Kwc.Directories.List');
Kwc.Directories.List.ViewAjax = function(config) {
    $.extend(this, config);
    this.init();
};

Kwc.Directories.List.ViewAjax.byComponentId = {};
Kwc.Directories.List.ViewAjax.byDirectoryViewComponentId = {};

Kwc.Directories.List.ViewAjax.prototype = {

    controllerUrl: null,
    loadMoreBufferPx: 700,
    initialPageSize: null,

    addHistoryEntryTimer: 0,

    init: function() {
        this.$el = $(this.el);
        Kwc.Directories.List.ViewAjax.byComponentId[this.componentId] = this;
        if (this.directoryViewComponentId) {
            Kwc.Directories.List.ViewAjax.byDirectoryViewComponentId[this.directoryViewComponentId] = this;
        }
        this.baseParams = {
            componentId: this.componentId,
            kwfSessionToken: Kwf.sessionToken
        };

        if (this.searchFormComponentId) {
            this.searchForm = formRegistry.getFormByComponentId(this.searchFormComponentId);
        }

        if (this.searchForm) {

            $.extend(this.baseParams, this.searchForm.getValues());

            this.searchForm.on('fieldChange', function(f) {
                if (f instanceof Kwf.FrontendForm.TextField && f.getValue().length < 3) return; //minimum length

                var values = this.searchForm.getValues();
                var diffFound = false;
                for (var i in values) {
                    if (values[i] != this.baseParams[i]) {
                        diffFound = true;
                        break;
                    }
                }
                if (diffFound) {
                    $.extend(this.baseParams, values);
                    this.load();
                    clearTimeout(this.addHistoryEntryTimer);
                    this.addHistoryEntryTimer = setTimeout(this.pushSearchFormHistoryState.bind(this), 2000);
                }
            }, this, { buffer: 500 });

            this.searchForm.on('beforeSubmit', function(f) {
                var values = this.searchForm.getValues();
                $.extend(this.baseParams, values);
                this.load();
                this.pushSearchFormHistoryState();
                return false;
            }, this);
        }

        if (!historyState.currentState.viewAjax) historyState.currentState.viewAjax = {};
        historyState.currentState.viewAjax[this.componentId] = {};

        if (this.searchForm) {
            this._getState().searchFormValues = this.searchForm.getValues();
        }

        //set menuLinkId to link that is current, be be able to set current again
        $('a[kwc-view-ajax-filter]').each((function(index, linkEl) {
            var config = $(linkEl).data('kwc-view-ajax-filter');
            if (config.viewComponentId == this.componentId) {
                if ($(linkEl).hasClass('current')) {
                    this._getState().menuLinkId = getUniqueIdForFilterLink(linkEl);
                }
            }
        }).bind(this));

        if (this.filterComponentId) {
            this._getState().viewFilter = this.filterComponentId;
            this.baseParams.filterComponentId = this.filterComponentId;
            delete this.filterComponentId;
        }

        historyState.updateState();


        historyState.on('popstate', function() {
            if (!this._getState()) return;
            if (this.searchForm) {
                this.searchForm.setValues(this._getState().searchFormValues);
            }
            if (this._getState().viewDetail) {
                this.showDetail(this._getState().viewDetail);
            } else if (this._getState().viewFilter) {
                this.loadView({
                    filterComponentId: this._getState().viewFilter
                });
            } else {
                this.loadView({
                    filterComponentId: null
                });
            }
            //commented out because it should work without manual scrolling (browser should restore scroll position)
            //but if it fails in real-world we can re-enable it
            //if (!this.visibleDetail && this._lastViewScrollPosition) {
            //    $(window).scrollTop(this._lastViewScrollPosition);
            //}
            if (this._getState().menuLinkId) {
                $('a[kwc-view-ajax-filter]').each((function(i, el) {
                    var config = $(el).data('kwc-view-ajax-filter');
                    if (config.viewComponentId == this.componentId) {
                        $(el).removeClass('current');
                    }
                }).bind(this));

                var el = $('#'+this._getState().menuLinkId);
                if (el) {
                    el.addClass('current');
                }
            }
        }, this);

        if (this.loadMoreBufferPx) {
            var timer = 0;
            $(window).scroll((function() {
                clearTimeout(timer);
                timer = setTimeout((function() {
                    var height = this.$el.offset().top+this.$el.height();
                    height -= $(window).height();
                    height = height - $(window).scrollTop();
                    if (height < this.loadMoreBufferPx) {
                        this.loadMore();
                    }
                }).bind(this), 50);
            }).bind(this));
        }

        if (this.loadDetailAjax) {
            this.$el.click((function(ev) {
                var a = $(ev.target).closest('a');
                if (!a.length) return;
                if (!a.data('kwc-detail')) return;
                var config = a.data('kwc-detail');
                if (!config.directoryComponentId) return;
                if (this.directoryComponentId) {
                    if (config.directoryComponentId != this.directoryComponentId) return;
                } else {
                    if (config.directoryComponentClass != this.directoryComponentClass) return;
                }

                ev.preventDefault();
                //more... Link clicked
                //this._lastViewScrollPosition = $(window).scrollTop();
                this.showDetail(a.attr('href'));

            }).bind(this));
        }

        this.kwfMainContent = this.$el.closest('.kwfMainContent');
    },

    showView: function() {
        if (!this.visibleDetail) return;
        this.visibleDetail = null;

        this.hideDetail();

        this.kwfMainContent.show();
    },

    pushSearchFormHistoryState: function()
    {
        clearTimeout(this.addHistoryEntryTimer);
        this.addHistoryEntryTimer = 0;
        if (this.visibleDetail) return;
        this._getState().searchFormValues = this.searchForm.getValues();
        var url = location.protocol+'//'+location.host+this.viewUrl+'?'+$.param(this.searchForm.getValuesIncludingPost());
        historyState.pushState(document.title, url);
    },

    _getState: function()
    {
        return historyState.currentState.viewAjax[this.componentId];
    },

    loadView: function(params)
    {
        if (!params) params = {};
        if (this.searchForm) {
            $.extend(params, this.searchForm.getValues());
        }
        var diffFound = false;
        for (var i in params) {
            if (params[i] != this.baseParams[i]) {
                diffFound = true;
                break;
            }
        }
        if (diffFound) {
            for (var i in params) this.baseParams[i] = params[i];
            this.load();
        }
        this.showView();
    },

    loadMore: function()
    {
        if (this.$el.find('.kwfViewAjaxItem').length<this.initialPageSize || this.loadingMore || this.visibleDetail) return;

        this.loadingMore = true;
        this.$el.addClass('loadingMore');
        var params = $.extend({
            start: this.$el.find('.kwfViewAjaxItem').length,
            limit: 10
        }, this.baseParams);
        $.ajax({
            data: params,
            url: this.controllerUrl+'/json-data',
            dataType: 'json'
        }).done((function(data) {
            this.$el.removeClass('loadingMore');
            if (data.rows.length) { //wenn nichts geladen sind wir bereits am ende
                this.loadingMore = false;
            }
            for (var i=0; i<data.rows.length; i++) {
                this.$el.append("<div class=\"kwfViewAjaxItem\">"+data.rows[i].content+"</div>");
            }
            onReady.callOnContentReady(this.$el, { action: 'render' });
        }).bind(this));
    },

    load : function(params) {
        if (!params) params = {};
        if (!params.start) {
            params.start = 0;
        }
        if (!params.limit) {
            params.limit = this.initialPageSize;
        }
        $.extend(params, this.baseParams);

        this.$el.addClass('loading');
        $.ajax({
            data: params,
            url: this.controllerUrl+'/json-data',
            dataType: 'json'
        }).done((function(data) {
            this.$el.removeClass('loading');
            var html = '';
            if (!data.rows.length) {
                html = '<span class="noEntriesFound">'+this.placeholder.noEntriesFound+'</span>';
            } else {
                for (var i=0; i<data.rows.length; i++) {
                    html += "<div class=\"kwfViewAjaxItem\">"+data.rows[i].content+"</div>";
                }
            }
            this.$el.html(html);
            onReady.callOnContentReady(this.$el, { action: 'render' });
        }).bind(this));
    },

    hideDetail: function()
    {
        if (this.detailEl) {
            this.detailEl.hide();
            onReady.callOnContentReady(this.detailEl, {action: 'hide'});
            this.detailEl.remove();
            this.detailEl = null;
        }
    },

    showDetail: function(href)
    {
        if (this.visibleDetail == href) return;
        this.visibleDetail = href;

        if (this._getState().viewDetail != href) {
            this._getState().viewDetail = href;
            historyState.pushState(document.title, href);
        }
        this.hideDetail();

        var classNames = this.$el.closest('.kwcDirectoriesListViewAjax').attr('class');

        this.kwfMainContent.hide();

            //style: 'width: ' + this.kwfMainContent.getStyle('width'),
        this.detailEl = $('<main class="kwfMainContent loadingContent '+classNames+'""><div class="loading"></div></main>');
        this.kwfMainContent.after(this.detailEl);

        $.ajax({
            url: getKwcRenderUrl(),
            data: { url: 'http://'+location.host+href },
            dataType: 'html'
        }).done((function(data) {
            if (!this.detailEl) return;
            this.detailEl.removeClass('loadingContent '+classNames);
            this.detailEl.html(data);
            statistics.count(href);

            var directoryUrl = href.match(/(.*)\/[^/]+/)[1];
            this.detailEl.find('a').each((function(index, el) {
                if ($(el).attr('href') == directoryUrl) {
                    $(el).data('kwfViewAjaxInitDone', true);
                    $(el).click((function(ev) {
                        if (history.length > 1) {
                            if (Kwf.Utils.HistoryState.entries > 0 || document.referrer.indexOf(document.domain) >= 0) {
                                ev.preventDefault();
                                history.back(); //keeps scroll position
                            }
                        } else {
                            this.showView();
                        }
                    }).bind(this));
                }
            }).bind(this));

            onReady.callOnContentReady(this.detailEl, {newRender: true});
            $(window).scrollTop(0);

        }).bind(this));
    }

};

})();
*/
