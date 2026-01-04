@extends('layouts.app')

@section('title', 'Welcome to Pothole Issue Reporter')

@section('extra_css')
    <style>
        .hero-section {
            padding: 30px 0;
        }

        .notice-card {
            background: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
        }

        .notice-card:hover {
            transform: translateY(-5px);
        }

        .btn-action {
            padding: 15px 30px;
            border-radius: 10px;
            font-size: 1.1rem;
            transition: all 0.3s;
        }

        .btn-report {
            background-color: var(--primary-soft);
            border: none;
            color: white;
        }

        .btn-report:hover {
            background-color: #357abd;
            color: white;
            box-shadow: 0 4px 15px rgba(74, 144, 226, 0.4);
        }

        .btn-track {
            background-color: white;
            border: 2px solid var(--primary-soft);
            color: var(--primary-soft);
        }

        .btn-track:hover {
            background-color: var(--primary-soft);
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.04);
        }

        .status-badge {
            font-size: 0.8rem;
            padding: 5px 12px;
            border-radius: 20px;
        }

        .carousel-item {
            height: 350px;
            border-radius: 20px;
            overflow: hidden;
        }

        .carousel-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            padding: 40px;
            color: white;
        }
    </style>
@endsection

@section('content')
    <!-- Notices Carousel -->
    <section class="hero-section">
        <div id="noticeCarousel" class="carousel slide shadow-lg mb-5" data-bs-ride="carousel"
            style="border-radius: 20px; overflow: hidden;">
            <div class="carousel-inner">
                <div class="carousel-item active" style="background: #4a90e2;">
                    <div class="carousel-overlay">
                        <h2 class="fw-bold">{{ __('Welcome to the Community Issue Portal') }}</h2>
                        <p class="lead">{{ __('Efficiently report and track infrastructure issues in your area.') }}</p>
                    </div>
                </div>
                <div class="carousel-item" style="background: #6272a4;">
                    <div class="carousel-overlay">
                        <h2 class="fw-bold">{{ __('Smart Infrastructure Tracking') }}</h2>
                        <p class="lead">{{ __("We've implemented a multiple-photo system for better issue verification.") }}
                        </p>
                    </div>
                </div>
                <div class="carousel-item" style="background: #48c774;">
                    <div class="carousel-overlay">
                        <h2 class="fw-bold">{{ __('Ward No 3 Official Notice') }}</h2>
                        <p class="lead">{{ __('All ongoing road works are now visible in the public table below.') }}</p>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#noticeCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#noticeCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
            </button>
        </div>

        <!-- Quick Action Buttons -->
        <div class="row text-center g-4 mb-5">
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <i class="bi bi-megaphone fs-1 text-primary mb-3 d-block"></i>
                    <h3>{{ __('Found a Pothole?') }}</h3>
                    <p class="text-muted mb-4">{{ __('Click below to submit a new report with photos and GPS location.') }}
                    </p>
                    <a href="{{ route('report') }}" class="btn btn-action btn-report w-100">{{ __('REPORT AN ISSUE') }}</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-4 bg-white rounded-4 shadow-sm h-100">
                    <i class="bi bi-search fs-1 text-primary mb-3 d-block"></i>
                    <h3>{{ __('Already Reported?') }}</h3>
                    <p class="text-muted mb-4">{{ __('Enter your Ticket ID to check the current repair progress.') }}</p>
                    <a href="{{ route('track') }}" class="btn btn-action btn-track w-100">{{ __('TRACK STATUS') }}</a>
                </div>
            </div>
        </div>

        <!-- Read-only Complaint Table -->
        <div class="table-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">{{ __('Public Issue Log') }}</h4>
                <div class="input-group w-50">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-geo-alt"></i></span>
                    <input type="text" id="locationSearch" class="form-control bg-light border-start-0"
                        placeholder="{{ __('Search by location...') }}">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle" id="complaintTable">
                    <thead class="table-light">
                        <tr>
                            <th>{{ __('Ticket ID') }}</th>
                            <th>{{ __('Submitted Date') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Location') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th class="text-end">{{ __('Photos') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($issues as $issue)
                            <tr>
                                <td class="fw-bold text-primary">{{ $issue->ticket_id }}</td>
                                <td>{{ \Carbon\Carbon::parse($issue->created_at)->format('d M, Y') }}</td>
                                <td>
                                    <span
                                        class="status-badge @if($issue->status == 'Pending') bg-warning-subtle text-warning @elseif($issue->status == 'Ongoing') bg-info-subtle text-info @elseif($issue->status == 'Completed') bg-success-subtle text-success @else bg-danger-subtle text-danger @endif">
                                        {{ __($issue->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span
                                        class="fw-bold location-text">{{ $issue->location_name ?? __('Ward') . ' ' . $issue->ward }}</span>
                                    <div class="small text-muted">{{ round($issue->latitude, 4) }},
                                        {{ round($issue->longitude, 4) }}</div>
                                </td>
                                <td>
                                    <span class="text-truncate d-inline-block" style="max-width: 250px;">
                                        {{ $issue->description }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    @if($issue->photo)
                                        @php
                                            $photos = json_decode($issue->photo, true);
                                            if (!is_array($photos)) {
                                                $photos = !empty($issue->photo) ? [$issue->photo] : [];
                                            }
                                        @endphp
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-image me-1"></i> {{ count($photos) }}
                                        </span>
                                    @else
                                        <span class="text-muted small">None</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">{{ __('No potholes reported yet.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection

@section('extra_js')
    <script>
        // Location search filter
        document.getElementById('locationSearch').addEventListener('keyup', function () {
            let value = this.value.toLowerCase();
            let rows = document.querySelectorAll('#complaintTable tbody tr');

            rows.forEach(row => {
                let locationCell = row.querySelector('.location-text');
                if (locationCell) {
                    let text = locationCell.textContent.toLowerCase();
                    row.style.display = text.includes(value) ? '' : 'none';
                }
            });
        });
    </script>
@endsection