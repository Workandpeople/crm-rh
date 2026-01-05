
export default function initActualitesManagement() {
    console.log("%c[actualitesManagement] init", "color: orange");

    const bs = window.bootstrap;
    const modalEl = document.getElementById("modalBlog");
    const form = document.getElementById("blogForm");

    if (!modalEl || !form || !bs?.Modal) {
        console.warn("[actualitesManagement] Modal/Form/Bootstrap manquant");
        return;
    }

    // Modales
    const modal = new bs.Modal(modalEl);
    const deleteModalEl = document.getElementById("modalDeleteBlog");
    const deleteModal = deleteModalEl ? new bs.Modal(deleteModalEl) : null;

    const viewModalEl = document.getElementById("modalBlogView");
    const viewModal = viewModalEl ? new bs.Modal(viewModalEl) : null;

    // DOM
    const confirmDeleteBtn = document.getElementById("confirmDeleteBlog");
    const modalTitle = document.getElementById("modalBlogTitle");
    const hiddenId = document.getElementById("blogId");
    const tableBody = document.querySelector("#actualitesTable tbody");
    const paginationContainer = document.querySelector("#paginationActualites");

    const statTotal = document.getElementById("statTotal");
    const statPublished = document.getElementById("statPublished");
    const statDraft = document.getElementById("statDraft");
    const statHighlighted = document.getElementById("statHighlighted");

    const csrfToken =
        document
            .querySelector('meta[name="csrf-token"]')
            ?.getAttribute("content") || "";

    const filtersEls = {
        search: document.getElementById("filterSearch"),
        status: document.getElementById("filterStatus"),
        highlight: document.getElementById("filterHighlight"),
    };

    // Company
    let companyId = localStorage.getItem("selectedCompanyId");

    // State
    const state = {
        page: 1,
        perPage: 6,
        filters: { search: "", status: "", highlight: "" },
        articles: [],
    };

    // Delete state
    let deleteId = null;

    // View state
    let viewId = null;

    // ---------- Utils
    const authorLabel = (blog) =>
        blog?.author?.full_name ||
        blog?.author?.name ||
        blog?.user?.full_name ||
        blog?.user?.name ||
        blog?.author_label ||
        "";

    function safeText(v) {
        return String(v ?? "").trim() || "—";
    }

    function formatDate(dateStr) {
        const date = new Date(dateStr);
        return isNaN(date) ? "-" : date.toLocaleDateString("fr-FR");
    }

    function badgeClass(status) {
        if (status === "published") return "published";
        if (status === "draft") return "draft";
        return "archived";
    }

    function statusLabel(status) {
        if (status === "published") return "Publié";
        if (status === "draft") return "Brouillon";
        return "Archivé";
    }

    function createToast(type = "success", message = "") {
        const containerId = "toastContainer";
        let container = document.getElementById(containerId);
        if (!container) {
            container = document.createElement("div");
            container.id = containerId;
            container.className =
                "toast-container position-fixed bottom-0 end-0 p-3";
            document.body.appendChild(container);
        }

        const toastEl = document.createElement("div");
        toastEl.className = `toast align-items-center text-bg-${
            type === "success" ? "success" : "danger"
        } border-0`;
        toastEl.role = "alert";
        toastEl.innerHTML = `
            <div class="d-flex">
              <div class="toast-body">${message}</div>
              <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toastEl);
        const toast = new bs.Toast(toastEl, { delay: 3500 });
        toast.show();
        toastEl.addEventListener("hidden.bs.toast", () => toastEl.remove());
    }

    // ---------- API
    async function ensureCompanyId() {
        try {
            const res = await fetch("/admin/companies", {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const companies = await res.json();

            if (!Array.isArray(companies) || !companies.length) {
                tableBody.innerHTML =
                    '<tr><td colspan="6" class="text-center py-4">Aucune entreprise disponible.</td></tr>';
                createToast("danger", "Aucune entreprise disponible.");
                return false;
            }

            const current = companies.find(
                (c) => String(c.id) === String(companyId)
            );

            if (!current) {
                companyId = companies[0].id;
                localStorage.setItem("selectedCompanyId", companyId);
            }

            const companyInput = document.getElementById("blogCompanyId");
            if (companyInput) companyInput.value = companyId;

            return true;
        } catch (e) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Impossible de récupérer l'entreprise : ${e.message}</td></tr>`;
            createToast(
                "danger",
                e.message || "Impossible de récupérer l'entreprise"
            );
            return false;
        }
    }

    async function loadArticles() {
        tableBody.innerHTML =
            '<tr><td colspan="6" class="text-center py-4">Chargement...</td></tr>';

        if (!(await ensureCompanyId())) return;

        try {
            const res = await fetch(`/admin/blogs?company_id=${companyId}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);

            const json = await res.json();
            const items = Array.isArray(json?.data)
                ? json.data
                : Array.isArray(json)
                ? json
                : [];

            state.articles = items.map((b) => ({
                ...b,
                company_id: b.company_id,
                author_label:
                    b.author?.full_name ||
                    b.author?.name ||
                    b.user?.full_name ||
                    b.user?.name ||
                    "",
                highlighted: Boolean(b.highlighted),
                status: b.status || "draft",
                published_at: b.published_at || b.created_at || "",
            }));

            renderTable();
        } catch (e) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center text-danger py-4">Erreur de chargement : ${e.message}</td></tr>`;
            createToast("danger", e.message || "Erreur de chargement");
        }
    }

    async function saveArticle(mode, payload = null, idOverride = null) {
        const data = payload ?? collectFormData();

        if (!(await ensureCompanyId()))
            throw new Error("Aucune entreprise sélectionnée.");

        const editId = idOverride ?? hiddenId.value;
        const url =
            mode === "edit" && editId
                ? `/admin/blogs/${editId}`
                : "/admin/blogs";
        const method = mode === "edit" ? "PUT" : "POST";
        const formData = buildFormData(data, method);

        const res = await fetch(url, {
            method: "POST", // POST + _method pour multipart
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: formData,
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            const msg =
                err?.message ||
                (err?.errors
                    ? Object.values(err.errors).flat().join("\n")
                    : null) ||
                `HTTP ${res.status}`;
            throw new Error(msg);
        }

        return res.json();
    }

    async function deleteArticle(id) {
        const res = await fetch(`/admin/blogs/${id}`, {
            method: "DELETE",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken,
            },
        });

        if (!res.ok) {
            const err = await res.json().catch(() => ({}));
            const msg =
                err?.message ||
                (err?.errors
                    ? Object.values(err.errors).flat().join("\n")
                    : null) ||
                `HTTP ${res.status}`;
            throw new Error(msg);
        }

        return res.json();
    }

    // ---------- Filters / Pagination / Render
    function applyFilters(rows) {
        return rows.filter((row) => {
            const search = state.filters.search;
            const matchesSearch =
                !search ||
                (row.title || "").toLowerCase().includes(search) ||
                authorLabel(row).toLowerCase().includes(search);

            const matchesStatus =
                !state.filters.status || row.status === state.filters.status;

            const matchesHighlight =
                !state.filters.highlight ||
                (state.filters.highlight === "yes"
                    ? row.highlighted
                    : !row.highlighted);

            return matchesSearch && matchesStatus && matchesHighlight;
        });
    }

    function paginate(rows) {
        const start = (state.page - 1) * state.perPage;
        return rows.slice(start, start + state.perPage);
    }

    function renderStats(rows) {
        statTotal.textContent = rows.length;
        statPublished.textContent = rows.filter(
            (r) => r.status === "published"
        ).length;
        statDraft.textContent = rows.filter((r) => r.status === "draft").length;
        statHighlighted.textContent = rows.filter((r) => r.highlighted).length;
    }

    function renderPagination(totalRows) {
        const totalPages = Math.max(1, Math.ceil(totalRows / state.perPage));
        state.page = Math.min(state.page, totalPages);

        paginationContainer.innerHTML = "";

        const prev = document.createElement("button");
        prev.className = "page-link-custom";
        prev.disabled = state.page === 1;
        prev.innerHTML = `<i class="fa-solid fa-arrow-left me-2"></i>Précédent`;
        prev.onclick = () => {
            state.page = Math.max(1, state.page - 1);
            renderTable();
        };

        const next = document.createElement("button");
        next.className = "page-link-custom";
        next.disabled = state.page >= totalPages;
        next.innerHTML = `Suivant<i class="fa-solid fa-arrow-right ms-2"></i>`;
        next.onclick = () => {
            state.page = Math.min(totalPages, state.page + 1);
            renderTable();
        };

        paginationContainer.appendChild(prev);
        paginationContainer.appendChild(next);
    }

    function renderTable() {
        const filtered = applyFilters(state.articles);
        const pageRows = paginate(filtered);

        tableBody.innerHTML = "";

        if (!pageRows.length) {
            tableBody.innerHTML = `<tr><td colspan="6" class="text-center py-4">Aucun article trouvé.</td></tr>`;
            renderPagination(filtered.length);
            renderStats(filtered);
            return;
        }

        pageRows.forEach((row) => {
            const tr = document.createElement("tr");

            tr.innerHTML = `
        <td class="text-start">
          <div class="fw-bold">${row.title || "-"}</div>
          <small class="text-muted">#${row.id}</small>
        </td>

        <td>${authorLabel(row) || "-"}</td>

        <td>
          <span class="badge-status ${badgeClass(row.status)}">${statusLabel(
                row.status
            )}</span>
        </td>

        <td>
          <button class="btn-action star toggle-highlight ${
              row.highlighted ? "active" : ""
          }" data-id="${row.id}" title="Mise en avant">
            ${row.highlighted ? "⭐" : "☆"}
          </button>
        </td>

        <td>${formatDate(row.published_at)}</td>

        <td class="text-center">
          <div class="table-actions justify-content-center">
            <button class="btn-action view view-btn" data-id="${row.id}" title="Voir">
              <i class="fa-regular fa-eye"></i>
            </button>
            <button class="btn-action edit edit-btn" data-id="${row.id}" title="Modifier">
              <i class="fa-solid fa-pen"></i>
            </button>
            <button class="btn-action delete delete-btn" data-id="${row.id}" title="Supprimer">
              <i class="fa-solid fa-trash"></i>
            </button>
          </div>
        </td>`;

            tableBody.appendChild(tr);
        });

        attachRowActions();
        renderPagination(filtered.length);
        renderStats(filtered);
    }

    function attachRowActions() {
        document.querySelectorAll(".view-btn").forEach((btn) => {
            btn.onclick = () => openViewModal(btn.dataset.id);
        });

        document.querySelectorAll(".edit-btn").forEach((btn) => {
            btn.onclick = () => openModal("edit", btn.dataset.id);
        });

        document.querySelectorAll(".delete-btn").forEach((btn) => {
            btn.onclick = () => {
                deleteId = btn.dataset.id;
                deleteModal?.show();
            };
        });

        document.querySelectorAll(".toggle-highlight").forEach((btn) => {
            btn.onclick = async () => {
                const art = state.articles.find(
                    (a) => String(a.id) === String(btn.dataset.id)
                );
                if (!art) return;

                try {
                    await saveArticle(
                        "edit",
                        {
                            highlighted: !art.highlighted,
                            company_id: companyId || art.company_id,
                        },
                        art.id
                    );
                    await loadArticles();
                    createToast("success", "Mise à jour de la mise en avant");
                } catch (e) {
                    createToast(
                        "danger",
                        e.message || "Erreur lors de la mise à jour"
                    );
                }
            };
        });
    }

    // ---------- Modal CRUD
    function resetPreviews() {
        document.querySelectorAll(".img-input").forEach((input) => {
            input.value = "";
        });

        [
            "#preview-main",
            "#preview-second",
            "#preview-third",
            "#preview-fourth",
        ].forEach((sel) => {
            const img = document.querySelector(sel);
            if (img) {
                img.src = "";
                img.classList.add("d-none");
            }
        });
    }

    function setPreview(selector, src) {
        const img = document.querySelector(selector);
        if (img && src) {
            img.src = src;
            img.classList.remove("d-none");
        }
    }

    function openModal(mode, id = null) {
        form.reset();
        hiddenId.value = id ?? "";

        const companyInput = document.getElementById("blogCompanyId");
        if (companyInput) companyInput.value = companyId || "";

        resetPreviews();
        modalTitle.textContent =
            mode === "edit" ? "Modifier un article" : "Nouvel article";

        if (mode === "edit") {
            const article = state.articles.find(
                (a) => String(a.id) === String(id)
            );
            if (!article) return;

            form.title.value = article.title || "";
            form.status.value = article.status || "draft";
            form.highlighted.checked = Boolean(article.highlighted);

            if (companyInput && article.company_id)
                companyInput.value = article.company_id;

            form.main_image_credit.value = article.main_image_credit || "";

            form.second_title.value = article.second_title || "";
            form.second_type.value = article.second_type || "horizontal";
            form.second_image_credit.value = article.second_image_credit || "";
            form.second_content.value = article.second_content || "";

            form.third_type.value = article.third_type || "horizontal";
            form.third_image_credit.value = article.third_image_credit || "";
            form.third_content.value = article.third_content || "";

            form.fourth_type.value = article.fourth_type || "horizontal";
            form.fourth_image_credit.value = article.fourth_image_credit || "";
            form.fourth_content.value = article.fourth_content || "";

            setPreview("#preview-main", article.main_image);
            setPreview("#preview-second", article.second_image);
            setPreview("#preview-third", article.third_image);
            setPreview("#preview-fourth", article.fourth_image);
        }

        modal.show();
    }

    function collectFormData() {
        const hiddenCompany =
            document.getElementById("blogCompanyId")?.value || companyId;

        return {
            title: form.title.value.trim(),
            status: form.status.value || "draft",
            highlighted: form.highlighted.checked,
            company_id: hiddenCompany,

            main_image: form.main_image.files?.[0] || null,
            main_image_credit: form.main_image_credit.value.trim(),

            second_title: form.second_title.value.trim(),
            second_image: form.second_image.files?.[0] || null,
            second_type: form.second_type.value || "horizontal",
            second_image_credit: form.second_image_credit.value.trim(),
            second_content: form.second_content.value.trim(),

            third_content: form.third_content.value.trim(),
            third_image: form.third_image.files?.[0] || null,
            third_image_credit: form.third_image_credit.value.trim(),
            third_type: form.third_type.value || "horizontal",

            fourth_image: form.fourth_image.files?.[0] || null,
            fourth_image_credit: form.fourth_image_credit.value.trim(),
            fourth_type: form.fourth_type.value || "horizontal",
            fourth_content: form.fourth_content.value.trim(),
        };
    }

    function buildFormData(data, method) {
        const fd = new FormData();
        if (method === "PUT") fd.append("_method", "PUT");
        fd.append("_token", csrfToken);

        Object.entries(data).forEach(([key, value]) => {
            if (value === undefined || value === null) return;
            fd.append(key, value);
        });

        return fd;
    }

    // Submit CRUD
    form.addEventListener("submit", async (e) => {
        e.preventDefault();
        try {
            const res = await saveArticle(hiddenId.value ? "edit" : "create");
            modal.hide();
            state.page = 1;
            await loadArticles();
            createToast("success", res?.message || "Article enregistré");
        } catch (err) {
            createToast(
                "danger",
                err.message || "Erreur lors de l'enregistrement"
            );
        }
    });

    // Preview live
    document.querySelectorAll(".img-input").forEach((input) => {
        input.addEventListener("change", (e) => {
            const target = e.currentTarget.dataset.preview;
            if (!target) return;

            const img = document.querySelector(target);
            if (!img) return;

            const file = e.currentTarget.files?.[0];
            if (!file) {
                img.src = "";
                img.classList.add("d-none");
                return;
            }

            const reader = new FileReader();
            reader.onload = (ev) => {
                img.src = ev.target?.result;
                img.classList.remove("d-none");
            };
            reader.readAsDataURL(file);
        });
    });

    // ---------- Modal VIEW (Détail)
    const $ = (id) => document.getElementById(id);

    function setBadgeStatus(el, status) {
        if (!el) return;
        el.classList.remove("status-published", "status-draft", "status-archived");
        el.classList.add(`status-${status || "draft"}`);
        el.textContent = statusLabel(status || "draft");
    }

    function setBadgeHighlight(el, highlighted) {
        if (!el) return;
        el.classList.remove("highlight-yes", "highlight-no");
        el.classList.add(highlighted ? "highlight-yes" : "highlight-no");
        el.textContent = highlighted ? "Mise en avant" : "Non mise en avant";
    }

    function setImg(imgId, placeholderId, src) {
        const img = $(imgId);
        const ph = $(placeholderId);
        if (!img || !ph) return;

        if (src) {
            img.src = src;
            img.classList.remove("d-none");
            ph.classList.add("d-none");
        } else {
            img.src = "";
            img.classList.add("d-none");
            ph.classList.remove("d-none");
        }
    }

    function setSection(sectionNum, data) {
        const wrap = $(`blogViewSection${sectionNum}`);
        if (!wrap) return;

        const type = data?.type || "horizontal";
        const hasContent =
            (data?.title && data.title.trim()) ||
            (data?.content && data.content.trim()) ||
            Boolean(data?.image);

        wrap.classList.toggle("d-none", !hasContent);
        if (!hasContent) return;

        const typeEl = $(`blogViewSection${sectionNum}Type`);
        const layout = $(`blogViewSection${sectionNum}Layout`);

        if (typeEl) typeEl.textContent = type === "vertical" ? "Vertical" : "Horizontal";
        if (layout) layout.classList.toggle("is-vertical", type === "vertical");

        if (sectionNum === 2) {
            const t = $("blogViewSecondTitle");
            const c = $("blogViewSecondContent");
            const cr = $("blogViewSecondCredit");
            if (t) t.textContent = data?.title ? data.title.trim() : "";
            if (c) c.textContent = data?.content ? data.content.trim() : "—";
            if (cr) cr.textContent = data?.credit ? `Crédit : ${data.credit}` : "";
            setImg("blogViewSecondImage", "blogViewSecondPlaceholder", data?.image);
        }

        if (sectionNum === 3) {
            const c = $("blogViewThirdContent");
            const cr = $("blogViewThirdCredit");
            if (c) c.textContent = data?.content ? data.content.trim() : "—";
            if (cr) cr.textContent = data?.credit ? `Crédit : ${data.credit}` : "";
            setImg("blogViewThirdImage", "blogViewThirdPlaceholder", data?.image);
        }

        if (sectionNum === 4) {
            const c = $("blogViewFourthContent");
            const cr = $("blogViewFourthCredit");
            if (c) c.textContent = data?.content ? data.content.trim() : "—";
            if (cr) cr.textContent = data?.credit ? `Crédit : ${data.credit}` : "";
            setImg("blogViewFourthImage", "blogViewFourthPlaceholder", data?.image);
        }
    }

    function openViewModal(id) {
        if (!viewModal) return;

        const article = state.articles.find((a) => String(a.id) === String(id));
        if (!article) return;

        viewId = article.id;

        const title = $("blogViewTitle");
        const bid = $("blogViewId");
        const author = $("blogViewAuthor");
        const date = $("blogViewDate");
        const company = $("blogViewCompany");
        const mainCredit = $("blogViewMainCredit");

        if (title) title.textContent = safeText(article.title);
        if (bid) bid.textContent = `#${article.id}`;
        if (author) author.textContent = authorLabel(article) || "—";
        if (date) date.textContent = formatDate(article.published_at || article.created_at);

        // company label fallback
        const companyLabel =
            article.company?.name ||
            article.company_name ||
            (article.company_id ? `ID ${article.company_id}` : "—");
        if (company) company.textContent = companyLabel;

        if (mainCredit) mainCredit.textContent = safeText(article.main_image_credit);

        const statusEl = $("blogViewStatus");
        const hiEl = $("blogViewHighlight");
        setBadgeStatus(statusEl, article.status);
        setBadgeHighlight(hiEl, Boolean(article.highlighted));

        setImg("blogViewMainImage", "blogViewMainPlaceholder", article.main_image);

        setSection(2, {
            title: article.second_title,
            content: article.second_content,
            image: article.second_image,
            credit: article.second_image_credit,
            type: article.second_type,
        });

        setSection(3, {
            content: article.third_content,
            image: article.third_image,
            credit: article.third_image_credit,
            type: article.third_type,
        });

        setSection(4, {
            content: article.fourth_content,
            image: article.fourth_image,
            credit: article.fourth_image_credit,
            type: article.fourth_type,
        });

        viewModal.show();
    }

    const btnEditFromView = document.getElementById("btnEditFromView");
    btnEditFromView?.addEventListener("click", () => {
        if (!viewId) return;
        viewModal?.hide();
        openModal("edit", String(viewId));
    });

    // ---------- Events (Filters / Buttons)
    Object.entries(filtersEls).forEach(([key, el]) => {
        if (!el) return;
        el.addEventListener("input", () => {
            state.filters[key] = el.value.trim().toLowerCase();
            state.page = 1;
            renderTable();
        });
    });

    const btnReset = document.getElementById("btnResetFilters");
    if (btnReset) {
        btnReset.onclick = () => {
            Object.entries(filtersEls).forEach(([key, el]) => {
                if (!el) return;
                el.value = "";
                state.filters[key] = "";
            });
            state.page = 1;
            renderTable();
        };
    }

    const btnRefresh = document.getElementById("btnRefresh");
    if (btnRefresh) btnRefresh.onclick = () => loadArticles();

    const btnCreate = document.getElementById("btnCreate");
    if (btnCreate) btnCreate.onclick = () => openModal("create");

    if (confirmDeleteBtn) {
        confirmDeleteBtn.onclick = async () => {
            if (!deleteId) return;
            try {
                await deleteArticle(deleteId);
                deleteModal?.hide();
                await loadArticles();
                createToast("success", "Article supprimé");
            } catch (e) {
                createToast("danger", e.message || "Erreur lors de la suppression");
            } finally {
                deleteId = null;
            }
        };
    }

    // ---------- Init
    loadArticles();
}
