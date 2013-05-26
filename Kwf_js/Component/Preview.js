Ext.namespace('Kwf.Component');
Kwf.Component.Preview = Ext.extend(Ext.Panel, {
    autoScroll: true,
    initComponent: function() {
        this.previewMode = false;
        if (decodeURIComponent(this.getParam('url')).indexOf('kwcPreview') !== -1) this.previewMode = true;
        this.classNames = ['desktop', 'notebook', 'smartphonePortrait', 'smartphoneLandscape', 'tabletPortrait', 'tabletLandscape'];
        this.tbar = [];

        this.kwfComponentPreviewUrl = new Ext.form.TextField({
            name: 'kwfComponentPreviewUrl',
            cls: 'kwfComponentPreviewUrl',
            width: 400,
            enableKeyEvents: true,
            listeners: {
                keypress: function(el, ev) {
                    if (ev.keyCode == 13) {
                        var regExp = /(http|https):\/\//;
                        var url = el.getValue();
                        if (!regExp.test(url)) url = 'http://' + url;
                        if (url.indexOf('kwcPreview') === -1) {
                            this.previewMode = false;
                        } else {
                            this.previewMode = true;
                        }
                        Ext.getBody().child('.kwfComponentPreviewIframe').dom.src = url;
                    }
                },
                scope: this
            },
            scope: this
        }, this);
        this.tbar.add(this.kwfComponentPreviewUrl);

        if (this.config.responsive) {
            this.addResponsiveButtons();
        }
        
        var previewButton = new Ext.Button({
            cls: 'x-btn-text',
            enableToggle: true,
            text: 'Preview Modus',
            pressed: this.previewMode,
            listeners: {
                toggle: function(button, pressed) {
                    this.previewMode = pressed;
                    var iframeUrl = window.frames['kwfComponentPreviewIframe'].location.href;
                    if (iframeUrl.indexOf(window.location.host)) {
                        if (this.previewMode) {
                            iframeUrl = this.buildPreviewLink(iframeUrl);
                        } else {
                            if (iframeUrl.indexOf('kwcPreview') !== -1) {
                                iframeUrl = iframeUrl.slice(0, iframeUrl.indexOf('kwcPreview')-1);
                            }
                        }
                    }
                    Ext.getBody().child('.kwfComponentPreviewIframe').dom.src = iframeUrl;
                },
                scope: this
            }
        }, this);

        this.tbar.add('->');
        this.tbar.add(previewButton);

        var reloadButton = new Ext.Button({
            cls: 'x-btn-icon',
            icon: '/assets/silkicons/arrow_rotate_clockwise.png',
            handler: function() {
                window.frames['kwfComponentPreviewIframe'].location.reload();
            },
            scope: this
        }, this);

        this.tbar.add(reloadButton);
        Kwf.Component.Preview.superclass.initComponent.call(this);
    },
    
    addResponsiveButtons: function() {
        var buttonGroup = [
            new Ext.Button({
                cls: 'x-btn-text',
                enableToggle: true,
                text: 'Desktop',
                handler: function() {
                    var kwfComponentPreview = Ext.getBody().child('.kwfComponentPreview');
                    kwfComponentPreview.removeClass(this.classNames).addClass('desktop');
                },
                scope: this
            }, this),
            new Ext.Button({
                cls: 'x-btn-text',
                enableToggle: true,
                text: 'Notebook',
                handler: function() {
                    var kwfComponentPreview = Ext.getBody().child('.kwfComponentPreview');
                    kwfComponentPreview.removeClass(this.classNames).addClass('notebook');
                },
                scope: this
            }, this),
            new Ext.Button({
                cls: 'x-btn-text',
                enableToggle: true,
                text: 'Tablet Hochformat',
                handler: function() {
                    var kwfComponentPreview = Ext.getBody().child('.kwfComponentPreview');
                    kwfComponentPreview.removeClass(this.classNames).addClass('tabletPortrait');
                },
                scope: this
            }, this),
            new Ext.Button({
                cls: 'x-btn-text',
                enableToggle: true,
                text: 'Tablet Querformat',
                handler: function() {
                    var kwfComponentPreview = Ext.getBody().child('.kwfComponentPreview');
                    kwfComponentPreview.removeClass(this.classNames).addClass('tabletLandscape');
                },
                scope: this
            }, this),
            new Ext.Button({
                cls: 'x-btn-text',
                enableToggle: true,
                text: 'Smartphone Hochformat',
                handler: function() {
                    var kwfComponentPreview = Ext.getBody().child('.kwfComponentPreview');
                    kwfComponentPreview.removeClass(this.classNames).addClass('smartphonePortrait');
                },
                scope: this
            }, this),
            new Ext.Button({
                cls: 'x-btn-text',
                enableToggle: true,
                text: 'Smartphone Querformat',
                handler: function() {
                    var kwfComponentPreview = Ext.getBody().child('.kwfComponentPreview');
                    kwfComponentPreview.removeClass(this.classNames).addClass('smartphoneLandscape');
                },
                scope: this
            }, this)
        ];
        buttonGroup.each(function(extButton) {
            extButton.on('toggle', function(button, pressed) {
                if (pressed) {
                    buttonGroup.each(function(innerExtButton) {
                        if (innerExtButton instanceof Ext.Button && innerExtButton != button) {
                            innerExtButton.toggle(false);
                        };
                    }, this);
                }
            }, this);
            this.tbar.add(extButton);
        }, this);
    },

    afterRender: function() {
        var kwfComponentPreview = this.body.createChild({
            tag: 'div',
            cls: 'kwfComponentPreview desktop'
        });
        var kwfComponentPreviewDevice = kwfComponentPreview.createChild({
            tag: 'div',
            cls: 'device'
        });
        var iframeUrl = window.location.protocol + '//' + window.location.host;
        if (this.getParam('url')) iframeUrl = decodeURIComponent(this.getParam('url'));
        var kwfComponentPreviewIframe = kwfComponentPreviewDevice.createChild({
            tag: 'iframe',
            name: 'kwfComponentPreviewIframe',
            src: iframeUrl,
            cls: 'kwfComponentPreviewIframe'
        });
        kwfComponentPreviewIframe.on('load', function() {
            var textfieldValue = window.frames['kwfComponentPreviewIframe'].location.href;
            if (textfieldValue.indexOf(window.location.host)) {
                if (this.previewMode) {
                    textfieldValue = this.buildPreviewLink(textfieldValue);
                } else {
                    if (textfieldValue.indexOf('kwcPreview') !== -1) {
                        textfieldValue = textfieldValue.slice(0, textfieldValue.indexOf('kwcPreview')-1);
                    }
                }
            }
            this.kwfComponentPreviewUrl.setValue(textfieldValue);
        }, this);
        Kwf.Component.Preview.superclass.afterRender.call(this);
    },

    buildPreviewLink: function(link) {
        var separator = '?';
        if (link.indexOf('?') !== -1) separator = '&';
        if (link.indexOf('kwcPreview') === -1) link += separator + 'kwcPreview';
        return link;
    },

    getParam: function(param) {
        var query = window.location.search.substring(1);
        if (query == param) return true;
        var params = query.split("&");
        for (var i=0; i<params.length; i++) {
            var pair = params[i].split("=");
            if (pair[0] == param) return pair[1];

            if (params[i] == param) return true;
        }
        return false;
    }
});
Ext.reg('kwf.component.preview', Kwf.Component.Preview);
