import './bootstrap';

import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: '4d82b8e2b19a8c96bb2f',
    cluster: 'ap2',
    forceTLS: false
  });

// Listen for notifications
// Echo.private('App.Models.User.' + userId)
//     .notification((notification) => {
//         console.log(notification);
//         // Handle the notification display
//     });
