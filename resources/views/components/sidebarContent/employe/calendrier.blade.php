<div class="calendar-page">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Mon calendrier RH</h2>
        <div class="d-flex align-items-center gap-2">
            <button class="btn calendar-nav prev"><i class="fa-solid fa-chevron-left"></i></button>
            <h5 class="month-title mb-0">Octobre 2025</h5>
            <button class="btn calendar-nav next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>

    {{-- LEGENDE --}}
    <div class="calendar-legend mb-3">
        <span><i class="legend-dot conge"></i> Congé</span>
        <span><i class="legend-dot formation"></i> Formation</span>
        <span><i class="legend-dot entretien"></i> Entretien</span>
        <span><i class="legend-dot evenement"></i> Événement interne</span>
    </div>

    {{-- CALENDRIER (statique pour l'instant) --}}
    <div class="calendar-grid">
        {{-- Jours de la semaine --}}
        <div class="day-name">Lun</div>
        <div class="day-name">Mar</div>
        <div class="day-name">Mer</div>
        <div class="day-name">Jeu</div>
        <div class="day-name">Ven</div>
        <div class="day-name">Sam</div>
        <div class="day-name">Dim</div>

        {{-- Jours (exemple : Octobre 2025) --}}
        @for ($i = 1; $i <= 31; $i++)
            <div class="day-cell">
                <div class="day-number">{{ $i }}</div>

                {{-- Événements simulés pour l’instant --}}
                @if (in_array($i, [3, 10]))
                    <div class="event conge">Congé</div>
                @elseif (in_array($i, [15]))
                    <div class="event formation">Formation</div>
                @elseif (in_array($i, [21]))
                    <div class="event entretien">Entretien</div>
                @elseif (in_array($i, [28]))
                    <div class="event evenement">Événement</div>
                @endif
            </div>
        @endfor
    </div>
</div>
