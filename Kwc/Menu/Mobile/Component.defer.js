Kwf.onJElementReady('.cssClass', function mobileMenu(el, config) {
    var slideDuration = 400;
    var menuLink = el.children('.showMenu');
    var left = 100;

    // Store
    var menuData = {};
    var menuHtml = [];
    var fetchedPages = {};

    var template = _.template(
        '<ul class="menu">\n' +
            '<% if (isRoot) { %>' +
                '<% _.each(item.pages, function(page) { %>' +
                    '<li class="<% if (page.hasChildren) {  %>hasChildren<% } else if (page.isParent) { %>parent<% } %>">\n' +
                        '<a href="<%= page.url %>" data-id="<%= page.id %>" data-children="<%= (page.hasChildren || page.children && page.children.length) || false %>"><%= page.name %></a>\n'+
                    '</li>\n'+
                '<% }) %>'+
            '<% } else { %>'+
                '<% if (item.children && item.children.length) { %>'+
                    '<li class="back"><a href="#">'+trlKwf('back')+'</a></li>\n'+
                '<% } %>'+
                '<% _.each(item.children, function(child) { %>'+
                    '<li class="<% if (child.hasChildren) {  %>hasChildren<% } else if (child.isParent) { %>parent<% } %>">\n' +
                        '<a href="<%= child.url %>" data-id="<%= child.id %>" data-children="<%= child.hasChildren %>"><%= child.name %></a>\n'+
                    '</li>\n' +
                '<% }) %>' +
            '<% } %>' +
        '</ul>\n'
    );

    var slide = function(direction, id) {
        var menu = el.find('ul.menu');
        var slider = el.find('.slider');

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

    el.on('click', 'li.back', function(e) {
        e.preventDefault();
        if (el.find('.slider').is(':animated')) return false;
        slide('right');
    });

    el.on('click', 'a[data-children="true"]', function(e) {
        e.preventDefault();
        if (el.find('.slider').is(':animated')) return false;

        var data = $(e.target).data();
        var responseAnimation = false;

        if (!_.has(menuData, data.id)) {
            responseAnimation = true;
            el.addClass('loading');
            el.find('.menu').hide();
            el.find('.slider').removeAttr('style');
        }
        if (_.has(menuData, data.id)) {
            slide('left', data.id);
        }

        if (!_.has(fetchedPages, data.id)) {
            fetchedPages[data.id] = true;
            var request = $.ajax({
                url: config.controllerUrl + '/json-index',
                data: {
                    pageId: data.id,
                    componentId: config.componentId,
                    kwfSessionToken: Kwf.sessionToken
                }
            });
            request.done(function(res) {
                _.each(res.pages, function(page) {
                    menuData[page.id] = page;
                });
                if (responseAnimation) {
                    el.removeClass('loading');
                    slide('left', data.id);
                }
            });
        }
    });

    menuLink.click(function(e) {
        menuLink.trigger('menuToggle', slideDuration);
        e.preventDefault();
        var slider = el.find('.slider');
        var menu = el.find('.menu');

        var sliders = $('.kwcMenuMobile .slider').not(slider);
        if (sliders.length) {
            sliders.parent().find('.active').removeClass('active');
            sliders.parent().removeClass('open');
            $('body').removeClass('kwcMobileMenuOpen');
            sliders.animate({height: 0}, slideDuration);
        }

        slider.stop();

        if (!menu.length) {
            el.addClass('loading');
        }
        menuLink.toggleClass('active');
        $('body').toggleClass('kwcMobileMenuOpen');
        if (menuLink.parent().hasClass('open')) {
            slider.animate({height: 0}, slideDuration);
        } else {
            slider.animate({height: menu.height()}, slideDuration);
        }
        menuLink.parent().toggleClass('open');
    });

    // Inital Request
    $.ajax({
        url: config.controllerUrl + '/json-index',
        data: {
            subrootComponentId: config.subrootComponentId,
            componentId: config.componentId,
            kwfSessionToken: Kwf.sessionToken
        },
        dataType: 'JSON',
        success: function(res) {
            _.each(res.pages, function(page) {
                page.root = true;
                menuData[page.id] = page;
            });

            if (!el.find('.slider').length) el.append('<div class="slider"></div>');

            var html = template({item: res, isRoot: true});
            el.find('.slider').html(html);
            menuHtml.push(html);
            if (el.hasClass('loading')) {
                el.find('.slider').animate({height: el.find('ul.menu').height()}, slideDuration);
                el.trigger('menuToggle', slideDuration);
            }
            el.removeClass('loading');
        }
    });

}, { checkVisibility: true, defer: true });
