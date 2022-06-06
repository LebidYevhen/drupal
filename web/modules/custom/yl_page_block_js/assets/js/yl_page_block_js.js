/**
 * @file
 * JavaScript Page Block Js file.
 */

(function ($, Drupal, settings) {

  "use strict";

  Drupal.behaviors.yl_page_block_js = {
    attach: _.once(function (context) {
      var body = $('body').attr('class');
      $('#block-pageblockjs > .content').append('<h3>body classes</h3>' + body, '<h3>Current date: ' + currentDate() + '</h3>');
    })
  }

  function currentDate() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();

    return mm + '/' + dd + '/' + yyyy;
  }

})(jQuery, Drupal, drupalSettings);