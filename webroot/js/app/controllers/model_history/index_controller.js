App.Controllers.ModelHistoryIndexController = Frontend.AppController.extend({
    startup: function() {
        this.$('.model-history form').on('submit', this._onAddComment.bind(this));
    },
    _onAddComment: function(e) {
        e.preventDefault();

        var url = {
            plugin: 'model_history',
            controller: 'model_history',
            action: 'index',
            pass: [
                this.getVar('model'), this.getVar('foreignKey')
            ]
        };
        
        App.Main.loadJsonAction(url, {
            data: this.$('.model-history form').serialize(),
            target: this._dom.parent()
        });
    }
});