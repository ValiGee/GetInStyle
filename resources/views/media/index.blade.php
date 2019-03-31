@extends('shared.layout')

@section('content')
    <!-- Display photos -->
    <div id="photos-container">
        @foreach($media as $_media)
            <div class="panel panel-flat animation" data-animation="bounceInLeft" style="visibility: visible;">
                <div class="panel-body">
                    <a href="{{ route('media.show', ['media_id' => $_media["id"]]) }}" onclick="fixAnchorTagClick(event)"><img src={{ URL::asset($_media["stylized_path"]) }}></a>
                </div>
                <div class="panel-footer panel-footer-condensed">
                    <div class="heading-elements not-collapsible">
                        <ul class="list-inline list-inline-separate heading-text text-muted">
                            <li>
                                <a href="#" class="text-muted"><i class="icon-heart6 text-size-base text-pink position-left"></i>14</a>
                            </li>
                        </ul>
                        <a href="#" class="heading-text pull-right"><i class="icon-comments position-right"></i> 5</a>
                    </div>
                </div>
            </div>
        @endforeach
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
        -webkit-column-gap:   5px;
        -moz-column-count:    5;
        -moz-column-gap:      5px;
        column-count:         5;
        column-gap:           5px;
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
        /* Following 2 lines make panel as a whole unit, so they don't split on separate columns */
        display: inline-block;
        width: 100%;
        /* Override property from bootstrap for visual aspect of panels stacked vertically . Default is 20px */
        margin-bottom: 5px;
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
        function fixAnchorTagClick(e) { //Fara event listener nu se deschidea link-ul
            window.location.href = e.currentTarget.href;
        }

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

        }
    </script>
@endpush
