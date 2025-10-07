import Pusher from 'pusher-js';
import Echo from 'laravel-echo';

export default function initEcho() {
    window.Pusher = Pusher;

    const key     = import.meta.env.VITE_REVERB_APP_KEY;
    const host    = import.meta.env.VITE_REVERB_HOST;
    const port    = Number(import.meta.env.VITE_REVERB_PORT);
    const cluster = import.meta.env.VITE_REVERB_APP_CLUSTER;

    window.Echo = new Echo({
        broadcaster: 'pusher',
        key,
        cluster,
        wsHost: host,
        wsPort: port,
        wssPort: port,
        forceTLS: false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });

    window.Laravel = { reverbHost: host, reverbPort: port };

    console.log('✅ Laravel Echo initialisé');
}
