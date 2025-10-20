<div class="parametres-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Paramètres système</h2>
        <button class="btn btn-save">
            <i class="fa-solid fa-floppy-disk me-2"></i> Enregistrer les changements
        </button>
    </div>

    {{-- SECTIONS DE PARAMÈTRES --}}
    <div class="parametres-grid">
        {{-- PARAMÈTRES GÉNÉRAUX --}}
        <div class="param-card">
            <h5><i class="fa-solid fa-gear me-2 text-primary"></i> Paramètres généraux</h5>
            <div class="param-body">
                <div class="param-item">
                    <label>Nom de l’application</label>
                    <input type="text" value="WorkAndPeople CRM" class="form-control">
                </div>

                <div class="param-item">
                    <label>Logo principal</label>
                    <input type="file" class="form-control">
                </div>

                <div class="param-item">
                    <label>Langue</label>
                    <select class="form-select">
                        <option>Français</option>
                        <option>Anglais</option>
                    </select>
                </div>

                <div class="param-item">
                    <label>Fuseau horaire</label>
                    <select class="form-select">
                        <option>Europe/Paris</option>
                        <option>UTC</option>
                        <option>America/New_York</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- PARAMÈTRES DE SÉCURITÉ --}}
        <div class="param-card">
            <h5><i class="fa-solid fa-shield-halved me-2 text-primary"></i> Sécurité</h5>
            <div class="param-body">
                <div class="param-item">
                    <label>Expiration du mot de passe</label>
                    <select class="form-select">
                        <option>90 jours</option>
                        <option>180 jours</option>
                        <option>Jamais</option>
                    </select>
                </div>

                <div class="param-item d-flex align-items-center justify-content-between">
                    <label>Authentification à deux facteurs</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="twoFA" checked>
                        <label for="twoFA"></label>
                    </div>
                </div>

                <div class="param-item d-flex align-items-center justify-content-between">
                    <label>Journalisation des connexions</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="logs" checked>
                        <label for="logs"></label>
                    </div>
                </div>
            </div>
        </div>

        {{-- PARAMÈTRES MAIL --}}
        <div class="param-card">
            <h5><i class="fa-solid fa-envelope me-2 text-primary"></i> Notifications & E-mails</h5>
            <div class="param-body">
                <div class="param-item">
                    <label>Adresse e-mail d’envoi</label>
                    <input type="email" class="form-control" value="noreply@workandpeople.fr">
                </div>

                <div class="param-item">
                    <label>Nom d’expéditeur</label>
                    <input type="text" class="form-control" value="WorkAndPeople RH">
                </div>

                <div class="param-item">
                    <label>Serveur SMTP</label>
                    <input type="text" class="form-control" value="smtp.mailtrap.io">
                </div>
            </div>
        </div>

        {{-- MAINTENANCE --}}
        <div class="param-card">
            <h5><i class="fa-solid fa-wrench me-2 text-primary"></i> Maintenance & système</h5>
            <div class="param-body">
                <div class="param-item d-flex align-items-center justify-content-between">
                    <label>Mode maintenance</label>
                    <div class="toggle-switch">
                        <input type="checkbox" id="maintenance">
                        <label for="maintenance"></label>
                    </div>
                </div>

                <div class="param-item">
                    <label>Dernière sauvegarde</label>
                    <input type="text" readonly value="12/10/2025 - 02h31" class="form-control readonly">
                </div>

                <button class="btn btn-backup mt-2">
                    <i class="fa-solid fa-database me-2"></i> Sauvegarder maintenant
                </button>
            </div>
        </div>
    </div>
</div>

