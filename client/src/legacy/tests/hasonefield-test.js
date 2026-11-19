/* global jest, test, expect, beforeAll */
import jQuery from 'jquery';

const rules = {};

beforeAll(() => {
  jQuery.entwine = jest.fn((namespace, callback) => {
    callback((selector) => ({
      entwine: (definition) => {
        rules[selector] = definition;
      },
    }));
  });
  // Load after the entwine mock is in place
  // eslint-disable-next-line global-require
  require('../hasonefield');
});

const selector = '.grid-field.hasonebutton .add-existing-autocompleter .action_gridfield_relationadd';

test('registers an entwine rule in the ss namespace for the add existing button', () => {
  expect(jQuery.entwine).toHaveBeenCalledWith('ss', expect.any(Function));
  expect(rules[selector]).toBeDefined();
});

test('clicking add existing calls the parent handler and reloads the grid field', () => {
  const reload = jest.fn();
  const context = {
    _super: jest.fn(),
    getGridField: () => ({ reload }),
  };
  const event = { type: 'click' };

  rules[selector].onclick.call(context, event);

  expect(context._super).toHaveBeenCalledWith(event);
  expect(reload).toHaveBeenCalledTimes(1);
});
