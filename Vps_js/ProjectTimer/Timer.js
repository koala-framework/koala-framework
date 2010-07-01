Ext.namespace("Vps.ProjectTimer");

Ext.util.Format.secondsToTimeProjectTimer = function(v, format, r, idx, id) {
    var ret = Ext.util.Format.secondsToTime(v, format, r, idx, id);

    if (parseInt(r.data.spent_time) >= parseInt(r.data.included_time)) {
        format.css += ' projectTimerRed';
    } else {
        format.css += ' projectTimerGreen';
    }

    return ret;
};

Vps.ProjectTimer.Index = Ext.extend(Ext.Panel, {

    initComponent: function() {
        var timerGrid = new Vps.Auto.GridPanel({
            controllerUrl: this.timerGridControllerUrl,
            region: 'west',
            split: true,
            width: 500,
            title: trlVps('Timer')
        });

        var yearsGrid = new Vps.Auto.GridPanel({
            controllerUrl: this.yearsGridControllerUrl,
            region: 'center',
            title: trlVps('Spent time by years'),
            cls: 'projectTimerYears'
        });

        this.layout = 'border';
        this.items = [ timerGrid, yearsGrid ];

        Vps.ProjectTimer.Index.superclass.initComponent.call(this);
    }
});
