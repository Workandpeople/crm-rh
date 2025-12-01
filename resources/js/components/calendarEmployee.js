export default function initCalendarEmployee() {
    const page = document.querySelector(".calendar-employee-page");
    if (!page) return;

    const FC = window.FullCalendar;
    if (!FC) {
        console.error("[calendarEmployee] FullCalendar non chargé");
        return;
    }

    const container = document.getElementById("calendarEmployeeContainer");
    if (!container) return;

    const btnPrev = page.querySelector(".calendar-nav.prev");
    const btnNext = page.querySelector(".calendar-nav.next");
    const monthTitleEl = page.querySelector(".month-title");

    const companyId = localStorage.getItem("selectedCompanyId");

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

    const calendar = new FC.Calendar(container, {
        initialView: "dayGridMonth",
        locale: "fr",
        firstDay: 1,
        height: "auto",
        headerToolbar: false,
        buttonText: { today: "Aujourd’hui" },
        events: async (info, success, failure) => {
            try {
                const url = new URL("/admin/backlogs", window.location.origin);
                if (companyId) url.searchParams.set("company_id", companyId);
                url.searchParams.set("mine", "1");
                url.searchParams.set("status", "valide");
                url.searchParams.set("start", info.startStr);
                url.searchParams.set("end", info.endStr);

                const res = await fetch(url.toString(), {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                const data = await res.json();
                const events =
                    (data.tickets || []).map((t) => {
                        const details = t.details || {};
                        const start =
                            details.start_date ||
                            t.created_at ||
                            info.startStr;
                        const end = details.end_date || t.due_date || start;
                        const type = t.type || "autre";
                        return {
                            id: t.id,
                            title: t.title || type,
                            start,
                            end,
                            allDay: true,
                            classNames: [
                                "rh-calendar-event",
                                `rh-event-${type}`,
                            ],
                            extendedProps: {
                                type,
                                priority: t.priority,
                            },
                        };
                    }) || [];
                success(events);
            } catch (e) {
                console.error("[calendarEmployee] events error", e);
                failure(e);
            }
        },
        eventDidMount: (info) => {
            const type = info.event.extendedProps.type || "-";
            info.el.title = `${type} • ${info.event.title}`;
        },
        datesSet: (arg) => {
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
}
