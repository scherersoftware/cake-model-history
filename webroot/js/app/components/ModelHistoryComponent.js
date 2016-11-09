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
        $('.model-history-area .load-more-history').off('click').on('click', function(e) {
            $target = $(e.currentTarget);
            this.loadMoreEntries($target.data('model'), $target.data('id'), $target.data('limit'), $target.data('page'), $target);
            return e.preventDefault();
        }.bind(this));
        $('.model-history form').off('submit').on('submit', this._onAddComment.bind(this));
    },
    loadMoreEntries: function(model, foreignKey, limit, page, $element) {
        var url = {
                plugin: 'model_history',
                action: 'loadMore',
                controller: 'ModelHistory',
                pass: [model, foreignKey, limit, page + 1]
            },
            $parentWrapper = $element.parents('.model-history-area');

        App.Main.UIBlocker.blockElement($parentWrapper);
        App.Main.loadJsonAction(url, {
            replaceTarget: false,
            onComplete: function(controller, response) {
                $(response.data.html).insertBefore($element.parents('tr'));
                $element.data('page', page + 1);
                App.Main.UIBlocker.unblockElement($parentWrapper);

                if (!response.data.frontendData.jsonData.showMoreEntriesButton) {
                    $element.parents('tr').remove();
                }
            }.bind(this),
        });
    },
    _onAddComment: function(e) {
        e.preventDefault();

        var model = $('input[name=data]', e.currentTarget).data('model'),
            foreignKey = $('input[name=data]', e.currentTarget).data('foreignkey'),
            loadMoreButton = $(e.currentTarget).parents('.form').next('table').find('.load-more-history'),
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
