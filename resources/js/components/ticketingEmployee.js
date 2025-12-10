export default function initTicketingEmployee() {
    console.log("%c[initTicketingEmployee] init", "color: #22c55e");

    const page = document.querySelector(".ticketing-employee-page");
    if (!page) return;

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;

    // Elements
    const list = page.querySelector(".ticket-list");
    const statsCards = page.querySelectorAll(".ticket-stats .stat-card p");

    const selectType = document.getElementById("filter-ticket-type");
    const selectStatus = document.getElementById("filter-ticket-status");
    const inputSearch = document.getElementById("filter-ticket-search");

    const btnAddTicket = document.getElementById("btnAddTicket");
    const modalCreateEl = document.getElementById("modalTicketCreate");
    const modalCreate = modalCreateEl
        ? new window.bootstrap.Modal(modalCreateEl)
        : null;
    const formCreateTicket = document.getElementById("formCreateTicket");

    const modalDetailsEl = document.getElementById("modalTicketDetails");
    const modalDetails = modalDetailsEl
        ? new window.bootstrap.Modal(modalDetailsEl)
        : null;

    const assigneeSelect = document.getElementById("ticketAssignee");
    const relatedUserSelect = document.getElementById("ticketRelatedUser");

    let ticketsCache = [];

    const FILTER_STORAGE_KEY = "employeeTicketFilters";

    function getCompanyId() {
        return localStorage.getItem("selectedCompanyId") || "";
    }

    /* ------------------ TOAST HELPER ------------------ */
    function showToast(message, type = "success") {
        const bs = window.bootstrap;
        if (!bs?.Toast) {
            alert(message);
            return;
        }

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
        new bs.Toast(toastEl, { delay: 3000 }).show();
    }

    /* ------------------ FILTRES <-> STORAGE ------------------ */
    function loadSavedFilters() {
        try {
            const raw = localStorage.getItem(FILTER_STORAGE_KEY);
            if (!raw) {
                return { type: "", status: "", search: "" };
            }
            const parsed = JSON.parse(raw);
            return {
                type: parsed.type || "",
                status: parsed.status || "",
                search: parsed.search || "",
            };
        } catch {
            return { type: "", status: "", search: "" };
        }
    }

    function saveFiltersToStorage(filters) {
        localStorage.setItem(FILTER_STORAGE_KEY, JSON.stringify(filters));
    }

    function getFiltersFromUI() {
        return {
            type: selectType?.value || "",
            status: selectStatus?.value || "",
            search: inputSearch?.value || "",
        };
    }

    function applyFiltersToUI(filters) {
        if (selectType) selectType.value = filters.type || "";
        if (selectStatus) selectStatus.value = filters.status || "";
        if (inputSearch) inputSearch.value = filters.search || "";
    }

    /* ------------------ LOAD ASSIGNEES / USERS ------------------ */
    async function loadOptions() {
        if (!assigneeSelect && !relatedUserSelect) return;

        try {
            const url = new URL(
                "/admin/backlogs/options",
                window.location.origin
            );
            const companyId = getCompanyId();
            if (companyId) url.searchParams.set("company_id", companyId);

            const res = await fetch(url.toString(), {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            const users = data.users || data.assignees || [];

            const optionsHtml =
                '<option value="">— Sélectionner —</option>' +
                users
                    .map(
                        (u) =>
                            `<option value="${u.id}">${
                                u.full_name ||
                                (u.first_name ?? "") + " " + (u.last_name ?? "")
                            }</option>`
                    )
                    .join("");

            if (relatedUserSelect) relatedUserSelect.innerHTML = optionsHtml;
            if (assigneeSelect) {
                assigneeSelect.innerHTML =
                    '<option value="">— À définir plus tard —</option>' +
                    users
                        .map(
                            (u) =>
                                `<option value="${u.id}">${
                                    u.full_name ||
                                    (u.first_name ?? "") +
                                        " " +
                                        (u.last_name ?? "")
                                }</option>`
                        )
                        .join("");
            }
        } catch (err) {
            console.error(err);
            showToast("Erreur lors du chargement des options", "error");
        }
    }

    /* ------------------ LOAD TICKETS (MES TICKETS) ------------------ */
    async function loadTickets() {
        if (!list) return;

        const companyId = getCompanyId();
        if (!companyId) {
            list.innerHTML =
                '<p class="text-muted p-3">Sélectionnez d’abord une entreprise dans le header.</p>';
            return;
        }

        const filters = getFiltersFromUI();
        saveFiltersToStorage(filters);

        list.innerHTML =
            '<p class="text-muted p-3">Chargement de vos tickets...</p>';

        try {
            const url = new URL("/admin/backlogs", window.location.origin);
            url.searchParams.set("company_id", companyId);
            url.searchParams.set("mine", "1"); // 🔹 très important

            if (filters.type) url.searchParams.set("type", filters.type);
            if (filters.status) url.searchParams.set("status", filters.status);
            if (filters.search)
                url.searchParams.set("search", filters.search.trim());

            const res = await fetch(url.toString(), {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            ticketsCache = data.tickets || [];

            renderTickets(ticketsCache);
            updateStats(data.stats || {});
        } catch (err) {
            console.error(err);
            list.innerHTML = `<p class="text-danger p-3">Erreur de chargement : ${err.message}</p>`;
        }
    }

    /* ------------------ RENDER ------------------ */
    function formatStatus(status) {
        switch (status) {
            case "en_attente":
                return "En attente";
            case "valide":
                return "Validé";
            case "refuse":
                return "Refusé";
            default:
                return status || "";
        }
    }

    function formatType(type) {
        switch (type) {
            case "conge":
                return "Congé";
            case "note_frais":
                return "Note de frais";
            case "incident":
                return "Incident";
            case "document_rh":
                return "Document RH";
            case "autre":
                return "Autre";
            default:
                return type || "";
        }
    }

    function icon(type) {
        switch (type) {
            case "conge":
                return '<i class="fa-solid fa-plane-departure"></i>';
            case "note_frais":
                return '<i class="fa-solid fa-receipt"></i>';
            case "incident":
                return '<i class="fa-solid fa-triangle-exclamation"></i>';
            case "document_rh":
                return '<i class="fa-solid fa-file-lines"></i>';
            default:
                return '<i class="fa-solid fa-circle-question"></i>';
        }
    }

    function formatDate(dateStr) {
        if (!dateStr) return "—";
        return new Date(dateStr).toLocaleDateString("fr-FR");
    }

    function renderTickets(tickets) {
        if (!tickets.length) {
            list.innerHTML =
                '<p class="text-muted p-3">Aucun ticket trouvé avec ces filtres.</p>';
            return;
        }

        list.innerHTML = tickets
            .map((t) => {
                const creatorName =
                    t.creator?.full_name ||
                    `${t.creator?.first_name ?? ""} ${
                        t.creator?.last_name ?? ""
                    }`.trim() ||
                    "Moi";

                return `
        <div class="ticket-card">
          <div class="ticket-header">
            <div class="left">
              <span class="ticket-type ${t.type}">
                ${icon(t.type)} ${formatType(t.type)}
              </span>
              <span class="ticket-user">${creatorName}</span>
            </div>
            <span class="ticket-status ${t.status}">
              ${formatStatus(t.status)}
            </span>
          </div>
          <h5 class="ticket-title">${t.title}</h5>
          <p class="ticket-desc">${t.description ?? ""}</p>
          <div class="ticket-footer">
            <small>Créé le ${formatDate(t.created_at)}</small>
            <div class="actions">
              <button class="btn-action details" data-id="${
                  t.id
              }" title="Détails">
                <i class="fa-solid fa-eye"></i>
              </button>
            </div>
          </div>
        </div>
      `;
            })
            .join("");
    }

    function updateStats(stats) {
        const [totalEl, pendingEl, validatedEl, refusedEl] = statsCards;
        if (!totalEl) return;

        totalEl.textContent = stats.total ?? "0";
        pendingEl.textContent = stats.pending ?? "0";
        validatedEl.textContent = stats.validated ?? "0";
        refusedEl.textContent = stats.refused ?? "0";
    }

    /* ------------------ MODALE CRÉATION ------------------ */
    btnAddTicket?.addEventListener("click", () => {
        formCreateTicket?.reset();
        modalCreate?.show();
        loadOptions();
    });

    formCreateTicket?.addEventListener("submit", async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(formCreateTicket);
            const companyId = getCompanyId();
            if (companyId && !formData.get("company_id")) {
                formData.append("company_id", companyId);
            }

            const res = await fetch("/admin/backlogs", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: formData,
            });

            if (!res.ok) {
                const txt = await res.text();
                console.error(txt);
                throw new Error(`HTTP ${res.status}`);
            }

            modalCreate?.hide();
            showToast("Ticket créé avec succès", "success");
            await loadTickets();
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la création du ticket", "error");
        }
    });

    /* ------------------ MODALE DÉTAIL ------------------ */
    list.addEventListener("click", async (e) => {
        const btnDetails = e.target.closest(".btn-action.details");
        if (!btnDetails) return;

        const id = btnDetails.dataset.id;
        if (!id) return;

        try {
            const res = await fetch(`/admin/backlogs/${id}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const t = await res.json();
            fillTicketDetailsModal(t);
            modalDetails?.show();
        } catch (err) {
            console.error(err);
            showToast("Erreur lors du chargement du ticket", "error");
        }
    });

    function fillTicketDetailsModal(t) {
        const byId = (id) => document.getElementById(id);

        const typeLabel = {
            conge: "Congé",
            note_frais: "Note de frais",
            incident: "Incident",
            document_rh: "Document RH",
            autre: "Autre",
        };

        const priorityLabel = {
            basse: "Basse",
            moyenne: "Moyenne",
            haute: "Haute",
        };

        byId("ticketDetailTitle").textContent = t.title || "—";
        byId("ticketDetailDescription").textContent = t.description || "—";

        const typeEl = byId("ticketDetailType");
        typeEl.textContent = typeLabel[t.type] ?? t.type ?? "—";
        typeEl.className = "badge ticket-type-badge " + (t.type || "");

        const prioEl = byId("ticketDetailPriority");
        prioEl.textContent =
            priorityLabel[t.priority] ?? t.priority ?? "Moyenne";
        prioEl.className =
            "badge ticket-priority-badge " + (t.priority || "moyenne");

        const statusEl = byId("ticketDetailStatus");
        statusEl.textContent = formatStatus(t.status);
        statusEl.className = "badge ticket-status-badge " + (t.status || "");

        byId("ticketDetailCreator").textContent = t.creator?.full_name ?? "—";
        byId("ticketDetailAssignee").textContent =
            t.assignee?.full_name ?? "Non assigné";
        byId("ticketDetailRelatedUser").textContent =
            t.related_user?.full_name ?? "—";
        byId("ticketDetailCompany").textContent = t.company?.name ?? "—";

        byId("ticketDetailCreatedAt").textContent = t.created_at
            ? formatDate(t.created_at)
            : "—";
        byId("ticketDetailDueDate").textContent = t.due_date
            ? formatDate(t.due_date)
            : "Aucune";

        // Si tu veux, tu pourras compléter ici pour remplir les blocs spécifiques
        // leave / expense / document / incident avec t.leave_type, t.leave_start_date, etc.
    }

    /* ------------------ FILTRES EVENTS ------------------ */
    let searchDebounce;
    function onFiltersChange() {
        loadTickets();
    }

    selectType?.addEventListener("change", onFiltersChange);
    selectStatus?.addEventListener("change", onFiltersChange);

    inputSearch?.addEventListener("input", () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(onFiltersChange, 300);
    });

    /* ------------------ INIT ------------------ */
    const initialFilters = loadSavedFilters();
    applyFiltersToUI(initialFilters);
    loadOptions();
    loadTickets();
}
