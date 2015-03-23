var amdlc = require("amdlc");
amdlc.compile({
    baseDir: "vendor/bower_components/tinymce/js/tinymce/classes",
    rootNS: "tinymce",
    outputSource: "temp/tinymce-build-out.js",
    verbose: false,
    expose: "public",
    compress: false,
    moduleOverrides: {
        "tinymce/dom/Sizzle": "vendor/bower_components/tinymce/js/tinymce/classes/dom/Sizzle.jQuery.js"
    },

    from: [
        "dom/DomQuery.js",
        "EditorManager.js",
        "LegacyInput.js"
    ]
});
