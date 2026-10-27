const assert = require( 'node:assert/strict' );
const { readFileSync } = require( 'node:fs' );
const { resolve } = require( 'node:path' );
const { test } = require( 'node:test' );
const { JSDOM } = require( 'jsdom' );

const bundle = readFileSync( resolve( __dirname, '../../assets/js/app.js' ), 'utf8' );

async function render( t, content, { afterReady = false } = {} ) {
	const dom = new JSDOM( `<body>${ content }</body>`, {
		runScripts: 'outside-only',
		url: 'https://example.org/forums/',
	} );
	t.after( () => dom.window.close() );
	const { window } = dom;
	const ready = new Promise( ( done ) => window.document.addEventListener( 'DOMContentLoaded', done, { once: true } ) );
	const errors = [];
	window.console.error = ( ...args ) => errors.push( args );
	window.fetch = async () => { throw new Error( 'Unexpected fetch request' ); };
	if ( afterReady ) {
		await ready;
	}
	window.SNOW_MONKEY_BBPRESS_SUPPORT_REPLIES_STARS = {
		endpoint: '/wp-admin/admin-ajax.php',
		action: 'reply-stars',
		secure: 'reply-nonce',
	};
	window.SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS = {
		endpoint: '/wp-admin/admin-ajax.php',
		action: 'topic-stars',
		secure: 'topic-nonce',
	};
	window.eval( bundle );
	await ready;
	assert.equal( window.jQuery, undefined );
	assert.equal( window.$, undefined );
	return { window, errors };
}

test( 'marks closed topic titles and leaves open topics unchanged', async ( t ) => {
	const { window } = await render( t, `
		<article class="status-closed"><h1 class="c-entry__title">Closed topic</h1></article>
		<li class="status-closed"><a class="bbp-topic-title">Closed topic in list</a></li>
		<article><h1 class="c-entry__title">Open topic</h1></article>
	` );
	const titles = window.document.querySelectorAll( '.status-closed .c-entry__title, .status-closed .bbp-topic-title' );
	for ( const title of titles ) {
		assert.ok( title.firstElementChild.matches( 'i.fas.fa-check[aria-hidden="true"]' ) );
		assert.match( title.textContent, /^Closed topic/ );
	}
	assert.equal( window.document.querySelector( 'article:not(.status-closed) i' ), null );
} );

for ( const kind of [ 'replies', 'topic' ] ) {
	test( `${ kind } stars send IDs and nonce and update only the selected counter and users`, async ( t ) => {
		const id = kind === 'replies' ? 'reply' : 'topic';
		const { window } = await render( t, `
			<button class="smbbpress-${ kind }-stars" data-${ id }-id="42" data-${ id }-author="7"><span class="smbbpress-stars__count">1</span></button>
			<div class="smbbpress-stars-users"><span class="smbbpress-stars-users__names">Alice</span></div>
			<button class="smbbpress-${ kind }-stars" data-${ id }-id="43" data-${ id }-author="8"><span class="smbbpress-stars__count">9</span></button>
			<div class="smbbpress-stars-users"><span class="smbbpress-stars-users__names">Carol</span></div>
		` );
		const requests = [];
		window.fetch = async ( endpoint, options ) => {
			requests.push( { endpoint, data: Object.fromEntries( options.body ) } );
			assert.equal( options.method, 'POST' );
			assert.equal( options.credentials, 'same-origin' );
			assert.ok( options.body instanceof window.URLSearchParams );
			return { ok: true, json: async () => ( { stars: 2, users: '<a href="/users/bob/">Bob</a>' } ) };
		};
		window.document.querySelector( 'button' ).click();
		await new Promise( setImmediate );
		assert.deepEqual( requests, [ {
			endpoint: '/wp-admin/admin-ajax.php',
			data: {
				action: `${ id }-stars`,
				secure: `${ id }-nonce`,
				[ `${ id }Id` ]: '42',
				authorId: '7',
			},
		} ] );
		const counters = window.document.querySelectorAll( '.smbbpress-stars__count' );
		assert.equal( counters[ 0 ].textContent, '2' );
		assert.equal( counters[ 1 ].textContent, '9' );
		const names = window.document.querySelectorAll( '.smbbpress-stars-users__names' );
		assert.equal( names[ 0 ].querySelector( 'a' ).getAttribute( 'href' ), '/users/bob/' );
		assert.equal( names[ 0 ].textContent, 'Bob' );
		assert.equal( names[ 1 ].textContent, 'Carol' );
	} );
}

function paste( window, text, value = 'Before selected after', start = 7, end = 15 ) {
	const textarea = window.document.querySelector( 'textarea' );
	textarea.value = value;
	textarea.setSelectionRange( start, end );
	const event = new window.Event( 'paste', { bubbles: true, cancelable: true } );
	Object.defineProperty( event, 'clipboardData', { value: { getData: () => text } } );
	textarea.dispatchEvent( event );
	return { value: textarea.value, prevented: event.defaultPrevented };
}

test( 'wraps pasted multiline or long code and preserves text around the selection', async ( t ) => {
	const { window } = await render( t, '<div id="wp-bbp_reply_content-wrap"><textarea></textarea></div>' );
	for ( const code of [ 'const a = 1;\nconst b = 2;\nconsole.log(a + b);', 'a'.repeat( 500 ) ] ) {
		assert.deepEqual( paste( window, `${ code }  \n` ), {
			value: `Before \`${ code }\` after`,
			prevented: true,
		} );
	}
} );

test( 'leaves short, non-ASCII, empty and already wrapped pastes to the browser', async ( t ) => {
	const { window } = await render( t, '<div id="wp-bbp_reply_content-wrap"><textarea></textarea></div>' );
	for ( const text of [ '', '   \n', 'short text', '日本語\nの\n文章', '`code\ncode\ncode`', 'code\ncode\ncode`' ] ) {
		assert.deepEqual( paste( window, text ), { value: 'Before selected after', prevented: false } );
	}
	assert.deepEqual( paste( window, 'code\ncode\ncode', '`', 1, 1 ), { value: '`', prevented: false } );
} );

for ( const afterReady of [ false, true ] ) {
	test( `initializes once when loaded ${ afterReady ? 'after' : 'before' } DOMContentLoaded`, async ( t ) => {
		const { window } = await render( t, '<article class="status-closed"><h1 class="c-entry__title">Closed</h1></article>', { afterReady } );
		assert.equal( window.document.querySelectorAll( '.fa-check' ).length, 1 );
	} );
}

test( 'handles pages without topic controls or a reply editor', async ( t ) => {
	const { errors } = await render( t, '<main>No forum here</main>' );
	assert.deepEqual( errors, [] );
} );

for ( const kind of [ 'replies', 'topic' ] ) {
	for ( const failure of [ 'http', 'network', 'json' ] ) {
		test( `${ kind } stars preserve the display and report ${ failure } failures`, async ( t ) => {
			const { window, errors } = await render( t, `
				<button class="smbbpress-${ kind }-stars"><span class="smbbpress-stars__count">3</span></button>
				<div class="smbbpress-stars-users"><span class="smbbpress-stars-users__names">Alice</span></div>
			` );
			window.fetch = async () => {
				if ( failure === 'network' ) {
					throw new Error( 'Network unavailable' );
				}
				return {
					ok: failure !== 'http',
					status: 403,
					json: async () => { throw new Error( 'Invalid JSON' ); },
				};
			};
			window.document.querySelector( 'button' ).click();
			await new Promise( setImmediate );
			assert.equal( window.document.querySelector( '.smbbpress-stars__count' ).textContent, '3' );
			assert.equal( window.document.querySelector( '.smbbpress-stars-users__names' ).textContent, 'Alice' );
			assert.equal( errors.length, 1 );
			assert.equal( errors[ 0 ][ 0 ], 'Failed to update stars.' );
			assert.match( errors[ 0 ][ 1 ].message, /403|Network unavailable|Invalid JSON/ );
		} );
	}
}

test( 'encodes nonce and action as form data and waits for a successful response', async ( t ) => {
	const { window, errors } = await render( t, '<button class="smbbpress-topic-stars" data-topic-id="42" data-topic-author="7"><span class="smbbpress-stars__count">1</span></button>' );
	window.SNOW_MONKEY_BBPRESS_SUPPORT_TOPIC_STARS.secure = 'nonce +&=';
	const pending = Promise.withResolvers();
	window.fetch = ( endpoint, options ) => {
		assert.equal( new URLSearchParams( options.body.toString() ).get( 'secure' ), 'nonce +&=' );
		return pending.promise;
	};
	window.document.querySelector( 'button' ).click();
	assert.equal( window.document.querySelector( '.smbbpress-stars__count' ).textContent, '1' );
	pending.resolve( { ok: true, json: async () => ( { stars: 2, users: '' } ) } );
	await new Promise( setImmediate );
	assert.equal( window.document.querySelector( '.smbbpress-stars__count' ).textContent, '2' );
	assert.deepEqual( errors, [] );
} );

test( 'updates the counter with the current PHP wrapper markup without disturbing existing avatars', async ( t ) => {
	const { window, errors } = await render( t, `
		<div class="smbbpress-stars-wrapper">
			<div class="smbbpress-stars-wrapper__button"><button class="smbbpress-topic-stars" data-topic-id="42" data-topic-author="7"><span class="smbbpress-stars__count">1</span></button></div>
			<div class="smbbpress-stars-wrapper__users"><div class="smbbpress-stars-users"><span class="smbbpress-stars-users__users"><img src="/avatar.png" alt="Alice"></span></div></div>
		</div>
	` );
	window.fetch = async () => ( { ok: true, json: async () => ( { stars: 2, users: '<a href="/users/bob/">Bob</a>' } ) } );
	window.document.querySelector( 'button' ).click();
	await new Promise( setImmediate );
	assert.equal( window.document.querySelector( '.smbbpress-stars__count' ).textContent, '2' );
	assert.equal( window.document.querySelector( '.smbbpress-stars-users__users img' ).getAttribute( 'alt' ), 'Alice' );
	assert.deepEqual( errors, [] );
} );

test( 'ignores paste events without clipboard data or outside the textarea', async ( t ) => {
	const { window } = await render( t, '<div id="wp-bbp_reply_content-wrap"><textarea>Original</textarea><button>Other target</button></div>' );
	for ( const element of window.document.querySelectorAll( 'textarea, button' ) ) {
		const event = new window.Event( 'paste', { bubbles: true, cancelable: true } );
		element.dispatchEvent( event );
		assert.equal( event.defaultPrevented, false );
	}
	assert.equal( window.document.querySelector( 'textarea' ).value, 'Original' );
} );
