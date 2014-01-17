/**
 * Based on ext4/examples/portal/
 *
 * A layout column class used internally be {@link Kwf.Ext4.Portal.Panel}.
 */
Ext4.define('Kwf.Ext4.Portal.Column', {
    extend: 'Ext.container.Container',
    alias: 'widget.portalcolumn',

    requires: [
        'Ext.layout.container.Anchor',
        'Kwf.Ext4.Portal.Portlet'
    ],

    layout: 'anchor',
    defaultType: 'portlet',
    cls: 'x-portal-column'

    // This is a class so that it could be easily extended
    // if necessary to provide additional behavior.
});
