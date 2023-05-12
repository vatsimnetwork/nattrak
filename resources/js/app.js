import './bootstrap';
import * as bootstrap from 'bootstrap'
import swal from 'sweetalert2';
window.Swal = swal;
import $ from 'jquery';
window.$ = $;
import DataTable from 'datatables.net-dt';
window.DataTable = DataTable;
import '@fortawesome/fontawesome-free/js/solid.js';
import '@fortawesome/fontawesome-free/js/fontawesome';

import Echo from "laravel-echo";

import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST,
    wsPort: import.meta.env.VITE_PUSHER_PORT,
    wssPort: import.meta.env.VITE_PUSHER_PORT,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
});
