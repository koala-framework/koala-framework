Ext2.ns('Kwc.Newsletter.Detail');
Kwc.Newsletter.Detail.PreviewPanel = Ext2.extend(Kwf.Binding.AbstractPanel, {
    border: false,
    autoScroll: true,
    bodyCssClass: 'mailPreviewPanel',
    button: [],
    sources: [],
    html: false,
    text: false,
    disableComboBox: false,

    initComponent : function()
    {
        this.button['html'] = new Ext2.Toolbar.Button ({
            icon    : '/assets/silkicons/html.png',
            cls     : 'x2-btn-text-icon',
            text    : trlKwf('HTML'),
            enableToggle: true,
            toggleGroup: 'format',
            pressed : true,
            toggleHandler: this.toggleButton,
            scope: this,
            name : 'html'
        });
        this.button['text'] = new Ext2.Toolbar.Button ({
            icon    : '/assets/silkicons/page_white_text.png',
            cls     : 'x2-btn-text-icon',
            text    : trlKwf('Text'),
            enableToggle: true,
            toggleGroup: 'format',
            pressed : false,
            toggleHandler: this.toggleButton,
            scope: this,
            name: 'text'
        });

        this.sendButton = new Ext2.Toolbar.Button ({
            icon    : '/assets/silkicons/email_go.png',
            cls     : 'x2-btn-text-icon',
            text    : trlKwf('Send'),
            handler : function(a, b, c) {
                Ext2.Ajax.request({
                    url : this.controllerUrl + '/json-send-mail',
                    mask: this.el,
                    maskText: trlKwf('Sending...'),
                    params: Ext2.apply(this.baseParams, {
                        address: this.addressField.getValue(),
                        format: this.button['html'].pressed ? 'html' : 'text'
                    }),
                    success: function(response, options, r) {
                        Ext2.MessageBox.alert(trlKwf('Status'), r.message);
                    },
                    scope: this
                });
            },
            scope   : this
        });

        this.addressField = new Ext2.form.TextField({
            width: 200,
            emptyText: trlKwf('Send testmail to...'),
            value: this.authedUserEmail,
            vtype: 'email'
        });

        for (var key in this.recipientSources) {
            if (this.recipientSources[key].title) {
                this.sources.push([key, this.recipientSources[key].title]);
            } else {
                this.sources.push([key, this.recipientSources[key].model]);
            }
        }

        this.subscribeModelComboBox = new Kwf.Form.ComboBox({
            triggerAction: 'all',
            editable: false,
            width: 200,
            maxHeight: 350,
            listWidth: 280,
            emptyText: trlKwf('Select an subscriber pool...'),
            store: {
                data: this.sources
            },
            listeners: {
                select: function(combo, record, index) {
                    var object = { subscribeModelKey: record.data.id };
                    Ext2.apply(this.baseParams, object);
                    this.recipientComboBox.setFormBaseParams(object);
                    if (this.recipientComboBox.disabled) this.recipientComboBox.enable();
                    delete this.recipientComboBox.lastQuery;
                },
                scope: this
            }
        });

        if (this.sources.length == 1) {
            this.subscribeModelComboBox.hide();
        } else {
            this.disableComboBox = true;
        }

        this.recipientComboBox = new Kwf.Form.ComboBox({
            fieldLabel: trlKwf('Subscriber'),
            store: {
                url: this.recipientsControllerUrl + '/json-data'
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
            tpl: new Ext2.XTemplate(
                '<tpl for=".">',
                    '<div class="x2-combo-list-item changeuser-list-item">',
                        '<h3>{lastname:htmlEncode}&nbsp;{firstname:htmlEncode}</h3>',
                        '{email:htmlEncode}',
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

        this.tbar = [this.button['html'], this.button['text'], '-', trlKwf('Send testmail to:'), this.addressField, this.sendButton, '-', this.subscribeModelComboBox, this.recipientComboBox];
        Kwc.Newsletter.Detail.PreviewPanel.superclass.initComponent.call(this);
    },

    applyBaseParams : function(baseParams) {
        Kwc.Newsletter.Detail.PreviewPanel.superclass.applyBaseParams.call(this, baseParams);
        var object = {
            newsletterId: this.baseParams.componentId.substr(this.baseParams.componentId.lastIndexOf('_')+1)
        };
        if (this.sources.length == 1) {
            for (var key in this.recipientSources) {
                object.subscribeModelKey = key;
            }
        }
        Ext2.apply(this.baseParams, object);
    },

    load: function(params, options) {
        this.body.dom.style.backgroundColor = '#FFFFFF';
        this.body.dom.innerHTML = '';
        Ext2.Ajax.request({
            url: this.controllerUrl + '/json-data',
            params:  this.baseParams,
            mask: this.el,
            success: function(r, options, data) {
                this.recipientComboBox.setValue(data.recipientId);
                this.html = data.html;
                this.text = data.text;
                this.body.dom.innerHTML = this.html;
                this.button[data.format].toggle(true);
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
Ext2.reg('kwc.newsletter.detail.preview', Kwc.Newsletter.Detail.PreviewPanel);
