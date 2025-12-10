export default function initBacklogsManagement() {
    console.log("%c[backlogsManagement] init", "color: #6366f1");

    const page = document.querySelector(".ticketing-admin-page");
    if (!page) return;

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;

    // --- Elements principaux ---
    const list = page.querySelector(".ticket-list");
    const statsCards = page.querySelectorAll(".ticket-stats .stat-card p");

    // Filtres avancés
    const selectType = document.getElementById("filter-ticket-type");
    const selectEmployee = document.getElementById("filter-ticket-employee");
    const selectStatus = document.getElementById("filter-ticket-status");
    const inputStart = document.getElementById("filter-ticket-start");
    const inputEnd = document.getElementById("filter-ticket-end");
    const inputSearch = document.getElementById("filter-ticket-search");
    const btnReset = document.getElementById("btnTicketsReset");
    const extraFilterGroups = document.querySelectorAll(".filter-group-extra");

    // Bouton + modale création
    const btnAddTicket = document.getElementById("btnAddTicket");
    const modalEl = document.getElementById("modalTicketCreate");
    const modalTicket = modalEl ? new window.bootstrap.Modal(modalEl) : null;
    const formCreateTicket = document.getElementById("formCreateTicket");
    const assigneeSelect = document.getElementById("ticketAssignee");
    const relatedUserSelect = document.getElementById("ticketRelatedUser");

    // Elements spécifiques à la nouvelle modale
    const typeToggles = document.querySelectorAll(".ticket-type-toggle");
    const ticketTypeInput = document.getElementById("ticketTypeInput");
    const extraGroups = document.querySelectorAll(".ticket-extra-group");

    // Modale détails
    const modalDetailsEl = document.getElementById("modalTicketDetails");
    const modalTicketDetails = modalDetailsEl
        ? new window.bootstrap.Modal(modalDetailsEl)
        : null;

    // Cache local
    let ticketsCache = [];

    const FILTER_STORAGE_KEY = "backlogFilters";

    function getCompanyId() {
        return localStorage.getItem("selectedCompanyId") || "";
    }

    // --- TOAST helper ---
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

    // --- Helpers filtres <-> localStorage ---
    function loadSavedFilters() {
        try {
            const raw = localStorage.getItem(FILTER_STORAGE_KEY);
            if (!raw) {
                return {
                    type: "",
                    employee_id: "",
                    status: "",
                    start: "",
                    end: "",
                    search: "",
                };
            }
            const parsed = JSON.parse(raw);
            return {
                type: parsed.type || "",
                employee_id: parsed.employee_id || "",
                status: parsed.status || "",
                start: parsed.start || "",
                end: parsed.end || "",
                search: parsed.search || "",
            };
        } catch (e) {
            console.warn(
                "[backlogsManagement] Impossible de parser les filtres",
                e
            );
            return {
                type: "",
                employee_id: "",
                status: "",
                start: "",
                end: "",
                search: "",
            };
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

            // filtres spécifiques
            leave_start: document.getElementById("filter-leave-start")?.value || "",
            leave_end: document.getElementById("filter-leave-end")?.value || "",
            expense_min: document.getElementById("filter-expense-min")?.value || "",
            expense_max: document.getElementById("filter-expense-max")?.value || "",
            document_type: document.getElementById("filter-document-type")?.value || "",
            incident_severity: document.getElementById("filter-incident-severity")?.value || "",
        };
    }

    function applyFiltersToUI(filters) {
        if (selectType) selectType.value = filters.type || "";
        if (selectEmployee) selectEmployee.value = filters.employee_id || "";
        if (selectStatus) selectStatus.value = filters.status || "";
        if (inputStart) inputStart.value = filters.start || "";
        if (inputEnd) inputEnd.value = filters.end || "";
        if (inputSearch) inputSearch.value = filters.search || "";
    }

    function updateExtraFiltersVisibility() {
        const type = selectType?.value || "";

        extraFilterGroups.forEach((group) => {
            const extraType = group.getAttribute("data-extra-type");
            if (!type || type !== extraType) {
                group.classList.add("d-none");
            } else {
                group.classList.remove("d-none");
            }
        });
    }

    // --- Chargement assignables (dropdown dans la modale) ---
    async function loadAssignees() {
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
            const assignees = data.assignees || [];

            // Pour l’instant, on utilise la même liste pour "Attribuer à" et éventuellement "Employé concerné"
            if (assigneeSelect) {
                assigneeSelect.innerHTML =
                    `<option value="">— À définir plus tard —</option>` +
                    assignees
                        .map(
                            (a) =>
                                `<option value="${
                                    a.id
                                }">${a.last_name.toUpperCase()} ${
                                    a.first_name
                                } — ${a.email}</option>`
                        )
                        .join("");
            }

            if (relatedUserSelect) {
                relatedUserSelect.innerHTML =
                    `<option value="">— Sélectionner un employé —</option>` +
                    assignees
                        .map(
                            (a) =>
                                `<option value="${a.id}">${
                                    a.first_name
                                } ${a.last_name.toUpperCase()}</option>`
                        )
                        .join("");
            }
        } catch (err) {
            console.error(err);
            showToast("Erreur lors du chargement des utilisateurs", "error");
        }
    }

    // --- Chargement des tickets depuis le back ---
    async function loadTickets(applyEmployeesFromResult = true) {
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
            '<p class="text-muted p-3">Chargement des tickets...</p>';

        try {
            const url = new URL("/admin/backlogs", window.location.origin);
            url.searchParams.set("company_id", companyId);

            // ======================
            // 📌 FILTRES DE BASE
            // ======================

            if (filters.type)
                url.searchParams.set("type", filters.type);

            if (filters.status)
                url.searchParams.set("status", filters.status);

            if (filters.search)
                url.searchParams.set("search", filters.search.trim());

            // ======================
            // 📌 FILTRES SPÉCIFIQUES AU TYPE DE TICKET
            // ======================

            // --- CONGÉS ---
            if (filters.leave_start)
                url.searchParams.set("leave_start", filters.leave_start);

            if (filters.leave_end)
                url.searchParams.set("leave_end", filters.leave_end);

            // --- NOTES DE FRAIS ---
            if (filters.expense_min)
                url.searchParams.set("expense_min", filters.expense_min);

            if (filters.expense_max)
                url.searchParams.set("expense_max", filters.expense_max);

            // --- DOCUMENTS RH ---
            if (filters.document_type)
                url.searchParams.set("document_type", filters.document_type);

            // --- INCIDENTS ---
            if (filters.incident_severity)
                url.searchParams.set("incident_severity", filters.incident_severity);

            // ======================
            // 📡 REQUÊTE
            // ======================

            const res = await fetch(url.toString(), {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            ticketsCache = data.tickets || [];

            renderTickets(ticketsCache);
            updateStats(data.stats || {});

            if (applyEmployeesFromResult) {
                populateEmployeeFilterFromTickets(
                    ticketsCache,
                    filters.employee_id
                );
            }
        } catch (err) {
            console.error(err);
            list.innerHTML = `<p class="text-danger p-3">Erreur de chargement : ${err.message}</p>`;
        }
    }


    function populateEmployeeFilterFromTickets(tickets, selectedId = "") {
        if (!selectEmployee) return;

        const map = new Map();
        tickets.forEach((t) => {
            if (t.creator) {
                map.set(t.creator.id, {
                    id: t.creator.id,
                    full_name:
                        t.creator.full_name ||
                        `${t.creator.first_name ?? ""} ${
                            t.creator.last_name ?? ""
                        }`.trim(),
                });
            }
        });

        const options = [...map.values()];
        selectEmployee.innerHTML =
            '<option value="">Tous</option>' +
            options
                .map(
                    (u) =>
                        `<option value="${u.id}" ${
                            u.id === selectedId ? "selected" : ""
                        }>${u.full_name}</option>`
                )
                .join("");
    }

    // --- Rendu tickets & stats ---
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
    function formatMoney(amount) {
        if (amount == null) return "—";
        const n = Number(amount);
        if (Number.isNaN(n)) return amount;
        return n.toFixed(2).replace(".", ",") + " €";
    }

    function getEmployeeName(t) {
        const creatorName =
            t.creator?.full_name ||
            `${t.creator?.first_name ?? ""} ${
                t.creator?.last_name ?? ""
            }`.trim() ||
            "Utilisateur inconnu";

        const relatedName =
            t.related_user?.full_name ||
            `${t.related_user?.first_name ?? ""} ${
                t.related_user?.last_name ?? ""
            }`.trim();

        return relatedName || creatorName;
    }

    function buildExtraInfo(t) {
        const employeeName = getEmployeeName(t);

        switch (t.type) {
            case "conge": {
                const start = t.leave_start_date
                    ? formatDate(t.leave_start_date)
                    : "—";
                const end = t.leave_end_date
                    ? formatDate(t.leave_end_date)
                    : "—";
                return `
                    <p class="ticket-desc">${t.description ?? ""}</p>
                    <p class="ticket-meta">
                        <span><strong>Période :</strong> ${start} → ${end}</span>
                        <span><strong>Employé :</strong> ${employeeName}</span>
                    </p>
                `;
            }

            case "note_frais": {
                const date = t.expense_date ? formatDate(t.expense_date) : "—";
                const amount = formatMoney(t.expense_amount);
                const type = formatType(t.type); // ici on garde "Note de frais"
                return `
                    <p class="ticket-desc">${t.description ?? ""}</p>
                    <p class="ticket-meta">
                        <span><strong>Employé :</strong> ${employeeName}</span>
                        <span><strong>Montant :</strong> ${amount}</span>
                        <span><strong>Date :</strong> ${date}</span>
                    </p>
                `;
            }

            case "document_rh": {
                const expires = t.document_expires_at
                    ? formatDate(t.document_expires_at)
                    : "—";
                const docType = t.document_type || "—";
                return `
                    <p class="ticket-desc">${t.description ?? ""}</p>
                    <p class="ticket-meta">
                        <span><strong>Employé :</strong> ${employeeName}</span>
                        <span><strong>Document :</strong> ${docType}</span>
                        <span><strong>Expiration :</strong> ${expires}</span>
                    </p>
                `;
            }

            case "incident": {
                const severity =
                    t.incident_severity === "critique"
                        ? "Critique"
                        : t.incident_severity === "majeur"
                        ? "Majeur"
                        : t.incident_severity === "mineur"
                        ? "Mineur"
                        : "—";
                const due = t.due_date ? formatDate(t.due_date) : "Aucune";

                return `
                    <p class="ticket-desc">${t.description ?? ""}</p>
                    <p class="ticket-meta">
                        <span><strong>Gravité :</strong> ${severity}</span>
                        <span><strong>Échéance :</strong> ${due}</span>
                    </p>
                `;
            }

            case "autre":
            default:
                return `
                    <p class="ticket-desc">${t.description ?? ""}</p>
                `;
        }
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
                    "Utilisateur inconnu";

                const extraHtml = buildExtraInfo(t);

                return `
            <div class="ticket-card">
            <div class="ticket-header">
                <div class="left">
                <span class="ticket-type ${t.type}">
                    ${icon(t.type)} ${formatType(t.type)}
                </span>
                <span class="ticket-user">${creatorName}</span>
                </div>
                <span class="ticket-status ${t.status}">${formatStatus(
                    t.status
                )}</span>
            </div>

            <h5 class="ticket-title">${t.title}</h5>
            ${extraHtml}

            <div class="ticket-footer">
                <small>Créé le ${formatDate(t.created_at)}</small>
                <div class="actions">
                ${
                    t.status === "en_attente"
                        ? `
                    <button class="btn-action valide" data-id="${t.id}" title="Valider">
                    <i class="fa-solid fa-check"></i>
                    </button>
                    <button class="btn-action refuse" data-id="${t.id}" title="Refuser">
                    <i class="fa-solid fa-xmark"></i>
                    </button>
                `
                        : ""
                }
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

    // --- Helpers pour la modale multi-types ---
    function setTicketType(type) {
        if (ticketTypeInput) {
            ticketTypeInput.value = type;
        }

        // boutons
        typeToggles.forEach((btn) => {
            const t = btn.dataset.ticketType;
            if (t === type) {
                btn.classList.add("active");
            } else {
                btn.classList.remove("active");
            }
        });

        // blocs spécifiques
        extraGroups.forEach((block) => {
            const t = block.getAttribute("data-ticket-type");
            if (t === type) {
                block.classList.remove("d-none");
            } else {
                block.classList.add("d-none");
            }
        });
    }

    // --- Création ticket ---
    btnAddTicket?.addEventListener("click", () => {
        if (formCreateTicket) {
            formCreateTicket.reset();
        }

        // type par défaut = "conge"
        setTicketType("conge");

        // on recharge les listes d’utilisateurs / assignees
        loadAssignees();

        modalTicket?.show();
    });

    // Click sur les boutons de type
    typeToggles.forEach((btn) => {
        btn.addEventListener("click", () => {
            const type = btn.dataset.ticketType || "conge";
            setTicketType(type);
        });
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

            modalTicket?.hide();
            showToast("Ticket créé avec succès", "success");
            await loadTickets(); // recharge avec filtres en cours
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la création du ticket", "error");
        }
    });

    // --- Délégation actions Valider / Refuser / Détails ---
    list.addEventListener("click", async (e) => {
        const btnDetails = e.target.closest(".btn-action.details");
        const btnValide = e.target.closest(".btn-action.valide");
        const btnRefuse = e.target.closest(".btn-action.refuse");

        // Détails
        if (btnDetails) {
            const id = btnDetails.dataset.id;
            if (!id) return;

            try {
                const res = await fetch(`/admin/backlogs/${id}`, {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);

                const t = await res.json();
                fillTicketDetailsModal(t);
                modalTicketDetails?.show();
            } catch (err) {
                console.error(err);
                showToast("Erreur lors du chargement du ticket", "error");
            }
            return;
        }

        // Valider / Refuser
        if (!btnValide && !btnRefuse) return;
        const id = (btnValide || btnRefuse).dataset.id;
        if (!id) return;

        const newStatus = btnValide ? "valide" : "refuse";

        try {
            const res = await fetch(`/admin/backlogs/${id}/status`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken,
                },
                body: JSON.stringify({ status: newStatus }),
            });

            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            showToast(
                `Ticket ${newStatus === "valide" ? "validé" : "refusé"}`,
                "success"
            );
            await loadTickets(false); // garde les options employés actuelles
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la mise à jour du statut", "error");
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

        // --- Générique ---
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

        // Pour incident on réutilise due_date dans le bloc spécifique
        // (mais on garde le champ s'il est présent)
        // byId("ticketDetailDueDate").textContent = t.due_date
        //     ? formatDate(t.due_date)
        //     : "Aucune";

        // --- Gestion de l'affichage des blocs par type ---
        const extraGroups = document.querySelectorAll(".ticket-details-extra");
        extraGroups.forEach((g) => g.classList.add("d-none"));

        const activeGroup = document.querySelector(
            `.ticket-details-extra[data-ticket-type="${t.type}"]`
        );
        if (activeGroup) activeGroup.classList.remove("d-none");

        // --- Remplissage spécifique par type ---
        switch (t.type) {
            case "conge": {
                const leaveTypeLabel = {
                    CP: "Congés payés",
                    SansSolde: "Sans solde",
                    Exceptionnel: "Absence exceptionnelle",
                    Maladie: "Maladie",
                };
                document.getElementById("ticketDetailLeaveType").textContent =
                    leaveTypeLabel[t.leave_type] ?? t.leave_type ?? "—";

                document.getElementById("ticketDetailLeaveStart").textContent =
                    t.leave_start_date ? formatDate(t.leave_start_date) : "—";

                document.getElementById("ticketDetailLeaveEnd").textContent =
                    t.leave_end_date ? formatDate(t.leave_end_date) : "—";
                break;
            }

            case "note_frais": {
                const expenseTypeLabel = {
                    repas: "Repas",
                    peage: "Péage / autoroute",
                    hebergement: "Hébergement",
                    km: "Kilométrage",
                };
                document.getElementById("ticketDetailExpenseType").textContent =
                    expenseTypeLabel[t.expense_type] ?? t.expense_type ?? "—";

                document.getElementById(
                    "ticketDetailExpenseAmount"
                ).textContent =
                    t.expense_amount != null
                        ? `${Number(t.expense_amount).toFixed(2)} €`
                        : "—";

                document.getElementById("ticketDetailExpenseDate").textContent =
                    t.expense_date ? formatDate(t.expense_date) : "—";
                break;
            }

            case "document_rh": {
                document.getElementById(
                    "ticketDetailDocumentType"
                ).textContent = t.document_type ?? t.details?.doc_type ?? "—";

                const exp =
                    t.document_expires_at ?? t.details?.expires_at ?? null;
                document.getElementById(
                    "ticketDetailDocumentExpiresAt"
                ).textContent = exp ? formatDate(exp) : "Aucune";
                break;
            }

            case "incident": {
                const severityLabel = {
                    mineur: "Mineur",
                    majeur: "Majeur",
                    critique: "Critique",
                };
                document.getElementById(
                    "ticketDetailIncidentSeverity"
                ).textContent =
                    severityLabel[t.incident_severity] ??
                    t.incident_severity ??
                    "—";

                document.getElementById("ticketDetailDueDate").textContent =
                    t.due_date ? formatDate(t.due_date) : "Aucune";
                break;
            }

            case "autre":
            default: {
                // Rien de spécial, le bloc "autre" affiche juste un texte d'aide.
                break;
            }
        }
    }

    // --- Écouteurs sur les filtres ---
    let searchDebounce;
    function onFiltersChange() {
        loadTickets();
    }

    selectType?.addEventListener("change", () => {
        updateExtraFiltersVisibility();
        onFiltersChange();
    });
    selectEmployee?.addEventListener("change", onFiltersChange);
    selectStatus?.addEventListener("change", onFiltersChange);
    inputStart?.addEventListener("change", onFiltersChange);
    inputEnd?.addEventListener("change", onFiltersChange);

    inputSearch?.addEventListener("input", () => {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(onFiltersChange, 300);
    });

    btnReset?.addEventListener("click", () => {
        const emptyFilters = {
            type: "",
            employee_id: "",
            status: "",
            start: "",
            end: "",
            search: "",
        };
        applyFiltersToUI(emptyFilters);
        saveFiltersToStorage(emptyFilters);
        loadTickets();
    });

    // --- INIT ---
    const initialFilters = loadSavedFilters();
    applyFiltersToUI(initialFilters);
    updateExtraFiltersVisibility();
    loadTickets(true);

    // type par défaut dans la modale
    if (ticketTypeInput && !ticketTypeInput.value) {
        setTicketType("conge");
    }
}
