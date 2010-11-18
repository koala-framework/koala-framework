Vps.onContentReady(function()
{
    var tooltipWrappers = Ext.query('.tooltipWrapper');
    Ext.each(tooltipWrappers, function(tooltipWrapper) {
        tooltipWrapper = Ext.get(tooltipWrapper);
        var contentHtml = tooltipWrapper.down('.tooltipContent').dom.innerHTML;
        var tooltipActivator = tooltipWrapper.down('.tooltipActivator');

        new Ext.ToolTip({
            target: tooltipActivator,
            html: contentHtml,
            dismissDelay:0,
            showDelay:100
        });
    });
});
