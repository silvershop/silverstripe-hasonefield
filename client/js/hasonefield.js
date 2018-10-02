(function($) {
    $.entwine('ss', function($) {
        // Covers both tabular delete button, and the button on the detail form
        $('.grid-field.hasonebutton .add-existing-autocompleter .action_gridfield_relationadd').entwine({
            onclick: function(e) {
                this._super(e);

                grid_field = this.getGridField();
                grid_field.reload();
            }
        });
    });
})(jQuery);
