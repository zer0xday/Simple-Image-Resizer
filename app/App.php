<?php
require_once 'EventLogger.php';
use EventLogger as Logger;

class App {
    protected $formData;
    protected $imageData;
    protected $logger;
    protected $outputMessage;
    protected $allowedExtensions = [
        'jpg', 'jpeg', 'png'
    ];
    protected $startTime;
    protected $executionDate;

    const IMAGE_DESTINATION_DIR = '/images/';

    public function __construct($formData, $imageData) {
        $this->formData = $formData;
        $this->imageData = $imageData;
        $this->logger = new Logger;
        $this->startTime = microtime(true);
        $this->executionDate = new DateTime();
    }

    protected function validateErrors() {
        $validated = false;
        $img = $this->imageData;

        switch ($img['error']) {
            case UPLOAD_ERR_OK:
                $validated = true;
                $this->outputMessage = 'Image has been uploaded.';
                break;

            case UPLOAD_ERR_NO_FILE:
                $this->outputMessage = 'No image sent';
                break;

            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $this->outputMessage = 'Exceeded filesize limit.';
                break;

            default:
                $this->outputMessage = 'Unknown errors';
                break;
        }
        $this->logger->log($this->outputMessage);
        return $validated;
    }

    protected function logCommonImageData() {
        $img = $this->imageData;
        $this->logger->log('Image original name: ' . $img['name']);
        $this->logger->log('Image size: ' . $img['size'] . ' bytes');
    }

    protected function validateExtension() {
        $img = $this->imageData;
        $imgExtension = pathinfo($img['name'], PATHINFO_EXTENSION);

        foreach($this->allowedExtensions as $extension) {
            if($imgExtension === $extension) {
                $this->outputMessage = 'Image extension [.' . $extension . '] => OK';
                $this->logger->log($this->outputMessage);
                return true;
            }
        }

        $this->outputMessage = 'Not allowed image extension';
        $this->logger->log($this->outputMessage);
        return false;
    }

    protected function scaleImage() {
        $target = [
            'width' => $this->formData['width'],
            'height' => $this->formData['height'],
            'name' => md5(microtime(true))
        ];
        $img = $this->imageData;
        
        list($imgWidth, $imgHeight) = getimagesize($img['tmp_name']);
        if($imgWidth && $imgHeight) {
            $this->logger->log('Image original width: ' . $imgWidth . 'px & height: ' . $imgHeight . 'px');
        } else {
            $this->logger->log('Image dimensions could not be get');
        }

        $extension = pathinfo($img['name'], PATHINFO_EXTENSION);
        $imageNewName = $target['name'] . '.' . $extension;
        $imageDestinationDir = $_SERVER['DOCUMENT_ROOT'] . self::IMAGE_DESTINATION_DIR . $imageNewName;

        switch($extension) {
            case 'jpeg':
            case 'jpg':
                $imageResource = imagecreatefromjpeg($img['tmp_name']); 
                $targetResource = $this->resizeImage($imageResource, $imgWidth, $imgHeight, $target['width'], $target['height']);
                imagejpeg($targetResource, $imageDestinationDir, 100);
                break;

            case 'png':
                $imageResource = imagecreatefrompng($img['tmp_name']); 
                $targetResource = $this->resizeImage($imageResource, $imgWidth, $imgHeight, $target['width'], $target['height']);
                imagepng($targetResource, $imageDestinationDir);
                break;

            default: 
                $this->outputMessage = 'Image could not be scaled - inappropriate file type';
                $this->logger->log($this->outputMessage);
                return false;
                break;
        }

        $this->outputMessage = 
            'Image created:'.
                ' [name : ' . $target['name'] . '] &'.
                ' [width : ' . $target['width'] . '] &'.
                ' [height : '. $target['height'] .'] &'.
                ' [type : ' . $extension . ']';

        $this->logger->log($this->outputMessage);

        if(move_uploaded_file($imageResource, $imageDestinationDir)) {
            $this->logger->log('New image saved to: ' . $imageDestinationDir);
        };

        return $imageNewName;
    }

    protected function resizeImage($imageResource, $srcWidth, $srcHeight, $targetWidth, $targetHeight) {
        $layer = imagecreatetruecolor($targetWidth, $targetHeight);
        imagecopyresampled($layer, $imageResource, 0,0,0,0, $targetWidth, $targetHeight, $srcWidth, $srcHeight);
        return $layer;
    }

    protected function durationTime() {
        $endTime = microtime(true);
        $executionTime = $endTime - $this->startTime;
        $this->logger->log('Execution time:' . $executionTime);

        return $executionTime;
    }

    public function execute() {
        $this->logger->log('--------');
        $this->logger->log('Executed at: ' . $this->executionDate->format('d-m-Y'));
        $this->logger->log('Verifying image...');

        if(!$this->validateErrors()) {
            return [
                'success' => false,
                'message' => $this->outputMessage
            ];
        }

        if(!$this->validateExtension()) {
            return [
                'success' => false,
                'message' => $this->outputMessage
            ];
        }

        $this->logCommonImageData();

        $newImageName = $this->scaleImage();
        if(!$newImageName) {
            return [
                'success' => false,
                'message' => $this->outputMessage
            ];
        };

        $this->outputMessage = 'Image resized';
        $this->logger->log('Image has been successfully resized');
        $this->durationTime();
        $this->logger->log('--------');

        return [
            'success' => true,
            'imgName' => $newImageName,
            'message' => $this->outputMessage
        ];
    }
}
$app = new App($_POST, $_FILES['image']);
$result = $app->execute();

header('Location: /index.php?' . http_build_query($result));
die();
