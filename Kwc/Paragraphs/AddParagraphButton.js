Vpc.Paragraphs.AddParagraphButton = Ext.extend(Ext.Button, {
    text : trlVps('Add Paragraph'),
    icon : '/assets/vps/images/paragraphAdd.png',
    cls  : 'x-btn-text-icon',
    initComponent: function() {
        this.addEvents('addParagraph');
        var buildMenu = function(components, addToItem)
        {
            if (components.length == 0) { return; }
            for (var i in components) {
                if (typeof components[i] == 'string') {
                    addToItem.addItem(
                        new Ext.menu.Item({
                            component: components[i],
                            text: i,
                            handler: function(menu) {
                                this.fireEvent('addParagraph', menu.component);
                            },
                            icon: this.componentIcons[components[i]],
                            scope: this
                        })
                    );
                } else {
                    var item = new Ext.menu.Item({text: i, menu: []});
                    addToItem.addItem(item);
                    buildMenu.call(this, components[i], addToItem.items.items[addToItem.items.length - 1].menu);
                }
            }
        };
        this.menu = new Ext.menu.Menu();
        buildMenu.call(this, this.components, this.menu);

        Vpc.Paragraphs.AddParagraphButton.superclass.initComponent.call(this);
    }
});
