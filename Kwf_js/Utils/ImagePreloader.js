Vps.Utils.ImagePreloader = function () {
    this._toLoadImages = [];
    this._loadingImages = [];
};

Vps.Utils.ImagePreloader.prototype =
{
    addImage: function(url) {
        this._toLoadImages.push(url);
    },

    addImages: function(urls) {
        for (var i = 0; i < urls.length; i++) {
            this.addImage(urls[i]);
        }
    },

    preload: function() {
        for (var i = 0; i < this._toLoadImages.length; i++) {
            var im = new Image();
            im.src = this._toLoadImages[i];
            this._loadingImages.push(im);
        }
    },

    getImagesAmount: function() {
        return this._toLoadImages.length;
    },

    getLoadedImagesAmount: function() {
        var ret = 0;
        for (var i = 0; i < this._loadingImages.length; i++) {
            if (this._loadingImages[i].complete) ret += 1;
        }
        return ret;
    },

    isComplete: function() {
        if (this.getLoadedImagesAmount() == this.getImagesAmount()) {
            return true;
        }
        return false;
    }
};
