<?php
$pageTitleKey = "report_issue";
include 'header.php';
?>

<div class="bg-light py-4 border-bottom mb-4">
    <div class="container text-center">
        <h2 class="text-primary"><?php echo __('report_issue'); ?></h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb justify-content-center">
                <li class="breadcrumb-item"><a href="index.php"><?php echo __('home'); ?></a></li>
                <li class="breadcrumb-item active"><?php echo __('report_issue'); ?></li>
            </ol>
        </nav>
    </div>
</div>

<div class="container mb-5">
    <div class="row">
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 bg-white" style="border-top: 5px solid var(--gov-red) !important;">
                <h4 class="mb-4 text-center border-bottom pb-2"><?php echo __('submit'); ?></h4>
                <form action="submit_issue.php" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label fw-bold"><?php echo $lang == 'en' ? 'Full Name' : 'पूरा नाम'; ?> <small
                                class="text-muted">(Optional)</small></label>
                        <input type="text" class="form-control" name="name"
                            placeholder="<?php echo $lang == 'en' ? 'E.g., Ram Bahadur' : 'राम बहादुर'; ?>">
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold"><?php echo __('ward'); ?></label>
                            <select class="form-select border-primary" name="ward" required>
                                <option value="" selected disabled>
                                    <?php echo $lang == 'en' ? 'Select Ward' : 'वडा छान्नुहोस्'; ?></option>
                                <?php for ($i = 1; $i <= 11; $i++)
                                    echo "<option value='$i'>$i</option>"; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold"><?php echo __('category'); ?></label>
                            <select class="form-select border-primary" name="category" required>
                                <option value="" selected disabled>
                                    <?php echo $lang == 'en' ? 'Select Type' : 'वर्ग छान्नुहोस्'; ?></option>
                                <option value="Pothole">Pothole (सडकको खाल्डो)</option>
                                <option value="Street Light">Street Light (सडक बत्ती)</option>
                                <option value="Waste">Waste (फोहोर व्यवस्थापन)</option>
                                <option value="Other">Other (अन्य)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><?php echo __('description'); ?></label>
                        <textarea class="form-control" name="description" rows="3"
                            placeholder="<?php echo $lang == 'en' ? 'Provide details...' : 'विवरण प्रदान गर्नुहोस्...'; ?>"
                            required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold"><?php echo __('landmark'); ?></label>
                        <input type="text" class="form-control" name="landmark"
                            placeholder="<?php echo $lang == 'en' ? 'Near Temple, School etc.' : 'मन्दिर, विद्यालय नजिक आदि'; ?>"
                            required>
                    </div>

                    <div class="mb-4 p-3 bg-light rounded border text-dark">
                        <label class="form-label fw-bold d-block mb-3"><?php echo __('photo'); ?></label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-dark shadow-sm" id="openCameraBtn">📷
                                <?php echo $lang == 'en' ? 'Use Camera' : 'क्यामेरा प्रयोग गर्नुहोस्'; ?></button>
                            <input type="file" class="form-control" name="photo" id="photoFile" accept="image/*">
                        </div>
                        <div id="camera-container" class="mt-3 border p-1 bg-dark rounded shadow" style="display:none;">
                            <video id="video" autoplay playsinline style="width:100%; height:auto;"></video>
                            <button type="button" class="btn btn-danger btn-sm w-100 mt-2" id="captureBtn">Take Snapshot
                                / फोटो खिच्नुहोस्</button>
                        </div>
                        <img id="snapshot-preview" class="mt-3 rounded shadow" style="display:none; width:100%;">
                        <input type="hidden" id="cameraData" name="cameraData">
                    </div>

                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">

                    <button type="submit"
                        class="btn btn-primary btn-lg w-100 shadow mt-3"><?php echo __('submit_to_mun'); ?></button>
                </form>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-3 bg-white" style="border-top: 5px solid var(--gov-blue) !important;">
                <h5 class="card-title text-center text-primary mb-3"><?php echo __('pin_location'); ?></h5>
                <div class="text-center mb-3">
                    <button type="button" class="btn btn-outline-primary btn-sm" id="detectLocBtn">📍
                        <?php echo __('detect_loc'); ?></button>
                </div>
                <div id="map" style="height: 400px; border-radius: 8px;" class="border"></div>
                <div id="coords-text" class="mt-3 text-center badge bg-light text-dark border p-2 d-block">No location
                    picked / स्थान चुनिएको छैन</div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    var map = L.map('map').setView([27.766, 85.305], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    var marker;

    function updateMarkerAndInputs(lat, lng) {
        if (marker) marker.setLatLng([lat, lng]);
        else marker = L.marker([lat, lng]).addTo(map);
        map.setView([lat, lng], 16);
        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);
        document.getElementById('coords-text').innerText = "📍 Picked: " + lat.toFixed(4) + ", " + lng.toFixed(4);
    }

    map.on('click', function (e) {
        updateMarkerAndInputs(e.latlng.lat, e.latlng.lng);
    });

    document.getElementById('detectLocBtn').onclick = function () {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function (position) {
                updateMarkerAndInputs(position.coords.latitude, position.coords.longitude);
                alert("<?php echo __('loc_detected'); ?>");
            });
        }
    };
    const video = document.getElementById('video');
    const openBtn = document.getElementById('openCameraBtn');
    const container = document.getElementById('camera-container');
    const captureBtn = document.getElementById('captureBtn');
    const preview = document.getElementById('snapshot-preview');
    const input = document.getElementById('cameraData');
    openBtn.onclick = async () => {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        container.style.display = 'block';
    };
    captureBtn.onclick = () => {
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth; canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const data = canvas.toDataURL('image/jpeg');
        preview.src = data; preview.style.display = 'block';
        input.value = data;
        video.srcObject.getTracks().forEach(t => t.stop());
        container.style.display = 'none';
    };
</script>
<?php include 'footer.php'; ?>