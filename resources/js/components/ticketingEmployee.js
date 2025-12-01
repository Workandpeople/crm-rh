// resources/js/components/ticketingEmployee.js
export default function initTicketingEmployee() {
    const page = document.querySelector(".ticketing-employee-page");
    if (!page) return;

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    const companyId = page.dataset.companyId || "";
    const userId = page.dataset.userId || "";

    const list = document.getElementById("ticketListMe");
    const statTotal = document.getElementById("stat-total");
    const statPending = document.getElementById("stat-pending");
    const statValidated = document.getElementById("stat-validated");
    const statRefused = document.getElementById("stat-refused");

    // Filtres
    const selectType = document.getElementById("filter-ticket-type-me");
    const selectStatus = document.getElementById("filter-ticket-status-me");
    const inputStart = document.getElementById("filter-ticket-start-me");
    const inputEnd = document.getElementById("filter-ticket-end-me");
    const inputSearch = document.getElementById("filter-ticket-search-me");

    // Modale création
    const modalCreateEl = document.getElementById("modalTicketCreateMe");
    const modalCreate = modalCreateEl
        ? new window.bootstrap.Modal(modalCreateEl)
        : null;
    const formCreate = document.getElementById("formCreateTicketMe");
    const btnAddTicket = document.getElementById("btnEmployeeAddTicket");
    const hiddenCompany = document.getElementById("ticketCompanyMe");

    // Modale détail
    const modalDetailsEl = document.getElementById("modalTicketDetailsMe");
    const modalDetails = modalDetailsEl
        ? new window.bootstrap.Modal(modalDetailsEl)
        : null;

    const detailRefs = {
        type: document.getElementById("ticketDetailTypeMe"),
        priority: document.getElementById("ticketDetailPriorityMe"),
        status: document.getElementById("ticketDetailStatusMe"),
        statusText: document.getElementById("ticketDetailStatusTextMe"),
        title: document.getElementById("ticketDetailTitleMe"),
        description: document.getElementById("ticketDetailDescriptionMe"),
        creator: document.getElementById("ticketDetailCreatorMe"),
        assignee: document.getElementById("ticketDetailAssigneeMe"),
        due: document.getElementById("ticketDetailDueDateMe"),
    };

    const FILTER_KEY = "ticketingEmployeeFilters";

    const showToast = (message, type = "success") => {
        const container =
            document.getElementById("toastContainer") ||
            (() => {
                const c = document.createElement("div");
                c.id = "toastContainer";
                c.className = "toast-container position-fixed top-0 end-0 p-3";
                document.body.appendChild(c);
                return c;
            })();

        const wrap = document.createElement("div");
        wrap.innerHTML = `
          <div class="toast align-items-center text-white bg-${
              type === "success" ? "success" : "danger"
          } border-0 mb-2" role="alert">
            <div class="d-flex">
              <div class="toast-body">${message}</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
          </div>`;
        const toastEl = wrap.firstElementChild;
        container.appendChild(toastEl);
        new window.bootstrap.Toast(toastEl, { delay: 3000 }).show();
    };

    function getSavedFilters() {
        try {
            return JSON.parse(localStorage.getItem(FILTER_KEY) || "{}");
        } catch {
            return {};
        }
    }

    function applyFiltersToUI(f) {
        if (selectType) selectType.value = f.type || "";
        if (selectStatus) selectStatus.value = f.status || "";
        if (inputStart) inputStart.value = f.start || "";
        if (inputEnd) inputEnd.value = f.end || "";
        if (inputSearch) inputSearch.value = f.search || "";
    }

    function getFilters() {
        return {
            type: selectType?.value || "",
            status: selectStatus?.value || "",
            start: inputStart?.value || "",
            end: inputEnd?.value || "",
            search: inputSearch?.value || "",
        };
    }

    function saveFilters(f) {
        localStorage.setItem(FILTER_KEY, JSON.stringify(f));
    }

    function formatDate(d) {
        if (!d) return "—";
        return new Date(d).toLocaleDateString("fr-FR");
    }

    function formatDateTime(d) {
        if (!d) return "—";
        return new Date(d).toLocaleString("fr-FR", {
            dateStyle: "short",
            timeStyle: "short",
        });
    }

    function typeIcon(type) {
        const map = {
            conge: "fa-plane-departure",
            note_frais: "fa-receipt",
            document_rh: "fa-file-contract",
            incident: "fa-triangle-exclamation",
            autre: "fa-ticket",
        };
        return map[type] || "fa-ticket";
    }

    function statusLabel(status) {
        const map = {
            en_attente: "En attente",
            valide: "Validé",
            refuse: "Refusé",
        };
        return map[status] || status || "—";
    }

    function priorityLabel(priority) {
        const map = { basse: "Basse", moyenne: "Moyenne", haute: "Haute" };
        return map[priority] || priority || "—";
    }

    async function loadTickets() {
        if (!list) return;

        const filters = getFilters();
        saveFilters(filters);

        list.innerHTML = '<p class="text-muted p-3">Chargement des tickets...</p>';

        try {
            const url = new URL("/admin/backlogs", window.location.origin);
            if (companyId) url.searchParams.set("company_id", companyId);
            url.searchParams.set("mine", "1");
            if (filters.type) url.searchParams.set("type", filters.type);
            if (filters.status) url.searchParams.set("status", filters.status);
            if (filters.start) url.searchParams.set("start", filters.start);
            if (filters.end) url.searchParams.set("end", filters.end);
            if (filters.search) url.searchParams.set("search", filters.search);

            const res = await fetch(url.toString(), {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();

            renderTickets(data.tickets || []);
            updateStats(data.stats || {});
        } catch (err) {
            console.error(err);
            list.innerHTML = `<p class="text-danger p-3">Erreur de chargement : ${err.message}</p>`;
        }
    }

    function renderTickets(tickets) {
        if (!tickets.length) {
            list.innerHTML =
                '<p class="text-muted p-3">Aucun ticket pour l’instant.</p>';
            return;
        }

        list.innerHTML = tickets
            .map((t) => {
                const statusCls = `ticket-status ${t.status || ""}`;
                const due =
                    t.due_date && new Date(t.due_date) < new Date()
                        ? `<span class="text-danger ms-2 small">(échu)</span>`
                        : "";
                return `
          <div class="ticket-card" data-id="${t.id}">
            <div class="ticket-header">
              <span class="ticket-type ${t.type || ""}">
                <i class="fa-solid ${typeIcon(t.type)} me-1"></i>
                ${t.type || "-"}
              </span>
              <span class="${statusCls}">${statusLabel(t.status)}</span>
            </div>
            <h5 class="ticket-title">${t.title ?? "-"}</h5>
            <p class="ticket-desc">${t.description ?? ""}</p>
            <div class="ticket-footer">
              <small>Créé le ${formatDate(t.created_at)}${due ? due : ""}</small>
              <button class="btn-ticket-action" data-id="${t.id}">Détails</button>
            </div>
          </div>`;
            })
            .join("");

        list.querySelectorAll(".btn-ticket-action").forEach((btn) => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.id;
                await openDetails(id);
            });
        });
    }

    async function openDetails(id) {
        try {
            const res = await fetch(`/admin/backlogs/${id}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const t = await res.json();

            detailRefs.type.className =
                "badge ticket-type-badge " + (t.type || "");
            detailRefs.type.textContent = t.type || "-";
            detailRefs.priority.className =
                "badge ticket-priority-badge " + (t.priority || "");
            detailRefs.priority.textContent = priorityLabel(t.priority);
            detailRefs.status.className =
                "badge ticket-status-badge " + (t.status || "");
            detailRefs.status.textContent = statusLabel(t.status);
            detailRefs.statusText.textContent = statusLabel(t.status);
            detailRefs.title.textContent = t.title || "-";
            detailRefs.description.textContent = t.description || "-";
            detailRefs.creator.textContent = t.creator?.full_name || "—";
            detailRefs.assignee.textContent =
                t.assignee?.full_name || "Non assigné";
            detailRefs.due.textContent = formatDate(t.due_date);

            modalDetails?.show();
        } catch (err) {
            console.error(err);
            showToast("Impossible de charger le ticket", "error");
        }
    }

    function updateStats(stats) {
        if (statTotal) statTotal.textContent = stats.total ?? "0";
        if (statPending) statPending.textContent = stats.pending ?? "0";
        if (statValidated) statValidated.textContent = stats.validated ?? "0";
        if (statRefused) statRefused.textContent = stats.refused ?? "0";
    }

    // --- Events ---
    [selectType, selectStatus, inputStart, inputEnd, inputSearch].forEach(
        (el) => {
            el?.addEventListener("input", () => loadTickets());
        }
    );

    btnAddTicket?.addEventListener("click", () => {
        if (hiddenCompany && companyId) hiddenCompany.value = companyId;
        modalCreate?.show();
    });

    formCreate?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const fd = new FormData(formCreate);
        if (hiddenCompany && companyId) {
            fd.set("company_id", companyId);
        }

        try {
            const res = await fetch("/admin/backlogs", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: fd,
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            modalCreate?.hide();
            formCreate.reset();
            showToast("Ticket créé avec succès");
            await loadTickets();
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la création", "error");
        }
    });

    // --- Init ---
    applyFiltersToUI(getSavedFilters());
    loadTickets();
}
