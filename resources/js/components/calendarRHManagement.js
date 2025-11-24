export default function initCalendarRHManagement() {
    console.log("%c[calendarRHManagement] init", "color: #38bdf8");

    const page = document.querySelector(".calendar-admin-page");
    if (!page) return;

    // FullCalendar via CDN global
    const FC = window.FullCalendar;
    if (!FC) {
        console.error("[calendarRhManagement] FullCalendar non chargé (CDN)");
        return;
    }

    const calendarContainer = document.createElement("div");
    calendarContainer.id = "rhCalendar";
    calendarContainer.style.minHeight = "500px";

    // Si tu as encore l’ancienne grille statique, on la masque
    const oldGrid = page.querySelector(".calendar-grid");
    if (oldGrid) oldGrid.style.display = "none";

    // On insère le vrai calendrier juste après le header / légende
    const legend = page.querySelector(".calendar-legend");
    if (legend && legend.nextElementSibling) {
        legend.parentNode.insertBefore(
            calendarContainer,
            legend.nextElementSibling
        );
    } else {
        page.appendChild(calendarContainer);
    }

    // Boutons prev / next et titre du mois
    const btnPrev = page.querySelector(".calendar-nav.prev");
    const btnNext = page.querySelector(".calendar-nav.next");
    const monthTitleEl = page.querySelector(".month-title");

    const companyId = localStorage.getItem("selectedCompanyId");
    const teamId = localStorage.getItem("selectedTeamId");

    function updateMonthTitle(calendar) {
        if (!monthTitleEl) return;
        const currentDate = calendar.getDate();
        const formatter = new Intl.DateTimeFormat("fr-FR", {
            month: "long",
            year: "numeric",
        });
        let txt = formatter.format(currentDate);
        // Première lettre en majuscule
        txt = txt.charAt(0).toUpperCase() + txt.slice(1);
        monthTitleEl.textContent = txt;
    }

    const calendar = new FC.Calendar(calendarContainer, {
        initialView: "dayGridMonth",
        locale: "fr",
        firstDay: 1, // Lundi
        height: "auto",
        expandRows: true,
        headerToolbar: false, // on gère la navigation avec tes propres boutons
        buttonText: {
            today: "Aujourd’hui",
        },
        // Chargement des événements (congés) via ton controller
        events: async (info, successCallback, failureCallback) => {
            try {
                const url = new URL(
                    "/admin/calendar-rh/events",
                    window.location.origin
                );
                url.searchParams.set("start", info.startStr);
                url.searchParams.set("end", info.endStr);
                if (companyId) url.searchParams.set("company_id", companyId);
                if (teamId) url.searchParams.set("team_id", teamId);

                const res = await fetch(url.toString(), {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();
                successCallback(data.events || []);
            } catch (e) {
                console.error(
                    "[calendarRHManagement] Erreur chargement events",
                    e
                );
                failureCallback(e);
            }
        },

        // Classes custom pour colorer selon le type
        eventClassNames: (info) => {
            const type = info.event.extendedProps.type || "autre";
            return ["rh-calendar-event", `rh-event-${type}`];
        },

        // Tooltip simple en title HTML
        eventDidMount: (info) => {
            const type = info.event.extendedProps.type;
            const baseTitle = info.event.title || "";
            let prefix = "";

            switch (type) {
                case "conge":
                    prefix = "Congé : ";
                    break;
                case "maladie":
                    prefix = "Maladie : ";
                    break;
                case "formation":
                    prefix = "Formation : ";
                    break;
                case "entretien":
                    prefix = "Entretien : ";
                    break;
                case "evenement":
                    prefix = "Événement interne : ";
                    break;
                default:
                    prefix = "";
            }

            info.el.title = prefix + baseTitle;
        },
        datesSet: () => {
            updateMonthTitle(calendar);
        },
    });

    calendar.render();
    updateMonthTitle(calendar);

    // Navigation mois précédent / suivant
    btnPrev?.addEventListener("click", () => {
        calendar.prev();
        updateMonthTitle(calendar);
    });

    btnNext?.addEventListener("click", () => {
        calendar.next();
        updateMonthTitle(calendar);
    });
}
