/**
 * Based on ext4/examples/portal/
 *
 * A {@link Ext.panel.Panel Panel} class used for providing drag-drop-enabled portal layouts.
 */
Ext4.define('Kwf.Ext4.Portal.Panel', {
    extend: 'Ext.panel.Panel',
    alias: 'widget.portalpanel',

    requires: [
        'Ext.layout.container.Column',

        'Kwf.Ext4.Portal.DropZone',
        'Kwf.Ext4.Portal.Column'
    ],

    cls: 'x-portal',
    bodyCls: 'x-portal-body',
    defaultType: 'portalcolumn',
    autoScroll: true,
    stateEvents: ['drop'],

    manageHeight: false,

    initComponent : function() {
        var me = this;

        // Implement a Container beforeLayout call from the layout to this Container
        this.layout = {
            type : 'column'
        };
        this.callParent();

        this.addEvents({
            validatedrop: true,
            beforedragover: true,
            dragover: true,
            beforedrop: true,
            drop: true
        });
    },

    // Set columnWidth, and set first and last column classes to allow exact CSS targeting.
    beforeLayout: function() {
        var items = this.layout.getLayoutItems(),
            len = items.length,
            firstAndLast = ['x-portal-column-first', 'x-portal-column-last'],
            i, item, last;

        for (i = 0; i < len; i++) {
            item = items[i];
            item.columnWidth = 1 / len;
            last = (i == len-1);

            if (!i) { // if (first)
                if (last) {
                    item.addCls(firstAndLast);
                } else {
                    item.addCls('x-portal-column-first');
                    item.removeCls('x-portal-column-last');
                }
            } else if (last) {
                item.addCls('x-portal-column-last');
                item.removeCls('x-portal-column-first');
            } else {
                item.removeCls(firstAndLast);
            }
        }

        return this.callParent(arguments);
    },

    // private
    initEvents : function(){
        this.callParent();
        this.dd = Ext4.create('Kwf.Ext4.Portal.DropZone', this, this.dropConfig);
    },

    // private
    beforeDestroy : function() {
        if (this.dd) {
            this.dd.unreg();
        }
        this.callParent();
    },

    //state handling, based on http://www.sencha.com/forum/showthread.php?278264-Stateful-portal-panel
    getState: function () {
        var portlets = this.query('portlet'), result = []
        for (var y = 0; y < portlets.length; y++) {
            var portalColumn = portlets[y].up('portalcolumn');
            result.push({
                portletId: portlets[y].getItemId(),
                porletColumnId: portalColumn.getItemId()
            });
        }
        return {
            portletsArray: result
        };
    },
    saveState: function () {
        var me = this, id = me.stateful && me.getStateId(), state;
        if (id) {
            state = me.getState() || [];
            Ext4.state.Manager.set(id, state);
        }
    },
    applyState: function (state) {
        var me = this;
        for (var i = 0; i < state.portletsArray.length; i++) {
            var porletStatedConfig = state.portletsArray[i]
                    , portalColumn = me.down('portalcolumn[itemId=' + porletStatedConfig.porletColumnId + ']')
                    , portlet = me.down('portlet[itemId=' + porletStatedConfig.portletId + ']');
            portalColumn.add(portlet);
        }
    }
});
