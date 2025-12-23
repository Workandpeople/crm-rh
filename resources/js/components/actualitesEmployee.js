export default function initActualitesEmployee() {
    const root = document.querySelector(".employee-actualites");
    if (!root) return;

    const cardsEl = document.getElementById("employeeBlogCards");
    const loadingEl = document.getElementById("employeeBlogLoading");
    const emptyEl = document.getElementById("employeeBlogEmpty");
    const searchInput = document.getElementById("employeeBlogSearch");
    const refreshBtn = document.getElementById("employeeBlogRefresh");
    const backBtn = document.getElementById("employeeBlogBack");
    const listView = root.querySelector('[data-view="list"]');
    const detailView = root.querySelector('[data-view="detail"]');
    const detailCard = document.getElementById("employeeBlogDetail");

    const state = {
        blogs: [],
    };

    const formatDate = (value) => {
        if (!value) return "";
        const date = new Date(value);
        if (Number.isNaN(date.getTime())) return "";
        return new Intl.DateTimeFormat("fr-FR", {
            day: "2-digit",
            month: "short",
            year: "numeric",
        }).format(date);
    };

    const authorLabel = (blog) =>
        blog?.author?.full_name ||
        blog?.author?.name ||
        blog?.user?.full_name ||
        blog?.user?.name ||
        blog?.author_label ||
        "";

    const buildExcerpt = (blog) => {
        const raw =
            blog?.second_content ||
            blog?.third_content ||
            blog?.fourth_content ||
            "";
        return raw
            .replace(/<[^>]*>/g, " ")
            .replace(/\s+/g, " ")
            .trim()
            .slice(0, 140);
    };

    const setLoading = (isLoading) => {
        if (!loadingEl) return;
        loadingEl.classList.toggle("d-none", !isLoading);
    };

    const setView = (view) => {
        if (view === "detail") {
            listView?.classList.add("d-none");
            detailView?.classList.remove("d-none");
        } else {
            detailView?.classList.add("d-none");
            listView?.classList.remove("d-none");
        }
    };

    const renderCards = (blogs) => {
        if (!cardsEl) return;
        cardsEl.innerHTML = "";
        emptyEl?.classList.toggle("d-none", blogs.length > 0);

        if (!blogs.length) return;

        blogs.forEach((blog) => {
            const card = document.createElement("button");
            card.type = "button";
            card.className = "blog-card";
            card.addEventListener("click", () => openDetail(blog.id));

            const thumb = document.createElement("div");
            thumb.className = "thumb";
            if (blog.main_image) {
                thumb.style.backgroundImage = `url('${blog.main_image}')`;
            } else {
                thumb.style.backgroundImage =
                    "linear-gradient(135deg, rgba(79,70,229,.3), rgba(251,191,36,.25))";
            }

            if (blog.highlighted) {
                const badge = document.createElement("span");
                badge.className = "badge-highlight";
                badge.textContent = "Mise en avant";
                thumb.appendChild(badge);
            }

            const body = document.createElement("div");
            body.className = "body";

            const title = document.createElement("p");
            title.className = "title";
            title.textContent = blog.title || "Sans titre";

            const meta = document.createElement("div");
            meta.className = "meta";
            const author = authorLabel(blog);
            if (author) {
                const authorSpan = document.createElement("span");
                authorSpan.textContent = author;
                meta.appendChild(authorSpan);
            }
            const dateLabel = formatDate(blog.created_at);
            if (dateLabel) {
                const dateSpan = document.createElement("span");
                dateSpan.textContent = dateLabel;
                meta.appendChild(dateSpan);
            }

            const excerpt = document.createElement("p");
            excerpt.className = "excerpt";
            excerpt.textContent = buildExcerpt(blog) || "Cliquez pour lire l'article.";

            body.append(title, meta, excerpt);
            card.append(thumb, body);
            cardsEl.appendChild(card);
        });
    };

    const applyFilter = () => {
        const query = (searchInput?.value || "").trim().toLowerCase();
        const filtered = state.blogs.filter((blog) => {
            if (!query) return true;
            const author = authorLabel(blog).toLowerCase();
            return (
                (blog.title || "").toLowerCase().includes(query) ||
                author.includes(query)
            );
        });
        renderCards(filtered);
    };

    const buildSection = ({ title, content, image, credit, type, variant }) => {
        if (!title && !content && !image) return null;
        const section = document.createElement("section");
        section.className = `container py-5 fade-section ${variant}`.trim();
        const hasImage = Boolean(image);
        const hasContent = Boolean(content || title);
        const isVertical = type === "vertical";

        const row = document.createElement("div");
        row.className = "row g-5 align-items-center";

        const textBlock = document.createElement("div");
        const imageBlock = document.createElement("div");

        if (variant === "blog-second-section") {
            textBlock.className = `col-12 ${isVertical ? "col-md-10" : "col-md-9"}`;
            imageBlock.className = `col-12 ${isVertical ? "col-md-2" : "col-md-3"}`;
        } else {
            textBlock.className = `col-12 ${isVertical ? "col-md-8" : "col-md-7"}`;
            imageBlock.className = `col-12 ${isVertical ? "col-md-4" : "col-md-5"}`;
        }

        if (title) {
            const heading = document.createElement("h3");
            heading.className = "fw-bold mb-4 detail-title";
            heading.textContent = title;
            textBlock.appendChild(heading);
        }
        if (content) {
            const paragraph = document.createElement("p");
            paragraph.textContent = content;
            const wrapper = document.createElement("div");
            wrapper.className = "detail-divider";
            wrapper.appendChild(paragraph);
            textBlock.appendChild(wrapper);
        }

        if (image) {
            const img = document.createElement("img");
            img.src = image;
            img.alt = title || "Illustration";
            img.className = "w-100 rounded-3 shadow-sm object-fit-cover";
            imageBlock.appendChild(img);
            if (credit) {
                const caption = document.createElement("div");
                caption.className = "detail-credit d-block mt-2";
                caption.textContent = `© ${credit}`;
                imageBlock.appendChild(caption);
            }
        }

        if (hasImage && hasContent) {
            if (variant === "blog-second-section") {
                row.append(textBlock, imageBlock);
            } else {
                row.append(imageBlock, textBlock);
            }
        } else if (hasImage) {
            row.append(imageBlock);
        } else {
            row.append(textBlock);
        }

        section.appendChild(row);
        return section;
    };

    const renderDetail = (blog) => {
        if (!detailCard) return;
        detailCard.innerHTML = "";
        const article = document.createElement("article");
        article.className = "blog-details-page";

        const banner = document.createElement("section");
        banner.className = "blog-banner container py-5 fade-section";

        const title = document.createElement("h1");
        title.className = "fw-bold display-5 mb-3";
        title.textContent = blog.title || "Sans titre";

        const divider = document.createElement("hr");
        divider.className = "border-2 opacity-100 mb-5";
        divider.style.maxWidth = "150px";

        const row = document.createElement("div");
        row.className = "row align-items-start g-4";

        const infoCol = document.createElement("div");
        infoCol.className = "col-12 col-md-4";

        const author = authorLabel(blog);
        if (author) {
            const authorLine = document.createElement("p");
            authorLine.className = "mb-1 fw-semibold";
            authorLine.textContent = `Par ${author}`;
            infoCol.appendChild(authorLine);
        }

        const dateLabel = formatDate(blog.created_at);
        if (dateLabel) {
            const dateLine = document.createElement("p");
            dateLine.className = "text-muted mb-3";
            dateLine.textContent = dateLabel;
            infoCol.appendChild(dateLine);
        }

        if (blog.highlighted) {
            const badge = document.createElement("span");
            badge.className = "badge border px-3 py-2 fw-semibold";
            badge.textContent = "Mise en avant";
            infoCol.appendChild(badge);
        }

        const imageCol = document.createElement("div");
        imageCol.className = "col-12 col-md-8";
        if (blog.main_image) {
            const img = document.createElement("img");
            img.src = blog.main_image;
            img.alt = blog.title || "Illustration principale";
            img.className = "w-100 rounded-3 shadow-sm object-fit-cover";
            imageCol.appendChild(img);
            if (blog.main_image_credit) {
                const credit = document.createElement("small");
                credit.className = "d-block mt-2 text-muted fst-italic detail-credit";
                credit.textContent = `© ${blog.main_image_credit}`;
                imageCol.appendChild(credit);
            }
        }

        row.append(infoCol, imageCol);
        banner.append(title, divider, row);
        article.appendChild(banner);

        const sections = [
            {
                title: blog.second_title,
                content: blog.second_content,
                image: blog.second_image,
                credit: blog.second_image_credit,
                type: blog.second_type,
                variant: "blog-first-section",
            },
            {
                title: null,
                content: blog.third_content,
                image: blog.third_image,
                credit: blog.third_image_credit,
                type: blog.third_type,
                variant: "blog-second-section",
            },
            {
                title: null,
                content: blog.fourth_content,
                image: blog.fourth_image,
                credit: blog.fourth_image_credit,
                type: blog.fourth_type,
                variant: "blog-third-section",
            },
        ];

        let hasSections = false;
        sections.forEach((sectionData) => {
            const section = buildSection(sectionData);
            if (section) {
                hasSections = true;
                article.appendChild(section);
            }
        });
        if (!hasSections) {
            const empty = document.createElement("p");
            empty.className = "empty-state container py-5";
            empty.textContent = "Aucun contenu detaille pour cet article.";
            article.appendChild(empty);
        }

        detailCard.appendChild(article);
        detailCard.scrollIntoView({ behavior: "smooth", block: "start" });
    };

    const openDetail = async (id) => {
        if (!id) return;
        setView("detail");
        if (detailCard) {
            detailCard.innerHTML = '<div class="loading-state">Chargement...</div>';
        }

        try {
            const res = await fetch(`/admin/blogs/${id}`, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`Erreur HTTP ${res.status}`);
            const blog = await res.json();
            renderDetail(blog);
        } catch (e) {
            if (detailCard) {
                detailCard.innerHTML = '<div class="empty-state">Impossible de charger cet article.</div>';
            }
        }
    };

    const fetchBlogs = async () => {
        setLoading(true);
        try {
            const params = new URLSearchParams();
            const companyId = localStorage.getItem("selectedCompanyId");
            if (companyId) params.set("company_id", companyId);
            params.set("status", "published");
            const url = params.toString() ? `/admin/blogs?${params}` : "/admin/blogs";
            const res = await fetch(url, {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            });
            if (!res.ok) throw new Error(`Erreur HTTP ${res.status}`);
            const data = await res.json();
            state.blogs = Array.isArray(data) ? data : [];
            applyFilter();
        } catch (e) {
            state.blogs = [];
            renderCards([]);
        } finally {
            setLoading(false);
        }
    };

    searchInput?.addEventListener("input", applyFilter);
    refreshBtn?.addEventListener("click", fetchBlogs);
    backBtn?.addEventListener("click", () => setView("list"));

    fetchBlogs();
}
