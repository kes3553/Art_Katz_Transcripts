
<?php
if (isset($_GET['file_path'])) {
    $file_path = $_GET['file_path'];
    
    // Security measure: Make sure the file is in a directory you expect. Do not let arbitrary file access.
    if (is_file($file_path) && file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');  // Force it as a download regardless of file type
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        
        readfile($file_path);  // Outputs the file content
        exit;  // End the script
    }
}

// Handle invalid file path or unauthorized access
header("HTTP/1.0 404 Not Found");
echo "File not found!";
?>

