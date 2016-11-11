App.Components.ModelHistoryComponent = Frontend.Component.extend({
    startup: function() {
        this._addHandlers();
    },
    _addHandlers: function() {
        $('[data-toggle=popover]').popover({
            trigger: 'hover',
            placement: 'top',
            container: 'body'
        });

        $('.model-history-area .load-next-history').off('click').on('click', function(e) {
            e.preventDefault();
            var $target = $(e.currentTarget);
            this.loadNextEntries($target.data('page'), $target);
        }.bind(this));

        $('.model-history-area .load-prev-history').off('click').on('click', function(e) {
            e.preventDefault();
            var $target = $(e.currentTarget);
            this.loadPrevEntries($target.data('page'), $target);
        }.bind(this));

        $('.model-history-comment form').off('submit').on('submit', this._onAddComment.bind(this));

        $('.model-history-filter form').off('submit').on('submit', this._onFilter.bind(this));
        $('.model-history-filter .reset-btn').off('click').on('click', this._onResetFilter.bind(this));
    },
    loadNextEntries: function(page, $element) {
        var page = page + 1;
        this._loadEntries(page, $element);
    },
    loadPrevEntries: function(page, $element) {
        var page = page - 1;
        if (page <= 0) {
            page = 1;
        }
        this._loadEntries(page, $element);
    },
    _loadEntries: function(page, $element) {
        var $parentWrapper = $element.parents('.model-history-area'),
            model = $parentWrapper.data('model'),
            foreignKey = $parentWrapper.data('foreignkey'),
            limit = $parentWrapper.data('limit'),
            url = {
                plugin: 'model_history',
                action: 'loadEntries',
                controller: 'ModelHistory',
                pass: [model, foreignKey, limit, page]
            };
        App.Main.UIBlocker.blockElement($parentWrapper);
        App.Main.loadJsonAction(url, {
            replaceTarget: true,
            target: $('tbody', $parentWrapper),
            onComplete: function(controller, response) {
                App.Main.UIBlocker.unblockElement($parentWrapper);
            }.bind(this),
        });
    },
    _onAddComment: function(e) {
        e.preventDefault();

        var $parentWrapper = $(e.currentTarget).parents('.model-history-area'),
            model = $parentWrapper.data('model'),
            foreignKey = $parentWrapper.data('foreignkey'),
            limit = 10,
            page = 1;

        if ($parentWrapper.data('limit')) {
            limit = $parentWrapper.data('limit');
        }

        var url = {
            plugin: 'model_history',
            controller: 'ModelHistory',
            action: 'index',
            pass: [
                model,
                foreignKey,
                limit,
                page
            ]
        };
        App.Main.UIBlocker.blockElement($(e.currentTarget));
        App.Main.loadJsonAction(url, {
            data: $(e.currentTarget).serialize(),
            target: $parentWrapper,
            onComplete: function(controller, response) {
                App.Main.UIBlocker.unblockElement($(e.currentTarget));
            }.bind(this)
        });
    },
    _onResetFilter: function(e) {
        console.log(e);
    },
    _onFilter: function(e) {
        var $parentWrapper = $(e.currentTarget).parents('.model-history-area'),
            model = $parentWrapper.data('model'),
            foreignKey = $parentWrapper.data('foreignkey'),
            url = {
                plugin: 'model_history',
                controller: 'ModelHistory',
                action: 'filter',
                pass: [model, foreignKey]
            };

        App.Main.UIBlocker.blockElement($(e.currentTarget));
        App.Main.loadJsonAction(url, {
            data: $(e.currentTarget).serialize(),
            target: $parentWrapper.parents('.model-history-wrapper'),
            onComplete: function(controller, response) {
                App.Main.UIBlocker.unblockElement($(e.currentTarget));
            }.bind(this)
        });
        e.preventDefault();
    }
});
