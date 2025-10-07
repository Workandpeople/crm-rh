import axios from 'axios';

export default function initAxios() {
    window.axios = axios;
    window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
    console.log('✅ Axios initialisé');
}
