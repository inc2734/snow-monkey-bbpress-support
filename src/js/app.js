import $ from 'jquery';

$(() => {
  /**
   * Add close mark
   */
  $('.status-closed').find('.c-entry__title, .bbp-topic-title').prepend(
    $('<i class="fas fa-check" aria-hidden="true" />')
  );

  /**
   * Add replies stars
   */
  $('button.smbbpress-replies-stars').on('click', (e) => {
    const btn = $(e.currentTarget);
    const replyId = parseInt(btn.attr('data-reply-id'));
    const authorId = parseInt(btn.attr('data-reply-author'));
    const starsUsers = $(btn).next('.smbbpress-stars-users');

    $.post(
      SNOW_MONKEY_BBPRESS_SUPPORT_REPLIES_STARS.endpoint,
      {
        action: SNOW_MONKEY_BBPRESS_SUPPORT_REPLIES_STARS.action,
        secure: SNOW_MONKEY_BBPRESS_SUPPORT_REPLIES_STARS.secure,
        replyId: replyId,
        authorId: authorId,
      },
      (response) => {
        const counter = btn.find('.smbbpress-stars__count');
        counter.text(response.stars);
        const names = starsUsers.find('.smbbpress-stars-users__names');
        names.html(response.users);
      }
    );
  });

  /**
   * Add topic stars
   */
  $('button.smbbpress-topic-stars').on('click', (e) => {
    const btn = $(e.currentTarget);
    const topicId = parseInt(btn.attr('data-topic-id'));
    const authorId = parseInt(btn.attr('data-topic-author'));
    const starsUsers = $(btn).next('.smbbpress-stars-users');

    $.post(
      SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS.endpoint,
      {
        action: SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS.action,
        secure: SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS.secure,
        topicId: topicId,
        authorId: authorId,
      },
      (response) => {
        const counter = btn.find('.smbbpress-stars__count');
        counter.text(response.stars);
        const names = starsUsers.find('.smbbpress-stars-users__names');
        names.html(response.users);
      }
    );
  });

  /**
   * @link https://github.com/WordPress/wordpress.org/blob/72e581d7e397451260771be596c0463fcc26b9b1/wordpress.org/public_html/wp-content/themes/pub/wporg-support/js/forums.js#L26-L50
   */
  $('#wp-bbp_reply_content-wrap').on('paste', (e) => {
    const textarea = $(e.target);
    const val      = $(e.target).val();
    const paste    = (e.originalEvent.clipboardData || window.clipboardData).getData('text').trimEnd();

    // If no pasted text, skip.
    if (! paste.length) {
      return;
    }

    // Start with non-byte characters, skip.
    if (! paste.match(/^[A-Za-z\t\s_/]/)) {
      return;
    }

    if (
      paste.length < 500 &&        // Super long pastes get code wrapped
      paste.split("\n").length < 3 // in addition to many-lines pastes.
    ) {
      return;
    }

    // See if the author is pasting into a code block already
    if ('`' === val.substr(textarea.prop('selectionStart') - 1, 1)) {
      return;
    }

    // If the code being pasted is already wrapped in backticks (well, starts with OR ends with), skip.
    if (
      '`' === paste.substr(0, 1) ||
      '`' === paste.substr(-1, 1)
    ) {
      return;
    }

    $(e.target).val(
      val.substr(0, textarea.prop('selectionStart')) +      // Text before cusor/selection
      "`" + paste + "`" +                                   // The pasted text, wrapping with `
      val.substr(textarea.prop('selectionEnd'), val.length) // Text after cursor position/selection
    );

    e.preventDefault();
  });
});
