import Alpine from 'alpinejs';

export default function initAlpine() {
    window.Alpine = Alpine;
    Alpine.start();
    console.log('✅ Alpine initialisé');
}
