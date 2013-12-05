Ext4.define('Kwf.Ext4.Data.StoreSyncQueue', {
    constructor: function()
    {
        this._queue = [];
        this._running = [];
        this.exceptions = [];
        this.hasException = false;
        this.isRunning = false;
    },

    start: function(startOptions)
    {
        this._startOptions = startOptions;
        this.isRunning = true;
        Ext4.each(this._queue, function(i) {
            this._startSync(i.store, i.options);
        }, this);
        this._queue.length = 0;

        if (!this._running.length) {
            this._callbackFinished();
        }
    },

    /**
     * Add a store that should be synced to the queue. If sync is already running start sync immediately.
     */
    add: function(store, options)
    {
        options = options || {};
        if (this.isRunning) {
            this._startSync(store, options);
        } else {
            this._queue.push({store: store, options: options});
        }
    },

    _startSync: function(store, options)
    {
        if (store.getNewRecords().length || store.getUpdatedRecords().length || store.getRemovedRecords().length) {
            this._running.push(store);
            store.sync({
                store: store,
                queueOptions: options,
                callback: function(batch, options) {
                    var store = options.store;
                    this._running.splice(this._running.indexOf(store), 1);
                    this._callback(batch.hasException, batch, options);
                    if (!this._running.length) {
                        this._callbackFinished();
                    }
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
    },

    _callbackFinished: function()
    {
        this.isRunning = false;
        if (this.hasException) {
            if (this._startOptions.failure) this._startOptions.failure.call(this._startOptions.scope || this, this);
        } else {
            if (this._startOptions.success) this._startOptions.success.call(this._startOptions.scope || this, this);
        }
        if (this._startOptions.callback) this._startOptions.callback.call(this._startOptions.scope || this, this);
    }
});
