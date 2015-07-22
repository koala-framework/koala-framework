var onReady = require('kwf/on-ready');

onReady.onContentReady(function()
{
    var tooltipWrappers = Ext2.query('.tooltipWrapper');
    Ext2.each(tooltipWrappers, function(tooltipWrapper) {
        tooltipWrapper = Ext2.get(tooltipWrapper);
        var contentHtml = tooltipWrapper.down('.tooltipContent').dom.innerHTML;
        var tooltipActivator = tooltipWrapper.down('.tooltipActivator');

        new Ext2.ToolTip({
            target: tooltipActivator,
            html: contentHtml,
            dismissDelay:0,
            showDelay:100
        });
    });
});
