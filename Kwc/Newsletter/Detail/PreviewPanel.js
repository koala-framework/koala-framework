Ext.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.PreviewPanel = Ext.extend(Kwf.Binding.AbstractPanel, {
    border: false,
    autoScroll: true,
    bodyCssClass: 'mailPreviewPanel',
    button: [],
    recipientSources: [],
    html: false,
    text: false,
    disableComboBox: false,

    initComponent : function()
    {
        this.getRecipientSources();
        this.button['html'] = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/html.png',
            cls     : 'x-btn-text-icon',
            text    : trlKwf('HTML'),
            enableToggle: true,
            toggleGroup: 'format',
            pressed : true,
            toggleHandler: this.toggleButton,
            scope: this,
            name : 'html'
        });
        this.button['text'] = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/page_white_text.png',
            cls     : 'x-btn-text-icon',
            text    : trlKwf('Text'),
            enableToggle: true,
            toggleGroup: 'format',
            pressed : false,
            toggleHandler: this.toggleButton,
            scope: this,
            name: 'text'
        });

        this.sendButton = new Ext.Toolbar.Button ({
            icon    : '/assets/silkicons/email_go.png',
            cls     : 'x-btn-text-icon',
            text    : trlKwf('Send'),
            handler : function(a, b, c) {
                Ext.Ajax.request({
                    url : this.controllerUrl + '/json-send-mail',
                    mask: this.el,
                    maskText: trlKwf('Sending...'),
                    params: Ext.apply(this.baseParams, {
                        address: this.addressField.getValue(),
                        format: this.button['html'].pressed ? 'html' : 'text'
                    }),
                    success: function(response, options, r) {
                        Ext.MessageBox.alert(trlKwf('Status'), r.message);
                    },
                    scope: this
                });
            },
            scope   : this
        });

        this.addressField = new Ext.form.TextField({
            width: 200,
            emptyText: trlKwf('Send testmail to...'),
            value: this.authedUserEmail,
            vtype: 'email'
        });

        this.tbar = [this.button['html'], this.button['text'], '-', this.addressField, this.sendButton, '-'];
        Kwc.Newsletter.Detail.PreviewPanel.superclass.initComponent.call(this);
    },

    applyBaseParams : function(baseParams) {
        Kwc.Newsletter.Detail.PreviewPanel.superclass.applyBaseParams.call(this, baseParams);
        Ext.apply(this.baseParams, {
            newsletterId: this.baseParams.componentId.substr(this.baseParams.componentId.lastIndexOf('_')+1)
        });
    },

    load: function(params, options) {
        this.body.dom.style.backgroundColor = '#FFFFFF';
        this.body.dom.innerHTML = '';
        Ext.Ajax.request({
            url: this.controllerUrl + '/json-data',
            params:  this.baseParams,
            mask: this.el,
            success: function(r, options, data) {
                this.html = data.html;
                this.text = data.text;
                this.body.dom.innerHTML = this.html;
                this.button[data.format].toggle(true);
            },
            scope: this
        });
    },

    getRecipientSources: function() {
        if (this.recipientSources.length) return;
        Ext.Ajax.request({
            url: this.subscribersControllerUrl + '/json-get-recipient-sources',
            params:  this.baseParams,
            success: function(r, options, data) {
                Ext.apply(this.baseParams, {
                    recipientId: data.recipientId
                });
                for (var key in data.sources) {
                    if (data.sources[key].title) {
                        this.recipientSources.push([data.sources[key].model, data.sources[key].title]);
                    } else {
                        this.recipientSources.push([data.sources[key].model, data.sources[key].model]);
                    }
                }

                if (this.recipientSources.length == 1) {
                    for (var key in data.sources) {
                        Ext.apply(this.baseParams, {
                            subscribeModel: data.sources[key].model
                        });
                    }
                } else {
                    this.disableComboBox = true;
                    this.subscribeModelComboBox = new Kwf.Form.ComboBox({
                        triggerAction: 'all',
                        editable: false,
                        width: 200,
                        maxHeight: 350,
                        listWidth: 280,
                        emptyText: trlKwf('Select an subscriber pool...'),
                        store: {
                            data: this.recipientSources
                        },
                        listeners: {
                            select: function(combo, record, index) {
                                Ext.apply(this.baseParams, {
                                    subscribeModel: record.data.id
                                });
                                if (this.recipientComboBox.disabled) this.recipientComboBox.enable();
                                delete this.recipientComboBox.lastQuery;
                            },
                            scope: this
                        }
                    });
                    this.getTopToolbar().addField(this.subscribeModelComboBox);
                }

                this.recipientComboBox = new Kwf.Form.ComboBox({
                    fieldLabel: trlKwf('Subscriber'),
                    store: {
                        url: this.subscribersControllerUrl + '/json-data'
                    },
                    disabled: this.disableComboBox,
                    baseParams: this.baseParams,
                    minChars: 0,
                    width: 200,
                    maxHeight: 350,
                    listWidth: 280,
                    displayField: 'email',
                    pageSize: 10,
                    typeAhead: true,
                    triggerAction: 'all',
                    emptyText: trlKwf('Select an subscriber...'),
                    selectOnFocus: true,
                    forceSelection: true,
                    loadingText: trlKwf('Searching...'),
                    value: data.recipientId,
                    tpl: new Ext.XTemplate(
                        '<tpl for=".">',
                            '<div class="x-combo-list-item changeuser-list-item">',
                                '<h3>{lastname}&nbsp;{firstname}</h3>',
                                '{email}',
                            '</div>',
                        '</tpl>'
                    ),
                    listeners: {
                        select: function(combo, record, index) {
                            this.applyBaseParams({
                                recipientId: record.data.id
                            });
                            this.html = false; this.text = false;
                            this.load();
                        },
                        scope: this
                    }
                }, this);
                this.getTopToolbar().addField(this.recipientComboBox);
            },
            scope: this
        });
    },

    toggleButton : function(button, pressed)
    {
        if (pressed) {
            if (button.name == 'html') {
                this.body.dom.innerHTML = this.html;
            } else {
                this.body.dom.innerHTML = this.text;
            }
        }
    }
});
Ext.reg('kwc.newsletter.detail.preview', Kwc.Newsletter.Detail.PreviewPanel);
