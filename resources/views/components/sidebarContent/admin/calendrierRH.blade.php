<div class="calendar-admin-page" data-script="calendarRHManagement">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Calendrier RH</h2>
        <div class="d-flex align-items-center gap-2">
            <button class="btn calendar-nav prev" type="button">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <h5 class="month-title mb-0" id="calendarMonthTitle">Mois année</h5>

            <button class="btn calendar-nav next" type="button">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Bouton “Aujourd’hui” -->
            <button class="btn calendar-nav today" type="button" title="Revenir à aujourd’hui">
                <i class="fa-solid fa-location-crosshairs"></i>
            </button>
        </div>
    </div>


    {{-- LEGENDE --}}
    <div class="calendar-legend mb-4">
        <span><i class="legend-dot leave-cp"></i> Congés payés</span>
        <span><i class="legend-dot leave-sans-solde"></i> Sans solde</span>
        <span><i class="legend-dot leave-exceptionnel"></i> Absence exceptionnelle</span>
        <span><i class="legend-dot leave-maladie"></i> Maladie</span>
    </div>


    {{-- CALENDRIER (rempli en JS) --}}
    <div class="calendar-grid" id="calendarGrid">
        {{-- Le JS injectera :
            - la ligne des noms de jours (Lun, Mar, …)
            - les cases de jours avec leurs événements
        --}}
    </div>
</div>
