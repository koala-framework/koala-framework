var onReady = require('kwf/commonjs/on-ready');
var t = require('kwf/commonjs/trl');

onReady.onContentReady(function(el) {
    Ext2.query('.kwcClass', el).each(function(el) {
        if (el.initDone) return;
        el.initDone = true;
        el = Ext2.get(el);
        var config = Ext2.decode(el.child('input.config').getValue());
        el.child('form').on('submit', function(ev) {
            ev.stopEvent();
            var tag = el.child('input[name="tag"]').getValue();
            tag = Ext2.util.Format.trim(tag);
            if (tag && tag != t.trlKwf('Enter tag...')) {
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
                    el.errorEl = el.child('.kwfUp-kwfField').createChild({
                        cls: 'kwfUp-kwfFieldErrorIconBubble'
                    });
                    el.errorEl.createChild({
                        cls: 'kwfUp-message'
                    });
                    el.errorEl.child('.kwfUp-message').enableDisplayMode('block');
                    el.errorEl.child('.kwfUp-message').hide();
                    el.errorEl.alignTo(el.child('input[name="tag"]'), 'tr', [-20, 4]);
                    el.errorEl.enableDisplayMode('block');
                    el.errorEl.hide();
                    Kwf.Event.on(el.errorEl, 'mouseEnter', function() {
                        this.errorEl.child('.kwfUp-message').fadeIn({duration: 0.4});
                    }, el);
                    Kwf.Event.on(el.errorEl, 'mouseLeave', function() {
                        this.errorEl.child('.kwfUp-message').fadeOut({duration: 0.2});
                    }, el);
                }
                el.errorEl.child('.kwfUp-message').update(t.trlKwf('Please enter a tag.'));
                el.errorEl.clearOpacity();
                el.errorEl.fadeIn({
                    endOpacity: 0.8 //TODO read from css (but that's hard for IE)
                });
                return;
            }
        }, this);
    });
});
