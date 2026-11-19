import jQuery from 'jquery';

jQuery.entwine('ss', ($) => {
  // Reload the field once a record has been linked with the "add existing" autocompleter
  $('.grid-field.hasonebutton .add-existing-autocompleter .action_gridfield_relationadd').entwine({
    onclick(e) {
      this._super(e);

      this.getGridField().reload();
    }
  });
});
