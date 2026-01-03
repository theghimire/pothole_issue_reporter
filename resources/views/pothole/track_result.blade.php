@extends('layouts.app')

@section('title', 'Ticket Details')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom ticket-card">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ __('Ticket Details') }}</h4>
                        <p class="text-muted mb-0 small">{{ __('Issue Reference') }}: {{ $report->ticket_id }}</p>
                    </div>
                    <span
                        class="badge @if($report->status == 'Pending') bg-warning @elseif($report->status == 'Ongoing') bg-info @elseif($report->status == 'Completed') bg-success @else bg-danger @endif fs-6">
                        {{ __($report->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small fw-bold">{{ __('SUBMITTED ON') }}</p>
                            <p>{{ \Carbon\Carbon::parse($report->created_at)->format('d M Y, h:i A') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-1 text-muted small fw-bold text-uppercase">{{ __('WARD NUMBER') }}</p>
                            <p>{{ __('Ward') }} {{ $report->ward }}</p>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <p class="mb-1 text-muted small fw-bold">{{ __('DESCRIPTION') }}</p>
                        <p>{{ $report->description }}</p>
                    </div>

                    @if($report->photo)
                        <div class="mb-4">
                            <p class="mb-2 text-muted small fw-bold text-uppercase">{{ __('Photo Evidence') }}</p>
                            @php 
                                $photos = json_decode($report->photo, true);
                                if (!is_array($photos)) {
                                    $photos = !empty($report->photo) ? [$report->photo] : [];
                                }
                            @endphp
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($photos as $p)
                                    <a href="{{ asset('storage/' . $p) }}" target="_blank" class="d-block">
                                        <img src="{{ asset('storage/' . $p) }}" alt="{{ __('Photo Evidence') }}" 
                                            class="img-fluid rounded border shadow-sm" 
                                            style="width: 120px; height: 120px; object-fit: cover;">
                                    </a>
                                @empty
                                    <span class="text-muted small">{{ __('No photos available') }}</span>
                                @endforelse
                            </div>
                        </div>
                    @endif

                    <hr>

                    <div class="mb-0">
                        <p class="mb-1 text-muted small fw-bold">{{ __('ADMIN REMARK') }}</p>
                        @if($report->admin_remark)
                            <div class="p-3 bg-light rounded text-dark">
                                {{ $report->admin_remark }}
                            </div>
                        @else
                            <p class="text-muted italic">{{ __('No remarks from the department yet.') }}</p>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="small text-muted mb-0">{{ __('Last updated:') }}
                            {{ \Carbon\Carbon::parse($report->updated_at)->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('track') }}" class="btn btn-outline-primary">&larr; {{ __('Track Another ID') }}</a>
            </div>
        </div>
    </div>
@endsection