<style>
    .progress {
        display: none;
        margin-bottom: 1rem;
        margin-top: 20px;
    }

    .pdt-image-alert {
        display: none;
        margin-top: 15px;
        padding: 12px;
    }

    .img-container img {
        max-width: 100%;

    }

    #product_image_preview {
        color: white;
        margin-top: 10px;
    }
    #pdt-image{
        display: none !important;
    }

</style>

<input type="file" hidden class="form-control" id="pdt-image" name="product_image" accept="image/*">

<a class="btn btn-success form-control"
   id="product_image_preview" {{ $edit ? "data-featherlight=".url($product->pdt_imgurl):"" }}>@lang('app.preview_product_image')</a>

<input hidden type="text" id="pdt_image_path" name="product_image_path" value="{{ $edit ? $product->pdt_imgurl:'' }}">

<div class="progress">
    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0"
         aria-valuemin="0" aria-valuemax="100">0%
    </div>
</div>

<div class="alert pdt-image-alert" role="alert"></div>

<div class="modal fade" id="modal" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLabel">Crop the image</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="img-container">
                    <img id="image" src="">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="crop">Crop</button>
            </div>
        </div>
    </div>
</div>

<script>
    var pdtImgUploadURL = '{{route("product.upload.image")}}';

    window.addEventListener('DOMContentLoaded', function () {
        var image = document.getElementById('image');
        var input = document.getElementById('pdt-image');
        var $progress = $('.progress');
        var $progressBar = $('.progress-bar');
        var $alert = $('.pdt-image-alert');
        var $modal = $('#modal');
        var cropper;

        $('#select-product-file').click(function () {
            input.value = "";
            input.click();
        });

        input.addEventListener('change', function (e) {

            var files = e.target.files;
            var done = function (url) {
                image.src = url;
                $alert.hide();
                $modal.modal('show');
            };
            var reader;
            var file;
            var url;

            if (files && files.length > 0) {
                file = files[0];

                if (URL) {
                    done(URL.createObjectURL(file));
                } else if (FileReader) {
                    reader = new FileReader();
                    reader.onload = function (e) {
                        done(reader.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        $modal.on('shown.bs.modal', function () {
            cropper = new Cropper(image, {
//                aspectRatio: 10/16,
                viewMode: 1,
            });
        }).on('hidden.bs.modal', function () {
            cropper.destroy();
            cropper = null;
        });

        document.getElementById('crop').addEventListener('click', function () {
            var canvas;

            $modal.modal('hide');

            if (cropper) {
                canvas = cropper.getCroppedCanvas();

                $progress.show();
                $alert.removeClass('alert-success alert-warning');
                canvas.toBlob(function (blob) {
                    var formData = new FormData();

                    formData.append('_token', tokenStr);
                    formData.append('pdt-image', blob);

                    $.ajax(pdtImgUploadURL, {
                        method: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,

                        xhr: function () {
                            var xhr = new XMLHttpRequest();

                            xhr.upload.onprogress = function (e) {
                                var percent = '0';
                                var percentage = '0%';

                                if (e.lengthComputable) {
                                    percent = Math.round((e.loaded / e.total) * 100);
                                    percentage = percent + '%';
                                    $progressBar.width(percentage).attr('aria-valuenow', percent).text(percentage);
                                }
                            };
                            return xhr;
                        },

                        success: function (res) {
                            $alert.show().addClass('alert-success').text('Upload success');
                            $alert.slideUp(1500);

                            $('#product_image_preview').attr('data-featherlight',siteURL+"/"+res);
                            $('#pdt_image_path').val(res);
                        },

                        error: function () {
                            $alert.show().addClass('alert-warning').text('Upload error');
                            $alert.slideUp(1800);
                        },

                        complete: function () {
                            $progress.hide();


                        },
                    });
                });
            }
        });
    });
</script>