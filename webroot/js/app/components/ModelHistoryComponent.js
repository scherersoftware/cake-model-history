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
                    $element.remove();
                }
            }.bind(this),
        });
    }
});
