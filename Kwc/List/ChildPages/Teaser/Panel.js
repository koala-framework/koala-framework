Ext2.namespace('Kwc.List.ChildPages.Teaser');
Kwc.List.ChildPages.Teaser.Panel = Ext2.extend(Kwc.Abstract.List.List, {
    showCopyPaste: false,
    initComponent: function() {
        Kwc.List.ChildPages.Teaser.Panel.superclass.initComponent.call(this);
        this.grid.getSelectedId = function() {
            var s = this.getSelected();
            if (s) return s.get('child_id');
            return null;
        };
    }
});
Ext2.reg('kwc.List.childPages.teaser', Kwc.List.ChildPages.Teaser.Panel);
