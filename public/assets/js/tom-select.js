(function () {
  "use strict";

  /* default input */
  if (document.querySelector("#input-tags")) {
    new TomSelect("#input-tags", {
      persist: false,
      createOnBlur: true,
      create: true
    });
  }

  /* Basic select */
  if (document.querySelector("#select-beast")) {
    new TomSelect("#select-beast", {
      create: true,
      sortField: {
        field: "text",
        direction: "asc"
      }
    });
  }

  /* diasble select */
  if (document.querySelector("#select-beast-disabled")) {
    new TomSelect("#select-beast-disabled");
  }

  /* Multiple select */
  if (document.querySelector("#select-state")) {
    new TomSelect("#select-state", {
      maxItems: 8
    });
  }
})();
