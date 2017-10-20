Ext2.namespace("Kwf.MaintenanceJobs");
Kwf.MaintenanceJobs.Index = Ext2.extend(Ext2.Panel, {
    initComponent: function() {
        var form = new Kwf.Auto.FormPanel({
            controllerUrl: KWF_BASE_URL+'/kwf/maintenance-jobs/run',
            region: 'center'
        });
        var runsGrid = new Kwf.Auto.GridPanel({
            controllerUrl: KWF_BASE_URL+'/kwf/maintenance-jobs/runs',
            region: 'center',
            bindings: [form]
        });

        var jobsGrid = new Kwf.Auto.GridPanel({
            controllerUrl: KWF_BASE_URL+'/kwf/maintenance-jobs/jobs',
            region: 'north',
            split: true,
            height: 300,
            bindings: [{
                item: runsGrid,
                queryParam: 'job'
            }]
        });

        this.layout = 'border';
        this.items = [{
            layout: 'border',
            width: 800,
            split: true,
            region: 'west',
            items: [jobsGrid, runsGrid]
        }, form ];

        Kwf.MaintenanceJobs.Index.superclass.initComponent.call(this);
    }
});
