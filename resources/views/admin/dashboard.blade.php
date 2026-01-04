@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
        <h3 class="fw-bold text-primary mb-0">{{ __('Infrastructure Management Dashboard') }}</h3>
        <div>
            <a href="{{ route('admin.export.csv') }}" class="btn btn-success me-2 text-uppercase">📤 {{ __('Export Records (CSV)') }}</a>
            <button onclick="window.print()" class="btn btn-outline-secondary me-2">{{ __('Print View') }}</button>
            <a href="{{ route('admin.logout') }}" class="btn btn-danger">{{ __('Logout') }}</a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row mb-4 no-print">
        <div class="col-md-3">
            <div class="card bg-warning text-white border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">{{ __('Pending') }}</h6>
                    <h2 class="mb-0">{{ $counts['Pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">{{ __('Ongoing') }}</h6>
                    <h2 class="mb-0">{{ $counts['Ongoing'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">{{ __('Completed') }}</h6>
                    <h2 class="mb-0">{{ $counts['Completed'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white border-0 shadow-sm mb-3">
                <div class="card-body">
                    <h6 class="text-uppercase small fw-bold">{{ __('Rejected') }}</h6>
                    <h2 class="mb-0">{{ $counts['Rejected'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4 no-print">
        <div class="card-body">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">{{ __('Filter by Status') }}</label>
                    <div class="input-group">
                        <select name="status" class="form-select">
                            <option value="">{{ __('All Statuses') }}</option>
                            <option value="Pending" {{ request('status') == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                            <option value="Ongoing" {{ request('status') == 'Ongoing' ? 'selected' : '' }}>{{ __('Ongoing') }}</option>
                            <option value="Completed" {{ request('status') == 'Completed' ? 'selected' : '' }}>{{ __('Completed') }}
                            </option>
                            <option value="Rejected" {{ request('status') == 'Rejected' ? 'selected' : '' }}>{{ __('Rejected (Already Fixed)') }}</option>
                        </select>
                        <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                    </div>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary w-100">{{ __('Reset View') }}</a>
                </div>
                <div class="col-md-6 text-md-end">
                    <span class="badge bg-white text-primary border">{{ __('Tarkeshwor Municipality: Ward 3') }}</span>
                </div>
            </form>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-3 border-0">{{ __('Ticket ID') }}</th>
                            <th class="border-0">{{ __('Status') }}</th>
                            <th class="border-0 text-center">{{ __('Priority') }}</th>
                            <th class="border-0 text-center">{{ __('Reports') }}</th>
                            <th class="border-0 text-center">{{ __('Verification') }}</th>
                            <th class="border-0 no-print text-center">{{ __('Photo') }}</th>
                            <th class="border-0 no-print text-end pe-3">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td class="ps-3 fw-bold">{{ $report->ticket_id }}</td>
                                <td>
                                    <span
                                        class="badge @if($report->status == 'Pending') bg-warning @elseif($report->status == 'Ongoing') bg-info @elseif($report->status == 'Completed') bg-success @else bg-danger @endif">
                                        {{ __($report->status) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($report->is_high_priority)
                                        <span class="badge bg-danger animate-pulse shadow-sm">🚨 {{ __('HIGH') }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border">{{ __('Normal') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold">{{ $report->report_count }}</span>
                                </td>
                                <td class="text-center">
                                    @if($report->capture_lat)
                                        @php 
                                            $dist = abs($report->latitude - $report->capture_lat) + abs($report->longitude - $report->capture_lng);
                                        @endphp
                                        @if($dist < 0.001)
                                            <span class="badge bg-success-subtle text-success border border-success">{{ __('Verified') }}</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger" title="Reported location differs from photo location">🚨 {{ __('Potential Proxy') }}</span>
                                        @endif
                                    @else
                                        <span class="badge bg-light text-muted border">{{ __('Unknown') }}</span>
                                    @endif
                                </td>
                                <td class="no-print text-center">
                                    @if($report->photo)
                                        @php 
                                            $photos = json_decode($report->photo, true);
                                            // Handle legacy non-JSON strings or failed decodes
                                            if (!is_array($photos)) {
                                                $photos = !empty($report->photo) ? [$report->photo] : [];
                                            }
                                        @endphp
                                        @if(count($photos) > 0)
                                            <div class="position-relative d-inline-block">
                                                <a href="{{ asset('storage/' . $photos[0]) }}" target="_blank">
                                                    <img src="{{ asset('storage/' . $photos[0]) }}" width="40" height="40"
                                                        class="rounded border shadow-sm">
                                                </a>
                                                @if(count($photos) > 1)
                                                    <span
                                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-primary"
                                                        style="font-size: 0.6rem;">
                                                        +{{ count($photos) - 1 }}
                                                    </span>
                                                @endif
                                            </div>
                                        @else
                                            <span class="text-muted small">No photo</span>
                                        @endif
                                    @else
                                        <span class="text-muted small">No photo</span>
                                    @endif
                                </td>
                                <td class="no-print text-end pe-3">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal"
                                            data-bs-target="#updateModal{{ $report->id }}">{{ __('Manage') }}</button>

                                        <form action="{{ route('admin.report.destroy', $report->id) }}" method="POST"
                                            onsubmit="return confirm('WARNING: This will permanently delete this report. Are you sure?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">{{ __('Delete') }}</button>
                                        </form>

                                        <a href="https://www.google.com/maps?q={{ $report->latitude }},{{ $report->longitude }}"
                                            target="_blank" class="btn btn-sm btn-outline-secondary">{{ __('Map') }}</a>
                                    </div>
                                </td>
                            </tr>

                            <!-- Update Modal -->
                            <div class="modal fade" id="updateModal{{ $report->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <form action="{{ route('admin.status.update', $report->id) }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="ticket_id" value="{{ $report->ticket_id }}">
                                            <div class="modal-header">
                                                <h5 class="modal-title">{{ __('Manage Ticket') }}: {{ $report->ticket_id }} 
                                                    @if($report->is_high_priority)
                                                        <span class="badge bg-danger ms-2">{{ __('HIGH PRIORITY') }}</span>
                                                    @endif
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <p class="small text-muted mb-1 fw-bold text-uppercase">{{ __('Citizen Report:') }} ({{ $report->report_count }} {{ __('Total Reports') }})</p>
                                                    <div class="row g-2">
                                                        <div class="col-8">
                                                            <div class="p-2 bg-light rounded small h-100">{{ $report->description }}</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="p-2 border rounded small h-100">
                                                                <span class="d-block text-muted fw-bold">{{ __('Contact') }}:</span>
                                                                {{ $report->name ?? __('Anonymous') }}<br>
                                                                <span class="text-primary">{{ $report->phone ?? __('No Phone') }}</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if($report->capture_lat)
                                                <div class="mb-3">
                                                    <p class="small text-muted mb-1 fw-bold text-uppercase">{{ __('Security Check (Anti-Fraud):') }}</p>
                                                    <div class="p-2 border rounded small @if(abs($report->latitude - $report->capture_lat) + abs($report->longitude - $report->capture_lng) < 0.001) border-success bg-success-subtle @else border-danger bg-danger-subtle @endif">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>
                                                                {{ __('Reported:') }} {{ round($report->latitude, 4) }}, {{ round($report->longitude, 4) }}<br>
                                                                {{ __('Captured:') }} {{ round($report->capture_lat, 4) }}, {{ round($report->capture_lng, 4) }}
                                                            </span>
                                                            <a href="https://www.google.com/maps/dir/{{ $report->latitude }},{{ $report->longitude }}/{{ $report->capture_lat }},{{ $report->capture_lng }}" target="_blank" class="btn btn-sm btn-dark py-1">{{ __('Compare') }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="mb-3">
                                                    <p class="small text-muted mb-2 fw-bold text-uppercase">{{ __('Evidence Photos:') }}</p>
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @if($report->photo)
                                                            @php 
                                                                $photos = json_decode($report->photo, true);
                                                                if (!is_array($photos)) {
                                                                    $photos = !empty($report->photo) ? [$report->photo] : [];
                                                                }
                                                            @endphp
                                                            @forelse($photos as $p)
                                                                <a href="{{ asset('storage/' . $p) }}" target="_blank" class="d-block">
                                                                    <img src="{{ asset('storage/' . $p) }}" width="80" height="80"
                                                                        class="rounded border object-fit-cover shadow-sm">
                                                                </a>
                                                            @empty
                                                                <span class="text-muted small">{{ __('No photos available') }}</span>
                                                            @endforelse
                                                        @else
                                                            <span class="text-muted small">{{ __('No photos available') }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('Update Status') }}</label>
                                                    <select name="status" class="form-select">
                                                        <option value="Pending" {{ $report->status == 'Pending' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                                        <option value="Ongoing" {{ $report->status == 'Ongoing' ? 'selected' : '' }}>{{ __('Ongoing') }}</option>
                                                        <option value="Completed" {{ $report->status == 'Completed' ? 'selected' : '' }}>{{ __('Completed') }}</option>
                                                        <option value="Rejected" {{ $report->status == 'Rejected' ? 'selected' : '' }}>{{ __('Rejected (Already Fixed)') }}</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold">{{ __('Admin Remark') }}</label>
                                                    <textarea name="admin_remark" class="form-control" rows="3"
                                                        placeholder="{{ __('Enter remarks for the citizen...') }}">{{ $report->admin_remark }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-toggle="modal">{{ __('Close') }}</button>
                                                <button type="submit" class="btn btn-gov fw-bold">{{ __('Save Changes') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">{{ __('No reports found matching the criteria.') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection