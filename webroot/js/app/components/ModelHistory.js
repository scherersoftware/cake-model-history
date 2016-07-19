App.Components.ModelHistoryComponent = Frontend.Component.extend({
    startup: function() {
        if(this.Controller.$('.model-history-list').length > 0) {
            this.loadModelHistoryList();
        }
    },
    loadModelHistoryList: function() {
        var $listContainers = this.Controller.$('.model-history-list');
        $listContainers.each(function(index, listContainer) {
            var $listContainer = $(listContainer);
            var url = {
                plugin: 'model_history',
                controller: 'ModelHistory',
                action: 'index',
                pass: [
                    $listContainer.data('repository'),
                    $listContainer.data('id')
                ]
            };
            App.Main.UIBlocker.blockElement($listContainer);
            App.Main.loadJsonAction(url, {
                target: $listContainer,
                onComplete: function(controller, response) {
                    App.Main.UIBlocker.unblockElement($listContainer);
                    if (this.Controller.$('.model-history-list').attr("data-comment-box") == 1) {
                        this.Controller.$('.model-history').show();
                    } else {
                        this.Controller.$('.model-history').hide();
                    }
                }.bind(this)
            });
        }.bind(this));
    }
});