Kwf.onJElementReady('.kwcMenuMobile', function mobileMenu(el, config) {
    var slideDuration = 400;
    var menuLink = el.children('.showMenu');
    var menu = null;
    var showMenuAfterLoad = false;
    var ajaxRequest = null;

    $(window).load(function(event) {
        showMenuAfterLoad = false;
        getMenu();
    });

    menuLink.click(function(event) {
        event.preventDefault();
        if (menu && menu.is(':animated')) return;

        var activeEl = $('.kwcMenuMobile').children('.showMenu.active');
        if (activeEl.length && activeEl.get(0) != menuLink.get(0)) {
            activeEl
                .removeClass('active')
                .next('.slider')
                .children('ul.menu')
                .slideToggle(slideDuration, function(){
                    $(this)
                        .parent().parent()
                        .removeClass('open');
                });
        }

        if (!menu) {
            showMenuAfterLoad = true;
            el.addClass('loading');
            getMenu();
        } else {
            menu.slideToggle(slideDuration);
            el.trigger('menuToggle',[slideDuration]);
        }
        menuLink.toggleClass('active');
        menuLink.parent().toggleClass('open');
    });

    _.mixin({
        mobileMenuPartial: function(item) {
            var partial = _.template(
                '<li class="<%= item.class %>">' +
                    '<a href="<%= item.url %>"><%= item.name %></a>'+
                    '<ul class="subMenu">'+
                        '<% if (item.children && !_.isEmpty(item.children)) { %>'+
                            '<% _.each(item.children, function(child) { %>'+
                                '<%= _(child).mobileMenuPartial() %>'+
                            '<% }) %>'+
                        '<% } %>'+
                    '</ul>'+
                '</li>'
            );
            return partial({item: item});
        }
    });

    var getMenu = function() {
        if (!ajaxRequest) {
            ajaxRequest = $.getJSON(config.controllerUrl + '/json-index', {
                subrootComponentId: config.subrootComponentId,
                componentId: config.componentId,
                kwfSessionToken: Kwf.sessionToken
            }, function(data) {
                var tpl = _.template(
                    '<div class="slider">' +
                        '<ul class="menu">' +
                            '<% _.each(pages, function(page) { %>'+
                                '<li class="<%= page.class %>">' +
                                '<a href="<%= page.url %>"><%= page.name %></a>'+
                                    '<ul class="subMenu">' +
                                        '<% _.each(page.children, function(child) { %>'+
                                            '<%= _(child).mobileMenuPartial() %>'+
                                        '<% }) %>'+
                                    '</ul>'+
                                '</li>'+
                            '<% }) %>'+
                        '</ul>'+
                    '</div>'
                );
                //compatibility for old templates that don't contain slider element
                if(el.find('.slider').length == 0) el.append('<div class="slider"></div>');

                el.find('.slider').replaceWith(tpl(data));
                menu = el.find('ul.menu');
                if (showMenuAfterLoad) {
                    menu.slideDown(slideDuration);
                    el.trigger('menuToggle',[slideDuration]);
                }

                menu.find('li.hasChildren ul.subMenu').prepend('<li class="back">\n' +
                    '<a href="#">\n' +
                        trlKwf('back') + '\n' +
                    '</a>\n' +
                '</li>');

                //after menu is loaded
                var currentLeft = 0;
                var left = 100;
                el.removeClass('loading');
                menu.find('li.hasChildren').each(function(index, child) {
                    $(child).children('a').click(function(event) {
                        menu.css('height', 'auto');
                        event.preventDefault();
                        currentLeft += left;
                        $(child).addClass('moved');
                        el.children('.slider').animate({
                            left: '-' + currentLeft + '%'
                        });
                        menu.animate({
                            height: $(child).find('ul.subMenu').height()
                        });
                    });
                });
                menu.find('li.back').each(function(index, child) {
                    $(child).children('a').click(function(event) {
                        menu.css('height', 'auto');
                        event.preventDefault();
                        currentLeft -= left;
                        el.children('.slider').animate({
                            left: '-' + currentLeft + '%'
                        }, function() {
                            $(child).parent().parent().removeClass('moved');
                        });
                        menu.animate({
                            height: $(child).parents('ul').parents('ul').height()
                        });
                    });
                });

                // Hide menu if link matches hash change (anchor scroll)
                menu.find('li > a').each(function(index, link) {
                    if($(link).attr('href').match('^/#')) {
                        $(link).click(function(e) {
                            menuLink.trigger('click');
                        });
                    }
                });
            });
        }
    };
}, { checkVisibility: true, defer: true });
