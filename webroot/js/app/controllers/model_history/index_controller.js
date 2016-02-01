App.Controllers.ModelHistoryIndexController = Frontend.AppController.extend({
    startup: function() {
        console.log($('.model-history form'));
        this.$('.model-history form').on('submit', this._onAddComment.bind(this));
        this.$('.pager .prev:not(.disabled) a').click(this._onPaginatorClick.bind(this));
        this.$('.pager .next:not(.disabled) a').click(this._onPaginatorClick.bind(this));
        
        // Toggle view
        if ($('.model-history-toggle-btn').length) {
            $('.model-history-list').toggle();
            $('.model-history-toggle-btn').click(function(e) {
                e.preventDefault();
                $('.model-history-list').toggle();
            }.bind(this));
        }
    },
    _onAddComment: function(e) {
        e.preventDefault();

        var url = {
            plugin: 'model_history',
            controller: 'ModelHistory',
            action: 'index',
            pass: [
                this.getVar('model'),
                this.getVar('foreignKey')
            ]
        };
        App.Main.UIBlocker.blockElement(this.$('.model-history form'));
        App.Main.loadJsonAction(url, {
            data: this.$('.model-history form').serialize(),
            target: this._dom.parent(),
            onComplete: function(controller, response) {
                $('.model-history-list').toggle();
                App.Main.UIBlocker.unblockElement(this.$('.model-history form'));
            }.bind(this)
        });
    },
    _onPaginatorClick: function(e) {
        e.preventDefault();
        App.Main.UIBlocker.blockElement($(e.currentTarget).parent());
        App.Main.loadJsonAction($(e.currentTarget).attr('href'), {
            target: $(e.currentTarget).parents('.controller.model_history-index'),
            onComplete: function(controller, response) {
                $('.model-history-list').toggle();
                App.Main.UIBlocker.unblockElement($(e.currentTarget).parent());
            }.bind(this)
        });
    }
});
