import initTicketChat from "../modules/ticketChat";

export default function initTicketingEmployee() {
    console.log("%c[ticketingEmployee] Initialisation", "color: violet;");

    const page = document.querySelector(".ticketing-employee-page");
    if (!page) return;

    const listEl = document.getElementById("employeeTicketsList");

    // Filters
    const filterType = document.getElementById("filterEmployeeType");
    const filterStatus = document.getElementById("filterEmployeeStatus");
    const filterSearch = document.getElementById("filterEmployeeSearch");

    // Stats
    const statTotal = document.getElementById("statTotal");
    const statPending = document.getElementById("statPending");
    const statValidated = document.getElementById("statValidated");
    const statRefused = document.getElementById("statRefused");

    // Modals
    const createModalEl = document.getElementById("modalEmployeeTicketCreate");
    const detailsModalEl = document.getElementById(
        "modalEmployeeTicketDetails"
    );

    const btnOpenCreate = document.getElementById("btnOpenCreateTicket");
    const formCreate = document.getElementById("formEmployeeCreateTicket");

    const createError = document.getElementById("employeeCreateError");
    const createSuccess = document.getElementById("employeeCreateSuccess");
    const createSpinner = document.getElementById("employeeCreateSpinner");

    let ticketsCache = [];

    // Bootstrap modal instances
    const createModal = createModalEl
        ? new bootstrap.Modal(createModalEl)
        : null;
    const detailsModal = detailsModalEl
        ? new bootstrap.Modal(detailsModalEl)
        : null;

    const empChatContainer = detailsModalEl?.querySelector(".ticket-chat");
    const ticketChat = initTicketChat({
        modalEl: detailsModalEl,
        listEl: document.getElementById("empTicketChatList"),
        emptyEl: document.getElementById("empTicketChatEmpty"),
        formEl: document.getElementById("empTicketChatForm"),
        inputEl: document.getElementById("empTicketChatInput"),
        currentUserId: empChatContainer?.dataset.currentUserId,
    });

    // -------- TYPE SWITCHER (create modal)
    const typeInput = document.getElementById("employeeTicketTypeInput");
    const typeToggles =
        createModalEl?.querySelectorAll(".ticket-type-toggle") || [];
    const extraGroups =
        createModalEl?.querySelectorAll(".ticket-extra-group") || [];

    function setCreateType(type) {
        if (!typeInput) return;
        typeInput.value = type;

        typeToggles.forEach((btn) => {
            btn.classList.toggle("active", btn.dataset.ticketType === type);
        });

        extraGroups.forEach((grp) => {
            const isMatch = grp.dataset.ticketType === type;
            grp.classList.toggle("d-none", !isMatch);
        });
    }

    typeToggles.forEach((btn) => {
        btn.addEventListener("click", () =>
            setCreateType(btn.dataset.ticketType)
        );
    });

    // -------- Helpers
    function esc(str) {
        return String(str ?? "").replace(
            /[&<>"']/g,
            (m) =>
                ({
                    "&": "&amp;",
                    "<": "&lt;",
                    ">": "&gt;",
                    '"': "&quot;",
                    "'": "&#039;",
                }[m])
        );
    }

    function fmtDate(iso) {
        if (!iso) return "—";
        const d = new Date(iso);
        if (Number.isNaN(d.getTime())) return "—";
        return d.toLocaleDateString("fr-FR");
    }

    function typeLabel(type) {
        return (
            {
                conge: "Congé",
                note_frais: "Note de frais",
                document_rh: "Document RH",
                incident: "Incident",
                autre: "Autre",
            }[type] || type
        );
    }

    function statusLabel(s) {
        return (
            {
                en_attente: "En attente",
                valide: "Validé",
                refuse: "Refusé",
            }[s] || s
        );
    }

    function priorityLabel(p) {
        return (
            {
                basse: "Basse",
                moyenne: "Moyenne",
                haute: "Haute",
            }[p] || p
        );
    }

    function applyBadges(el, value, kind) {
        if (!el) return;
        el.textContent = value ?? "—";
        el.classList.remove(
            "badge-conge",
            "badge-note_frais",
            "badge-document_rh",
            "badge-incident",
            "badge-autre",
            "badge-status-pending",
            "badge-status-ok",
            "badge-status-no",
            "badge-priority-low",
            "badge-priority-mid",
            "badge-priority-high"
        );

        if (kind === "type") {
            el.classList.add(
                {
                    conge: "badge-conge",
                    note_frais: "badge-note_frais",
                    document_rh: "badge-document_rh",
                    incident: "badge-incident",
                    autre: "badge-autre",
                }[value] || "badge-autre"
            );
        }

        if (kind === "status") {
            el.classList.add(
                {
                    en_attente: "badge-status-pending",
                    valide: "badge-status-ok",
                    refuse: "badge-status-no",
                }[value] || "badge-status-pending"
            );
        }

        if (kind === "priority") {
            el.classList.add(
                {
                    basse: "badge-priority-low",
                    moyenne: "badge-priority-mid",
                    haute: "badge-priority-high",
                }[value] || "badge-priority-mid"
            );
        }
    }

    function showOnlyDetailsBlock(type) {
        const blocks =
            detailsModalEl?.querySelectorAll(".ticket-details-extra") || [];
        blocks.forEach((b) =>
            b.classList.toggle("d-none", b.dataset.ticketType !== type)
        );
    }

    // -------- Render list
    function renderTickets(items) {
        if (!listEl) return;

        if (!items.length) {
            listEl.innerHTML = `<div class="empty-state">
                <i class="fa-regular fa-folder-open"></i>
                <div>
                    <h6 class="mb-1">Aucun ticket</h6>
                    <p class="mb-0">Crée ton premier ticket RH.</p>
                </div>
            </div>`;
            return;
        }

        listEl.innerHTML = items
            .map((t) => {
                const tType = t.type;
                const badgeType = esc(typeLabel(tType));
                const badgeStatus = esc(statusLabel(t.status));
                const badgePriority = esc(priorityLabel(t.priority));

                // mini résumé contextuel
                let meta = "";
                if (tType === "conge") {
                    meta = `Du <b>${esc(
                        fmtDate(t.leave_start_date)
                    )}</b> au <b>${esc(fmtDate(t.leave_end_date))}</b>`;
                } else if (tType === "note_frais") {
                    meta = `<b>${esc(t.expense_amount ?? "—")}€</b> • ${esc(
                        t.expense_type ?? "—"
                    )} • ${esc(fmtDate(t.expense_date))}`;
                } else if (tType === "document_rh") {
                    meta = `<b>${esc(t.document_type ?? "—")}</b> • exp. ${esc(
                        fmtDate(t.document_expires_at)
                    )}`;
                } else if (tType === "incident") {
                    meta = `Gravité : <b>${esc(
                        t.incident_severity ?? "—"
                    )}</b> • échéance ${esc(fmtDate(t.due_date))}`;
                }

                return `
            <button class="ticket-row" data-id="${esc(t.id)}" type="button">
                <div class="ticket-row-main">
                    <div class="ticket-row-top">
                        <div class="ticket-row-title">${esc(t.title)}</div>
                        <div class="ticket-row-badges">
                            <span class="badge ticket-type-badge ${tType}">${badgeType}</span>
                            <span class="badge ticket-status-badge ${esc(
                                t.status
                            )}">${badgeStatus}</span>
                            <span class="badge ticket-priority-badge ${esc(
                                t.priority
                            )}">${badgePriority}</span>
                        </div>
                    </div>
                    <div class="ticket-row-sub">
                        <span class="text-muted">${esc(
                            fmtDate(t.created_at)
                        )}</span>
                        ${
                            meta
                                ? `<span class="ticket-row-meta">${meta}</span>`
                                : ""
                        }
                    </div>
                </div>
                <div class="ticket-row-action">
                    <i class="fa-solid fa-chevron-right"></i>
                </div>
            </button>`;
            })
            .join("");
    }

    function updateStats(stats) {
        statTotal.textContent = stats.total ?? 0;
        statPending.textContent = stats.pending ?? 0;
        statValidated.textContent = stats.validated ?? 0;
        statRefused.textContent = stats.refused ?? 0;
    }

    // -------- API
    async function loadTickets() {
        if (!listEl) return;

        listEl.innerHTML = `<p class="text-muted p-3">Chargement…</p>`;

        const url = new URL("/admin/backlogs", window.location.origin);
        url.searchParams.set("mine", "true");

        if (filterType?.value) url.searchParams.set("type", filterType.value);
        if (filterStatus?.value)
            url.searchParams.set("status", filterStatus.value);
        if (filterSearch?.value?.trim())
            url.searchParams.set("search", filterSearch.value.trim());

        const res = await fetch(url.toString(), {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);

        const data = await res.json();
        ticketsCache = data.tickets || [];
        renderTickets(ticketsCache);
        updateStats(data.stats || {});
    }

    async function loadTicketDetails(id) {
        const url = new URL(`/admin/backlogs/${id}`, window.location.origin);
        const res = await fetch(url.toString(), {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        return res.json();
    }

    // -------- Details modal fill
    function fillDetails(ticket) {
        const type = ticket.type;

        const typeEl = document.getElementById("empDetailType");
        const statusEl = document.getElementById("empDetailStatus");
        const priorityEl = document.getElementById("empDetailPriority");

        applyBadges(typeEl, type, "type");
        applyBadges(statusEl, ticket.status, "status");
        applyBadges(priorityEl, ticket.priority, "priority");

        typeEl.textContent = typeLabel(type);
        statusEl.textContent = statusLabel(ticket.status);
        priorityEl.textContent = priorityLabel(ticket.priority);

        document.getElementById("empDetailCreatedAt").textContent = fmtDate(
            ticket.created_at
        );
        document.getElementById("empDetailTitle").textContent =
            ticket.title || "—";
        document.getElementById("empDetailDescription").textContent =
            ticket.description || "—";

        showOnlyDetailsBlock(type);

        // fill extras
        if (type === "conge") {
            document.getElementById("empLeaveType").textContent =
                ticket.leave_type || "—";
            document.getElementById("empLeaveStart").textContent = fmtDate(
                ticket.leave_start_date
            );
            document.getElementById("empLeaveEnd").textContent = fmtDate(
                ticket.leave_end_date
            );
        }
        if (type === "note_frais") {
            document.getElementById("empExpenseType").textContent =
                ticket.expense_type || "—";
            document.getElementById("empExpenseAmount").textContent =
                ticket.expense_amount ? `${ticket.expense_amount} €` : "—";
            document.getElementById("empExpenseDate").textContent = fmtDate(
                ticket.expense_date
            );
        }
        if (type === "document_rh") {
            document.getElementById("empDocType").textContent =
                ticket.document_type || "—";
            document.getElementById("empDocExp").textContent = fmtDate(
                ticket.document_expires_at
            );
        }
        if (type === "incident") {
            document.getElementById("empIncidentSeverity").textContent =
                ticket.incident_severity || "—";
            document.getElementById("empIncidentDueDate").textContent = fmtDate(
                ticket.due_date
            );
        }
    }

    // -------- Create ticket
    async function createTicket(formEl) {
        const url = new URL("/admin/backlogs", window.location.origin);

        const fd = new FormData(formEl);

        const res = await fetch(url.toString(), {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
            },
            body: fd,
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            const msg =
                data?.message || data?.error || `Erreur HTTP ${res.status}`;
            throw new Error(msg);
        }

        if (data?.success === false) {
            throw new Error(data?.message || "Erreur lors de la création");
        }

        return data; // IMPORTANT
    }

    function setCreateAlerts({ error = "", success = "" }) {
        if (createError) {
            createError.classList.toggle("d-none", !error);
            createError.textContent = error;
        }
        if (createSuccess) {
            createSuccess.classList.toggle("d-none", !success);
            createSuccess.textContent = success;
        }
    }

    // -------- Events
    btnOpenCreate?.addEventListener("click", () => {
        setCreateAlerts({ error: "", success: "" });

        formCreate?.reset();
        setCreateType("conge");

        createModal?.show();
    });

    formCreate?.addEventListener("submit", async (e) => {
        e.preventDefault();
        setCreateAlerts({ error: "", success: "" });

        createSpinner?.classList.remove("d-none");

        try {
            const data = await createTicket(formCreate);

            // ✅ Injection immédiate si l’API renvoie le ticket
            if (data?.ticket) {
                ticketsCache = [data.ticket, ...(ticketsCache || [])];
                renderTickets(ticketsCache);
            }

            setCreateAlerts({ success: "Ticket créé ✅" });

            // ✅ Reload pour être sûr que tout est cohérent
            await loadTickets();

            setTimeout(() => createModal?.hide(), 450);
            formCreate.reset();
        } catch (err) {
            setCreateAlerts({ error: err.message || "Erreur" });
        } finally {
            createSpinner?.classList.add("d-none");
        }
    });

    // click row -> details
    listEl?.addEventListener("click", async (e) => {
        const row = e.target.closest(".ticket-row");
        if (!row) return;

        const id = row.dataset.id;
        try {
            const data = await loadTicketDetails(id);
            // show endpoint renvoie un objet "ticket" ? chez toi c'est un payload custom
            // on gère les 2 cas
            const ticket = data.ticket || data;
            fillDetails(ticket);
            ticketChat?.open(ticket.id);
            detailsModal?.show();
        } catch (err) {
            console.error(err);
            alert("Erreur de chargement du détail.");
        }
    });

    // filters
    const debounce = (fn, wait = 250) => {
        let t;
        return (...args) => {
            clearTimeout(t);
            t = setTimeout(() => fn(...args), wait);
        };
    };

    filterType?.addEventListener("change", loadTickets);
    filterStatus?.addEventListener("change", loadTickets);
    filterSearch?.addEventListener("input", debounce(loadTickets, 300));

    // init
    loadTickets().catch((e) => {
        console.error(e);
        if (listEl)
            listEl.innerHTML = `<p class="text-danger p-3">Erreur de chargement.</p>`;
    });
}
