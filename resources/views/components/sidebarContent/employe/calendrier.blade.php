@include('partials.fullCalendar')

<div class="calendar-page calendar-employee-page" data-script="calendarEmployee">
    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="fw-bold">Mon calendrier RH</h2>
        <div class="d-flex align-items-center gap-2">
            <button class="btn calendar-nav prev"><i class="fa-solid fa-chevron-left"></i></button>
            <h5 class="month-title mb-0">—</h5>
            <button class="btn calendar-nav next"><i class="fa-solid fa-chevron-right"></i></button>
        </div>
    </div>

    {{-- LEGENDE --}}
    <div class="calendar-legend mb-3">
        <span><i class="legend-dot conge"></i> Congé</span>
        <span><i class="legend-dot note_frais"></i> Note de frais</span>
        <span><i class="legend-dot document_rh"></i> Document RH</span>
        <span><i class="legend-dot incident"></i> Incident</span>
        <span><i class="legend-dot autre"></i> Autre</span>
    </div>

    <div id="calendarEmployeeContainer"></div>
</div>
