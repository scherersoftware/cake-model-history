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
            $target = $(e.currentTarget);
            this.loadNextEntries($target.data('model'), $target.data('id'), $target.data('limit'), $target.data('page'), $target);
            return e.preventDefault();
        }.bind(this));
        $('.model-history-area .load-prev-history').off('click').on('click', function(e) {
            $target = $(e.currentTarget);
            this.loadPrevEntries($target.data('model'), $target.data('id'), $target.data('limit'), $target.data('page'), $target);
            return e.preventDefault();
        }.bind(this));

        $('.model-history form').off('submit').on('submit', this._onAddComment.bind(this));
    },
    loadNextEntries: function(model, foreignKey, limit, page, $element) {
        var page = page + 1;
        this._loadEntries(model, foreignKey, limit, page, $element);
    },
    loadPrevEntries: function(model, foreignKey, limit, page, $element) {
        var page = page - 1;
        if (page <= 0) {
            page = 1;
        }
        this._loadEntries(model, foreignKey, limit, page, $element);
    },
    _loadEntries: function(model, foreignKey, limit, page, $element) {
        var url = {
                plugin: 'model_history',
                action: 'loadEntries',
                controller: 'ModelHistory',
                pass: [model, foreignKey, limit, page]
            },
            $parentWrapper = $element.parents('.model-history-area');
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

        var model = $('input[name=data]', e.currentTarget).data('model'),
            foreignKey = $('input[name=data]', e.currentTarget).data('foreignkey'),
            loadMoreButton = $(e.currentTarget).parents('.form').next('table').find('.load-next-history'),
            limit = 10,
            page = 1;

        if (loadMoreButton.length == 1) {
            limit = loadMoreButton.data('limit');
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
            target: $(e.currentTarget).parents('.model-history-area'),
            onComplete: function(controller, response) {
                App.Main.UIBlocker.unblockElement($(e.currentTarget));
            }.bind(this)
        });
    }
});
