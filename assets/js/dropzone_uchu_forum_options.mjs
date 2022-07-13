
// Not used - options are set directly in dropzone.js
export function uf_options() {

  var options;
  options = {
    maxFilesize: 30, // MB
    uploadMultiple: true,
    parallelUploads: 4,
    maxFiles: 4,
    acceptedFiles: "image/png,image/jpeg", 
    addRemoveLinks: true,
    dictDefaultMessage: 'Drag &amp; drop <i class="fa fa-cloud-upload fa-5x" aria-hidden="true"></i> or click here',
    dictInvalidFileType: "File type unsupported.",
    dictFileTooBig: "File too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.",
    dictMaxFilesExceeded: "File upload limit reached.",
    dictCancelUpload: "Cancel upload",
    dictCancelUploadConfirmation: "Cancel upload?"
  };

  return options;

}

