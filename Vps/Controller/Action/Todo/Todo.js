Ext.util.Format.todoStatusIcon = function(value, p, record, rowIndex, colIndex, store, column) {
    p.css += 'vps-cell-icon';
    if (value) {

        switch(value) {
            case 'open':
                p.attr += 'style="background-image:url(/assets/silkicons/flag_red.png);" ';
                p.attr += ' ext:qtip="'+trlVps('Open')+'"';
                break;
            case 'prod':
                p.attr += 'style="background-image:url(/assets/silkicons/flag_green.png);" ';
                p.attr += ' ext:qtip="'+trlVps('Done')+'"';
                break;
            case 'committed':
                p.attr += 'style="background-image:url(/assets/silkicons/flag_yellow.png);" ';
                p.attr += ' ext:qtip="'+trlVps('In progress')+'"';
                break;
            case 'test':
                p.attr += 'style="background-image:url(/assets/silkicons/flag_blue.png);" ';
                p.attr += ' ext:qtip="'+trlVps('Is being tested')+'"';
                break;
        }
    }
    return '';
};

Ext.namespace('Vps.Todo');

Vps.Todo.List = Ext.extend(Ext.Panel, {
    initComponent: function()
    {
        this.layout = 'border';
        this.items = new Vps.Auto.GridPanel({
            controllerUrl: '/vps/todo/todos',
            region: 'center',
            editDialog: new Vps.Todo.Overview()
        });

        Vps.Todo.List.superclass.initComponent.call(this);
    }
});


Vps.Todo.Overview = Ext.extend(Ext.Window,
{
    initComponent: function() {
        this.tpl = new Ext.XTemplate(
            '<p>'+trlVps('Title')+': {title}</p>',
            '<p>'+trlVps('Description')+': {description}</p>',
            '<p>'+trlVps('Estimated time')+': {estimated_time}</p>',
            '<p>'+trlVps('Create date')+': {create_date}</p>',
            '<p>'+trlVps('Deadline')+': {deadline}</p>'
        );
        this.width = 400;
        this.height = 400;
        this.title = trlVps('Todo Details');
        this.layout = 'border';
        this.closeAction = 'hide';
        this.modal = false;
        this.plain = true;
        this.content = new Ext.Panel({
            cls: 'vps-overview-panel',
            border: false,
            autoScroll: true,
            bodyStyle: 'padding: 10px;',
            region: 'center'
        });
        this.items = [ this.content ];

        Vps.Todo.Overview.superclass.initComponent.call(this);
    },

    showOverview : function(id) {
        this.currentId = id;
        Ext.Ajax.request({
            url: '/vps/todo/todos/json-overview-data',
            params: { id: id },
            success: function(response, options, r) {
                this.show();
                this.tpl.overwrite(this.content.body, r);
            },
            scope: this
        });
    },

    showEdit : function(id) {
        this.showOverview(id);
    }
});