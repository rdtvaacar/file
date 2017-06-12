<?php

namespace Acr\File\Controllers;


use Acr\File\Model\acr_files;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Acr\File\Model\File_model;
use Acr\File\Model\File_dosya_model;
use Auth;
use Acr\File\Controllers\MailController;

class AcrFileController extends Controller
{
    function create($acr_file_id = null, $parent_id = null)
    {
        $acr_file_model = new acr_files();
        return $acr_file_model->kaydet($acr_file_id, $parent_id);

    }

    function delete(Request $request)
    {
        $acr_file_model = new acr_files();
        $file           = $request->input('file');
        $acr_file_id    = $request->input('acr_file_id');

        $session_id = $acr_file_model->acr_file_session($acr_file_id);
        @unlink('/acr_files/' . $session_id . '/' . $file);
        @unlink('/acr_files/' . $session_id . '/thumbnail/' . $file);
        @unlink('/acr_files/' . $session_id . '/medium/' . $file);
    }

    function option($acr_file_id = null)
    {
        $options = [
            'acr_file_id'              => $acr_file_id,
            'upload_dir'               => '/acr_files/',
            'upload_url'               => '/acr_files/',
            'script_url'               => '/acr/file/upload/',
            // the redirect parameter, e.g. '/files/'.
            'download_via_php'         => false,
            // Read files in chunks to avoid memory limits when download_via_php
            // is enabled, set to 0 to disable chunked reading of files:
            'readfile_chunk_size'      => 10 * 1024 * 1024, // 10 MiB
            // Defines which files can be displayed inline when downloaded:
            'inline_file_types'        => '/\.(gif|jpe?g|png)$/i',
            // Defines which files (based on their names) are accepted for upload:
            'accept_file_types'        => '/.+$/i',
            // The php.ini settings upload_max_filesize and post_max_size
            // take precedence over the following max_file_size setting:
            'max_file_size'            => null,
            'min_file_size'            => 1,
            // The maximum number of files for the upload directory:
            'max_number_of_files'      => null,
            // Defines which files are handled as image files:
            'image_file_types'         => '/\.(gif|jpe?g|png)$/i',
            // Use exif_imagetype on all files to correct file extensions:
            'correct_image_extensions' => false,
            // Image resolution restrictions:
            'max_width'                => null,
            'max_height'               => null,
            'min_width'                => 1,
            'min_height'               => 1,
            // Set the following option to false to enable resumable uploads:
            'discard_aborted_uploads'  => true,
            // Set to 0 to use the GD library to scale and orient images,
            // set to 1 to use imagick (if installed, falls back to GD),
            // set to 2 to use the ImageMagick convert binary directly:
            'image_library'            => 1,
            // Uncomment the following to define an array of resource limits
            // for imagick:
            /*
            'imagick_resource_limits' => array(
                imagick::RESOURCETYPE_MAP => 32,
                imagick::RESOURCETYPE_MEMORY => 32
            ),
            */
            // Command or path for to the ImageMagick convert binary:
            'convert_bin'              => 'convert',
            // Uncomment the following to add parameters in front of each
            // ImageMagick convert call (the limit constraints seem only
            // to have an effect if put in front):
            /*
            'convert_params' => '-limit memory 32MiB -limit map 32MiB',
            */
            // Command or path for to the ImageMagick identify binary:
            'identify_bin'             => 'identify',
            'image_versions'           => array(
                // The empty image version key defines options for the original image:
                '' => array(
                    // Automatically rotate images based on EXIF meta data:
                    'auto_orient' => true
                ),
                // Uncomment the following to create medium sized images:

                'medium' => array(
                    'max_width'  => 1200,
                    'max_height' => 1200
                ),

                'thumbnail' => array(
                    // Uncomment the following to use a defined directory for the thumbnails
                    // instead of a subdirectory based on the version identifier.
                    // Make sure that this directory doesn't allow execution of files if you
                    // don't pose any restrictions on the type of uploaded files, e.g. by
                    // copying the .htaccess file from the files directory for Apache:
                    //'upload_dir' => dirname($this->get_server_var('SCRIPT_FILENAME')).'/thumb/',
                    //'upload_url' => $this->get_full_url().'/thumb/',
                    // Uncomment the following to force the max
                    // dimensions and e.g. create square thumbnails:
                    //'crop' => true,
                    'max_width'  => 180,
                    'max_height' => 180
                )
            ),
            'print_response'           => true
        ];
        return $options;

    }

    function index(Request $request)
    {
        $acr_file_id = $request->input('acr_file_id');
        new UploadHandler(self::option($acr_file_id));
    }

    function login(Request $request)
    {
        $file_model = new File_model();
        if ($request->server('SERVER_NAME') == 'file2') {
            Auth::loginUsingId(1, true);
            echo $file_model->uye_id();
        }
    }

    function kontrol(Request $request)
    {
        if (Auth::check()) {
            echo 'giriş yapıldı';
        } else {
            echo 'giriş yapılmadı';
        }
    }

    function logOut()
    {
        Auth::logOut();
    }

    function css()
    {
        return '
    <!-- blueimp Gallery styles -->
    <link rel="stylesheet" href="//blueimp.github.io/Gallery/css/blueimp-gallery.min.css">
    <!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
    <link rel="stylesheet" href="/plugins/jfup/css/jquery.fileupload.css">
    <link rel="stylesheet" href="/plugins/jfup/css/jquery.fileupload-ui.css">
    <!-- CSS adjustments for browsers with JavaScript disabled -->
    <noscript>
        <link rel="stylesheet" href="/plugins/jfup/css/jquery.fileupload-noscript.css">
    </noscript>
    <noscript>
        <link rel="stylesheet" href="/plugins/jfup/css/jquery.fileupload-ui-noscript.css">
    </noscript>';
    }

    function form()
    {
        return '<form id="fileupload" action="/acr/file/upload" method="POST" enctype="multipart/form-data">
    <!-- Redirect browsers with JavaScript disabled to the origin page -->
    <noscript><input type="hidden" name="redirect" value="https://blueimp.github.io/jQuery-File-Upload/"></noscript>
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-buttonbar">
        <div class="col-lg-7">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Dosyaları Seç</span>
                    <input type="file" name="files[]" multiple>
                </span>
            <button type="submit" class="btn btn-primary start">
                <i class="glyphicon glyphicon-upload"></i>
                <span>Başlat</span>
            </button>
            <button type="reset" class="btn btn-warning cancel">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span>İptal Et</span>
            </button>
            <button type="button" class="btn btn-danger delete">
                <i class="glyphicon glyphicon-trash"></i>
                <span>Sil</span>
            </button>
            <input type="checkbox" class="toggle">
            <!-- The global file processing state -->
            <span class="fileupload-process"></span>
        </div>
        <!-- The global progress state -->
        <div class="col-lg-5 fileupload-progress fade">
            <!-- The global progress bar -->
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
            <!-- The extended global progress state -->
            <div class="progress-extended">&nbsp;</div>
        </div>
    </div>
    <!-- The table listing the files available for upload/download -->
    <table role="presentation" class="table table-striped">
        <tbody class="files"></tbody>
    </table>
</form>

<!-- The blueimp Gallery widget -->
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls" data-filter=":even">
    <div class="slides"></div>
    <h3 class="title"></h3>
    <a class="prev">‹</a>
    <a class="next">›</a>
    <a class="close">×</a>
    <a class="play-pause"></a>
    <ol class="indicator"></ol>
</div>
<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
            <p class="name">{%=file.name%}</p>
            <strong class="error text-danger"></strong>
        </td>
        <td>
            <p class="size">Processing...</p>
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
        </td>
        <td>
            {% if (!i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start" disabled>
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Başlat</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>İptal</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
    <script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
        <tr class="template-download fade">
            <td>
                <span class="preview">
                    {% if (file.thumbnailUrl) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" data-gallery><img src="{%=file.thumbnailUrl%}"></a>
                    {% } %}
                </span>
            </td>
            <td>
                <p class="name">
                    {% if (file.url) { %}
                        <a href="{%=file.url%}" title="{%=file.name%}" download="{%=file.name%}" {%=file.thumbnailUrl?\'data-gallery\':\'\'%}>{%=file.name%}</a>
                    {% } else { %}
                        <span>{%=file.name%}</span>
                    {% } %}
                </p>
                {% if (file.error) { %}
                    <div><span class="label label-danger">Error</span> {%=file.error%}</div>
                {% } %}
            </td>
            <td>
                <span class="size">{%=o.formatFileSize(file.size)%}</span>
            </td>
            <td>
                {% if (file.deleteUrl) { %}
                    <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields=\'{"withCredentials":true}\'{% } %}>
                        <i class="glyphicon glyphicon-trash"></i>
                        <span>SİL</span>
                    </button>
                    <input type="checkbox" name="delete" value="1" class="toggle">
                {% } else { %}
                    <button class="btn btn-warning cancel">
                        <i class="glyphicon glyphicon-ban-circle"></i>
                        <span>İptal</span>
                    </button>
                {% } %}
            </td>
        </tr>
    {% } %}
    </script>';
    }

    function js($acr_file_id)
    {
        return '<script src="/plugins/jfup/js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="//blueimp.github.io/JavaScript-Templates/js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Load-Image/js/load-image.all.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<script src="//blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
<!-- blueimp Gallery script -->
<script src="//blueimp.github.io/Gallery/js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="/plugins/jfup/js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="/plugins/jfup/js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="/plugins/jfup/js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="/plugins/jfup/js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<script src="/plugins/jfup/js/jquery.fileupload-audio.js"></script>
<!-- The File Upload video preview plugin -->
<script src="/plugins/jfup/js/jquery.fileupload-video.js"></script>
<!-- The File Upload validation plugin -->
<script src="/plugins/jfup/js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="/plugins/jfup/js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="/plugins/jfup/js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
<script>
    $(function () {
        \'use strict\';

        // Initialize the jQuery File Upload widget:
        $(\'#fileupload\').fileupload({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url: \'/acr/file/upload?acr_file_id=' . $acr_file_id . '\'
        });

        // Enable iframe cross-domain access via redirect option:
        $(\'#fileupload\').fileupload(
            \'option\',
            \'redirect\',
            window.location.href.replace(
                /\/[^\/]*$/,
                \'/cors/result.html?%s\'
            )
        );
        // Load existing files:
        $(\'#fileupload\').addClass(\'fileupload-processing\');
        $.ajax({
            // Uncomment the following to send cross-domain cookies:
            //xhrFields: {withCredentials: true},
            url     : $(\'#fileupload\').fileupload(\'option\', \'url\'),
            dataType: \'json\',
            context : $(\'#fileupload\')[0]
        }).always(function () {
            $(this).removeClass(\'fileupload-processing\');
        }).done(function (result) {
            $(this).fileupload(\'option\', \'done\')
                .call(this, $.Event(\'done\'), {result: result});
        });


    });

</script>
';
    }
}