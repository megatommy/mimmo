<?php

$upload_dir = "/path/to/the/upload/dir";
$image_max_width = 1920;
$image_max_height = 1080;
$max_filesize = 15000000; // 15mb

// ----------------------
header('Content-Type: text/plain; charset=utf-8');

try {
    // Undefined | Multiple Files | $_FILES Corruption Attack
    // If this request falls under any of them, treat it invalid.
    if (
        !isset($_FILES['file']['error']) ||
        is_array($_FILES['file']['error'])
    ) {
        throw new RuntimeException('❌ Invalid parameters.');
    }

    // Check $_FILES['file']['error'] value.
    switch ($_FILES['file']['error']) {
        case UPLOAD_ERR_OK:
            break;
        case UPLOAD_ERR_NO_FILE:
            throw new RuntimeException('❌ No file sent.');
        case UPLOAD_ERR_INI_SIZE:
        case UPLOAD_ERR_FORM_SIZE:
            throw new RuntimeException('❌ Exceeded filesize limit.');
        default:
            throw new RuntimeException('❌ Unknown errors.');
    }

    // Checking max filesize
    $filesize = $_FILES['file']['size'];
    if ($filesize > $max_filesize) { 
        throw new RuntimeException('❌ Exceeded filesize limit.');
    }

    // DO NOT TRUST $_FILES['file']['mime'] VALUE !!
    // Check MIME Type by yourself.
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    if (false === $ext = array_search(
        $finfo->file($_FILES['file']['tmp_name']),
        array(
            'jpg' => 'image/jpeg',
            'mp4' => 'video/mp4',
        ),
        true
    )) {
        throw new RuntimeException('❌ Invalid file format.');
    }

    /**
     * I use a prefix that is based on the amount of seconds REMAINING to 2021-01-01.
     * This way the files when sorted in alfabetical order, the last ones uploaded will be the first
     * When starting the slideshow, this is what happens.
     */
    $timestamp_future = 4102444800; // 2100-01-01 00:00:00
    $time = time();
    $prefix = $timestamp_future - $time;
    $prefix = str_pad($prefix, 10, "0", STR_PAD_LEFT);
    $filename = sprintf("%s-%s.%s", $prefix, sha1_file($_FILES['file']['tmp_name']), $ext);
    if (!move_uploaded_file($_FILES['file']['tmp_name'], "$upload_dir/$filename")) {
        throw new RuntimeException('❌ Failed to move uploaded file.');
    }
    $filepath = realpath("$upload_dir/$filename");

    // file is a big image (> 1mb)
    if($ext == 'jpg' && $filesize > 1000000){
        $image = new \Imagick($filepath);

        $width = $image->getImageWidth();
        $height = $image->getImageHeight();

        // Only resize if the image is larger than the target dimensions
        if ($width > $image_max_width || $height > $image_max_height) {
            // Resize the image
            $image->resizeImage($image_max_width, $image_max_height, \Imagick::FILTER_LANCZOS, 1, true);
            
            $filesize_after_resize = strlen( $image->getImageBlob() );
            
            //if image is still too big
            if($filesize_after_resize > 500000){
                $image->setImageCompressionQuality(80);
            }

            $image->writeImage($filepath);
        }
    }



    echo '✅ File is uploaded successfully.';

} catch (RuntimeException $e) {

    echo $e->getMessage();

}
