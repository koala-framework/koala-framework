Ext.namespace('Vps.Auto.Grid');

Vps.Auto.Grid.ExportWindow = Ext.extend(Ext.Window,
{
    initComponent: function() {
        this.actions = { };
        this.actions.close = new Ext.Action({
            text    : trlVps('Close'),
            handler: function() {
                this.breakRecurse = true;
                this.close();
            },
            scope: this
        });

        this.breakRecurse = false;
        this.width = 400;
        this.height = 300;
        this.title = trlVps('Export');
        this.closable = false;
        this.layout = 'border';
        this.modal = true;
        this.plain = true;
        this.content = new Ext.Panel({
            cls: 'vps-export-panel',
            border: false,
            autoScroll: true,
            bodyStyle: 'padding: 10px;',
            region: 'center',
            html:'<h1>'+trlVps('Data Export')+'</h1>'
                +'<strong>'+trlVps('Current step')+'</strong>'
                +'<div id="exportStatusbarStep"></div>'
                +'<strong>'+trlVps('Overall')+'</strong>'
                +'<div id="exportStatusbarOverall"></div>'
                +'<div class="exportDownloadLinkWrapper">'
                +'<a id="exportDownloadLink" href="#" target="_blank">'+trlVps('Download export file')+'</a>'
                +'</div>'
        });
        this.items = [ this.content ];

        if (!this.buttons) this.buttons = [];
        this.buttons.push(this.actions.close);

        Vps.Auto.Grid.ExportWindow.superclass.initComponent.call(this);
    },

    showExport: function() {
        this.show();
        this.uniqueExportKey = Math.floor(Math.random() * 1000000000) + 1;
        this.pbarStep = new Ext.ProgressBar({
            text:' ',
            id:'pbarStep',
            cls:'left-align',
            renderTo:'exportStatusbarStep',
            animate: true
        });
        this.pbarOverall = new Ext.ProgressBar({
            text:' ',
            id:'pbarOverall',
            cls:'left-align',
            renderTo:'exportStatusbarOverall',
            animate: true
        });

        // generating the excel file
        this.doExportRequest();
    },

    doExportRequest: function() {
        Ext.Ajax.request({
            url: this.controllerUrl+'/json-xls?'+this.exportParams,
            params: { uniqueExportKey: this.uniqueExportKey },
            success: function(response, options, r) {
                var ret = Ext.decode(response.responseText);

                var excelSaved = 0;
                if (ret.status.collected != ret.status.count) {
                    var stat = ret.status.collected / ret.status.count;
                    var text = trlVps('Collecting data: {0} of {1} ({2}%)', [
                        ret.status.collected,
                        ret.status.count,
                        Math.floor((ret.status.collected / ret.status.count) * 100)
                    ]);
                } else if (ret.status.done != ret.status.count) {
                    var stat = ret.status.done / ret.status.count;
                    var text = trlVps('Creating Excel file: {0} of {1} ({2}%)', [
                        ret.status.done,
                        ret.status.count,
                        Math.floor((ret.status.done / ret.status.count) * 100)
                    ]);
                } else {
                    if (!ret.status.downloadkey || ret.status.downloadkey == '') {
                        var stat = 0;
                        var text = trlVps('Saving Excel file...');
                        excelSaved = 0;
                    } else {
                        var stat = 1;
                        var text = trlVps('Excel file saved');
                        excelSaved = 1;
                        document.getElementById('exportDownloadLink').style.display = 'inline';
                        Ext.EventManager.addListener('exportDownloadLink', 'click', function(ev, el, o) {
                            ev.stopEvent();
                            window.open(this.controllerUrl+'/download-export-file?downloadkey='+o.downloadkey);
                        }, this, ret.status);
                    }
                }

                this.pbarStep.updateProgress(stat, text);

                var statOverall = (ret.status.collected + ret.status.done + excelSaved) / ((ret.status.count * 2) + 1);
                this.pbarOverall.updateProgress(statOverall, Math.floor(statOverall * 100)+'%');

                if (!this.breakRecurse && (!ret.status.downloadkey || ret.status.downloadkey == '')) {
                    // recursing
                    this.doExportRequest();
                }
            },
            scope: this
        });
    }
});


