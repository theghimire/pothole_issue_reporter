<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $category = $_POST['category'];
    $description = $_POST['description'];
    $ward = intval($_POST['ward']);
    $landmark = $_POST['landmark'];
    $latitude = !empty($_POST['latitude']) ? floatval($_POST['latitude']) : 0;
    $longitude = !empty($_POST['longitude']) ? floatval($_POST['longitude']) : 0;

    $photo_name = "";
    $target_dir = "uploads/";

    // Ensure directory exists
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    // 1. Handle regular file upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $temp_name = time() . "_" . basename($_FILES["photo"]["name"]);
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_dir . $temp_name)) {
            $photo_name = $temp_name;
        }
    }
    // 2. Handle camera data (base64)
    else if (!empty($_POST['cameraData'])) {
        $data = $_POST['cameraData'];
        // Fix potential data URL prefix issues
        if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
            $data = substr($data, strpos($data, ',') + 1);
            $img = base64_decode($data);
            if ($img !== false) {
                $temp_name = "cam_" . time() . ".jpg";
                if (file_put_contents($target_dir . $temp_name, $img)) {
                    $photo_name = $temp_name;
                }
            }
        }
    }

    // 3. Database Insert
    // Ensure all fields are escaped to prevent SQL injection (junior level)
    $name = mysqli_real_escape_string($conn, $name);
    $category = mysqli_real_escape_string($conn, $category);
    $description = mysqli_real_escape_string($conn, $description);
    $landmark = mysqli_real_escape_string($conn, $landmark);
    $photo_name = mysqli_real_escape_string($conn, $photo_name);

    $sql = "INSERT INTO issues (name, category, description, ward, landmark, photo, latitude, longitude, status) 
            VALUES ('$name', '$category', '$description', '$ward', '$landmark', '$photo_name', '$latitude', '$longitude', 'Pending')";

    if (mysqli_query($conn, $sql)) {
        $last_id = mysqli_insert_id($conn);
        header("Location: success.php?id=$last_id");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conn);
    }

    mysqli_close($conn);
} else {
    header("Location: report_issue.php");
}
?>