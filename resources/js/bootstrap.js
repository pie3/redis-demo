window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
  window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.textContent;
} else {
  console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// --begin 基于 Redis 发布订阅（Redis::publish + Redis::subscribe） + socket.io
/* const io = require('socket.io-client');
const socket = io(window.location.hostname + ':3000');
socket.on('redis_demo_database_test-channel:UserSignedUp', data => {
  console.log(data.username);
}); */
// --end


import Echo from 'laravel-echo';

// --begin 基于 Pusher
// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: process.env.MIX_PUSHER_APP_KEY,
//     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
//     forceTLS: true
// });
// --end


// --begin 基于 socket.io 客户端
window.io = require('socket.io-client');

window.Echo = new Echo({
  broadcaster: 'socket.io',
  host: window.location.hostname + ':6001'
});

// 客户端请求头包含 X-Socket-ID, Laravel Echo 初始化时会为每个连接分配一个唯一的 Socket ID，用于标识不同的 Websocket 客户端
// window.axios.defaults.headers.common['X-Socket-ID'] = window.Echo.socketId();

window.Echo.channel('redis_demo_database_test-channel').listen('UserSignedUp', event => {
  console.log(event.user);
});
// --end

