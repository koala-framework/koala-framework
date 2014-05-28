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
                } else {
                    var item = new Ext2.menu.Item({text: i.replace(/\>\>/, ''), menu: []});
                    addToItem.addItem(item);
                    buildMenu.call(this, components[i], addToItem.items.items[addToItem.items.length - 1].menu);
                }
            }
        };
        this.menu = new Ext2.menu.Menu();
        buildMenu.call(this, this.components, this.menu);

        Kwc.Paragraphs.AddParagraphButton.superclass.initComponent.call(this);
    }
});
