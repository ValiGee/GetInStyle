@extends('shared.layout')

@section('content')
    <div>
        <div id="loadingIcon"></div>
        <h3 align="center">Choose your style image</h3>
        <!-- The carouse wrapper -->
        <div class="kc-wrap" id="wrapper">
            <!-- Carousel items follow -->
            @foreach($styles as $style)
                <div class="kc-item">
                        <a href="#" onclick="return pickedStyle(event, {{ $style }})"><img src="{{ URL::asset($style["image_path"]) }}" alt="{{ $style["name"] }}"></a>
                </div>
            @endforeach
        </div>

        <div id="stages-container" class="row">
            <div id="first-stage-container" class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <div id="chosen-style-container">
                        <div class="thumbnail no-padding">
                            <div class="thumb">
                                <img id="chosen-style-image" src="" alt="">
                                <div class="caption-overflow">
                        <span>
                            <h5 id="chosen-style-name"></h5>
                        </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4"></div>
            </div>
            <div class="row">
                <div class="col-md-12 center-container">
                    <button class="btn btn-info">+</button>
                </div>
            </div>
            <form id="main-form" action="{{ route('media.store') }}">
                @csrf
                <div id="second-stage-container" class="row">
                    <div class="col-md-12">
                        <!-- Single file upload -->
                        <div class="file-upload">
                            <div class="image-upload-wrap">
                                <input type="hidden" name="style_id" id="styleIdInput" value="" />
                                <input name="userPhoto" class="file-upload-input" type='file' onchange="readURL(this);" accept="image/*" />
                                <div class="drag-text">
                                    <h3>Drag and drop a file or select add Image</h3>
                                </div>
                            </div>
                            <div class="file-upload-content">
                                <img class="file-upload-image" src="#" alt="your image" />
                                <div class="image-title-wrap">
                                    <button type="button" onclick="removeUpload()" class="remove-image">Remove <span class="image-title">Uploaded Image</span></button>
                                </div>
                            </div>
                        </div>
                        <!-- /single file upload -->
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 center-container">
                        <button id="submitFormButton" class="btn btn-info">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('css')
    <link href="{{ URL::asset('killercarousel/killercarousel.css') }}" type="text/css" rel="stylesheet" />

    <link href="{{ URL::asset('limitless/assets/css/icons/fontawesome/styles.min.css') }}" rel="stylesheet" type="text/css" />

    <style type = "text/css">

        /* CSS for images inside item wrapper */
        .kc-item img {
            position:absolute;
            width:100%;             /* Make images expand to wrapper size (used in 2d modes). */
        }

        #stages-container {
            display: none; /* Will be overridden in js */
        }

        .center-container {
            position: relative;
        }

        .center-container button {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        #second-stage-container {
            margin-left: 20px;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .file-upload {
            background-color: #ffffff;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }

        .file-upload-content {
            display: none;
            text-align: center;
        }

        .file-upload-input {
            position: absolute;
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
            outline: none;
            opacity: 0;
            cursor: pointer;
        }

        .image-upload-wrap {
            margin-top: 20px;
            border: 4px dashed #1FB264;
            position: relative;
        }

        .image-dropping,
        .image-upload-wrap:hover {
            background-color: #1FB264;
            border: 4px dashed #ffffff;
        }

        .image-title-wrap {
            padding: 0 15px 15px 15px;
            color: #222;
        }

        .drag-text {
            text-align: center;
        }

        .drag-text h3 {
            font-weight: 100;
            text-transform: uppercase;
            color: #15824B;
            padding: 60px 0;
        }

        .file-upload-image {
            max-height: 600px;
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }

        .remove-image {
            width: 200px;
            margin: 0;
            color: #fff;
            background: #cd4535;
            border: none;
            padding: 10px;
            border-radius: 4px;
            border-bottom: 4px solid #b02818;
            transition: all .2s ease;
            outline: none;
            text-transform: uppercase;
            font-weight: 700;
        }

        .remove-image:hover {
            background: #c13b2a;
            color: #ffffff;
            transition: all .2s ease;
            cursor: pointer;
        }

        .remove-image:active {
            border: 0;
            transition: all .2s ease;
        }
    </style>

    <style>
        /* Absolute Center Spinner */
        .loading {
            position: fixed;
            z-index: 999;
            height: 2em;
            width: 2em;
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
@endpush

@push('js')
    <script type="text/javascript" src="{{ URL::asset('killercarousel/killercarousel.js') }}"></script>

    <script type = "text/javascript">
        // Create the carousel.
        $(function() {
            $('.kc-wrap').KillerCarousel({
                // Default natural width of carousel.
                width: 800,
                // Item spacing in 3d (has CSS3 3d) mode.
                spacing3d: 120,
                // Item spacing in 2d (no CSS3 3d) mode.
                spacing2d: 120,
                showShadow: false,
                showReflection: true,
                // Looping mode.
                infiniteLoop: true,
                // Scale at 85% of parent element.
                autoScale: 85
            });

            $('#main-form').submit(function( event ) {
                let form = $(this);
                event.preventDefault();

                let formData = new FormData();
                let formParams = form.serializeArray();

                //First we include the files, which we cannot serialize
                $.each(form.find('input[type="file"]'), function(i, tag) {
                    $.each($(tag)[0].files, function(i, file) {
                        formData.append(tag.name, file);

                    });
                });

                //Then we include everything else that can be serialized
                $.each(formParams, function(i, val) {
                    formData.append(val.name, val.value);
                });

                let URL = $(this).attr('action');
                $('#loadingIcon').toggleClass('loading'); //activate the loading screen

                $.ajax({
                    url: URL,
                    type: 'post',
                    data: formData, // Remember that you need to have your csrf token included
                    processData: false,
                    contentType: false,
                    success:function(response){
                        console.log(response);
                        $('#loadingIcon').toggleClass('loading'); //deactivate the loading screen
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("Status: " + textStatus); alert("Error: " + errorThrown);
                        $('#loadingIcon').toggleClass('loading'); //deactivate the loading screen
                    }
                });
            });
        });

        function pickedStyle(e, style) {
            //1. Display the image of the chosen style ( only important the first time, since hidden implicitly )
            $('#stages-container').css('display', 'block');

            //2. Scroll to the photo upload stage for nice effects
            $('html, body').animate({
                scrollTop: $('#second-stage-container').offset().top
            }, 800);

            //3. Set the value of the style id in the hidden input
            $('#styleIdInput').val(style.id);

            //4. Add the name of the chosen style to be visible when hovering over the image
            $('#chosen-style-name').text(style.name);

            //5. Add the image of the chosen style
            var app_base_path = "{{ URL::to('/') }}";
            $('#chosen-style-image').attr('src', app_base_path + '/' + style.image_path);

            return false; //using return here and in html in order to not change the url
        }

        function readURL(input) {
            if (input.files && input.files[0]) {

                var reader = new FileReader();

                reader.onload = function(e) {
                    $('.image-upload-wrap').hide();

                    $('.file-upload-image').attr('src', e.target.result);
                    $('.file-upload-content').show();

                    $('html, body').animate({
                        scrollTop: $('#submitFormButton').offset().top
                    }, 800);

                    $('.image-title').html(input.files[0].name);
                };

                reader.readAsDataURL(input.files[0]);

            } else {
                removeUpload();
            }
        }

        function removeUpload() {
            $('.file-upload-input').replaceWith($('.file-upload-input').clone());
            $('.file-upload-content').hide();
            $('.image-upload-wrap').show();
        }
        $('.image-upload-wrap').bind('dragover', function () {
            $('.image-upload-wrap').addClass('image-dropping');
        });
        $('.image-upload-wrap').bind('dragleave', function () {
            $('.image-upload-wrap').removeClass('image-dropping');
        });

        $(document).ready(function() {
            // Select the node that will be observed for mutations
            var targetNode = document.getElementsByClassName('kc-wrap')[0]; // avem doar unul in pagina

            // Options for the observer (which mutations to observe)
            var config = { attributes: true, childList: true, subtree: true };

            // Callback function to execute when mutations are observed
            var callback = function(mutationsList, observer) {
                for(var mutation of mutationsList) {
                    if (mutation.type == 'childList') {
                        if (targetNode.childElementCount == 2) {
                            console.log('Removing red banner, since the childElementCount is 2');
                            targetNode.removeChild(targetNode.lastChild);

                            //stop observing, we're done
                            observer.disconnect();
                        }
                    }
                }
            };

            // Create an observer instance linked to the callback function
            var observer = new MutationObserver(callback);

            // Start observing the target node for configured mutations
            observer.observe(targetNode, config);
        })
    </script>
@endpush
