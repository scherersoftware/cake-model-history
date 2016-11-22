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

        $('.model-history-area .load-next-history').off('click').on('click', this.loadNextEntries.bind(this));
        $('.model-history-area .load-prev-history').off('click').on('click', this.loadPrevEntries.bind(this));
        $('.model-history-area .diff-btn').off('click').on('click', this._onDiff.bind(this));

        $('.model-history-comment form').off('submit').on('submit', this._onAddComment.bind(this));

        $('.model-history-filter form').off('submit').on('submit', this._onFilter.bind(this));
        $('.model-history-filter .reset-btn').off('click').on('click', this._onResetFilter.bind(this));
    },
    loadNextEntries: function(e) {
        var $parentWrapper = $(e.currentTarget).parents('.model-history-area'),
            page = $parentWrapper.data('page') + 1;

        $parentWrapper.data('page', page);
        if ($('.model-history-filter form', $parentWrapper).length) {
            e.currentTarget = $('.model-history-filter form', $parentWrapper);
        }
        this._onFilter(e);
    },
    loadPrevEntries: function(e) {
        var $parentWrapper = $(e.currentTarget).parents('.model-history-area'),
            page = $parentWrapper.data('page') - 1;
        if (page <= 0) {
            page = 1;
        }

        $parentWrapper.data('page', page);
        if ($('.model-history-filter form', $parentWrapper).length) {
            e.currentTarget = $('.model-history-filter form', $parentWrapper);
        }
        this._onFilter(e);
    },
    _onAddComment: function(e) {
        e.preventDefault();

        var $parentWrapper = $(e.currentTarget).parents('.model-history-area'),
            model = $parentWrapper.data('model'),
            foreignKey = $parentWrapper.data('foreignkey'),
            limit = 10,
            page = 1
            showFilter = $parentWrapper.data('showFilter'),
            showComment = $parentWrapper.data('showComment');

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
                page,
                showFilter,
                showComment
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
        e.preventDefault();

        var $button = $(e.currentTarget),
            $filterForm = $button.parents('form');

        e.currentTarget = $filterForm[0];
        this._onFilter(e, true);
    },
    _onFilter: function(e, reset) {
        var $parentWrapper = $(e.currentTarget).parents('.model-history-area'),
            model = $parentWrapper.data('model'),
            foreignKey = $parentWrapper.data('foreignkey'),
            limit = $parentWrapper.data('limit'),
            page = $parentWrapper.data('page'),
            showFilter = $parentWrapper.data('showFilter'),
            showComment = $parentWrapper.data('showComment'),
            url = {
                plugin: 'model_history',
                controller: 'ModelHistory',
                action: 'filter',
                pass: [model, foreignKey, limit, page, showFilter, showComment]
            };

        var formData = $(e.currentTarget).serialize();
        $parentWrapper.parents('.model-history-wrapper').data('filterActive', false);
        if (reset === true) {
            $parentWrapper.parents('.model-history-wrapper').data('filterActive', false);
            formData = {};
        }

        App.Main.UIBlocker.blockElement($parentWrapper);
        App.Main.loadJsonAction(url, {
            data: formData,
            target: $parentWrapper.parents('.model-history-wrapper'),
            onComplete: function(controller, response) {
                App.Main.UIBlocker.unblockElement($parentWrapper);
            }.bind(this)
        });
        e.preventDefault();
    },
    _onDiff: function(e) {
        e.preventDefault();
        var historyId = $(e.currentTarget).data('historyId');

        var url = {
            plugin: 'model_history',
            controller: 'ModelHistory',
            action: 'diff',
            pass: [historyId]
        };

        this._dialog = new Frontend.Dialog({
			onClose: function(arg) {
                console.log(arg);
            }.bind(this)
		});
		this._dialog.blockUi();
		App.Main.loadJsonAction(url, {
			parentController: this,
			dialog: this._dialog,
			target: this._dialog.getContent(),
			onComplete: function() {
				this._dialog.show();
				this._dialog.unblockUi();
			}.bind(this)
		});
        return false;
    }
});
