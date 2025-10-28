/**
 * resources/js/app.js
 */

// --- CSS global ---
import '../css/app.css';

// --- Imports des modules ---
import initBootstrap    from './modules/bootstrap';
import initAOS          from './modules/aos';
import initAlpine       from './modules/alpine';
import initAxios        from './modules/axios';
import initDayjs        from './modules/dayjs';
import initSweetAlert   from './modules/sweetalert';
import initEcho         from './modules/echo';
import monitorReverb    from './modules/reverbStatus';

// --- Initialisations ---
initBootstrap();
initAxios();
initDayjs();
initSweetAlert();
initAlpine();
initAOS();
initEcho();
monitorReverb();

// --- en bas de app.js ---
import initUsersManagement from './components/usersManagement';

// on stocke dans un registre global
window.pageScripts = {
    usersManagement: initUsersManagement,
};
