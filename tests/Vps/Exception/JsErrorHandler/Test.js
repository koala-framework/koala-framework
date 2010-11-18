if (Vps.Debug.displayErrors) { //für lokal extra initialisieren
    Ext.ux.ErrorHandler.init();
    Vps.Debug.displayErrors = false;
}

function testError1() {
    var test = null;
    test.arg = 5;
}

function testError2() {
    throw (new Error("Hello"));
}

function testError3() { //no stacktrace avaliable
    throw "Hello again";
}

function testError4() { //no stacktrace avaliable
    throw {
            myMessage: "stuff",
            customProperty: 5,
            anArray: [1, 2, 3]
    };
}
function testError5() {
    try {
            var test2 = null;
            test2.arg = 5;
    } catch(e) {
            Ext.ux.ErrorHandler.handleError(e);
    }
}
function testError6() {
    try {
            throw (new Error("Goodbye"));
    } catch(e) {
            Ext.ux.ErrorHandler.handleError(e);
    }
}
function testError7() {
    try {
            throw "Goodbye again";
    } catch(e) {
            Ext.ux.ErrorHandler.handleError(e);
    }
}
function testError8() {
    try {
            throw {
                    myMessage: "stuff",
                    customProperty: 5,
                    anArray: [1, 2, 3]
            };
    } catch(e) {
            Ext.ux.ErrorHandler.handleError(e);
    }
}
