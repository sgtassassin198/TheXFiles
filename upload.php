<?php
$targetDir = "weeb/"; // Directory where uploaded images will be stored

// Check if the directory exists, if not, create it
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0777, true);
}

// Array to store errors or success messages
$response = array();

if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_FILES["file"]["name"])) {
    $fileCount = count($_FILES["file"]["name"]); // Count uploaded files

    // Loop through each file and move it to the target directory
    for ($i = 0; $i < $fileCount; $i++) {
        $fileName = basename($_FILES["file"]["name"][$i]);
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $targetFilePath = $targetDir . $fileName;

        // Check if file already exists with the original name
        if (file_exists($targetFilePath)) {
            $response['error'] = "Sorry, a file with the same name already exists.";
        } else {
            // Check file size (10MB limit)
            if ($_FILES["file"]["size"][$i] > 10000000) {
                $response['error'] = "Sorry, your file is too large.";
            } else {
                // Allow certain file formats
                $allowedTypes = array("jpg", "jpeg", "png", "gif");
                if (in_array($fileExtension, $allowedTypes)) {
                    // Upload file
                    if (move_uploaded_file($_FILES["file"]["tmp_name"][$i], $targetFilePath)) {
                        // Get count of existing image files
                        $existingImages = glob($targetDir . "image*.jpg");
                        $imageCount = count($existingImages);

                        // Construct new file name
                        $newFileName = "image" . ($imageCount + 1);
                        if ($fileExtension != "jpg") {
                            $newFileName .= "." . $fileExtension;
                        } else {
                            $newFileName .= ".jpg";
                        }

                        // Rename the uploaded file
                        rename($targetFilePath, $targetDir . $newFileName);

                        // Update success message
                        $response['message'] = "The file " . $fileName . " has been uploaded successfully.";
                    } else {
                        $response['error'] = "Sorry, there was an error uploading your file.";
                    }
                } else {
                    $response['error'] = "Sorry, only JPG, JPEG, PNG, and GIF files are allowed.";
                }
            }
        }
    }
} else {
    $response['error'] = "Please select at least one file to upload.";
}

// Send response as JSON
header('Content-Type: application/json');
echo json_encode($response);
?>
