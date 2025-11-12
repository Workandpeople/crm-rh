/**
 * Gestion CRUD des utilisateurs (Super Admin)
 * + Recherche + Pagination + Toasts Bootstrap + Reset Password
 */
export default function initUsersManagement() {
    console.log("%c[usersManagement] Initialisation", "color: cyan");

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    const bs = window.bootstrap;
    if (!bs?.Modal) return console.error("Bootstrap manquant");

    // === Sélecteurs ===
    const tableBody = document.getElementById("usersTableBody");
    const roleFilter = document.getElementById("filter-role");
    const statusFilter = document.getElementById("filter-statut");
    const companyFilter = document.getElementById("filter-societe");
    const searchInput = document.getElementById("filter-search");
    const paginationEl = document.getElementById("usersPagination");

    // === Modals ===
    const modalCreate = document.getElementById("modalUserCreate")
        ? new bs.Modal("#modalUserCreate")
        : null;
    const modalEdit = document.getElementById("modalUserEdit")
        ? new bs.Modal("#modalUserEdit")
        : null;
    const modalDelete = document.getElementById("modalUserDelete")
        ? new bs.Modal("#modalUserDelete")
        : null;

    // === Données ===
    let usersCache = [];
    let rolesCache = [];
    let companiesCache = [];
    let currentPage = 1;
    const perPage = 15;
    let toDelete = null;

    // === UTILITAIRE : Toast Bootstrap ===
    function showToast(message, type = "success") {
        const container = document.getElementById("toastContainer");
        if (!container) return console.warn("Aucun conteneur de toast trouvé");

        const bg = type === "success" ? "bg-success" : "bg-danger";
        const icon =
            type === "success" ? "fa-circle-check" : "fa-triangle-exclamation";
        const id = `toast-${Date.now()}`;

        container.insertAdjacentHTML(
            "beforeend",
            `
            <div id="${id}" class="toast align-items-center text-white ${bg} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
              <div class="d-flex">
                <div class="toast-body">
                  <i class="fa-solid ${icon} me-2"></i>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
              </div>
            </div>`
        );

        const toastEl = document.getElementById(id);
        const toast = new bs.Toast(toastEl, { delay: 3500 });
        toast.show();
        toastEl.addEventListener("hidden.bs.toast", () => toastEl.remove());
    }

    const formatDate = (d) =>
        new Date(d).toLocaleString("fr-FR", {
            dateStyle: "short",
            timeStyle: "short",
        });

    // === OPTIONS ===
    async function fetchOptions() {
        try {
            const res = await fetch("/admin/users/options", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok)
                throw new Error("Erreur lors du chargement des options");
            const data = await res.json();
            rolesCache = data.roles;
            companiesCache = data.companies;
            fillRoleCompanySelects();
        } catch (e) {
            showToast(e.message, "error");
        }
    }

    function fillRoleCompanySelects() {
        const roleSelects = document.querySelectorAll(
            "#createRoleSelect, #editRoleSelect"
        );
        const companySelects = document.querySelectorAll(
            "#createCompanySelect, #editCompanySelect"
        );

        const rolesHTML =
            `<option value="">-- Choisir un rôle --</option>` +
            rolesCache
                .map(
                    (r) =>
                        `<option value="${r.id}">${r.label || r.name}</option>`
                )
                .join("");
        roleSelects.forEach((s) => (s.innerHTML = rolesHTML));

        const compHTML =
            `<option value="">Aucune</option>` +
            companiesCache
                .map((c) => `<option value="${c.id}">${c.name}</option>`)
                .join("");
        companySelects.forEach((s) => (s.innerHTML = compHTML));
    }

    // === CHARGEMENT UTILISATEURS ===
    async function loadUsers() {
        tableBody.innerHTML = `<tr><td colspan="7" class="text-center py-4">Chargement...</td></tr>`;
        try {
            const res = await fetch("/admin/users", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            usersCache = await res.json();
            fillCompanyFilter(usersCache);
            renderUsers();
        } catch (err) {
            showToast("Erreur chargement utilisateurs", "error");
        }
    }

    function fillCompanyFilter(users) {
        const companies = [
            ...new Set(users.map((u) => u.company?.name).filter(Boolean)),
        ];
        companyFilter.innerHTML =
            `<option value="">Toutes</option>` +
            companies.map((c) => `<option>${c}</option>`).join("");
    }

    // === Badges helpers ===
    function getStatusBadge(status = "") {
        const k = String(status || "").toLowerCase();
        const map = {
            active: { cls: "badge-status--active", label: "Actif" },
            inactive: { cls: "badge-status--inactive", label: "Inactif" },
            pending: { cls: "badge-status--pending", label: "En attente" },
        };
        const it = map[k] || {
            cls: "badge-status--pending",
            label: status || "-",
        };
        return `<span class="badge-pill ${it.cls}"><span class="dot"></span>${it.label}</span>`;
    }

    function getRoleBadge(roleName = "") {
        const k = String(roleName || "").toLowerCase();
        const map = {
            superadmin: { cls: "badge-role--superadmin", label: "Super Admin" },
            admin: { cls: "badge-role--admin", label: "Admin" },
            chef_equipe: {
                cls: "badge-role--chef_equipe",
                label: "Chef d'équipe",
            },
            employe: { cls: "badge-role--employe", label: "Employé" },
        };
        const it = map[k] || {
            cls: "badge-role--employe",
            label: roleName || "-",
        };
        return `<span class="badge-pill ${it.cls}"><span class="dot"></span>${it.label}</span>`;
    }

    // Company badge avec couleur auto (HSL) calculée à partir du nom
    function getCompanyBadge(companyName = "") {
        if (!companyName) return "-";
        const h = hashHue(companyName); // 0..360
        const s = 75,
            l = 60;
        return `<span class="badge-pill badge-company" style="--badge-h:${h}; --badge-s:${s}%; --badge-l:${l}%">
            <span class="dot"></span>${escapeHtml(companyName)}
          </span>`;
    }

    // Petit hash déterministe → teinte
    function hashHue(str) {
        let hash = 0;
        for (let i = 0; i < str.length; i++) {
            hash = str.charCodeAt(i) + ((hash << 5) - hash);
            hash |= 0;
        }
        return Math.abs(hash) % 360;
    }

    // Échappement basique pour outils de rendu
    function escapeHtml(s) {
        return String(s)
            .replaceAll("&", "&amp;")
            .replaceAll("<", "&lt;")
            .replaceAll(">", "&gt;")
            .replaceAll('"', "&quot;")
            .replaceAll("'", "&#039;");
    }

    // === RENDU TABLE + PAGINATION ===
    function renderUsers() {
        const rf = roleFilter.value;
        const sf = statusFilter.value;
        const cf = companyFilter.value;
        const searchVal = searchInput.value.trim().toLowerCase();

        const filtered = usersCache.filter((u) => {
            const matchRole = !rf || u.role?.name === rf;
            const matchStatus = !sf || u.status === sf;
            const matchCompany = !cf || u.company?.name === cf;
            const matchSearch =
                !searchVal ||
                `${u.first_name} ${u.last_name}`
                    .toLowerCase()
                    .includes(searchVal);
            return matchRole && matchStatus && matchCompany && matchSearch;
        });

        const totalPages = Math.ceil(filtered.length / perPage);
        if (currentPage > totalPages) currentPage = 1;
        const start = (currentPage - 1) * perPage;
        const pageData = filtered.slice(start, start + perPage);

        tableBody.innerHTML = pageData.length
            ? pageData
                  .map(
                      (u) => `
                <tr>
                    <td><strong>${u.first_name} ${u.last_name}</strong></td>
                    <td>${u.email}</td>
                    <td>${getRoleBadge(u.role?.name)}</td>
                    <td>${getCompanyBadge(u.company?.name)}</td>
                    <td>${getStatusBadge(u.status)}</td>
                    <td>${
                        u.last_login_at ? formatDate(u.last_login_at) : "-"
                    }</td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit" data-id="${
                                u.id
                            }" title="Modifier"><i class="fa-solid fa-pen"></i></button>
                            <button class="btn-action password" data-id="${
                                u.id
                            }" title="Réinitialiser le mot de passe"><i class="fa-solid fa-key"></i></button>
                            <button class="btn-action delete" data-id="${
                                u.id
                            }" title="Supprimer"><i class="fa-solid fa-trash"></i></button>
                        </div>
                    </td>
                </tr>`
                  )
                  .join("")
            : `<tr><td colspan="7" class="text-center py-4 text-muted">Aucun utilisateur trouvé</td></tr>`;

        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        if (totalPages <= 1) {
            paginationEl.innerHTML = "";
            return;
        }
        let html = `
            <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
                <a class="page-link" href="#" data-page="${
                    currentPage - 1
                }">«</a>
            </li>`;
        for (let i = 1; i <= totalPages; i++) {
            html += `
                <li class="page-item ${i === currentPage ? "active" : ""}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
        }
        html += `
            <li class="page-item ${
                currentPage === totalPages ? "disabled" : ""
            }">
                <a class="page-link" href="#" data-page="${
                    currentPage + 1
                }">»</a>
            </li>`;
        paginationEl.innerHTML = html;

        paginationEl.querySelectorAll(".page-link").forEach((a) => {
            a.addEventListener("click", (e) => {
                e.preventDefault();
                const page = parseInt(a.dataset.page);
                if (
                    page &&
                    page !== currentPage &&
                    page >= 1 &&
                    page <= totalPages
                ) {
                    currentPage = page;
                    renderUsers();
                }
            });
        });
    }

    // === FILTRES ===
    [roleFilter, statusFilter, companyFilter, searchInput].forEach((el) =>
        el?.addEventListener("input", () => {
            currentPage = 1;
            renderUsers();
        })
    );

    // === CRÉATION ===
    document.getElementById("btnNewUser")?.addEventListener("click", () => {
        fillRoleCompanySelects();
        modalCreate?.show();
    });

    document
        .getElementById("formCreateUser")
        ?.addEventListener("submit", async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            try {
                const res = await fetch("/admin/users", {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: formData,
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                modalCreate?.hide();
                await loadUsers();
                showToast("Utilisateur créé avec succès");
            } catch (err) {
                showToast("Erreur création : " + err.message, "error");
            }
        });

    // === ÉDITION ===
    tableBody.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-action.edit");
        if (!btn) return;
        const id = btn.dataset.id;
        try {
            const res = await fetch(`/admin/users/${id}`);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const user = await res.json();
            fillRoleCompanySelects();
            document.getElementById("editUserId").value = user.id;
            document.getElementById("editFirstName").value = user.first_name;
            document.getElementById("editLastName").value = user.last_name;
            document.getElementById("editEmail").value = user.email;
            document.getElementById("editRoleSelect").value =
                user.role_id ?? "";
            document.getElementById("editCompanySelect").value =
                user.company_id ?? "";
            document.getElementById("editStatusSelect").value =
                user.status ?? "active";
            modalEdit?.show();
        } catch (err) {
            showToast("Erreur chargement : " + err.message, "error");
        }
    });

    document
        .getElementById("formEditUser")
        ?.addEventListener("submit", async (e) => {
            e.preventDefault();
            const id = document.getElementById("editUserId").value;
            const formData = new FormData(e.target);
            try {
                const res = await fetch(`/admin/users/${id}`, {
                    method: "POST",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                    body: formData,
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                modalEdit?.hide();
                await loadUsers();
                showToast("Utilisateur mis à jour");
            } catch (err) {
                showToast("Erreur édition : " + err.message, "error");
            }
        });

    // === SUPPRESSION ===
    tableBody.addEventListener("click", (e) => {
        const btn = e.target.closest(".btn-action.delete");
        if (!btn) return;
        toDelete = btn.dataset.id;
        modalDelete?.show();
    });

    document
        .getElementById("btnConfirmDelete")
        ?.addEventListener("click", async () => {
            if (!toDelete) return;
            try {
                const res = await fetch(`/admin/users/${toDelete}`, {
                    method: "DELETE",
                    headers: {
                        "X-Requested-With": "XMLHttpRequest",
                        "X-CSRF-TOKEN": csrfToken,
                    },
                });
                if (!res.ok) throw new Error(`HTTP ${res.status}`);
                modalDelete?.hide();
                await loadUsers();
                showToast("Utilisateur supprimé");
            } catch (err) {
                showToast("Erreur suppression : " + err.message, "error");
            }
        });

    // === RESET PASSWORD ===
    tableBody.addEventListener("click", async (e) => {
        const btn = e.target.closest(".btn-action.password");
        if (!btn) return;
        const id = btn.dataset.id;
        try {
            const res = await fetch(`/admin/users/${id}/reset-password`, {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrfToken,
                },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            showToast(data.message || "Email de réinitialisation envoyé");
        } catch (err) {
            showToast("Erreur réinitialisation : " + err.message, "error");
        }
    });

    // === INIT ===
    fetchOptions();
    loadUsers();
}
