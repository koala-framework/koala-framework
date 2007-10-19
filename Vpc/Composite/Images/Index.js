Ext.namespace('Vpc.Composite.Images');
Vpc.Composite.Images.Index = Ext.extend(Ext.Panel,
{
    initComponent: function()
    {
        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            width: 300,
            split: true,
            region: 'west'
        });

        this.imagePanel = new Vps.Auto.FormPanel({
            region: 'center',
            autoload: false
        });

        this.imagePanel.on('datachange', function(i) {
            this.grid.reload();
        }, this);

        this.grid.on('deleterow', function() {
            this.grid.getGrid().getSelectionModel().selectPrevious();
        }, this);

        this.grid.on('rowselect', function(model, rowIndex, selected) {
            var controllerUrl = this.controllerUrl.substr(0, this.controllerUrl.length - 1);
            controllerUrl = controllerUrl.replace(/Vpc_Composite_Images_Index/, 'Vpc_Composite_Images_Edit');
            controllerUrl = controllerUrl + '-' + selected.data.id + '/';
            this.imagePanel.loadForm(controllerUrl);
        }, this);

        this.grid.onAdd = this.onAdd;
        this.layout = 'border';
        this.items = [this.grid, this.imagePanel];
        Vpc.Composite.Images.Index.superclass.initComponent.call(this);
    },

    onAdd : function()
    {
        Ext.Ajax.request({
            mask: true,
            url: this.controllerUrl + 'jsonInsert',
            success: function(response, options, r) {
                var result = Ext.decode(response.responseText);
                id = result.id;
                this.getSelectionModel().clearSelections();
                this.reload({
                    callback: function(o, r, s) {
                        this.getSelectionModel().selectLastRow();
                    },
                    scope: this
                });
            },
            scope: this
        });
    }

});