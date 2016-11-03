var onReady = require('kwf/on-ready');
var _ = require('underscore');
var $ = require('jQuery');
var t = require('kwf/trl');
var KwfBaseUrl = require('kwf/base-url');

onReady.onRender('.kwcClass', function mobileMenu(el, config) {
    var slideDuration = 400;
    var menuLink = el.children('.kwfUp-showMenu');
    var closeMask = el.children('.kwfUp-closeMask');
    var baseUrl = KwfBaseUrl.get();
    var left = 100;

    // Store
    var menuData = {};
    var menuHtml = [];
    var fetchedPages = {};

    var template = _.template(
        '<ul class="kwfUp-menu">\n' +
            '<% if (isRoot) { %>' +
                '<% _.each(item.pages, function(page) { %>' +
                    '<% if (!page.hidden) {  %>\n' +
                    '<li class="<% if (page.hasChildren) {  %>kwfUp-hasChildren<% } else if (page.isParent) { %>kwfUp-parent<% } %>">\n' +
                        '<a href="'+baseUrl+'<%= page.url %>" data-id="<%= page.id %>" data-children="<%= (page.hasChildren || page.children && page.children.length) || false %>"><%= page.name %></a>\n'+
                    '</li>\n'+
                    '<% } %>\n' +
                '<% }) %>'+
            '<% } else { %>'+
                '<% if (item.children && item.children.length) { %>'+
                    '<li class="kwfUp-back"><a href="#">'+t.trlKwf('back')+'</a></li>\n'+
                '<% } %>'+
                '<% _.each(item.children, function(child) { %>'+
                    '<% if (!child.hidden) {  %>\n' +
                    '<li class="<% if (child.hasChildren) {  %>kwfUp-hasChildren<% } else if (child.isParent) { %>kwfUp-parent<% } %>">\n' +
                        '<a href="'+baseUrl+'<%= child.url %>" data-id="<%= child.id %>" data-children="<%= child.hasChildren %>"><%= child.name %><% if (child.isParent) { %> <span class="kwfUp-overview">('+t.trlKwf('Overview')+')</span><% } %></a>\n'+
                    '</li>\n' +
                    '<% } %>\n' +
                '<% }) %>' +
            '<% } %>' +
        '</ul>\n'
    );

    var slide = function(direction, id) {
        var menu = el.find('.kwfUp-slider > ul.kwfUp-menu');
        var slider = el.find('.kwfUp-slider');

        if (direction == 'left') {
            var html = template({item: menuData[id], isRoot: false});
            menuHtml.push(html);
            $(html).insertAfter(menu);
            var secondMenu = menu.next();
            menu.animate({left: '-100%'}, function(){
                $(this).remove();
            });
            slider.animate({height: secondMenu.height()}, slideDuration);
            secondMenu.css({left: '100%'}).animate({left: 0});
            $('html, body').stop().animate({scrollTop: 0}, 300);

        } else if (direction == 'right') {
            menuHtml.splice(-1);
            var html = _.last(menuHtml);
            $(html).insertBefore(menu);
            var previousMenu = menu.prev();

            menu.animate({left: '100%'}, function(){
                $(this).remove();
            });
            slider.animate({height: previousMenu.height()}, slideDuration);
            previousMenu.css({left: '-100%'}).animate({left: 0});
        }

        return false;
    };

    el.on('click', 'li.kwfUp-back', function(e) {
        e.preventDefault();
        if (el.find('.kwfUp-slider').is(':animated')) return false;
        slide('right');
    });

    el.on('click', 'a[data-children="true"]', function(e) {
        e.preventDefault();
        if (el.find('.kwfUp-slider').is(':animated')) return false;

        var data = $(e.target).data();
        var responseAnimation = false;

        if (!_.has(menuData, data.id)) {
            responseAnimation = true;
            el.addClass('kwfUp-loading');
            el.find('.kwfUp-slider > ul.kwfUp-menu').hide();
            el.find('.kwfUp-slider').removeAttr('style');
        }
        if (_.has(menuData, data.id)) {
            slide('left', data.id);
        }

        if (!_.has(fetchedPages, data.id)) {
            fetchedPages[data.id] = true;
            var params = {
                pageId: data.id,
                componentId: config.componentId,
                pageUrl: location.href
            };

            var request = $.ajax({
                url: config.controllerUrl + '/json-index',
                data: params
            });
            request.done(function(res) {
                _.each(res.pages, function(page) {
                    menuData[page.id] = page;
                });
                if (responseAnimation) {
                    el.removeClass('kwfUp-loading');
                    slide('left', data.id);
                }
            });
        }
    });

    function toggleMenu() {
        menuLink.trigger('menuToggle', slideDuration);

        var slider = el.find('.kwfUp-slider');
        var menu = el.find('.kwfUp-slider > ul.kwfUp-menu');

        var sliders = $('[data-mobile-slider]').not(slider);
        if (sliders.length) {
            sliders.parent().find('.kwfUp-active').removeClass('kwfUp-active');
            sliders.parent().removeClass('kwfUp-open');
            $('body').removeClass('kwcMobileMenuOpen');
            sliders.animate({height: 0}, slideDuration);
        }

        slider.stop();

        if (!menu.length) {
            el.addClass('kwfUp-loading');
        }
        menuLink.toggleClass('kwfUp-active');
        $('body').toggleClass('kwfUp-kwcMobileMenuOpen');
        if (menuLink.parent().hasClass('kwfUp-open')) {
            slider.animate({height: 0}, slideDuration);
        } else {
            slider.animate({height: menu.height()}, slideDuration);
        }
        menuLink.parent().toggleClass('kwfUp-open');
    };

    menuLink.on('click', function(e) {
        e.preventDefault();
        toggleMenu();
    });

    closeMask.on('touchstart', function(e) {
        e.preventDefault();
        toggleMenu();
    });

    var params = {
        subrootComponentId: config.subrootComponentId,
        componentId: config.componentId,
        pageUrl: location.href
    };
    // Inital Request
    $.ajax({
        url: config.controllerUrl + '/json-index',
        data: params,
        dataType: 'JSON',
        success: function(res) {
            _.each(res.pages, function(page) {
                page.root = true;
                menuData[page.id] = page;
            });

            if (!el.find('.kwfUp-slider').length) el.append('<div class="kwfUp-slider"></div>');

            var html = template({item: res, isRoot: true});
            el.find('.kwfUp-slider').html(html);
            menuHtml.push(html);
            if (el.hasClass('kwfUp-loading')) {
                el.find('.kwfUp-slider').animate({height: el.find('.kwfUp-slider > ul.kwfUp-menu').height()}, slideDuration);
                el.trigger('menuToggle', slideDuration);
            }
            el.removeClass('kwfUp-loading');
        }
    });

}, { checkVisibility: true, defer: true });
