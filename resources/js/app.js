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
import initExpensesManagement from "./components/expensesManagement";
import initDocumentsManagement from "./components/documentsManagement";
import initCalendarRHManagement from './components/calendarRHManagement';
import initTicketingEmployee from "./components/ticketingEmployee";
import initCalendarEmployee from "./components/calendarEmployee";
import initDossierEmployee from "./components/dossierEmployee";

// Registre global appelé par le loader de la sidebar via data-script
window.pageScripts = {
    usersManagement: initUsersManagement,
    companiesManagement: initCompaniesManagement,
    teamsManagement: initTeamsManagement,
    backlogsManagement: initBacklogsManagement,
    leavesManagement: initLeavesManagement,
    expensesManagement: initExpensesManagement,
    documentsManagement: initDocumentsManagement,
    calendarRHManagement: initCalendarRHManagement,
    ticketingEmployee: initTicketingEmployee,
    calendarEmployee: initCalendarEmployee,
    dossierEmployee: initDossierEmployee,
};

function runPageScriptFromDOM(root = document) {
    const el = root.querySelector("[data-script]");
    if (!el) return;
    const key = el.dataset.script;
    if (!key) return;
    if (el.dataset.initialized === "1") return;
    if (window.pageScripts?.[key]) {
        window.pageScripts[key]();
        el.dataset.initialized = "1";
    }
}

window.runPageScriptFromDOM = runPageScriptFromDOM;

document.addEventListener("DOMContentLoaded", () => {
    runPageScriptFromDOM(document);
});
