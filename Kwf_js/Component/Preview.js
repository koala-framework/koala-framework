Ext.namespace('Kwf.Component');
Kwf.Component.Preview = Ext.extend(Ext.Panel, {
    autoScroll: true,
    initComponent: function() {
        this.previewMode = false;
        this.classNames = ['desktop', 'notebook', 'smartphonePortrait', 'smartphoneLandscape', 'tabletPortrait', 'tabletLandscape'];
        this.tbar = [];

        var kwfComponentPreviewUrl = new Ext.form.TextField({
            name: 'kwfComponentPreviewUrl',
            cls: 'kwfComponentPreviewUrl',
            width: 400,
            enableKeyEvents: true,
            listeners: {
                keypress: function(el, ev) {
                    if (ev.keyCode == 13) {
                        var regExp = /(http|https):\/\//;
                        var url = Ext.getBody().child('.kwfComponentPreviewUrl').dom.value;
                        if (!regExp.test(url)) url = 'http://' + url;
                        Ext.getBody().child('.kwfComponentPreviewIframe').dom.src = url;
                    }
                }
            },
            scope: this
        }, this);
        this.tbar.add(kwfComponentPreviewUrl);

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

        var previewButton = new Ext.Button({
            cls: 'x-btn-text',
            enableToggle: true,
            text: 'Preview Modus',
            listeners: {
                toggle: function(button, pressed) {
                    this.previewMode = pressed;
                    window.frames['kwfComponentPreviewIframe'].location.reload();
                }
            },
            scope: this
        }, this);

        var reloadButton = new Ext.Button({
            cls: 'x-btn-icon',
            icon: '/assets/silkicons/arrow_rotate_clockwise.png',
            handler: function() {
                window.frames['kwfComponentPreviewIframe'].location.reload();
            },
            scope: this
        }, this);

        this.tbar.add('->');
        this.tbar.add(previewButton);
        this.tbar.add(reloadButton);
        Kwf.Component.Preview.superclass.initComponent.call(this);
    },

    afterRender: function() {
        var kwfComponentPreview = this.body.createChild({
            tag: 'div',
            cls: 'kwfComponentPreview desktop'
        });
        var iframeUrl = window.location.protocol + '//' + window.location.host + "/";
        var kwfComponentPreviewIframe = kwfComponentPreview.createChild({
            tag: 'iframe',
            name: 'kwfComponentPreviewIframe',
            src: iframeUrl,
            cls: 'kwfComponentPreviewIframe'
        });
        kwfComponentPreviewIframe.on('load', function() {
            var iframeWindow = window.frames['kwfComponentPreviewIframe'];

            iframeWindow.Kwf.onContentReady(function() {
                this.addPreviewAttribute(iframeWindow, this.previewMode);
            }, this);

            this.addPreviewAttribute(iframeWindow, this.previewMode);

            Ext.getBody().child('.kwfComponentPreviewUrl').set({
                value: iframeWindow.location.href
            }, this);
        }, this);
        Kwf.Component.Preview.superclass.afterRender.call(this);
    },

    addPreviewAttribute: function(iframeWindow, previewMode) {
        var iframeBody = Ext.get(iframeWindow.document.body);
        iframeBody.select('a', true).each(function(a) {
            if (a.dom.href.indexOf(window.location.host) !== -1 && previewMode) { // intern
                var separator = '?';
                if (a.dom.href.indexOf('?') !== -1) separator = '&';
                var link = a.dom.href;
                if (a.dom.href.indexOf('preview=true') === -1) link += separator + 'preview=true';
                a.set({ href: link });
            }
        }, this);
    }
});
Ext.reg('kwf.component.preview', Kwf.Component.Preview);
