Kwf.onJElementReady('.redMalleeMenuMainVertical', function(el, config) {
    el.find('.showMenu').click(function(event) {
        event.preventDefault();
        var menu = el.find('> ul.menu');
        if (!menu.length) {
            $.getJSON(config.controllerUrl + '/json-index', { 
                componentId: config.componentId,
                kwfSessionToken: Kwf.sessionToken
            }, function(data) {
                var tpl = '<ul class="menu">\n' +
                    '{{#pages}}' +
                        '<li class="{{class}}">\n' +
                            '<a href="{{url}}">{{name}}</a>\n' +
                            '<ul class="subMenu">\n' +
                            '{{#children}}' +
                                '{{> children}}\n' +
                            '{{/children}}' +
                            '</ul>\n' +
                        '</li>\n' +
                    '{{/pages}}' +
                '</ul>';
                var partials = {
                    children: '<li class="{{class}}">\n' +
                        '<a href="{{url}}">{{name}}</a>\n' +
                        '<ul class="subMenu">\n' +
                        '{{#children}}' +
                            '{{> children}}\n' +
                        '{{/children}}' +
                        '</ul>\n' +
                    '</li>\n'
                };
                el.append(Mustache.render(tpl, data, partials));
                el.trigger('menuLoaded');
            });
        }
        menu.slideToggle();
    });

    var currentLeft = 0;
    var left = 100;
    el.on('menuLoaded', function() {
        menu = el.find('> ul.menu');
        menu.slideDown();
        el.find('ul.menu li.hasDropdown').each(function(index, child) {
            $(child).children('a').click(function(event) {
                event.preventDefault();
                menu.css('height', 'auto');
                currentLeft += left;
                $(child).addClass('moved');
                el.animate({
                    left: '-' + currentLeft + '%'
                });
                menu.animate({
                    height: $(child).find('ul.subMenu').height()
                });
            });
        });
        el.find('ul.menu li.back').each(function(index, child) {
            $(child).children('a').click(function(event) {
                event.preventDefault();
                menu.css('height', 'auto');
                currentLeft -= left;
                el.animate({
                    left: '-' + currentLeft + '%'
                }, function() {
                    $(child).parent().parent().removeClass('moved');
                });
                menu.animate({
                    height: $(child).parents('ul').parents('ul').height()
                });
            });
        });
    });
});
