Vps.onContentReady(function()
{
    var components = Ext.query('a.vpsLightbox');
    Ext.each(components, function(c) {
        var link = Ext.get(c);
        var settings = Ext.decode(link.child('.settings').getValue());
        if (!link.lightboxProcessed) {
            link.lightboxProcessed = true;
            link.on('click', function (ev) {
                ev.preventDefault();
                Vps.Lightbox.Lightbox.open(link.dom, '.' + settings.sel, settings);
            });
            
        }
    });
});

Ext.namespace("Vps.Lightbox");
Vps.Lightbox.Lightbox = (function(link, config) {
    var els = {},
        items = [],
        activeItem,
        extboxBorders = [],
        interfaceWidth,
        interfaceHeight,
        currentWidth = 250,
        currentHeight = 250,
        currentX,
        currentY,
        initialized = false,
        selectors = [],
        wrapper = false;

    return {
        version: '1.0',
        opts: {},
        defaults: {
            current: trlVps('Entry {current} of {total}'),
            previous: '&#8592;',
            next: '&#8594;',
            close: 'close',
            width: false,
            height: false,
            innerWidth: false,
            innerHeight: false,
            maxWidth: '90%',
            maxHeight: '90%',
            group: true,
            resizeDuration: 0.3,
            overlayOpacity: 0.1,
            overlayDuration: 0.2,
            hideInfo: false,
            easing: 'easeNone',
            title: false,
            navigate: true
        },
        
        // ************* open *****************
        open: function(item, sel, options) {
            Ext.apply(this.opts, options, this.defaults);
            this.setViewSize();
            els.overlay.fadeIn({
                duration: this.opts.overlayDuration,
                endOpacity: this.opts.overlayOpacity,
                callback: function() {
                    items = [];
                    var index = 0;
                    if(!this.opts.group) {
                        items.push(item);
                    } else {
                        console.log(sel);
                        items = Ext.query(sel);
                    }

                    // calculate top and left offset for the extbox
                    var pageScroll = Ext.fly(document).getScroll();
                    var extboxTop = (Ext.lib.Dom.getViewportHeight() - currentHeight) / 2 + pageScroll.top;
                    var extboxLeft = (Ext.lib.Dom.getViewportWidth() - currentWidth) / 2 + pageScroll.left;

                    els.extbox.setStyle({
                        top: extboxTop + 'px',
                        left: extboxLeft + 'px'
                    }).show();

                    this.loadItem(index);

                    // update controls
                    if (this.opts.navigate) {
                        els.navPrev.update(this.opts.previous);
                        els.navNext.update(this.opts.next);
                    }
                    els.navClose.update(this.opts.close);
                    
                    // check info visibility
                    if (this.opts.hideInfo == 'auto') {
                        els.extbox.on('mouseenter', this.showInfo, this);
                        els.extbox.on('mouseleave', this.hideInfo, this);
                        els.info.hide();
                    } else if (this.opts.hideInfo === false) {
                        els.extbox.un('mouseenter', this.showInfo, this);
                        els.extbox.un('mouseleave', this.hideInfo, this);
                        els.info.show();
                    } else if (this.opts.hideInfo === true) {
                        els.extbox.un('mouseenter', this.showInfo, this);
                        els.extbox.un('mouseleave', this.hideInfo, this);
                        els.info.hide();
                    }

                    Ext.fly(window).on('resize', this.resizeWindow, this);
                    this.fireEvent('open', items[index]);
                },
                scope: this
            });
        },
        
        loadItem: function(index) {
            var timeout, loadContent = {};
            activeItem = index;
            this.disableKeyNav();
            els.loadingOverlay.show();
            els.loading.show();

            if (this.opts.inline) {
                isImg = false;
                currentX = false;
                currentY = false;
                var cnt = Ext.query(this.opts.href);
                loadContent = {
                    tag: 'div',
                    id: 'ux-extbox-loadedContent',
                    html: cnt[0].innerHTML,
                    style: {display: 'none'}
                };
                
                Ext.DomHelper.overwrite(els.content, loadContent);
                this.resize();
            } else {
                var url = items[activeItem].href;
                Ext.Ajax.request({
                    params: {url: url},
                    url: '/vps/util/render/render',
                    success: function(response, options, r) {
                        currentX = false;
                        currentY = false;
                        loadContent = {
                            tag: 'div',
                            id: 'ux-extbox-loadedContent',
                            html: response.responseText,
                            style: {display: 'none'}
                        };
                        Ext.DomHelper.overwrite(els.content, loadContent);
                        Vps.callOnContentReady();
                        this.resize();
                    },
                    scope: this
                });
            }
            
            // update Nav
            (function () {
                this.enableKeyNav();

                // if not first image in set, display prev image button
                if (activeItem < 1) {
                    els.navPrev.hide();
                } else {
                    els.navPrev.show();
                }

                // if not last image in set, display next image button
                if (activeItem >= (items.length - 1)) {
                    els.navNext.hide();
                } else {
                    els.navNext.show();
                }
                els.loadingOverlay.hide();
                els.loading.hide();
            }).defer(this.opts.resizeDuration * 1000, this);
        },
        
        // ************* init *****************
        init: function() {
            if(!initialized) {
                Ext.apply(this, Ext.util.Observable.prototype);
                Ext.util.Observable.constructor.call(this);
                this.addEvents('open', 'close');
                this.initMarkup();
                this.initEvents();
                initialized = true;
            }
        },
        
        initMarkup: function() {
            els.overlay = Ext.DomHelper.insertFirst(document.body, {
                id: 'ux-extbox-overlay'
            }, true);
            els.overlay.setVisibilityMode(Ext.Element.DISPLAY).hide();
            
            if (Ext.isIE6) {
                els.shim = Ext.DomHelper.insertFirst(document.body, {
                    tag: 'iframe',
                    id: 'ux-extbox-shim',
                    frameborder: 0
                }, true);
                els.shim.setVisibilityMode(Ext.Element.DISPLAY);
                els.shim.hide();
            }
            
            var extboxTpl = new Ext.Template(this.getTemplate());
            els.extbox = extboxTpl.insertAfter(els.overlay, {}, true);
            els.extbox.setVisibilityMode(Ext.Element.DISPLAY).hide();
            
            var ids = ['container', 'content', 'loadingOverlay', 'loading', 'navPrev', 'navNext', 'navClose', 'info', 'title', 'current'];
            Ext.each(ids, function(id){
                els[id] = Ext.get('ux-extbox-' + id);
            });
            extboxBorders = [(
                els.extbox.getPadding('t') +
                els.extbox.getBorderWidth('t')
            ), (
                els.extbox.getPadding('r') +
                els.extbox.getBorderWidth('r')
            ), (
                els.extbox.getPadding('b') +
                els.extbox.getBorderWidth('b')
            ), (
                els.extbox.getPadding('l') +
                els.extbox.getBorderWidth('l')
            )];
            interfaceWidth =
                els.container.getPadding('rl') +
                els.container.getBorderWidth('rl') +
                els.content.getPadding('rl') +
                els.content.getBorderWidth('rl') +
                parseInt(els.container.getStyle('margin-left'), 10) +
                parseInt(els.container.getStyle('margin-right'), 10);
            interfaceHeight =
                els.container.getPadding('tb') +
                els.container.getBorderWidth('tb') +
                els.content.getPadding('tb') +
                els.content.getBorderWidth('tb') +
                parseInt(els.container.getStyle('margin-top'), 10) +
                parseInt(els.container.getStyle('margin-bottom'), 10);

            els.extbox.setStyle({
                width: currentWidth + 'px',
                height: currentHeight + 'px'
            });
            if (wrapper) {
                els.wrapper = els.container.wrap({tag: 'div', id: 'ux-extbox-trc'})
                .wrap({tag: 'div', id: 'ux-extbox-tlc'})
                .wrap({tag: 'div', id: 'ux-extbox-tb'})
                .wrap({tag: 'div', id: 'ux-extbox-brc'})
                .wrap({tag: 'div', id: 'ux-extbox-blc'})
                .wrap({tag: 'div', id: 'ux-extbox-bb'})
                .wrap({tag: 'div', id: 'ux-extbox-rb'})
                .wrap({tag: 'div', id: 'ux-extbox-lb'});
            }
        },
        
        getTemplate : function() {
            return [
                '<div id="ux-extbox">',
                    '<div id="ux-extbox-container">',
                        '<div id="ux-extbox-content">',

                        '</div>',
                        '<div id="ux-extbox-loadingOverlay">',
                            '<div id="ux-extbox-loading"></div>',
                        '</div>',
                        '<div id="ux-extbox-navPrev"></div>',
                        '<div id="ux-extbox-navNext"></div>',
                        '<div id="ux-extbox-navClose"></div>',
                        '<div id="ux-extbox-info">',
                            '<div id="ux-extbox-title"></div>',
                            '<div id="ux-extbox-current"></div>',
                        '</div>',
                    '</div>',
                '</div>'
            ];
        },
        
        initEvents: function() {
            var close = function(ev) {
                ev.preventDefault();
                this.close();
            };

            els.overlay.on('click', close, this);
            els.navClose.on('click', close, this);

            els.extbox.on('click', function(ev) {
                if(ev.getTarget().id == 'ux-extbox') {
                    this.close();
                }
            }, this);
            
            els.navPrev.on('click', function(ev) {
                ev.preventDefault();
                this.loadItem(activeItem - 1);
            }, this);

            els.navNext.on('click', function(ev) {
                ev.preventDefault();
                this.loadItem(activeItem + 1);
            }, this);
        },
        
        // ************* helping methods *****************
        setViewSize: function() {
            var viewSize = [
                Math.max(Ext.lib.Dom.getViewWidth(), Ext.lib.Dom.getDocumentWidth()),
                Math.max(Ext.lib.Dom.getViewHeight(), Ext.lib.Dom.getDocumentHeight())
            ];
            if (Ext.isIE6) {
                els.shim.setStyle({
                    width: viewSize[0] + 'px',
                    height: viewSize[1] + 'px'
                }).setOpacity(0).show();
                els.overlay.setStyle({
                    width: viewSize[0] + 'px',
                    height: viewSize[1] + 'px',
                    position: 'absolute'
                });
            } else {
                els.overlay.setStyle({
                    width: viewSize[0] + 'px',
                    height: viewSize[1] + 'px'
                });
            }
        },
        
        resize: function (w, h) {
            var c, x, y, cx, cy, cl, ct, loadedContent;
            var viewSize = [Ext.lib.Dom.getViewWidth(), Ext.lib.Dom.getViewHeight()];
            var pageScroll = Ext.fly(document).getScroll();
            var maxW = this.setSize(this.opts.maxWidth, 'x') - extboxBorders[3] - extboxBorders[1] - interfaceWidth;
            var maxH = this.setSize(this.opts.maxWidth, 'y') - extboxBorders[0] - extboxBorders[2] - interfaceHeight;
            cx = w || this.opts.innerWidth;
            cy = h || this.opts.innerHeight;
            x = (cx) ?
                cx : (this.opts.width) ?
                    this.opts.width - extboxBorders[3] - extboxBorders[1] : maxW;
            y = (cy) ?
                cy : (this.opts.height) ?
                    this.opts.height - extboxBorders[0] - extboxBorders[2] : maxH;
            x = parseInt(x, 10);
            y = parseInt(y, 10);
            currentWidth = x + interfaceWidth + extboxBorders[1] + extboxBorders[3];
            currentHeight = y + interfaceHeight + extboxBorders[0] + extboxBorders[2];
            cl = ((viewSize[0] - x - extboxBorders[1] - extboxBorders[3] - interfaceWidth) / 2) + pageScroll.left;
            ct = ((viewSize[1] - y - extboxBorders[0] - extboxBorders[2] - interfaceHeight) / 2)  + pageScroll.top;
            cl = (cl > 0) ? cl : 0;
            ct = (ct > 0) ? ct : 0;
            Ext.Fx.syncFx();

            els.extbox.shift({
                width: currentWidth,
                height: currentHeight,
                left: cl,
                top: ct,
                easing: this.opts.easing,
                duration: this.opts.resizeDuration,
                scope: this
            });
            els.content.shift({
                width: x,
                height: y,
                easing: this.opts.easing,
                duration: this.opts.resizeDuration,
                scope: this,
                callback: function() {
                    // update details
                    var title = this.opts.title || items[activeItem].title
                    els.title.update(title);
                    //els.title.show();
                    if (items.length > 1) {
                        els.current.update(this.opts.current.replace(/\{current\}/, activeItem+1).replace(/\{total\}/, items.length));
                        //els.current.show();
                    } else {
                        els.current.update('');
                    }
                }
            });
            loadedContent = Ext.get('ux-extbox-loadedContent');
            if (loadedContent !== null && loadedContent.isVisible()) {
                loadedContent.shift({
                    width: x - 20,
                    height: y,
                    easing: this.opts.easing,
                    duration: this.opts.resizeDuration
                });
            } else {
                loadedContent.shift({
                    width: x - 20,
                    height: y,
                    easing: this.opts.easing,
                    duration: this.opts.resizeDuration
                }).fadeIn({duration: this.opts.resizeDuration/2});
            }
            Ext.Fx.sequenceFx();
        },
        
        resizeWindow: function() {
                    this.setViewSize();
                    this.resize(currentX, currentY);
        },
        
        showInfo: function() {
            els.info.stopFx().fadeIn({duration: this.opts.resizeDuration});
        },
        
        hideInfo: function() {
            els.info.stopFx().fadeOut({duration: this.opts.resizeDuration});
        },
        
        enableKeyNav: function() {
            Ext.fly(document).on('keydown', this.keyNavAction, this);
        },
        
        disableKeyNav: function() {
            Ext.fly(document).un('keydown', this.keyNavAction, this);
        },
        
        keyNavAction: function(ev) {
            var keyCode = ev.getKey();
            if (
                keyCode == 88 || // x
                keyCode == 67 || // c
                keyCode == 27
            ) {
                this.close();
            } else if (keyCode == 80 || keyCode == 37) { // display previous item
                if (activeItem != 0){
                    this.loadItem(activeItem - 1);
                }
            } else if (keyCode == 78 || keyCode == 39) { // display next item
                if (activeItem != (items.length - 1)) {
                    this.loadItem(activeItem + 1);
                }
            }
        },
        
        close: function() {
            this.disableKeyNav();
            els.extbox.hide();
            els.overlay.fadeOut({
                duration: this.opts.overlayDuration
            });
            if (Ext.isIE6) els.shim.hide();
            Ext.DomHelper.overwrite(els.content, '');
            Ext.DomHelper.overwrite(els.title, '');
            Ext.DomHelper.overwrite(els.current, '');
            Ext.fly(window).un('resize', this.resizeWindow, this);
            this.fireEvent('close', activeItem);
        },
        
        setSize: function (size, dimension) {
            dimension = dimension === 'x' ? Ext.lib.Dom.getViewWidth() : Ext.lib.Dom.getViewHeight();
            return (typeof size === 'string') ? Math.round((size.match(/%/) ? (dimension / 100) * parseInt(size, 10) : parseInt(size, 10))) : size;
        }
    }
})();
Ext.onReady(Vps.Lightbox.Lightbox.init, Vps.Lightbox.Lightbox);