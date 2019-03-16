@extends('shared.layout')

@section('content')
    <!-- Display photos -->
    <b>Pozele sunt dummy data generate cu JS, de aia se incarca mai greu</b>
    <div id="photos-container">

    </div>
    <!-- /Display photos -->
@endsection

@push('css')
    <!-- This style defined the media grid and some adjustments -->
    <style type="text/css">
    #photos-container {
        /* Center horizontally */
        width: 80%;
        margin: 0 auto;

        /* Prevent vertical gaps */
        line-height: 0;

        -webkit-column-count: 5;
        -webkit-column-gap:   0;
        -moz-column-count:    5;
        -moz-column-gap:      0;
        column-count:         5;
        column-gap:           0;
    }

    #photos-container img {
        /* Just in case there are inline attributes */
        width: 100% !important;
        height: auto !important;
    }

    @media (max-width: 1200px) {
        #photos-container {
            -moz-column-count:    4;
            -webkit-column-count: 4;
            column-count:         4;
        }
    }

    @media (max-width: 1000px) {
        #photos-container {
            -moz-column-count:    3;
            -webkit-column-count: 3;
            column-count:         3;
        }
    }

    @media (max-width: 800px) {
        #photos-container {
            -moz-column-count:    2;
            -webkit-column-count: 2;
            column-count:         2;
        }
    }

    @media (max-width: 400px) {
        #photos-container {
            -moz-column-count:    1;
            -webkit-column-count: 1;
            column-count:         1;
            }
    }


    #photos-container .panel {
        margin-bottom: 5px;
        margin-right: 5px;

        display: inline-block;
        width: 100%;
    }

    #photos-container .panel:hover {
        opacity: 0.5
    }

    #photos-container .panel .panel-body {
        padding: 0;
    }


    /* TODO: test without this */
    body {
        margin: 0;
        padding: 0;
    }
    </style>

    <!-- The following link + style are for the animations -->
    <link href="{{ URL::asset('limitless/assets/css/extras/animate.min.css') }}" rel="stylesheet" type="text/css">
    <style type="text/css">
        #photos-container .panel.animation {
            visibility: hidden; /* set to hidden until all is loaded, so we have smooth animation */
        }
    </style>
@endpush

@push('js')
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/pages/animations_css3.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

    <script type="text/javascript">
        function getRandomSize(min, max) {
            return Math.round(Math.random() * (max - min) + min);
        }

        function imgLoaded(e) {
            //make visible
            let img = e; //am trimis imaginea ca parametru
            if (e.target !== undefined) //s-a declansat evenimentul
                img = e.target;

            let divPanel = img.parentNode.parentNode;
            divPanel.style.visibility = "visible";

            let animationData = divPanel.dataset.animation;
            //apply animation
            $(divPanel).addClass("animated " + animationData).one("webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend", function () {
                $(this).removeClass("animated " + animationData);
            });
        }

        window.onload = function(e) {
            let photosContainer = $('#photos-container');
            let animations = ["fadeInDownBig", "bounceInRight", "bounceInLeft", "lightSpeedIn", "rotateIn"];

            for (let i = 0; i < 25; i++) {
                let width = getRandomSize(200, 400);
                let height =  getRandomSize(200, 400);

                let divPanel = document.createElement("DIV");
                divPanel.classList.add("panel");
                divPanel.classList.add("panel-flat");
                divPanel.classList.add("animation");
                divPanel.dataset.animation = animations[Math.floor(Math.random()*animations.length)];

                let divPanelBody = document.createElement("DIV");
                divPanelBody.classList.add("panel-body");

                divPanel.appendChild(divPanelBody);

                let divPanelFooter = document.createElement("DIV");
                divPanelFooter.classList.add("panel-footer");
                divPanelFooter.classList.add("panel-footer-condensed");

                divPanel.appendChild(divPanelFooter);

                let img = document.createElement("IMG");
                img.src = "https://placekitten.com/" + width + "/" + height;

                divPanelBody.appendChild(img);

                divPanelFooter.innerHTML = '<div class="heading-elements not-collapsible">\n' +
                    '<ul class="list-inline list-inline-separate heading-text text-muted">\n' +
                    '<li><a href="#" class="text-muted"><i class="icon-heart6 text-size-base text-pink position-left"></i>' +
                    getRandomSize(0, 100) +
                    '</a></li>\n' +
                    '</ul>\n' +
                    '<a href="#" class="heading-text pull-right"><i class="icon-comments position-right"></i> ' +
                    getRandomSize(0, 100) +
                    '</a>\n' +
                    '</div>';

                photosContainer.append(divPanel);

                if (img.complete) {
                    imgLoaded(img)
                } else {
                    img.addEventListener('load', imgLoaded)
                }
            }
        }
    </script>
@endpush
