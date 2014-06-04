Ext4.define('Kwf.Ext4.Data.StoreSyncQueue', {
    mixins: {
        observable: 'Ext.util.Observable'
    },
    constructor: function()
    {
        this.mixins.observable.constructor.call(this);

        this._queue = [];
        this.exceptions = [];
        this.hasException = false;
        this.isRunning = false;
    },

    start: function(startOptions)
    {
        this._startOptions = startOptions || {};
        this.isRunning = true;
        this._syncNext();
    },

    _syncNext: function()
    {
        var i = this._queue.shift();
        this._startSync(i.store, i.options);
    },

    /**
     * Add a store that should be synced to the queue.
     */
    add: function(store, options)
    {
        options = options || {};
        this._queue.push({store: store, options: options});
    },

    _startSync: function(store, options)
    {
        if (store.getNewRecords().length || store.getUpdatedRecords().length || store.getRemovedRecords().length) {
            store.sync({
                store: store,
                queueOptions: options,
                callback: function(batch, options) {
                    var store = options.store;
                    this._callback(batch.hasException, batch, options);
                },
                scope: this
            });
        } else {
            this._callback(false, {}, {
                queueOptions: options,
                store: store
            });
        }
    },

    _callback: function(hasException, batch, options)
    {
        var store = options.store;
        var qo = options.queueOptions;
        if (qo.callback) qo.callback.call(qo.scope || this, batch, qo);

        if (hasException) {
            if (qo.failure) qo.failure.call(qo.scope || this, batch, qo);
            this.exceptions = Ext4.Array.merge(this.exceptions, batch.exceptions);
            this.hasException = true;
        } else {
            if (qo.success) qo.success.call(qo.scope || this, batch, qo);
        }

        if (this._queue.length) {
            this._syncNext();
        } else {
            this._callbackFinished();
        }
    },

    _callbackFinished: function()
    {
        this.isRunning = false;
        if (this.hasException) {
            if (this._startOptions.failure) this._startOptions.failure.call(this._startOptions.scope || this, this);
            this.fireEvent('failure', this);
        } else {
            if (this._startOptions.success) this._startOptions.success.call(this._startOptions.scope || this, this);
            this.fireEvent('success', this);
        }
        if (this._startOptions.callback) this._startOptions.callback.call(this._startOptions.scope || this, this);
        this.fireEvent('finished', this);
    }
});
