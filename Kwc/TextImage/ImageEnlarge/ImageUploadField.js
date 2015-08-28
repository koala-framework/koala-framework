Ext2.namespace('Kwc.TextImage.ImageEnlarge');
Kwc.TextImage.ImageEnlarge.ImageUploadField = Ext2.extend(Kwc.Basic.ImageEnlarge.ImageUploadField, {

    afterRender: function() {
        Kwc.TextImage.ImageEnlarge.ImageUploadField.superclass.afterRender.call(this);
        var actionField = this._findActionCombobox();
        actionField.on('changevalue', function (combo, value, index) {
            this._checkForImageTooSmall();
        }, this);
    },

    _findActionCombobox: function () {
        var actionSelectFields = this.findParentBy(function (component, container){
            if (component.identifier == 'kwc-basic-imageenlarge-form') {
                return true;
            }
            return false;
        }, this).findBy(function (component, container) {
                                                                    //TODO why isn't the first one not enough? Kwc.Basic.LinkTag.ComboBox inherits Kwc.Abstract.Cards.ComboBox!
            if (component instanceof Kwc.Abstract.Cards.ComboBox || component instanceof Kwc.Basic.LinkTag.ComboBox) {
                return true;
            }
            return false;
        }, this);
        return actionSelectFields[0];
    },

    _isValidateImageTooSmallUsingImageEnlargeDimensions: function () {
        // check if dropdown has selected imageenlarge
        var actionField = this._findActionCombobox();
        var action = actionField.defaultValue;
        if (actionField.getValue()) {
            action = actionField.getValue();
        }
        return action == 'enlarge';
    }
});

Ext2.reg('kwc.textimage.imageenlarge.imageuploadfield', Kwc.TextImage.ImageEnlarge.ImageUploadField);
