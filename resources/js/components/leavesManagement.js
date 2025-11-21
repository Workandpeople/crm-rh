// resources/js/components/leavesManagement.js
export default function initLeavesManagement() {
    console.log("%c[leavesManagement] Initialisation", "color: lightgreen");

    const page = document.querySelector(".conges-admin-page");
    if (!page) return;

    const csrfToken = document.querySelector(
        'meta[name="csrf-token"]'
    )?.content;
    const bs = window.bootstrap;

    // --- Filtres (type via boutons, les autres viendront plus tard) ---
    const filters = page.querySelectorAll(".filter-btn");
    const tableBody = document.getElementById("leavesTableBody");
    const statsCards = page.querySelectorAll(".conge-stats .stat-card p");

    const companyId = localStorage.getItem("selectedCompanyId");
    const teamId = localStorage.getItem("selectedTeamId");

    // --- MODALE DÉTAIL ---
    const modalDetailEl = document.getElementById("modalLeaveDetails");
    const modalDetail = modalDetailEl ? new bs.Modal(modalDetailEl) : null;

    const detailEmployee = document.getElementById("leaveDetailEmployee");
    const detailType = document.getElementById("leaveDetailType");
    const detailPeriod = document.getElementById("leaveDetailPeriod");
    const detailDuration = document.getElementById("leaveDetailDuration");
    const detailStatus = document.getElementById("leaveDetailStatus");
    const detailValidator = document.getElementById("leaveDetailValidator");
    const detailComments = document.getElementById("leaveDetailComments");
    const detailCreatedAt = document.getElementById("leaveDetailCreatedAt");
    const detailJustifLink = document.getElementById(
        "leaveDetailJustification"
    );

    // --- TOAST helper ---
    function showToast(message, type = "success") {
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
        } border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="d-flex">
            <div class="toast-body">${message}</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
          </div>
        </div>`;
        const toastEl = wrap.firstElementChild;
        container.appendChild(toastEl);
        new bs.Toast(toastEl, { delay: 3000 }).show();
    }

    // --- helpers de formatage ---
    function formatDate(dateStr, withTime = false) {
        if (!dateStr) return "—";
        const d = new Date(dateStr);
        return d.toLocaleString("fr-FR", {
            dateStyle: "short",
            ...(withTime ? { timeStyle: "short" } : {}),
        });
    }

    function computeDays(startStr, endStr) {
        if (!startStr || !endStr) return 0;
        const start = new Date(startStr);
        const end = new Date(endStr);
        const diffMs = end - start;
        const days = Math.round(diffMs / (1000 * 60 * 60 * 24)) + 1; // inclusif
        return days > 0 ? days : 0;
    }

    function typeLabel(type) {
        switch (type) {
            case "CP":
                return "Congés payés";
            case "SansSolde":
                return "Sans solde";
            case "Exceptionnel":
                return "Absence exceptionnelle";
            case "Maladie":
                return "Maladie";
            default:
                return type || "-";
        }
    }

    function statusLabel(status) {
        switch (status) {
            case "pending":
                return "En attente";
            case "approved":
                return "Validé";
            case "rejected":
                return "Refusé";
            default:
                return status || "-";
        }
    }

    function statusClass(status) {
        switch (status) {
            case "pending":
                return "en-attente";
            case "approved":
                return "valide";
            case "rejected":
                return "refuse";
            default:
                return "";
        }
    }

    // =========================
    //    CHARGEMENT / RENDU
    // =========================
    async function loadLeaves(type = "all") {
        if (!tableBody) return;

        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-muted py-3">Chargement...</td>
            </tr>
        `;

        try {
            const url = new URL("/admin/leaves", window.location.origin);
            if (companyId) url.searchParams.set("company_id", companyId);
            if (teamId) url.searchParams.set("team_id", teamId);
            if (type && type !== "all") url.searchParams.set("type", type);

            const res = await fetch(url.toString(), {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const data = await res.json();
            renderLeaves(data.leaves || []);
            updateStats(data.stats || {});
        } catch (err) {
            console.error(err);
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-danger py-3">
                        Erreur de chargement : ${err.message}
                    </td>
                </tr>
            `;
        }
    }

    function renderLeaves(leaves) {
        if (!leaves.length) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                        Aucun congé trouvé.
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = leaves
            .map((l) => {
                const employeeName = l.user
                    ? `${l.user.first_name} ${l.user.last_name}`
                    : "—";

                const period = `${formatDate(l.start_date)} → ${formatDate(
                    l.end_date
                )}`;
                const days = computeDays(l.start_date, l.end_date);
                const durationLabel =
                    days > 0 ? `${days} jour${days > 1 ? "s" : ""}` : "—";

                const sLabel = statusLabel(l.status);
                const sClass = statusClass(l.status);

                const actionsHtml =
                    l.status === "pending"
                        ? `
                        <button class="btn-action valide" data-id="${l.id}" title="Valider">
                            <i class="fa-solid fa-check"></i>
                        </button>
                        <button class="btn-action refuse" data-id="${l.id}" title="Refuser">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                        <button class="btn-action details" data-id="${l.id}" title="Détails">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    `
                        : `
                        <button class="btn-action details" data-id="${l.id}" title="Détails">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    `;

                return `
                <tr>
                    <td><strong>${employeeName}</strong></td>
                    <td>${typeLabel(l.type)}</td>
                    <td>${period}</td>
                    <td>${durationLabel}</td>
                    <td>
                        <span class="status ${sClass}">${sLabel}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            ${actionsHtml}
                        </div>
                    </td>
                </tr>
            `;
            })
            .join("");
    }

    function updateStats(stats) {
        const [totalEl, pendingEl, approvedEl, rejectedEl] = statsCards;
        if (!totalEl) return;

        totalEl.textContent = stats.total ?? "0";
        pendingEl.textContent = stats.pending ?? "0";
        approvedEl.textContent = stats.approved ?? "0";
        rejectedEl.textContent = stats.rejected ?? "0";
    }

    // =========================
    //      MODALE DÉTAIL
    // =========================
    async function loadLeaveDetails(id) {
        if (!id) return;
        try {
            const res = await fetch(`/admin/leaves/${id}`, {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            });
            if (!res.ok) {
                console.error("Erreur détail congé", res.status);
                throw new Error(`HTTP ${res.status}`);
            }

            const l = await res.json();

            const employeeName = l.user
                ? `${l.user.first_name} ${l.user.last_name}`
                : "—";
            const validatorName = l.validator
                ? `${l.validator.first_name} ${l.validator.last_name}`
                : "—";

            const period = `${formatDate(l.start_date)} → ${formatDate(
                l.end_date
            )}`;
            const days = computeDays(l.start_date, l.end_date);
            const durationLabel =
                days > 0 ? `${days} jour${days > 1 ? "s" : ""}` : "—";

            const sLabel = statusLabel(l.status);
            const sClass = statusClass(l.status);

            if (detailEmployee) detailEmployee.textContent = employeeName;
            if (detailType) detailType.textContent = typeLabel(l.type);
            if (detailPeriod) detailPeriod.textContent = period;
            if (detailDuration) detailDuration.textContent = durationLabel;

            if (detailStatus) {
                detailStatus.textContent = sLabel;
                detailStatus.className = "badge conge-status-badge " + sClass;
            }

            if (detailValidator) detailValidator.textContent = validatorName;
            if (detailComments) detailComments.textContent = l.comments || "—";
            if (detailCreatedAt)
                detailCreatedAt.textContent = formatDate(l.created_at, true);

            if (detailJustifLink) {
                if (l.justification_path) {
                    detailJustifLink.textContent = "Voir le justificatif";
                    detailJustifLink.href = `/${l.justification_path}`;
                    detailJustifLink.classList.remove("disabled");
                } else {
                    detailJustifLink.textContent = "Aucun justificatif fourni";
                    detailJustifLink.href = "#";
                    detailJustifLink.classList.add("disabled");
                }
            }

            modalDetail?.show();
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la récupération du congé", "error");
        }
    }

    // =========================
    //       LISTENERS
    // =========================

    // Filtres type (boutons)
    filters.forEach((btn) => {
        btn.addEventListener("click", () => {
            filters.forEach((b) => b.classList.remove("active"));
            btn.classList.add("active");
            const type = btn.dataset.type || "all";
            loadLeaves(type);
        });
    });

    // Actions Valider / Refuser / Détails (délégation)
    tableBody.addEventListener("click", async (e) => {
        const btnValide = e.target.closest(".btn-action.valide");
        const btnRefuse = e.target.closest(".btn-action.refuse");
        const btnDetails = e.target.closest(".btn-action.details");

        if (btnDetails) {
            const id = btnDetails.dataset.id;
            if (!id) return;
            loadLeaveDetails(id);
            return;
        }

        if (!btnValide && !btnRefuse) return;

        const id = (btnValide || btnRefuse).dataset.id;
        if (!id) return;

        const newStatus = btnValide ? "approved" : "rejected";

        try {
            const res = await fetch(`/admin/leaves/${id}/status`, {
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
                `Demande de congé ${
                    newStatus === "approved" ? "validée" : "refusée"
                }`,
                "success"
            );

            const activeType =
                document.querySelector(".conge-filters .filter-btn.active")
                    ?.dataset.type || "all";
            loadLeaves(activeType);
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la mise à jour du congé", "error");
        }
    });

    // INIT
    loadLeaves("all");
}
