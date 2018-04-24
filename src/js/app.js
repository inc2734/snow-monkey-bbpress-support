'use strict';

import $ from 'jquery';

$(() => {

  $('.status-closed').find('.c-entry__title, .bbp-topic-title').prepend(
    $('<i class="fas fa-check" aria-hidden="true" />')
  );

});
