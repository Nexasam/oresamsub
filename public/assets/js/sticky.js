"use strict";
(() => {
  var navbar = document.querySelector(".header");
  var navbar1 = document.querySelector(".app-sidebar");
  var sticky = navbar.clientHeight - (navbar.clientHeight - 1);
  var sticky1 = navbar1.clientHeight;
  function stickyFn() {
    if (window.scrollY >= sticky) {
      navbar.classList.add("sticky-pin")
      navbar1.classList.add("sticky-pin")
    } else {
      navbar.classList.remove("sticky-pin");
      navbar1.classList.remove("sticky-pin");
    }
  }
  window.addEventListener('scroll', stickyFn);
  window.addEventListener('DOMContentLoaded', stickyFn);

})();