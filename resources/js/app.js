/**
 * resources/js/app.js
 */

// --- CSS global ---
import "../css/app.css";

// --- Imports des modules ---
import initBootstrap from "./modules/bootstrap";
import initAOS from "./modules/aos";
import initAlpine from "./modules/alpine";
import initAxios from "./modules/axios";
import initDayjs from "./modules/dayjs";
import initSweetAlert from "./modules/sweetalert";
import initEcho from "./modules/echo";
import monitorReverb from "./modules/reverbStatus";

// --- Initialisations ---
initBootstrap();
initAxios();
initDayjs();
initSweetAlert();
initAlpine();
initAOS();
initEcho();
monitorReverb();

import initUsersManagement from "./components/usersManagement";
import initCompaniesManagement from "./components/companiesManagement";
import initTeamsManagement from "./components/teamsManagement";
import initBacklogsManagement from "./components/backlogsManagement";
import initLeavesManagement from "./components/leavesManagement";
// Registre global appelé par le loader de la sidebar via data-script
window.pageScripts = {
    usersManagement: initUsersManagement,
    companiesManagement: initCompaniesManagement,
    teamsManagement: initTeamsManagement,
    backlogsManagement: initBacklogsManagement,
    leavesManagement: initLeavesManagement,
};
