export default function initDossierEmployee() {
    const page = document.querySelector(".dossier-page");
    if (!page) {
        console.log("[dossierEmployee] page not found");
        return;
    }
    console.log("[dossierEmployee] init");

    const modalEl = document.getElementById("modalUploadDoc");
    const modal = modalEl ? new window.bootstrap.Modal(modalEl) : null;
    const btnAdd = page.querySelector(".btn-upload-doc");
    const uploadButtons = page.querySelectorAll(".btn-action.upload");
    const cancelButtons = () => page.querySelectorAll(".btn-action.cancel");
    const docTypeSelect = document.getElementById("docTypeSelect");
    const titleInput = document.getElementById("docTitleInput");
    const descInput = document.getElementById("docDescInput");
    const form = document.getElementById("formUploadDoc");
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    const userName = page.dataset.userName || "";

    function showToast(message, type = "success") {
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
    }

    function openModal(presetType = "", presetLabel = "", lockType = false) {
        if (!modal) return;
        if (docTypeSelect) {
            docTypeSelect.value = presetType || "";
            docTypeSelect.dataset.locked = lockType ? "1" : "0";
            docTypeSelect.dataset.lockValue = presetType || "";
        }
        if (titleInput) {
            const baseLabel = presetLabel || (presetType ? presetType : "document");
            titleInput.value = `Dépôt ${baseLabel} - ${userName}`.trim();
        }
        if (descInput) descInput.value = "";
        modal.show();
    }

    btnAdd?.addEventListener("click", () => openModal("", "", false));
    uploadButtons.forEach((btn) => {
        btn.addEventListener("click", () => {
            const t = btn.dataset.docType || "";
            const l = btn.dataset.docLabel || "";
            const lock = btn.dataset.lockType === "1";
            openModal(t, l, lock);
        });
    });

    if (docTypeSelect) {
        docTypeSelect.addEventListener("change", () => {
            if (docTypeSelect.dataset.locked === "1") {
                docTypeSelect.value = docTypeSelect.dataset.lockValue || "";
            }
        });
    }

    function bindCancel() {
        cancelButtons().forEach((btn) => {
            btn.addEventListener("click", async () => {
                const id = btn.dataset.docId;
                if (!id) return;
                if (!confirm("Annuler et supprimer ce document ?")) return;
                try {
                    const res = await fetch(`/admin/documents/${id}`, {
                        method: "DELETE",
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "X-CSRF-TOKEN": csrf,
                        },
                    });
                    let data = {};
                    try { data = await res.clone().json(); } catch {}
                    if (!res.ok) {
                        const msg = data.message || `HTTP ${res.status}`;
                        throw new Error(msg);
                    }
                    showToast(data.message || "Document supprimé");
                    window.location.reload();
                } catch (err) {
                    console.error(err);
                    showToast("Impossible de supprimer le document.", "error");
                }
            });
        });
    }

    form?.addEventListener("submit", async (e) => {
        e.preventDefault();
        const fd = new FormData(form);
        const docType = docTypeSelect?.value || "";
        const title = fd.get("title")?.toString().trim();
        const companyId = fd.get("company_id")?.toString().trim();

        if (!docType) {
            showToast("Veuillez choisir un type de document.", "error");
            return;
        }
        if (!title) {
            showToast("Veuillez renseigner un titre pour le ticket.", "error");
            return;
        }
        if (!companyId) {
            showToast("Aucune société associée à votre compte.", "error");
            return;
        }
        if (docType && !fd.get("description")) {
            fd.set("description", `Dépôt du document "${docType}".`);
        }
        try {
            console.log("[dossierEmployee] submitting ticket", Object.fromEntries(fd.entries()));
            const res = await fetch("/admin/backlogs", {
                method: "POST",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "X-CSRF-TOKEN": csrf,
                },
                body: fd,
            });
            let data = {};
            try { data = await res.clone().json(); } catch {}
            if (!res.ok) {
                const msg = data.message || `HTTP ${res.status}`;
                throw new Error(msg);
            }
            modal?.hide();
            form.reset();
            showToast(data.message || "Ticket de dépôt créé et envoyé à l’administration.");
            window.location.reload();
        } catch (err) {
            console.error(err);
            showToast("Erreur lors de la création du ticket.", "error");
        }
    });

    bindCancel();
}
