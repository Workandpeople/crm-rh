export default function initTicketChat({
    modalEl,
    listEl,
    emptyEl,
    formEl,
    inputEl,
    currentUserId,
}) {
    if (!modalEl || !listEl || !formEl || !inputEl) return null;

    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");

    const state = {
        ticketId: null,
        channel: null,
        messageIds: new Set(),
    };

    function formatTime(value) {
        if (!value) return "";
        const d = new Date(value);
        if (Number.isNaN(d.getTime())) return "";
        return d.toLocaleString("fr-FR", {
            day: "2-digit",
            month: "short",
            hour: "2-digit",
            minute: "2-digit",
        });
    }

    function clearMessages() {
        listEl.innerHTML = "";
        emptyEl?.classList.remove("d-none");
        state.messageIds.clear();
    }

    function renderMessage(message) {
        if (!message || state.messageIds.has(message.id)) return;
        state.messageIds.add(message.id);

        const wrapper = document.createElement("div");
        wrapper.className = "ticket-chat-message";
        if (currentUserId && String(message.user?.id) === String(currentUserId)) {
            wrapper.classList.add("mine");
        }

        const meta = document.createElement("div");
        meta.className = "ticket-chat-meta";
        const author = document.createElement("span");
        author.textContent = message.user?.full_name || "Utilisateur";
        const time = document.createElement("span");
        time.textContent = formatTime(message.created_at);
        meta.append(author, time);

        const body = document.createElement("p");
        body.className = "ticket-chat-body";
        body.textContent = message.body || "";

        wrapper.append(meta, body);
        listEl.appendChild(wrapper);
        emptyEl?.classList.add("d-none");
        listEl.scrollTop = listEl.scrollHeight;
    }

    async function fetchConversation(ticketId) {
        if (!ticketId) return;
        clearMessages();
        try {
            const res = await fetch(`/tickets/${ticketId}/conversation`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            const data = await res.json();
            const messages = Array.isArray(data.messages) ? data.messages : [];
            if (!messages.length) {
                emptyEl?.classList.remove("d-none");
                return;
            }
            messages.forEach(renderMessage);
        } catch (e) {
            emptyEl?.classList.remove("d-none");
            console.error("[ticketChat] load error", e);
        }
    }

    function joinChannel(ticketId) {
        if (!window.Echo || !ticketId) return;
        state.channel = window.Echo.private(`ticket.${ticketId}`)
            .listen(".ticket.message", (payload) => {
                if (payload?.message) renderMessage(payload.message);
            });
    }

    function leaveChannel() {
        if (window.Echo && state.ticketId) {
            window.Echo.leave(`ticket.${state.ticketId}`);
        }
        state.channel = null;
    }

    async function sendMessage(ticketId, body) {
        const res = await fetch(`/tickets/${ticketId}/messages`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-Requested-With": "XMLHttpRequest",
                "X-CSRF-TOKEN": csrfToken,
            },
            body: JSON.stringify({ body }),
        });
        if (!res.ok) throw new Error(`HTTP ${res.status}`);
        const data = await res.json();
        if (data?.message) renderMessage(data.message);
    }

    formEl.addEventListener("submit", async (e) => {
        e.preventDefault();
        const text = inputEl.value.trim();
        if (!text || !state.ticketId) return;
        inputEl.value = "";
        try {
            await sendMessage(state.ticketId, text);
        } catch (err) {
            console.error("[ticketChat] send error", err);
        }
    });

    modalEl.addEventListener("hidden.bs.modal", () => {
        leaveChannel();
        state.ticketId = null;
    });

    return {
        open: async (ticketId) => {
            if (!ticketId) return;
            if (state.ticketId && state.ticketId !== ticketId) {
                leaveChannel();
            }
            state.ticketId = ticketId;
            await fetchConversation(ticketId);
            if (!state.channel) {
                joinChannel(ticketId);
            }
        },
        close: () => {
            leaveChannel();
            state.ticketId = null;
        },
    };
}
