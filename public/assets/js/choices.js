(function () {
  "use strict";

  /* default multi select */
  const choicesMultipleDefault = document.querySelector('#choices-multiple-default');
  if (choicesMultipleDefault) {
    new Choices(choicesMultipleDefault, { allowSearch: false }).setValue(['Choice 2', 'Choie 3']);
  }

  /* multi select with remove button */
  const choicesMultipleRemove = document.querySelector('#choices-multiple-remove-button');
  if (choicesMultipleRemove) {
    new Choices(choicesMultipleRemove, {
      allowHTML: true,
      removeItemButton: true,
    });
  }

  /* multi select with option groups */
  const choicesMultipleGroups = document.getElementById('choices-multiple-groups');
  if (choicesMultipleGroups) {
    new Choices(choicesMultipleGroups, { allowHTML: true });
  }


  /* Start::Choices JS */
  document.addEventListener('DOMContentLoaded', function () {
    var genericExamples = document.querySelectorAll('[data-trigger]');
    for (let i = 0; i < genericExamples.length; ++i) {
      var element = genericExamples[i];
      new Choices(element, {
        allowHTML: true,
        placeholderValue: 'This is a placeholder set in the config',
        searchPlaceholderValue: 'Search',
      });
    }
  });
  /* passing through values */
  var choicesTextPreset = document.querySelector('#choices-text-preset-values');
  if (choicesTextPreset) {
    new Choices(choicesTextPreset, {
    allowHTML: true,
    items: [
      'one',
      {
        value: 'two',
        label: 'two',
        customProperties: {
          description: 'Numbers are infinite',
        },
      },
    ],
    });
  }

  /* email address only */
  var choicesTextEmail = document.querySelector('#choices-text-email-filter');
  if (choicesTextEmail) {
    new Choices(choicesTextEmail, {
    allowHTML: true,
    editItems: true,
    addItemFilter: function (value) {
      if (!value) {
        return false;
      }
      const regex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
      const expression = new RegExp(regex.source, 'i');
      return expression.test(value);
    },
    }).setValue(['abc@hotmail.com']);
  }

  /* options added via config with no search */
  var choicesSingleNoSearch = document.querySelector('#choices-single-no-search');
  if (choicesSingleNoSearch) {
    new Choices(choicesSingleNoSearch, {
    allowHTML: true,
    searchEnabled: false,
    removeItemButton: true,
    choices: [
      { value: 'One', label: 'Label One' },
      { value: 'Two', label: 'Label Two' },
      { value: 'Three', label: 'Label Three' },
    ],
  }).setChoices(
    [
      { value: 'Four', label: 'Label Four' },
      { value: 'Five', label: 'Label Five' },
      { value: 'Six', label: 'Label Six', selected: true },
    ],
    'value',
    'label',
    false
    );
  }

  /* passing unique values */
  var choicesTextUnique = document.querySelector('#choices-text-unique-values');
  if (choicesTextUnique) {
    new Choices(choicesTextUnique, {
    allowHTML: true,
    paste: false,
    duplicateItemsAllowed: false,
    editItems: true,
    });
  }
})();
