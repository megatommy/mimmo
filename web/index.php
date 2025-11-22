<?php
if(!extension_loaded('fileinfo')){
    echo "<h1>FILEINFO NOT LOADED</h1>";
}
if(!extension_loaded('imagick')){
    echo "<h1>IMAGICK NOT LOADED</h1>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload media</title>
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <style>
        .dz-message .dz-button {
            font-size: 25px !important;
        }
        .dz-success.dz-complete .dz-image {
            box-shadow: 0 0 0px 5px forestgreen;
        }
        .dz-error .dz-image {
            box-shadow: 0 0 0px 5px crimson;
        }
    </style>
</head>

<body>
    <form action="./upload.php" class="dropzone" id="my-great-dropzone"></form>
    <p>Cliccare qui su per caricare foto e video.<br>Ogni file può essere massimo 15mb. Se dopo il caricamento il bordo diventa verde, il file é caricato. Se diventa rosso c'é un problema. Vedere qui sotto nel log se tutto OK.</p>
    <h3>Log:</h3>
    <div id="log"></div>

    <script>
        let myDropzone = new Dropzone('#my-great-dropzone', { // camelized version of the `id`
            paramName: "file", // The name that will be used to transfer the file
            acceptedFiles: 'image/*,video/*',
            parallelUploads: 1,
            maxFilesize: 15
        });
        myDropzone.on("success", (file, response) => {
            console.log(file, response);
            document.getElementById('log').innerHTML += response + '<br>';
        });
    </script>
</body>

</html>