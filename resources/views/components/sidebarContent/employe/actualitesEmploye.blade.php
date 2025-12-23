<div class="employee-actualites" data-script="actualitesEmployee">
  <div class="head">
    <div>
      <h2>Actualites de l'entreprise</h2>
      <p class="subhead">Retrouvez les informations importantes et les temps forts publies pour votre equipe.</p>
    </div>
    <div class="actions">
      <label class="search" for="employeeBlogSearch">
        <i class="fa-solid fa-magnifying-glass"></i>
        <input type="text" id="employeeBlogSearch" placeholder="Rechercher un article">
      </label>
      <button class="btn-ghost" id="employeeBlogRefresh">
        <i class="fa-solid fa-rotate-right me-2"></i>Rafraichir
      </button>
    </div>
  </div>

  <div class="list-view" data-view="list">
    <div class="loading-state d-none" id="employeeBlogLoading">Chargement des actualites...</div>
    <div class="cards-grid" id="employeeBlogCards"></div>
    <div class="empty-state d-none" id="employeeBlogEmpty">Aucun article disponible pour le moment.</div>
  </div>

  <div class="detail-view d-none" data-view="detail">
    <button class="btn-ghost mb-3" id="employeeBlogBack">
      <i class="fa-solid fa-arrow-left me-2"></i>Retour aux actualites
    </button>
    <div class="detail-card" id="employeeBlogDetail"></div>
  </div>
</div>
