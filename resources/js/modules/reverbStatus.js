export default function monitorReverb() {
    console.log('🩵 Reverb Status Logger chargé...');

    document.addEventListener('DOMContentLoaded', () => {
        if (!window.Echo || !window.Echo.connector) {
            console.warn('⚠️ Echo non initialisé au moment du monitoring.');
            return;
        }

        const connection = window.Echo.connector.pusher?.connection;
        if (!connection) {
            console.error('❌ Impossible d\'accéder à la connexion Pusher.');
            return;
        }

        const host = window.Laravel?.reverbHost;
        const port = window.Laravel?.reverbPort;

        connection.bind('connecting',   () => console.log('⏳ WebSocket en cours de connexion...'));
        connection.bind('connected',    () => console.log(`✅ WebSocket CONNECTÉ : ${host}:${port}`));
        connection.bind('disconnected', () => console.log('❌ WebSocket DÉCONNECTÉ !'));
        connection.bind('error',        err => console.error('⚠️ Erreur WebSocket :', err));
    });
}
