// Kopiert von swfupload/plugins/swfupload.swfobject.js, weil darin das 
// swfobject 2.1 eingebunden ist, wir aber schon lokal swfobject einbinden
var SWFUpload;
if (typeof(SWFUpload) === "function") {
    SWFUpload.onload = function () {};

    swfobject.addDomLoadEvent(function () {
        if (typeof(SWFUpload.onload) === "function") {
            SWFUpload.onload.call(window);
        }
    });

    SWFUpload.prototype.initSettings = (function (oldInitSettings) {
        return function () {
            if (typeof(oldInitSettings) === "function") {
                oldInitSettings.call(this);
            }

            this.ensureDefault = function (settingName, defaultValue) {
                this.settings[settingName] = (this.settings[settingName] == undefined) ? defaultValue : this.settings[settingName];
            };

            this.ensureDefault("minimum_flash_version", "9.0.28");
            this.ensureDefault("swfupload_pre_load_handler", null);
            this.ensureDefault("swfupload_load_failed_handler", null);

            delete this.ensureDefault;

        };
    })(SWFUpload.prototype.initSettings);


    SWFUpload.prototype.loadFlash = function (oldLoadFlash) {
        return function () {
            var hasFlash = swfobject.hasFlashPlayerVersion(this.settings.minimum_flash_version);

            if (hasFlash) {
                this.queueEvent("swfupload_pre_load_handler");
                if (typeof(oldLoadFlash) === "function") {
                    oldLoadFlash.call(this);
                }
            } else {
                this.queueEvent("swfupload_load_failed_handler");
            }
        };

    }(SWFUpload.prototype.loadFlash);

    SWFUpload.prototype.displayDebugInfo = function (oldDisplayDebugInfo) {
        return function () {
            if (typeof(oldDisplayDebugInfo) === "function") {
                oldDisplayDebugInfo.call(this);
            }

            this.debug(
                [
                    "SWFUpload.SWFObject Plugin settings:", "\n",
                    "\t", "minimum_flash_version:                      ", this.settings.minimum_flash_version, "\n",
                    "\t", "swfupload_pre_load_handler assigned:     ", (typeof(this.settings.swfupload_pre_load_handler) === "function").toString(), "\n",
                    "\t", "swfupload_load_failed_handler assigned:     ", (typeof(this.settings.swfupload_load_failed_handler) === "function").toString(), "\n",
                ].join("")
            );
        };
    }(SWFUpload.prototype.displayDebugInfo);
}
