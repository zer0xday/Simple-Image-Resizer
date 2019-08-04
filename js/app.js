class App {
    constructor() {
        this.form = document.querySelector('form');
        this.uploadFileInput = document.querySelector('input#image-upload-hidden');
        this.buttonFileUpload = document.querySelector('button#upload-image');
        this.filenameLabel = document.querySelector('span#filename');
        this.submitButton = document.querySelector('button#submit-form');
        this.validationPass = false;
    }

    __autoDestructurizeConstructorProperties__(excluded = []) {
        for(let propName in this) {
            if(!excluded.includes(propName)) {
                window[propName] = this[propName];
            }
        }
    }

    customFileUploadEvent() {
        buttonFileUpload.addEventListener('click', () => uploadFileInput.click());
    }

    fileValidation() {
        uploadFileInput.addEventListener('change', () => { 
            if(this._validateFiles() === false) {
                filenameLabel.innerHTML = 'Your file is not allowed!';
                validationPass = false;
            } else if(this._validateFiles() === null) {
                filenameLabel.innerHTML = '';
                validationPass = false;
            } else {
                this._showFileName();
                validationPass = true;
            }
        });

        if(uploadFileInput.files.length) {
            this._showFileName();
            validationPass = true;
        }
    }

    _showFileName() {
        filenameLabel.innerHTML = uploadFileInput.files[0].name;
    }

    _validateFiles() {
        if(uploadFileInput.files.length) {
            const acceptedExtensions = uploadFileInput.accept.replace(/\s/g, '').split(',');
            let file = uploadFileInput.files[0];
            let fileTypes = file.type.split('/');
            fileTypes[1] = '.' + fileTypes[1];
    
            if(fileTypes[0] === 'image') {
                for(let extension of acceptedExtensions) {
                    if(fileTypes[1] === extension) {
                        return true;
                    }
                }
            }
            return false;
        }
        return null;
    }

    formValidation() {
        submitButton.addEventListener('click', (event) => {
            if(!uploadFileInput.files.length) {
                filenameLabel.innerHTML = "Select your image first!";
                buttonFileUpload.style.outline = '2px dashed red';
                event.preventDefault();
            }

            if(!validationPass) {
                event.preventDefault();
            }
        });
    }

    init() {
        this.__autoDestructurizeConstructorProperties__();
        
        this.customFileUploadEvent();
        this.fileValidation();
        this.formValidation();
    }
}

const APP = new App;
APP.init();