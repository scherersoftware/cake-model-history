App.Components.ModelHistoryComponent = Frontend.Component.extend({
    startup: function() {
        if(this.Controller.$('.model-history-list').length > 0) {
            this.loadModelHistoryList();
        }
    },
    loadModelHistoryList: function() {
        var url = {
            plugin: 'model_history',
            controller: 'model_history',
            action: 'show_model_history',
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
                if (this.Controller.$('.model-history-list').data('comment-box')) {
                    this.Controller.$('.comment-box').show();
                    this.Controller.$('.comment-box-save').click(this._onSaveComment.bind(this));
                } else {
                    this.Controller.$('.comment-box').hide();
                }
            }.bind(this),
            initController: false
        });
    },
    _onSaveComment: function() {
        var url = {
            plugin: 'model_history',
            controller: 'model_history',
            action: 'save_comment',
            pass: [
                this.Controller.$('.model-history-list').data('repository'),
                this.Controller.$('.model-history-list').data('id')
            ]
        };
        var data = {
            comment: this.Controller.$('.comment-box-content').val()
        }
        var $listContainer = this.Controller.$('.model-history-list');
        App.Main.UIBlocker.blockElement($listContainer);
        App.Main.request(url, data, function(response) {
            App.Main.UIBlocker.unblockElement($listContainer);
            console.log(response);
        });
    }
});