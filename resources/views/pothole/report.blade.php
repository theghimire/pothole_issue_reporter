@extends('layouts.app')

@section('title', 'Report a Pothole')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-custom">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-0 fw-bold text-primary">{{ __('Report an Issue') }}</h4>
                        <p class="text-muted mb-0 small">{{ __('Help us identify and fix infrastructure problems in your community.') }}</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary border border-primary px-3 py-2">{{ __('Jurisdiction') }}: {{ __('Ward 3, Tarkeshwor Mun.') }}</span>
                </div>
                <div class="card-body">
                    <form action="{{ route('report.store') }}" method="POST" enctype="multipart/form-data" id="reportForm">
                        @csrf

                        <!-- Frequency Alert (Populated by JS) -->
                        <div id="densityAlert" class="mb-4" style="display: none;"></div>

                        <!-- 1. Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">{{ __('Issue Description') }} <span
                                    class="text-danger">*</span></label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                placeholder="{{ __('Describe size, depth, nearby landmark...') }}" required></textarea>
                            <input type="hidden" name="ward" value="5">
                        </div>

                        <!-- 2. Map (Location Picker) -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('Pin Exact Location on Map') }} <span
                                    class="text-danger">*</span></label>
                            <div id="map" class="mb-2"></div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="detectLocation()">
                                    📍 {{ __('Detect My Location') }}
                                </button>
                                <span
                                    class="text-muted small align-self-center">{{ __('Or click on the map to pin manually') }}</span>
                            </div>
                            <input type="hidden" name="latitude" id="latitude" required>
                            <input type="hidden" name="longitude" id="longitude" required>

                            <!-- Hidden inputs for Anti-Fraud Verification (Captured on photo add) -->
                            <input type="hidden" name="capture_lat" id="capture_lat">
                            <input type="hidden" name="capture_lng" id="capture_lng">
                        </div>

                        <!-- 3. Evidence Photos -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">{{ __('Evidence Photos') }} <span
                                    class="text-danger">*</span></label>
                            <p class="text-muted small mb-3">
                                {{ __('Please take clear photos of the issue. You can add multiple photos.') }}</p>

                            <ul class="nav nav-tabs mb-3" id="photoTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="camera-tab" data-bs-toggle="tab"
                                        data-bs-target="#camera" type="button"
                                        onclick="startCamera()">{{ __('Take From Camera') }}</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload"
                                        type="button">{{ __('Upload from Device') }}</button>
                                </li>
                            </ul>
                            <div class="tab-content" id="photoTabContent">
                                <div class="tab-pane fade show active" id="camera">
                                    <div class="bg-light p-2 rounded text-center">
                                        <video id="video" width="100%" height="auto" autoplay class="rounded mb-2"
                                            style="max-height: 300px; object-fit: cover;"></video>
                                        <button type="button" class="btn btn-sm btn-gov w-100 py-2"
                                            onclick="takeSnapshot()">
                                            📸 Capture Photo
                                        </button>
                                        <canvas id="canvas" style="display:none;"></canvas>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="upload">
                                    <div class="border border-dashed p-4 text-center rounded bg-light">
                                        <input type="file" class="form-control" id="filePicker" accept="image/*" multiple
                                            onchange="handleFileSelect(this)">
                                        <p class="text-muted mt-2 mb-0 small">Select one or more photos</p>
                                    </div>
                                </div>
                            </div>

                            <div id="photoGallery" class="row g-2 mt-3"></div>
                            <div id="hiddenInputs"></div>
                        </div>

                        <!-- 4. Contact Details (Optional) -->
                        <div class="bg-light p-3 rounded mb-4">
                            <h6 class="fw-bold mb-3"><i class="bi bi-person me-2"></i>{{ __('Reporter Details') }} ({{ __('Optional') }})</h6>
                            <div class="row">
                                <div class="col-md-6 mb-2">
                                    <input type="text" class="form-control" id="name" name="name" placeholder="{{ __('Your Full Name') }}">
                                </div>
                                <div class="col-md-6 mb-2">
                                    <input type="text" class="form-control" id="phone" name="phone"
                                        placeholder="{{ __('Phone Number') }}">
                                </div>
                            </div>
                            <p class="text-muted mb-0" style="font-size: 0.75rem;">{{ __('Your contact info remains private and is only used if we need more details.') }}</p>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg py-3 fw-bold text-uppercase">{{ __('SUBMIT REPORT') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('extra_js')
    <script>
        // Map Setup
        var map = L.map('map').setView([27.7675, 85.3123], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        var marker = L.marker([27.7675, 85.3123], { draggable: true }).addTo(map);

        function updateCoords(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
            checkAreaDensity(lat, lng); // Trigger frequency check
        }

        async function checkAreaDensity(lat, lng) {
            const densityAlert = document.getElementById('densityAlert');
            try {
                const response = await fetch(`/report/density-check?lat=${lat}&lng=${lng}`);
                const data = await response.json();

                if (data.count >= 3) {
                    densityAlert.style.display = 'block';
                    densityAlert.innerHTML = `
                                        <div class="alert alert-info border-info shadow-sm d-flex align-items-center mb-0">
                                            <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                                            <div>
                                                <h6 class="fw-bold mb-1">High Report Frequency Detected</h6>
                                                <p class="mb-0 small">Multiple reports exist near this spot. Please provide specific details or landmark references below to help our team locate the exact issue.</p>
                                            </div>
                                        </div>
                                    `;
                } else {
                    densityAlert.style.display = 'none';
                }
            } catch (error) {
                console.error("Density check failed:", error);
            }
        }

        marker.on('dragend', function (e) {
            var pos = marker.getLatLng();
            updateCoords(pos.lat, pos.lng);
        });

        map.on('click', function (e) {
            marker.setLatLng(e.latlng);
            updateCoords(e.latlng.lat, e.latlng.lng);
        });

        function detectLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var lat = position.coords.latitude;
                    var lng = position.coords.longitude;
                    map.setView([lat, lng], 16);
                    marker.setLatLng([lat, lng]);
                    updateCoords(lat, lng);
                }, function () {
                    alert("Geolocation failed or denied. Please pin manually.");
                });
            }
        }

        updateCoords(27.7675, 85.3123);

        // Multi-Photo & Anti-Fraud Logic
        const photoGallery = document.getElementById('photoGallery');
        const hiddenInputs = document.getElementById('hiddenInputs');
        const captureLatInput = document.getElementById('capture_lat');
        const captureLngInput = document.getElementById('capture_lng');
        let photoCount = 0;

        // Anti-Fraud: Silently capture location whenever a photo is added
        function captureVerificationLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    captureLatInput.value = position.coords.latitude;
                    captureLngInput.value = position.coords.longitude;
                    console.log("Verification location captured");
                }, function (err) {
                    console.warn("Verification geolocation failed:", err.message);
                }, { enableHighAccuracy: true });
            }
        }

        // Camera Logic
        var video = document.getElementById('video');
        var canvas = document.getElementById('canvas');
        var stream;

        function startCamera() {
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } }).then(function (s) {
                    stream = s;
                    video.srcObject = s;
                    video.play();
                }).catch(err => {
                    console.error("Camera error:", err);
                    alert("Could not access camera. Please use file upload.");
                });
            }
        }

        function takeSnapshot() {
            if (!video.srcObject) {
                alert("Please start the camera first.");
                return;
            }
            var context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, video.videoWidth, video.videoHeight);

            var dataURL = canvas.toDataURL('image/jpeg', 0.8);
            addPhotoToGallery(dataURL);
            captureVerificationLocation(); // ANTI-FRAUD
        }

        // File Selection Logic
        function handleFileSelect(input) {
            const files = input.files;
            if (files.length > 0) {
                captureVerificationLocation(); // ANTI-FRAUD: Capture location when selecting files
            }
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    addPhotoToGallery(e.target.result);
                }
                reader.readAsDataURL(files[i]);
            }
            input.value = ""; // Reset file picker
        }

        function addPhotoToGallery(dataURL) {
            photoCount++;
            const photoId = 'photo_' + photoCount;

            // Create Thumbnail
            const col = document.createElement('div');
            col.className = 'col-4 col-md-3 position-relative photo-item mb-2';
            col.id = 'thumb_' + photoId;
            col.innerHTML = `
                                <div class="ratio ratio-1x1 border rounded overflow-hidden shadow-sm bg-light">
                                    <img src="${dataURL}" style="object-fit: cover;">
                                </div>
                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1 p-0 d-flex align-items-center justify-content-center shadow" 
                                        style="width: 24px; height: 24px; z-index: 10;" onclick="removePhoto('${photoId}')">
                                    <i class="bi bi-x"></i>
                                </button>
                            `;
            photoGallery.appendChild(col);

            // Create Hidden Input for Form Submission
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'photos[]';
            input.value = dataURL;
            input.id = photoId;
            hiddenInputs.appendChild(input);
        }

        function removePhoto(photoId) {
            const el = document.getElementById(photoId);
            const thumb = document.getElementById('thumb_' + photoId);
            if (el) el.remove();
            if (thumb) thumb.remove();
        }

        // Auto-start camera if tab is active
        document.addEventListener('DOMContentLoaded', () => {
            if (document.getElementById('camera-tab').classList.contains('active')) {
                startCamera();
            }
        });
    </script>
    <style>
        .border-dashed {
            border-style: dashed !important;
            border-width: 2px !important;
        }

        .photo-item img {
            transition: transform 0.2s;
        }

        .photo-item:hover img {
            transform: scale(1.05);
        }
    </style>
@endsection