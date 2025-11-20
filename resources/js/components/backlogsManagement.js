export default function initBacklogsManagement() {
    console.log("Initialisation de la gestion des backlogs");

    const page = document.querySelector(".ticketing-admin-page");
    if (!page) return;

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;

    // Modale création
    const btnAddTicket = document.getElementById("btnAddTicket");
    const modalEl = document.getElementById("modalTicketCreate");
    const modalTicket = modalEl ? new window.bootstrap.Modal(modalEl) : null;
    const formCreateTicket = document.getElementById("formCreateTicket");
    const assigneeSelect = document.getElementById("ticketAssignee");

    // Modale détails
    const modalDetailsEl = document.getElementById("modalTicketDetails");
    const modalTicketDetails = modalDetailsEl
        ? new window.bootstrap.Modal(modalDetailsEl)
        : null;

    const filters = page.querySelectorAll(".filter-btn");
    const list = page.querySelector(".ticket-list");
    const statsCards = page.querySelectorAll(".stat-card p");
    const companyId = localStorage.getItem("selectedCompanyId");
    const teamId = localStorage.getItem("selectedTeamId");

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

    async function loadAssignees() {
        if (!assigneeSelect) return;

        try {
            const url = new URL(
                "/admin/backlogs/options",
                window.location.origin
            );
            if (companyId) url.searchParams.set("company_id", companyId);

            const res = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            const assignees = data.assignees || [];

            assigneeSelect.innerHTML =
                `<option value="">— À définir plus tard —</option>` +
                assignees
                    .map(
                        (a) =>
                            `<option value="${
                                a.id
                            }">${a.last_name.toUpperCase()} ${a.first_name} — ${
                                a.email
                            }</option>`
                    )
                    .join("");
        } catch (err) {
            console.error(err);
            showToast("Erreur lors du chargement des assignations", "error");
        }
    }

    // Chargement initial
    loadTickets("all");
    loadAssignees();
    function formatStatus(status) {
    switch (status) {
        case 'en_attente': return 'En attente';
        case 'valide': return 'Validé';
        case 'refuse': return 'Refusé';
        default: return status;
    }
}

    // Filtres
    filters.forEach((btn) => {
        btn.addEventListener("click", () => {
            filters.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            loadTickets(btn.dataset.type);
        });
    });

    async function loadTickets(type = "all") {
        list.innerHTML = `<p class="text-muted p-3">Chargement...</p>`;
        try {
            const url = new URL(`/admin/backlogs`, window.location.origin);
            url.searchParams.set("type", type);
            url.searchParams.set("mode", "ajax");
            if (companyId) url.searchParams.set("company_id", companyId);
            if (teamId) url.searchParams.set("team_id", teamId);

            const res = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            renderTickets(data.tickets);
            updateStats(data.stats);
        } catch (err) {
            list.innerHTML = `<p class="text-danger p-3">Erreur de chargement : ${err.message}</p>`;
        }
    }

    function renderTickets(tickets) {
        if (!tickets.length) {
            list.innerHTML = `<p class="text-muted p-3">Aucun ticket trouvé.</p>`;
            return;
        }

        list.innerHTML = tickets
            .map(
                (t) => `
      <div class="ticket-card">
        <div class="ticket-header">
          <div class="d-flex align-items-center gap-2">
            <span class="ticket-type ${t.type}">
              ${icon(t.type)} ${t.type.replace("_", " ")}
            </span>
            <span class="ticket-user">${
                t.creator?.full_name ?? "Utilisateur inconnu"
            }</span>
          </div>
          <span class="ticket-status ${t.status}">${formatStatus(t.status)}</span>
        </div>
        <h5 class="ticket-title">${t.title}</h5>
        <p class="ticket-desc">${t.description ?? ""}</p>
        <div class="ticket-footer">
          <small>Créé le ${formatDate(t.created_at)}</small>
          <div class="actions">
            ${
                t.status === "en_attente"
                    ? `
              <button class="btn-action valide" data-id="${t.id}"><i class="fa-solid fa-check"></i></button>
              <button class="btn-action refuse" data-id="${t.id}"><i class="fa-solid fa-xmark"></i></button>
            `
                    : ""
            }
            <button class="btn-action details" data-id="${
                t.id
            }"><i class="fa-solid fa-eye"></i></button>
          </div>
        </div>
      </div>
    `
            )
            .join("");
    }

    function updateStats(stats) {
        const [total, pending, validated, refused] = statsCards;
        total.textContent = stats.total;
        pending.textContent = stats.pending;
        validated.textContent = stats.validated;
        refused.textContent = stats.refused;
    }

    function icon(type) {
        switch (type) {
            case "conge":
                return '<i class="fa-solid fa-plane-departure"></i>';
            case "note_frais":
                return '<i class="fa-solid fa-receipt"></i>';
            case "incident":
                return '<i class="fa-solid fa-triangle-exclamation"></i>';
            default:
                return '<i class="fa-solid fa-circle-question"></i>';
        }
    }

    function formatDate(dateStr) {
        return new Date(dateStr).toLocaleDateString("fr-FR");
    }

    btnAddTicket?.addEventListener("click", () => {
        // On peut pré-remplir certains champs si besoin
        formCreateTicket?.reset();
        modalTicket?.show();
    });

    formCreateTicket?.addEventListener("submit", async (e) => {
        e.preventDefault();
        try {
            const formData = new FormData(formCreateTicket);

            // Ajout du company_id depuis le localStorage si présent
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
            loadTickets(
                document.querySelector(".filter-btn.active")?.dataset.type ||
                    "all"
            );
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la création du ticket", "error");
        }
    });

    // Délégation pour les boutons Valider / Refuser
    list.addEventListener("click", async (e) => {
        const btnDetails = e.target.closest(".btn-action.details");
        const btnValide = e.target.closest(".btn-action.valide");
        const btnRefuse = e.target.closest(".btn-action.refuse");

        // === CAS 1 : DÉTAILS ===
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

        // === CAS 2 : VALIDER / REFUSER ===
        if (!btnValide && !btnRefuse) return;

        const card = e.target.closest(".ticket-card");
        const id =
            card?.querySelector(".btn-action.details")?.dataset.id ||
            btnValide?.dataset.id ||
            btnRefuse?.dataset.id;

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
            loadTickets(
                document.querySelector(".filter-btn.active")?.dataset.type ||
                    "all"
            );
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
            autre: "Autre",
        };

        const priorityLabel = {
            basse: "Basse",
            moyenne: "Moyenne",
            haute: "Haute",
        };

        // Titre / description
        byId("ticketDetailTitle").textContent = t.title || "—";
        byId("ticketDetailDescription").textContent = t.description || "—";

        // Type
        const typeEl = byId("ticketDetailType");
        typeEl.textContent = typeLabel[t.type] ?? t.type ?? "—";
        typeEl.className = "badge ticket-type-badge " + (t.type || "");

        // Priorité
        const prioEl = byId("ticketDetailPriority");
        prioEl.textContent =
            priorityLabel[t.priority] ?? t.priority ?? "Moyenne";
        prioEl.className =
            "badge ticket-priority-badge " + (t.priority || "moyenne");

        // Statut
        const statusEl = byId("ticketDetailStatus");
        statusEl.textContent = t.status ?? "—";
        statusEl.className = "badge ticket-status-badge " + (t.status || "");

        // Métadonnées
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
    }
}
