var observers = [];

var DataLayer = {};
DataLayer.onPush = function (fn) {
    observers.push(fn);
};

DataLayer.push = function (data) {
    observers.forEach(function (c) {
        c.call(this, data);
    }, this);
};

module.exports = DataLayer;
