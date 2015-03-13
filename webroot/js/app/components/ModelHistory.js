App.Components.ModelHistoryComponent = Frontend.Component.extend({
    startup: function() {
        if(this.Controller.$('.model-history-list').length > 0) {
            this.loadModelHistoryList();
            this.Controller.$('.model-history form').on('submit', function(e) {
                alert(e);
                //e.preventDefaults;
            });
        }
    },
    loadModelHistoryList: function() {
        var url = {
            plugin: 'model_history',
            controller: 'model_history',
            action: 'index',
            pass: [
                this.Controller.$('.model-history-list').data('repository'),
                this.Controller.$('.model-history-list').data('id')
            ]
        };
        var $listContainer = this.Controller.$('.model-history-list');
        App.Main.UIBlocker.blockElement($listContainer);
        App.Main.loadJsonAction(url, {
            target: $listContainer,
            onComplete: function(controller, response) {
                App.Main.UIBlocker.unblockElement($listContainer);
            }.bind(this),
            initController: false
        });
    }
});