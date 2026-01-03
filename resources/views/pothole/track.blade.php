@extends('layouts.app')

@section('title', 'Track Status')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-custom">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold text-primary">{{ __('Track My Complaint') }}</h4>
                    <p class="text-muted mb-0 small">{{ __('Enter your Ticket ID below to see latest progress updates.') }}
                    </p>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('track.check') }}" id="trackForm" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="ticket_id" class="form-label fw-bold">{{ __('Ticket ID') }}</label>
                            <input type="text" class="form-control form-control-lg" id="ticket_id" name="ticket_id"
                                placeholder="e.g. POT-ABC12345" required>
                        </div>

                        <!-- Recent Tickets Section -->
                        <div id="recentTicketsSection" style="display: none;" class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label
                                    class="form-label small fw-bold text-muted mb-0">{{ __('Recently Tracked Tickets:') }}</label>
                                <button type="button" class="btn btn-link btn-sm text-danger p-0 text-decoration-none"
                                    onclick="clearAllHistory()" style="font-size: 0.75rem;">{{ __('Clear All') }}</button>
                            </div>
                            <div id="ticketsList" class="d-flex flex-wrap gap-2">
                                <!-- Items will be added here via JS -->
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit"
                                class="btn btn-primary btn-lg fw-bold text-uppercase">{{ __('TRACK STATUS') }}</button>
                        </div>
                    </form>

                    <script>
                        const STORAGE_KEY = 'myPotholeTickets';

                        function getHistroy() {
                            return JSON.parse(localStorage.getItem(STORAGE_KEY)) || [];
                        }

                        function saveToHistory(id) {
                            let tickets = getHistroy();
                            if (!tickets.includes(id)) {
                                tickets.unshift(id); // Add to beginning
                                tickets = tickets.slice(0, 5); // Keep last 5
                                localStorage.setItem(STORAGE_KEY, JSON.stringify(tickets));
                            }
                        }

                        function removeFromHistory(id) {
                            let tickets = getHistroy();
                            tickets = tickets.filter(t => t !== id);
                            localStorage.setItem(STORAGE_KEY, JSON.stringify(tickets));
                            renderHistory();
                        }

                        function clearAllHistory() {
                            if (confirm('Clear all tracking history?')) {
                                localStorage.removeItem(STORAGE_KEY);
                                renderHistory();
                            }
                        }

                        function renderHistory() {
                            const tickets = getHistroy();
                            const section = document.getElementById('recentTicketsSection');
                            const list = document.getElementById('ticketsList');
                            const input = document.getElementById('ticket_id');
                            const form = document.getElementById('trackForm');

                            list.innerHTML = '';

                            if (tickets.length > 0) {
                                section.style.display = 'block';
                                tickets.forEach(id => {
                                    const wrapper = document.createElement('div');
                                    wrapper.className = 'btn-group btn-group-sm';

                                    const btn = document.createElement('button');
                                    btn.type = 'button';
                                    btn.className = 'btn btn-outline-secondary';
                                    btn.textContent = id;
                                    btn.onclick = function () {
                                        input.value = id;
                                        form.submit();
                                    };

                                    const delBtn = document.createElement('button');
                                    delBtn.type = 'button';
                                    delBtn.className = 'btn btn-outline-danger';
                                    delBtn.innerHTML = '&times;';
                                    delBtn.onclick = (e) => {
                                        e.stopPropagation();
                                        removeFromHistory(id);
                                    };

                                    wrapper.appendChild(btn);
                                    wrapper.appendChild(delBtn);
                                    list.appendChild(wrapper);
                                });
                            } else {
                                section.style.display = 'none';
                            }
                        }

                        // Save search on form submission
                        document.getElementById('trackForm').addEventListener('submit', function () {
                            const id = document.getElementById('ticket_id').value.trim();
                            if (id) saveToHistory(id);
                        });

                        // Initial render
                        renderHistory();
                    </script>
                </div>
            </div>
        </div>
    </div>
@endsection