/**
 * Standard Blocks Auswahl
 *
 * Wenn möglich sollte echte Text-Komponente verwendet werden
 *
 * (ungetestet, nicht in dependencies, nicht als enableBlock setting in HtmlEditor)
 */
Vps.Form.HtmlEditor.FormatBlock = Ext.extend(Ext.util.Observable, {
    formatBlocks : {
        'h1': 'Heading 1',
        'h2': 'Heading 2',
        'h3': 'Heading 3',
        'h4': 'Heading 4',
        'h5': 'Heading 5',
        'h6': 'Heading 6',
        'p': 'Normal',
        'address': 'Address',
        'pre': 'Formatted'
    },
    init: function(cmp){
        this.cmp = cmp;
        this.cmp.on('afterCreateToolbar', this.afterCreateToolbar, this);
        this.cmp.afterMethod('updateToolbar', this.updateToolbar, this);
    },
    // private
    afterCreateToolbar: function(tb) {

        this.blockSelect = tb.el.createChild({
            tag:'select',
            cls:'x-font-select',
            html: this.createBlockOptions()
        });
        this.blockSelect.on('change', function(){
            var v = this.blockSelect.dom.value;
            if (Ext.isIE) {
                v = '<'+v+'>';
            }
            this.cmp.relayCmd('formatblock', v);
            this.cmp.deferFocus();
        }, this);
        tb.insert(0, this.blockSelect.dom);
        tb.insert(1, '-');
    },

    createBlockOptions : function(){
        var buf = [];
        for (var i in this.formatBlocks) {
            buf.push(
                '<option value="',i,'"',
                    (i == 'p' ? ' selected="true">' : '>'),
                    this.formatBlocks[i],
                '</option>'
            );
        }
        return buf.join('');
    },

    // private
    updateToolbar: function() {
        if (Ext.isIE) {
            var selectedBlock = false;
            var el = this.cmp.getFocusElement();
            while (el) {
                for(var i in this.formatBlocks) {
                    if (el.tagName && i == el.tagName.toLowerCase()) {
                        if(i != this.blockSelect.dom.value){
                            this.blockSelect.dom.value = i;
                        }
                        selectedBlock = true;
                        break;
                    }
                }
                if (selectedBlock) break;
                el = el.parentNode;
            }
            if (!selectedBlock) {
                if('p' != this.blockSelect.dom.value){
                    this.blockSelect.dom.value = 'p';
                }
            }
        } else {
            var name = (this.cmp.doc.queryCommandValue('FormatBlock')||'p').toLowerCase();
            if(name != this.blockSelect.dom.value){
                this.blockSelect.dom.value = name;
            }
        }
    }
});