<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/style.css">
    <title>Simple Image Resizer by Majcherczyk Pawel</title>
</head>
<body>
    <section id="image-form">
        <h1 id='page-title'>Simple Image Resizer</h1>
        <div class="form-wrapper">
        <form action="app/App.php" method="post" enctype="multipart/form-data">
            <div class="form-control form-file">
                <button type="button" id='upload-image'>Upload image</button>
                <span class='info'>Allowed files: .jpg, .jpeg, .png</span>
                <span class='info'>Max file size: <?= ini_get('upload_max_filesize') ?>b</span>
                <span id='filename'></span>
                <input type="file" name="image" id="image-upload-hidden" class='file-hidden' accept=".jpg, .jpeg, .png">
            </div>
            <div class="form-control form-wrapper">
                <div class="form-input">
                    <label for="width">Width <span>[px]</span></label>
                    <input type="number" name="width" id="width" min='1' step='1' required>
                </div>
                <div class="form-input">
                    <label for="height">Height <span>[px]</span></label>
                    <input type="number" name="height" id="height" min='1' step='1' required>
                </div>
            </div>
            <div class="form-control">
                <button id='submit-form'>Resize</button>
            </div>
        </form>
    </div>
    <?php if(isset($_GET['success'])): ?>
        <div class="results">
            <span class="log <?= $_GET['success'] ? 'success' : 'error' ?>"><?= $_GET['message'] ?></span>
            <img class="image-container">
                <img src="images/<?= $_GET['imgName'] ?>">
            </div>
        </div>
    <?php endif; ?>
    </section>
</body>
<script src='js/app.js'></script>
</html>