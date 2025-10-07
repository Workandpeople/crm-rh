import AOS from 'aos';
import 'aos/dist/aos.css';

export default function initAOS() {
    window.AOS = AOS;
    AOS.init({
        duration: 800,
        once: true,
    });
    console.log('✅ AOS initialisé');
}
