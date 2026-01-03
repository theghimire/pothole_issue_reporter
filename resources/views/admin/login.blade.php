@extends('layouts.app')

@section('title', 'Admin Login')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-custom">
                <div class="card-header bg-white py-3">
                    <h4 class="mb-0 fw-bold text-primary text-center">Admin Portal Access</h4>
                    <p class="text-muted mb-0 small text-center">Secure access for community infrastructure management.</p>
                </div>
                <div class="card-body py-4">
                    <form action="{{ route('admin.login.submit') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="username" class="form-label fw-bold">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label for="password" class="form-label fw-bold">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-gov btn-lg fw-bold">LOGIN TO DASHBOARD</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer bg-light text-center py-3">
                    <a href="{{ route('home') }}" class="text-decoration-none small">&larr; Back to Reporter</a>
                </div>
            </div>
        </div>
    </div>
@endsection