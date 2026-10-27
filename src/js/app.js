function bindStars(selector, configName, idType) {
  document.querySelectorAll(selector).forEach((button) => {
    button.addEventListener('click', async () => {
      try {
        const config = window[configName];
        const response = await fetch(config.endpoint, {
          method: 'POST',
          credentials: 'same-origin',
          body: new URLSearchParams({
            action: config.action,
            secure: config.secure,
            [`${idType}Id`]: parseInt(button.dataset[`${idType}Id`], 10),
            authorId: parseInt(button.dataset[`${idType}Author`], 10),
          }),
        });

        if (!response.ok) {
          throw new Error(`Star request failed: ${response.status}`);
        }

        const result = await response.json();
        const counter = button.querySelector('.smbbpress-stars__count');
        if (counter) {
          counter.textContent = result.stars;
        }

        const users = button.nextElementSibling;
        const names = users?.matches('.smbbpress-stars-users')
          ? users.querySelector('.smbbpress-stars-users__names')
          : null;
        if (names) {
          names.innerHTML = result.users;
        }
      } catch (error) {
        console.error('Failed to update stars.', error);
      }
    });
  });
}

function initialize() {
  // Mark closed topics in both the topic page and the topic list.
  document.querySelectorAll('.status-closed .c-entry__title, .status-closed .bbp-topic-title').forEach((title) => {
    const icon = document.createElement('i');
    icon.className = 'fas fa-check';
    icon.setAttribute('aria-hidden', 'true');
    title.prepend(icon);
  });

  bindStars('button.smbbpress-replies-stars', 'SNOW_MONKEY_BBPRESS_SUPPORT_REPLIES_STARS', 'reply');
  bindStars('button.smbbpress-topic-stars', 'SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS', 'topic');

  /**
   * @link https://github.com/WordPress/wordpress.org/blob/72e581d7e397451260771be596c0463fcc26b9b1/wordpress.org/public_html/wp-content/themes/pub/wporg-support/js/forums.js#L26-L50
   */
  document.getElementById('wp-bbp_reply_content-wrap')?.addEventListener('paste', (event) => {
    const textarea = event.target;
    const clipboard = event.clipboardData || window.clipboardData;
    if (!(textarea instanceof HTMLTextAreaElement) || !clipboard) {
      return;
    }

    const value = textarea.value;
    const paste = clipboard.getData('text').trimEnd();
    if (!paste.length || !paste.match(/^[A-Za-z\t\s_/]/)) {
      return;
    }

    if (paste.length < 500 && paste.split('\n').length < 3) {
      return;
    }

    const { selectionStart, selectionEnd } = textarea;
    if (value.substr(selectionStart - 1, 1) === '`' || paste.startsWith('`') || paste.endsWith('`')) {
      return;
    }

    textarea.value = value.slice(0, selectionStart) + '`' + paste + '`' + value.slice(selectionEnd);
    event.preventDefault();
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', initialize, { once: true });
} else {
  initialize();
}
