Ext.namespace('Vpc.Composite.TextImages');
Vpc.Composite.TextImages.Index = Ext.extend(Ext.Panel,
{
    initComponent: function()
    {
        this.layout = 'border';
        this.grid = new Vps.Auto.GridPanel({
            controllerUrl: this.controllerUrl,
            width: 300,
            split: true,
            region: 'west'
        });

        var image = new Ext.Panel({
            region: 'center',
            html: 'foo'
        })

        this.grid.on('rendergrid', function() {
            this.grid.getGrid().on('rowclick', function(grid, index) {
                var selected = this.grid.getGrid().getSelectionModel().getSelected();
                if (selected) {
                    var controllerUrl = this.controllerUrl.substr(0, this.controllerUrl.length - 1);
                    controllerUrl = controllerUrl.replace(/Vpc_Composite_TextImages_Images/, 'Vpc_Composite_TextImages_ImagesEdit');
                    controllerUrl = controllerUrl + '-' + selected.data.id + '/';
                    var image = new Vps.Auto.FormPanel({
                        controllerUrl: controllerUrl,
                        region: 'center'
                    });
                    this.remove(this.layout.center.panel);
                    this.add(image);
                    this.layout.rendered = false;
                    this.doLayout();
                }
            }, this);
        }, this)

        this.items = [this.grid, image];

        Vpc.Composite.TextImages.Index.superclass.initComponent.call(this);
    }
});