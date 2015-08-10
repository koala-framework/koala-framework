var kwfNs = require('kwf/namespace');
var kwfExtend = require('kwf/extend');
var $ = require('jQuery');

$(function() {

kwfNs('Kwc.List.Switch');

Kwc.List.Switch.Component = kwfExtend(Kwf.EyeCandy.List,
{
    //transition: {},
    showArrows: true,

    defaultState: 'normal',

    childSelector: '> div > .listSwitchItem',

    _init: function() {
        this.states = [
            'normal'
        ];

        if (!this.plugins) this.plugins = [];

        this.plugins.push(new Kwf.EyeCandy.List.Plugins.ActiveListener.LargeContentAjax({
            largeContainerSelector: '.listSwitchLargeContent',
            transition: this.transition.type,
            transitionConfig: this.transition
        }));
        this.plugins.push(new Kwf.EyeCandy.List.Plugins.ActiveChanger.Click({}));
        this.plugins.push(new Kwf.EyeCandy.List.Plugins.ActiveChanger.DefaultActiveClass({}));
        if (this.showArrows) {
            this.plugins.push(new Kwf.EyeCandy.List.Plugins.ActiveChanger.NextPreviousLinks({
            }));
        }
        if (this.showPlayPause) {
            this.plugins.push(new Kwf.EyeCandy.List.Plugins.ActiveChanger.PlayPauseLink({
                autoPlay: this.autoPlay
            }));
        }
        if (this.useHistoryState) {
            this.plugins.push(new Kwf.EyeCandy.List.Plugins.ActiveChanger.HistoryState({
            }));
        }

        Kwc.List.Switch.Component.superclass._init.call(this);
    }
});

});
