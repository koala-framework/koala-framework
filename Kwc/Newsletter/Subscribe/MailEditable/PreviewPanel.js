Ext2.ns('Kwc.Newsletter.Subscribe.MailEditable');
Kwc.Newsletter.Subscribe.MailEditable.PreviewPanel = Ext2.extend(Kwf.Binding.AbstractPanel, {
    border: false,
    autoScroll: true,
    bodyCssClass: 'mailPreviewPanel',
    button: [],
    html: false,
    text: false,

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

        this.tbar = [this.button['html'], this.button['text']];
        Kwc.Newsletter.Subscribe.MailEditable.PreviewPanel.superclass.initComponent.call(this);
    },

    load: function(params, options) {
        this.body.dom.style.backgroundColor = '#FFFFFF';
        this.body.dom.innerHTML = '';
        Ext2.Ajax.request({
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
Ext2.reg('Kwc.Newsletter.Subscribe.MailEditable.preview', Kwc.Newsletter.Subscribe.MailEditable.PreviewPanel);
