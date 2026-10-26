<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <!--<title>Laravel Bootstrap 5 Progress Bar Example - ItSolutionStuff.com</title>-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.1/dist/css/bootstrap.min.css" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <style>
     /* Absolute Center Spinner */
.loading {
  position: fixed;
  z-index: 999;
  height: 2em;
  width: 2em;
  overflow: visible;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

/* Transparent Overlay */
.loading:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.3);
}

/* :not(:required) hides these rules from IE9 and below */
.loading:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.loading:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 1em;
  height: 1em;
  margin-top: -0.5em;
  -webkit-animation: spinner 1500ms infinite linear;
  -moz-animation: spinner 1500ms infinite linear;
  -ms-animation: spinner 1500ms infinite linear;
  -o-animation: spinner 1500ms infinite linear;
  animation: spinner 1500ms infinite linear;
  border-radius: 0.5em;
  -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
  box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
</style>
    </head>
    <div class="loading">Loading&#8230;</div>
    <body>
        <div class="container mt-5" style="max-width: 900px">

            <div class="alert alert-primary mb-4 text-center">
                Folder Uploader
                <!--<h2 class="display-6">Laravel File Upload with Ajax Progress Bar Example - ItSolutionStuff.com</h2>-->
            </div>  
            <form id="fileUploadForm" method="POST" action="{{ route('file.upload') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group mb-3">
                    <input name="file[]" type="file" id="files" class="form-control" directory webkitdirectory mozdirectory multiple  acc>
                </div>
                <div class="form-group">
                    <div class="progress">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                    </div>
                </div>
<!--                <div class="d-grid mt-4">
                    <input type="submit" value="Submit" class="btn btn-primary">
                </div>-->
            </form>
        </div>
        <div class=loading style-2>
    content
</div>
        <br> 
        <br> 
         
<div class="container">
  <ul class="list-group files">
      
<!--    <li class="list-group-item">First item</li>
    <li class="list-group-item">Second item</li>
    <li class="list-group-item">Third item</li>-->
    
  </ul>
</div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>    <!--<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/4.3.0/jquery.form.min.js"></script>-->
        <script>
//        $(function () {
//            $(document).ready(function () {
//                $('#fileUploadFormd').ajaxForm({
//                    beforeSend: function () {
//                        var percentage = '0';
//                    },
//                    uploadProgress: function (event, position, total, percentComplete) {
//                        var percentage = percentComplete;
//                        $('.progress .progress-bar').css("width", percentage+'%', function() {
//                          return $(this).attr("aria-valuenow", percentage) + "%";
//                        })
//                    },
//                    complete: function (xhr) {
//                        console.log(xhr);
//                        console.log('File has uploaded');
//                    }
//                });
//            });
//        });


$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});
//document.getElementById('files').onchange = function(e) {
    $(".loading").hide();
$("#files").change(function (e) {
    var file_size=0;
    var files = e.target.files; 
    for (var i = 0; i < files.length; ++i) {
       file_size+=files[i].size;
    }
    
    file_sizes=file_size/1024;
//    filesize = (Math.round((file_sizes / 1024) * 100) / 100);
//    if(file_sizes>100){
//        return alert('File size is exceed!');
//    }
//    return  false;
    
    
    
    
    $(".loading").show();
    // FileList
    for (var i = 0; i < files.length; ++i) {
//        $(".loading").show();
        var relative_path = files[i].webkitRelativePath;
        let file_data = files[i];
        let form_data = new FormData();
        form_data.append('file', file_data);
        let path = relative_path.substring(0, Math.max(relative_path.lastIndexOf("/"), relative_path.lastIndexOf("\\")));
        form_data.append('folder', path);
        
        $.ajax({
            url: "<?php echo route('aws_file_upload') ?>",
            method: 'POST',
            data: form_data,
            dataType: 'JSON',
            contentType: false,
            cache: false,
            processData: false,
            beforeSend: function() {
              $(".loading").show();
           },
            success: function (response)
            {
//                $('#image').attr('src', response.image).show();

//                alert(response.message)
                 $(".loading").hide();
//                 $("#files").val("");
//                 alert(response);
//                 console.log(response);
                var lioption= '<li class="list-group-item"><a href="'+response+'" target="_blank">'+path+'/'+file_data['name']+'</a></li>';
//                console.log(response);
                $(".files").append(lioption);
                 
            },
            error: function (response) {
                console.log(response);
                $('.error').remove();
                //                            
            }
        });
//    $(".loading").css('display','none');
    }
    
//     $(".loading").hide();



});
//
        </script>
    </body>
</html>