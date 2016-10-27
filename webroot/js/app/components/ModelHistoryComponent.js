App.Components.ModelHistoryComponent = Frontend.Component.extend({
    startup: function() {
        $('[data-toggle=popover]').popover({
            trigger: 'hover',
            placement: 'top',
            container: 'body'
        });
    }
});
