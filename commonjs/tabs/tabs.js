var onReady = require('kwf/on-ready');
var statistics = require('kwf/statistics');
var oneTransitionEnd = require('kwf/element/one-transition-end');
var $ = require('jQuery');

/***
 * Tabs class used to implement tab functionality on a Tabs_Component
 * @param el anchor element
 * @param config configuration object
 *      The configuration object can contain the bemClass property, which is used to derive all BEM class names.
 *      If no bemClass is provided, all classes have to be provided manually (i.e. when using the legacy tab component,
 *      or when using a custom template/css classes)
 * @constructor
 */
var Tabs = function(el, config)
{
    this.el = el;
    this.config = config || {};
    if (this.config.bemClass) {
        var autoConfigs = {
            linkClass: '__link',
            linksClass: '__links',
            linkActiveClass: '__link--active',
            contentClass: '__content',
            contentsClass: '__contents',
            contentActiveClass: '__content--active',
            tabFxClass: '--tabFx'
        };
        for (var c in autoConfigs) {
            if (!this.config[c]) {
                this.config[c] = this.config.bemClass + autoConfigs[c];
            }
        }
    }
    this.el.addClass(this.config.tabFxClass);
    this._activeTabIdx = null;
    this.switchEls = this.el.find('> .' + this.config.linkClass);
    this.contentEls = this.el.find('> .' + this.config.contentClass);

    var tabsLinks = $('<div class="' + this.config.linksClass + '"></div>').appendTo(this.el.first());
    this.tabsContents = $('<div class="' + this.config.contentsClass + '" data-width="100%"></div>').appendTo(this.el.first());

    for (var i = 0; i < this.contentEls.length; i++) {
        this.tabsContents.append(this.contentEls[i]);
    }

    var activeTabIdx = false;
    for (var i = 0; i < this.switchEls.length; i++) {
        tabsLinks.append(this.switchEls[i]);
        var swEl = $(this.switchEls[i]);

        $(this.contentEls[i]).removeClass(this.config.contentActiveClass);
        $(this.switchEls[i]).removeClass(this.config.linkActiveClass);

        // if it is important, show on startup
        if ($(this.contentEls[i]).children('.kwfUp-kwfImportant').length) {
            activeTabIdx = i;
        }

        if (activeTabIdx === false && $(this.contentEls[i]).hasClass(this.config.contentActiveClass)) {
            activeTabIdx = i;
            $(this.contentEls[i]).removeClass(this.config.contentActiveClass);
        }

        swEl.click({ idx: i }, (function(e) {
            this.activateTab(e.data.idx, { changeHash: true });
        }).bind(this));
    }

    // checks for a hash in the url to preselect a tab
    // the hashPrefix value is provided by the config object,
    // containing the ID for this tab element in case of multiple tab elements
    var hash = window.location.hash;
    var preSelect = -1;
    if (this.config.hashPrefix) {
        preSelect = (hash.indexOf(this.config.hashPrefix) < 0 ?
            -1 : hash.split(this.config.hashPrefix + ':')[1]);
    }
    if (preSelect < 0) {
        // if preSelect turns out to be -1, show first tab as default
        if (activeTabIdx === false && this.switchEls.length) {
            activeTabIdx = 0;
        }
    } else {
        // if any preSelect value was recognized in the hash,
        // scroll down to this element and then preselect the tab with given id
        if (preSelect < this.switchEls.length) {
            window.scrollTo(0, this.el.offset().top - 30);
            activeTabIdx = preSelect;
        }
    }

    if (activeTabIdx !== false) {
        this.activateTab(activeTabIdx, { changeHash: false });
        this._activeTabIdx = activeTabIdx;
    }
};

Tabs.prototype = {

    on: function(event, cb, scope) {
        if (typeof scope != 'undefined') cb = cb.bind(scope);
        $(this.el).on('kwfUp-tabs-'+event, cb);
    },

    fireEvent: function(event) {
        var args = [].slice.call(arguments, 1);
        $(this.el).trigger('kwfUp-tabs-'+event, args);
    },

    /**
     *
     * @param idx
     * @param options: { changeHash: true }
     */
    activateTab: function(idx, options) {
        this.fireEvent('beforeTabActivate', this, idx, this._activeTabIdx);
        if (this._activeTabIdx == idx) return;

        var newContentEl = $(this.contentEls[idx]);
        var oldContentEl = $(this.contentEls[this._activeTabIdx]);
        var newLinkEl = $(this.switchEls[idx]);
        var oldLinkEl = $(this.switchEls[this._activeTabIdx]);

        $(this.tabsContents).css('min-height', newContentEl.css('height'));

        oldLinkEl.removeClass(this.config.linkActiveClass);
        newLinkEl.addClass(this.config.linkActiveClass);

        oldContentEl.removeClass(this.config.contentActiveClass);
        this.contentEls.css('position', 'absolute');
        newContentEl.addClass(this.config.contentActiveClass);
        newContentEl.data('idx', idx);
        oneTransitionEnd(newContentEl, function() {
            if (this._activeTabIdx == newContentEl.data('idx')) $(newContentEl).css('position', 'relative');
        }.bind(this));

        onReady.callOnContentReady(this.contentEls[idx], {newRender: false});
        onReady.callOnContentReady(this.el, {action: 'show'});

        this.fireEvent('tabActivate', this, idx, this._activeTabIdx);
        this._activeTabIdx = idx;

        if (!options || typeof options.changeHash == 'undefined' || options.changeHash) {
            var url;
            if (this.config.hashPrefix) {
                // if this tab uses a hashPrefix to be recognized from the URL, set the new value.
                url = window.location.href.split('#')[0] + '#' + this.config.hashPrefix + ':' + idx;
                window.location.replace(url);
            } else {
                url = document.location.href + '#tab' + (idx + 1);
            }
            statistics.trackView(url);
        }
    }
};

module.exports = Tabs;
