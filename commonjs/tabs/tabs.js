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
    this.fxDuration = .5;

    var tabsLinks = $('<div class="' + this.config.linksClass + '"></div>').appendTo(this.el.first());
    this.tabsContents = $('<div class="' + this.config.contentsClass + '" data-width="100%"></div>').appendTo(this.el.first());

    for (var i = 0; i < this.contentEls.length; i++) {
        this.tabsContents.append(this.contentEls[i]);
    }

    var activeTabIdx = false;
    for (var i = 0; i < this.switchEls.length; i++) {
        tabsLinks.append(this.switchEls[i]);
        var swEl = $(this.switchEls[i]);

        $(this.contentEls[i]).hide();
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
            this.activateTab(e.data.idx);
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
        $(this.switchEls[activeTabIdx]).addClass(this.config.linkActiveClass);
        $(this.contentEls[activeTabIdx]).show();
        this._activeTabIdx = activeTabIdx;
    }
};

Tabs.prototype = {

    on: function(event, cb, scope)
    {
        if (typeof scope != 'undefined') cb = cb.bind(scope);
        $(this.el).on('kwfUp-tabs-'+event, cb);
    },

    fireEvent: function(event)
    {
        var args = [].shift.call(arguments);
        $(this.el).trigger('kwfUp-tabs-'+event, args);
    },

    activateTab: function(idx) {
        // passed arguments are: this, newIndex, oldIndex
        var el = this;
        this.fireEvent('beforeTabActivate', this, idx, this._activeTabIdx);
        if (this._activeTabIdx == idx) return;

        var newContentEl = $(this.contentEls[idx]);
        $(this.switchEls[idx]).addClass(this.config.linkActiveClass);
        newContentEl.css('z-index', '1');
        newContentEl.show();
        newContentEl.addClass(this.config.contentActiveClass);

        var oldContentEl = $(this.contentEls[this._activeTabIdx]);

        oldContentEl.stop();
        newContentEl.stop();
        this.tabsContents.stop();
        if (this._activeTabIdx !== null) {
            $(this.switchEls[this._activeTabIdx]).removeClass(this.config.linkActiveClass);
            oldContentEl.css({
                'z-index': 2,
                'position': 'absolute'
            });
            newContentEl.css({
                'z-index': 1,
                'position': 'absolute'
            });
            onReady.callOnContentReady(this.contentEls[idx], {newRender: false});
            oldContentEl.hide();
        }
        if (this._activeTabIdx !== null) {
            oldContentEl.show();

            newContentEl.show();
            onReady.callOnContentReady(this.el, {action: 'show'});
            newContentEl.hide();

            newContentEl.fadeIn({
                duration: this.fxDuration,
                complete: function() {
                    $(this).closest().css('height', 'auto');     //set the height after animation to auto because there are Components who change height when they are inside a tab
                }
            });
            oldContentEl.fadeOut({
                duration: this.fxDuration,
                complete: function() {
                    oldContentEl.css('position', 'static');
                    oldContentEl.removeClass(el.config.contentActiveClass);
                    newContentEl.css('position', 'static');
                    newContentEl.show();
                    newContentEl.css('height', 'auto');
                }
            });
        }

        this.tabsContents.height(oldContentEl.height());
        this.tabsContents.animate({ height: newContentEl.height() });

        // passed arguments are: this, newIndex, oldIndex
        this.fireEvent('tabActivate', this, idx, this._activeTabIdx);
        statistics.trackView(document.location.href + '#tab' + (idx + 1));

        this._activeTabIdx = idx;

        if (this.config.hashPrefix) {
            // if this tab uses a hashPrefix to be recognized from the URL, set the new value.
            window.location.replace(window.location.href.split('#')[0] + '#' + this.config.hashPrefix + ':' + idx);
        }
    },

    getIdxByContentEl: function(el) {
        if (el.dom) el = el.dom;
        for (var i = 0; i < this.contentEls.length; i++) {
            if (this.contentEls[i] === el) return i;
        }
        return (-1);
    },

    getContentElByIdx: function(idx) {
        if (this.contentEls[idx]) return this.contentEls[idx];
        return null;
    }
};

module.exports = Tabs;
