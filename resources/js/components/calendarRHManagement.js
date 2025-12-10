export default function initCalendarRHManagement() {
    console.log("%c[calendarRHManagement] init", "color: #38bdf8");

    const page = document.querySelector(".calendar-admin-page");
    if (!page) return;

    const FC = window.FullCalendar;
    if (!FC) {
        console.error("[calendarRHManagement] FullCalendar non chargé (CDN)");
        return;
    }

    const calendarContainer = document.createElement("div");
    calendarContainer.id = "rhCalendar";
    calendarContainer.style.minHeight = "500px";

    const oldGrid = page.querySelector(".calendar-grid");
    if (oldGrid) oldGrid.style.display = "none";

    const legend = page.querySelector(".calendar-legend");
    if (legend && legend.nextElementSibling) {
        legend.parentNode.insertBefore(calendarContainer, legend.nextElementSibling);
    } else {
        page.appendChild(calendarContainer);
    }

    const btnPrev      = page.querySelector(".calendar-nav.prev");
    const btnNext      = page.querySelector(".calendar-nav.next");
    const monthTitleEl = page.querySelector(".month-title");

    const companyId = localStorage.getItem("selectedCompanyId");
    const teamId    = localStorage.getItem("selectedTeamId");

    function updateMonthTitle(calendar) {
        if (!monthTitleEl) return;
        const currentDate = calendar.getDate();
        const formatter = new Intl.DateTimeFormat("fr-FR", {
            month: "long",
            year: "numeric",
        });
        let txt = formatter.format(currentDate);
        txt = txt.charAt(0).toUpperCase() + txt.slice(1);
        monthTitleEl.textContent = txt;
    }

    const calendar = new FC.Calendar(calendarContainer, {
        initialView: "dayGridMonth",
        locale: "fr",
        firstDay: 1,
        height: "auto",
        expandRows: true,
        headerToolbar: false,
        buttonText: {
            today: "Aujourd’hui",
        },

        // Récupération des congés depuis ton controller
        events: async (info, successCallback, failureCallback) => {
            try {
                const url = new URL("/admin/calendar-rh/events", window.location.origin);
                url.searchParams.set("start", info.startStr);
                url.searchParams.set("end", info.endStr);
                if (companyId) url.searchParams.set("company_id", companyId);
                if (teamId)    url.searchParams.set("team_id", teamId);

                const res = await fetch(url.toString(), {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const data = await res.json();
                successCallback(data.events || []);
            } catch (e) {
                console.error("[calendarRHManagement] Erreur chargement events", e);
                failureCallback(e);
            }
        },

        // Classes de couleur selon le type de congé brut (CP, SansSolde, Exceptionnel, Maladie)
        eventClassNames: (info) => {
            const rawType = (info.event.extendedProps.raw_type || "").toString();
            const slug = rawType
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "") // supprime accents
                .toLowerCase()
                .replace(/\s+/g, "-"); // SansSolde -> sanssolde, "Absence exceptionnelle" -> absence-exceptionnelle

            return [
                "rh-calendar-event",
                "rh-leave-event",
                slug ? `rh-leave-${slug}` : "rh-leave-unknown",
            ];
        },

        // Tooltip : "Congés payés — Jean Dupont" etc.
        eventDidMount: (info) => {
            const rawType = info.event.extendedProps.raw_type || "";
            const fullName = info.event.title || "";

            const labels = {
                CP: "Congés payés",
                SansSolde: "Sans solde",
                Exceptionnel: "Absence exceptionnelle",
                Maladie: "Maladie",
            };

            const label = labels[rawType] || rawType || "Congé";
            info.el.title = `${label} — ${fullName}`;
        },

        datesSet: () => {
            updateMonthTitle(calendar);
        },
    });

    calendar.render();
    updateMonthTitle(calendar);

    btnPrev?.addEventListener("click", () => {
        calendar.prev();
        updateMonthTitle(calendar);
    });

    btnNext?.addEventListener("click", () => {
        calendar.next();
        updateMonthTitle(calendar);
    });

    const btnToday = document.querySelector(".calendar-nav.today");
    btnToday?.addEventListener("click", () => {
        calendar.today();
        updateMonthTitle(calendar);
    });
}

