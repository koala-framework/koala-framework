Vps.Form.HtmlEditor.BreadCrumbs = Ext.extend(Ext.util.Observable, {
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.afterMethod('afterRender', this.afterRender, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
    },

    afterRender: function() {
        this.el = this.cmp.wrap.createChild({
            class: 'x-toolbar vps-htmleditor-breadcrumbs'
        });
        this.el.on('click', function(e, target) {
            e.stopEvent();
            e.preventDefault();
            if (target.tagName.toLowerCase() == 'a') {
                var num = 0;
                this.el.query('a').forEach(function(el) {
                    if (el == target) {
                        this.cmp.tinymceEditor.selection.select(this.getItems()[num]);
                    }
                    num++;
                }, this);
            }
        }, this);
    },
    getItems: function() {
        var parents = this.cmp.getParents();
        parents.push(this.cmp.getDoc().body);
        return parents.reverse();
    },
    updateToolbar: function() {
        var html = [];
        this.getItems().each(function(el) {
            var i = el.tagName.toLowerCase();
            if (el.className && el.tagName.toLowerCase() != 'body') {
                i += '.' + el.className;
            }
            html.push(' <a href="#">' + i + '</a> ');
        });
        this.el.update(html.join(' » '));
    },
});