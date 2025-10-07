import * as bootstrap from 'bootstrap';
window.bootstrap = bootstrap;

export default function initBootstrap() {
    document.addEventListener('DOMContentLoaded', () => {
        console.log('[Bootstrap] Initialisation JS réussie ✅', bootstrap);

        // Vérifie que Modal est accessible
        if (bootstrap.Modal) {
            console.log('[Bootstrap] Modal API détectée ✅');
        } else {
            console.error('[Bootstrap] Modal API introuvable ❌');
        }

        // Triggers manuels pour le modal langue
        const langTriggers = ['#openLangModal', '#openLangModalMobile', '#openLangModalMobile2'];
        langTriggers.forEach(sel => {
            const el = document.querySelector(sel);
            if (el) {
                el.addEventListener('click', e => {
                    e.preventDefault();
                    const modalEl = document.getElementById('langModal');
                    if (modalEl) new bootstrap.Modal(modalEl).show();
                });
            }
        });
    });
}
