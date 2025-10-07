import Swal from 'sweetalert2';

export default function initSweetAlert() {
    window.Swal = Swal;
    console.log('✅ SweetAlert2 initialisé');
}
