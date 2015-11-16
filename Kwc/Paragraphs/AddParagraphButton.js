Kwc.Paragraphs.AddParagraphButton = Ext2.extend(Ext2.Button, {
    text : trlKwf('Add Paragraph'),
    icon : '/assets/kwf/images/paragraphAdd.png',
    cls  : 'x2-btn-text-icon',
    initComponent: function() {
        this.addEvents('addParagraph');

        var buildMenu = function(components, addToItem)
        {
            if (components.length == 0) { return; }
            for (var i in components) {
                if (typeof components[i] == 'string') {
                    var contextsEquals = function(a, b) {
                        var ret = true;
                        for (var i in a) {
                            if (a[i] != b[i]) ret = false;
                        }
                        for (var i in b) {
                            if (a[i] != b[i]) ret = false;
                        }
                        return ret;
                    };
                    var componentSupported = true;
                    if (this.supportedMasterLayoutContexts[components[i]] && this.masterLayoutContexts) {
                        var supportedContexts = this.supportedMasterLayoutContexts[components[i]];
                        this.masterLayoutContexts.forEach(function(ctx) {
                            var foundMatch = false;
                            supportedContexts.forEach(function(i) {
                                if (contextsEquals(i, ctx)) {
                                    foundMatch = true;
                                }
                            }, this);
                            if (!foundMatch) componentSupported = false;
                        }, this);
                    }
                    if (componentSupported) {
                        addToItem.addItem(
                            new Ext2.menu.Item({
                                component: components[i],
                                text: i,
                                handler: function(menu) {
                                    this.fireEvent('addParagraph', menu.component);
                                },
                                icon: this.componentIcons[components[i]],
                                scope: this
                            })
                        );
                    }
                } else {
                    var item = new Ext2.menu.Item({text: i.replace(/\>\>/, ''), menu: []});
                    addToItem.addItem(item);
                    buildMenu.call(this, components[i], addToItem.items.items[addToItem.items.length - 1].menu);
                }
            }
        };

        this.menu = new Ext2.menu.Menu();
        this.menu.on('beforeshow', function() {
            //lazily build menu
            buildMenu.call(this, this.components, this.menu);
        }, this);
        this.menu.on('hide', function() {
            this.menu.removeAll();
        }, this);

        Kwc.Paragraphs.AddParagraphButton.superclass.initComponent.call(this);
    },

    setMasterLayoutContexts: function(masterLayoutContexts)
    {
        this.masterLayoutContexts = masterLayoutContexts;
    }
});
