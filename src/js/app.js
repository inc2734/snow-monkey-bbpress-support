'use strict';

import $ from 'jquery';

$(() => {

  /**
   * Add close mark
   */
  $('.status-closed').find('.c-entry__title, .bbp-topic-title').prepend(
    $('<i class="fas fa-check" aria-hidden="true" />')
  );

  /**
   * Add stars
   */
  $('button.smbbpress-stars').on('click', (e) => {
    const btn = $(e.currentTarget);
    const replyId = parseInt(btn.attr('data-reply-id'));
    const authorId = parseInt(btn.attr('data-reply-author'));

    $.post(
      SNOW_MONKEY_BBPRESS_SUPPORT.endpoint,
      {
        action: SNOW_MONKEY_BBPRESS_SUPPORT.action,
        secure: SNOW_MONKEY_BBPRESS_SUPPORT.secure,
        replyId: replyId,
        authorId: authorId,
      },
      (response) => {
        const counter = btn.find('.smbbpress-stars__count');
        counter.text(response.stars);
      }
    );
  });

});
