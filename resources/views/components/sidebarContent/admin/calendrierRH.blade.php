<div class="calendar-admin-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Calendrier RH</h2>
        <div class="d-flex align-items-center gap-2">
            <button class="btn calendar-nav prev"><i class="fa-solid fa-chevron-left"></i></button>
            <h5 class="month-title mb-0">Octobre 2025</h5>
            <button class="btn calendar-nav next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>

    {{-- LEGENDE --}}
    <div class="calendar-legend mb-4">
        <span><i class="legend-dot conge"></i> Congé</span>
        <span><i class="legend-dot maladie"></i> Maladie</span>
        <span><i class="legend-dot formation"></i> Formation</span>
        <span><i class="legend-dot entretien"></i> Entretien</span>
        <span><i class="legend-dot evenement"></i> Événement interne</span>
    </div>

    {{-- CALENDRIER --}}
    <div class="calendar-grid">
        {{-- Noms des jours --}}
        <div class="day-name">Lun</div>
        <div class="day-name">Mar</div>
        <div class="day-name">Mer</div>
        <div class="day-name">Jeu</div>
        <div class="day-name">Ven</div>
        <div class="day-name">Sam</div>
        <div class="day-name">Dim</div>

        {{-- Jours du mois (exemple Octobre 2025) --}}
        @for ($i = 1; $i <= 31; $i++)
            <div class="day-cell">
                <div class="day-number">{{ $i }}</div>

                {{-- Événements simulés --}}
                @if (in_array($i, [2, 3, 4]))
                    <div class="event conge">Julien Dupont</div>
                @elseif (in_array($i, [10]))
                    <div class="event formation">Formation sécurité</div>
                @elseif (in_array($i, [15]))
                    <div class="event entretien">Entretien annuel</div>
                @elseif (in_array($i, [22]))
                    <div class="event maladie">Absence maladie</div>
                @elseif (in_array($i, [28]))
                    <div class="event evenement">Afterwork RH</div>
                @endif
            </div>
        @endfor
    </div>
</div>
