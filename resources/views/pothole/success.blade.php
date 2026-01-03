@extends('layouts.app')

@section('title', 'Success')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom text-center py-5">
                <div class="card-body">
                    <div class="mb-4">
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                    </div>
                    <h3 class="fw-bold text-primary mb-3">Report Submitted Successfully!</h3>
                    <p class="text-muted mb-4">Your infrastructure concern has been logged. Our maintenance teams will
                        review it shortly.</p>

                    <div class="bg-light p-4 rounded mt-4 mb-4 d-flex align-items-center justify-content-center">
                        <div class="me-3 text-start">
                            <p class="text-muted mb-1 small">Your Ticket ID:</p>
                            <h3 class="text-primary fw-bold mb-0" id="ticketIdText">{{ $ticket_id }}</h3>
                        </div>
                        <button
                            class="btn btn-outline-primary btn-sm rounded-circle d-flex align-items-center justify-content-center"
                            onclick="copyTicketId()" id="copyBtn" title="Copy to Clipboard"
                            style="width: 40px; height: 40px;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                                viewBox="0 0 16 16">
                                <path
                                    d="M4 1.5H3a2 2 0 0 0-2 2V14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V3.5a2 2 0 0 0-2-2h-1v1h1a1 1 0 0 1 1 1V14a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V3.5a1 1 0 0 1 1-1h1v-1z" />
                                <path
                                    d="M9.5 1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5v-1a.5.5 0 0 1 .5-.5h3zm-3-1A1.5 1.5 0 0 0 5 1.5v1A1.5 1.5 0 0 0 6.5 4h3A1.5 1.5 0 0 0 11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3z" />
                            </svg>
                        </button>
                    </div>

                    <div class="alert alert-info border-0">
                        <strong>Important:</strong> Please save this Ticket ID to track your complaint status.
                    </div>

                    <script>
                        function copyTicketId() {
                            const ticketId = document.getElementById('ticketIdText').innerText;
                            const btn = document.getElementById('copyBtn');

                            navigator.clipboard.writeText(ticketId).then(() => {
                                // Simple visual feedback
                                const originalInner = btn.innerHTML;
                                btn.innerHTML = '✅';
                                btn.classList.replace('btn-outline-primary', 'btn-success');

                                setTimeout(() => {
                                    btn.innerHTML = originalInner;
                                    btn.classList.replace('btn-success', 'btn-outline-primary');
                                }, 2000);
                            });
                        }

                        // Save ticket to localStorage for quick tracking later
                        (function () {
                            const ticketId = "{{ $ticket_id }}";
                            if (ticketId) {
                                // Save as last ticket
                                localStorage.setItem('lastTicketId', ticketId);

                                // Save to a list of my tickets (avoiding duplicates)
                                let myTickets = JSON.parse(localStorage.getItem('myPotholeTickets')) || [];
                                if (!myTickets.includes(ticketId)) {
                                    myTickets.unshift(ticketId); // Add to beginning
                                    // Limit to last 5 tickets to keep it simple
                                    if (myTickets.length > 5) myTickets.pop();
                                    localStorage.setItem('myPotholeTickets', JSON.stringify(myTickets));
                                }
                            }
                        })();
                    </script>

                    <div class="d-grid gap-2 mt-4">
                        <a href="{{ route('track') }}" class="btn btn-gov btn-lg fw-bold">Track Status Now</a>
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">Report Another Issue</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection