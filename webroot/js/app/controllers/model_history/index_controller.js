App.Controllers.ModelHistoryIndexController = Frontend.AppController.extend({
    startup: function() {
        this.$('.model-history form').on('submit', this._onAddComment.bind(this));
        this.$('.next').click(this._onPaginatorNextClick.bind(this));
        this.$('.prev').click(this._onPaginatorPrevClick.bind(this));
    },
    _onAddComment: function(e) {
        e.preventDefault();

        var url = {
            plugin: 'model_history',
            controller: 'model_history',
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
                App.Main.UIBlocker.unblockElement(this.$('.model-history form'));
            }.bind(this)
        });
    },
    _onPaginatorNextClick: function(e) {
        e.preventDefault();
        var $page = parseInt(this.$('.pager').data('page'));
        $page++;
        var $nextPage = '?page=' + $page;
        var url = {
            plugin: 'model_history',
            controller: 'model_history',
            action: 'index',
            pass: [
                this.getVar('model'),
                this.getVar('foreignKey'),
                $nextPage
            ]
        };
        App.Main.UIBlocker.blockElement($(e.currentTarget).parent());
        App.Main.loadJsonAction(url, {
            target: $(e.currentTarget).parents('.controller.model_history-index'),
            onComplete: function(controller, response) {
                App.Main.UIBlocker.unblockElement($(e.currentTarget).parent());
            }.bind(this)
        });
    },
    _onPaginatorPrevClick: function(e) {
        e.preventDefault();
        var $page = parseInt(this.$('.pager').data('page'));
        $page--;
        var $nextPage = '?page=' + $page;
        var url = {
            plugin: 'model_history',
            controller: 'model_history',
            action: 'index',
            pass: [
                this.getVar('model'),
                this.getVar('foreignKey'),
                $nextPage
            ]
        };
        App.Main.UIBlocker.blockElement($(e.currentTarget).parent());
        App.Main.loadJsonAction(url, {
            target: $(e.currentTarget).parent(),
            onComplete: function(controller, response) {
                App.Main.UIBlocker.unblockElement($(e.currentTarget).parent());
            }.bind(this)
        });
    }
});
