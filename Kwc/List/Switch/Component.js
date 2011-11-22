Ext.namespace('Kwc.List.Switch');

Kwc.List.Switch.Component = Ext.extend(Kwf.EyeCandy.List,
{
    //transition: {},
    showArrows: true,

    defaultState: 'normal',

    childSelector: '.listSwitchItem',

    _init: function() {
        this.states = [
            'normal'
        ];

        this.plugins = [
            new Kwf.EyeCandy.List.Plugins.ActiveListener.LargeContentAjax({
                largeContainerSelector: '.listSwitchLargeContent',
                transition: this.transition.type,
                transitionConfig: this.transition
            }),
            new Kwf.EyeCandy.List.Plugins.ActiveChanger.Click({
            }),
            new Kwf.EyeCandy.List.Plugins.ActiveChanger.DefaultActiveClass({
            })
        ];
        if (this.showArrows) {
            this.plugins.push(new Kwf.EyeCandy.List.Plugins.ActiveChanger.NextPreviousLinks({
            }));
        }
        if (this.showPlayPause) {
            this.plugins.push(new Kwf.EyeCandy.List.Plugins.ActiveChanger.PlayPauseLink({
                autoPlay: this.autoPlay
            }));
        }

        Kwc.List.Switch.Component.superclass._init.call(this);
    }
});
