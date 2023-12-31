<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "images";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
function resizeImage($originalImage, $newWidth, $newHeight) {
    $width = imagesx($originalImage);
    $height = imagesy($originalImage);

    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($resizedImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    return $resizedImage;
}

if (isset($_POST['upload'])) {
    $file = $_FILES['file'];

    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];
    $fileSize = $file['size'];
    $fileError = $file['error'];
    $fileType = $file['type'];
    
    $allowedExtensions = ['jpg', 'jpeg', 'png'];
    
    $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

    if ($fileError === 0 && in_array($ext, $allowedExtensions)) {
        // Hedef dizin
        $uploadDirectory = '/Applications/XAMPP/xamppfiles/temp/';
        $fileDestination = $uploadDirectory . $fileName;
        list($width, $height) = getimagesize($fileTmpName);

        $newWidth = $width / 2;
        $newHeight = $height / 2;

        $originalImage = $ext == 'jpg' || $ext == 'jpeg' ? imagecreatefromjpeg($fileTmpName) : imagecreatefrompng($fileTmpName);
        $resizedImage = resizeImage($originalImage, $newWidth, $newHeight);

        // Save the resized image to the destination
        if ($ext == 'jpg' || $ext == 'jpeg' || $ext == 'png') {
            $ext == 'png' ? imagepng($resizedImage, $fileDestination) : imagejpeg($resizedImage, $fileDestination);
        }

        imagedestroy($originalImage);
        imagedestroy($resizedImage);
        $imageContent = file_get_contents($fileDestination);

        $stmt = $conn->prepare("INSERT INTO uploaded_images (file_name, file_path) VALUES (?, ?)");
        $stmt->bind_param("ss", $fileName, $imageContent);
        $stmt->execute();
        $stmt->close();

        // Redirect to prevent form resubmission
        header("Location: {$_SERVER['PHP_SELF']}");
        exit();
    } else {
        echo "Hata oluştu: $fileError";
    }
}

?>

<!DOCTYPE html>
<html lang="tr">

<head>
    <!-- ... (your head content) ... -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-size: 18px;
            margin-right: 10px;
        }

        input[type="file"] {
            margin-bottom: 10px;
        }

        button {
            background-color: #4caf50;
            color: white;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }

        .image-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .uploaded-image {
            margin: 10px;
            width: 100px; /* Set your preferred width */
            height: 200px; /* Set your preferred height */
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
        }

        .uploaded-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
    <h1>2024 Manifest</h1>
</head>

<body>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="file">Resim Seç:</label>
        <input type="file" name="file" id="file">
        <button type="submit" name="upload" onclick="displayMessage()">Resmi Yükle</button>
    </form>
    <?php
    // Display all uploaded images
    $sql = "SELECT file_path FROM uploaded_images";
    $result = $conn->query($sql);
    ?>

    <li>
        <div class="image-container">
            <?php
            /*
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $imageData = base64_encode($row["file_path"]); 
                    echo "<div class='uploaded-image'><img src='data:image/jpg;base64, $imageData' alt='Uploaded Image'></div>";
                }
            } else {
                echo "<p>Veritabanında yüklenmiş resim bulunamadı.</p>";
            }
            */
            ?>
        </div>
    </li>
<script>
    function displayMessage() {
    alert("Umarım birsürü güzel anılarını gerçekleştirdiğin bir yıl olur. Seviliyorsunuz!");
}

</script>
<?php

//A: ŞU ANKİ TARIHI VE SAATI KAYDEDIN
$today = time();
$event = mktime(0, 0, 0, 12, 31, 2024);
$countdown_seconds = $event - $today;

// Gün, saat ve dakika hesaplamaları
$days = floor($countdown_seconds / (60 * 60 * 24));
$hours = floor(($countdown_seconds % (60 * 60 * 24)) / (60 * 60));
$minutes = floor(($countdown_seconds % (60 * 60)) / 60);

echo "$days gün, $hours saat, $minutes --- resimlerin açılmasına bu kadar zaman kaldı ";

?>

</body>

</html>

<?php
// Close the connection
$conn->close();
?>
