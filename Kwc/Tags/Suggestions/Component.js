var onReady = require('kwf/on-ready');
var trlKwf = require('kwf/trl').trlKwf;

onReady.onContentReady(function(el) {
    Ext2.query('.kwcTagsSuggestions', el).each(function(el) {
        if (el.initDone) return;
        el.initDone = true;
        el = Ext2.get(el);
        var config = Ext2.decode(el.child('input.config').getValue());
        el.child('form').on('submit', function(ev) {
            ev.stopEvent();
            var tag = el.child('input[name="tag"]').getValue();
            tag = Ext2.util.Format.trim(tag);
            if (tag && tag != trlKwf('Enter tag...')) {
                Ext2.Ajax.request({
                    url: config.controllerUrl+'/json-suggest',
                    params: {
                        tag: tag,
                        componentId: config.componentId
                    },
                    success: function(request, options, response) {
                        el.child('input[name="tag"]').dom.value = '';
                        el.findParent('.kwcTags', 50, true).child('.tags').update(response.tags);
                        if(el.errorEl) {
                            el.errorEl.fadeOut();
                        }
                    },
                    scope: this
                });
            } else {
                if(!el.errorEl) {
                    el.errorEl = el.child('.kwfField').createChild({
                        cls: 'kwfFieldErrorIconBubble'
                    });
                    el.errorEl.createChild({
                        cls: 'message'
                    });
                    el.errorEl.child('.message').enableDisplayMode('block');
                    el.errorEl.child('.message').hide();
                    el.errorEl.alignTo(el.child('input[name="tag"]'), 'tr', [-20, 4]);
                    el.errorEl.enableDisplayMode('block');
                    el.errorEl.hide();
                    Kwf.Event.on(el.errorEl, 'mouseEnter', function() {
                        this.errorEl.child('.message').fadeIn({duration: 0.4});
                    }, el);
                    Kwf.Event.on(el.errorEl, 'mouseLeave', function() {
                        this.errorEl.child('.message').fadeOut({duration: 0.2});
                    }, el);
                }
                el.errorEl.child('.message').update(trlKwf('Please enter a tag.'));
                el.errorEl.clearOpacity();
                el.errorEl.fadeIn({
                    endOpacity: 0.8 //TODO read from css (but that's hard for IE)
                });
                return;
            }
        }, this);
    });
});
