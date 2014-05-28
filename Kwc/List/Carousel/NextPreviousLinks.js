Ext2.namespace("Kwc.List.Carousel");
Kwc.List.Carousel.NextPreviousLinks = Ext2.extend(Kwf.EyeCandy.List.Plugins.ActiveChanger.NextPreviousLinks, {
    onPrevious: function() {
        if (!this.list.getActiveChangeLocked()) {
            Kwc.List.Carousel.NextPreviousLinks.superclass.onPrevious.apply(this, arguments);
        }
    },
    onNext: function() {
        if (!this.list.getActiveChangeLocked()) {
            Kwc.List.Carousel.NextPreviousLinks.superclass.onNext.apply(this, arguments);
        }
    }
});
