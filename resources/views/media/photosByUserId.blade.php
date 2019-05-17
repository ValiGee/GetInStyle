@extends('shared.layout')

@section('content')
    <!-- Display photos -->
    <div id="mainContainer" class="content">
        @if(count($media)) 
        @foreach($media as $_media)
        <div class="row">
            <div class="col-lg-4 col-md-3"></div>
            <div class="col-lg-4 col-md-6">
                <div class="thumbnail no-padding no-margin">
                    <div class="thumb">
                        <a href="{{ route('media.show', $_media->id) }}" onclick="fixAnchorTagClick(event)"  class="img-responsive display-inline-block">
                            <img src={{ URL::asset($_media["stylized_path"]) }}>
                        </a>
                        <!-- <div class="caption-overflow">
                        </div> -->
                    </div>
                    
                </div>
                <div class="panel-footer panel-footer-condensed">
                    <div class="heading-elements not-collapsible">
                        <ul class="list-inline list-inline-separate heading-text text-muted">
                            <li>
                                <a href="#" class="text-muted"><i @if($_media->liked)
                                                                  class="icon-heart5 text-size-base text-pink position-left like-button"
                                                                  @else
                                                                  class="icon-heart6 text-size-base text-pink position-left like-button"
                                                                  @endif
                                                                  onclick="updateLike(event)"
                                                                  data-media-id="{{ $_media->id }}"
                                                                  data-liked="{{ $_media->liked }}">{{ sizeof($_media->likes) }}</i></a>
                            </li>
                        </ul>
                        <a href="{{ route('media.show', $_media->id) }}" onclick="fixAnchorTagClick(event)" class="heading-text pull-right"><i class="icon-comments position-right"></i> {{ sizeof($_media->comments) }}</a>
                    </div>
                </div>
        </div>
        <div class="col-lg-4 col-md-3"></div>
        
    </div>
    <hr>
    @endforeach
    @else 
        <div id="loadingIcon"></div>
            <h3 align="center">No images uploaded yet.</h3>
        </div>
    @endif
    </div>
    <!-- /Display photos -->
@endsection

@push('css')

@push('css')
        
    <!-- This style defined the media grid and some adjustments -->
    <style type="text/css">
    #mainContainer > .panel:first-child { /* only the first child panel */
            background-color: inherit;
            border: none;
        }

        #mainContainer .caption-overflow > div {
            position: absolute;
            left: 0;
            top: 50%;
            margin-top: -17px;
            width: 100%;
        }
        #mainContainer .big-icon {
            font-size: 6rem;
        }

        #mainContainer .white-icon {
            color: #ffffff;
        }

        #mainContainer .pink-icon {
            color: #E91E63;
        }

        #mainContainer #mediaLikesCount {
            top: 62%; /* are deja position absolute din functionalitatea thumbnail */
            font-size: 2rem;
        }
        .thumbnail:hover{
            opacity: 0.8;
        }
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
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/ckeditor/ckeditor.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/prettyDate.js') }}"></script>

    <script type="text/javascript">
        window.onload = function(e) {
            userId = @php if(Auth::guest()) echo -1; else echo $userId; @endphp;
        };
        $(function(){
            //add like functionality
                //like/unlike for comments
                userId = @php if(Auth::guest()) echo -1; else echo $userId; @endphp;
                if (userId !== -1) {
                $('.text-size-base.text-pink.position-left').on('click', function(e) {
                    e.preventDefault();

                    // Change like in view
                    let liked = this.dataset.liked;
                    let likes_count = parseInt(this.innerText);
                    if(liked == 0) {
                        likes_count = likes_count + 1;
                    }
                    else {
                        likes_count = likes_count - 1;
                    }
                    this.innerText = ' ' + likes_count;
                    this.dataset.liked = 1 - liked;
                    this.classList.toggle('icon-heart5');
                    this.classList.toggle('icon-heart6');

                });

            $('.media-like').on('click', function(e) {
                    e.preventDefault();

                    // Change like in view
                    let liked = this.dataset.liked;
                    let likesCountContainer = document.getElementById('mediaLikesCount');
                    let likes_count = parseInt(likesCountContainer.innerText);
                    if(liked == 0) {
                        likes_count = likes_count + 1;
                    }
                    else {
                        likes_count = likes_count - 1;
                    }
                    likesCountContainer.innerText = likes_count;
                    this.dataset.liked = 1 - liked;
                    this.classList.toggle('white-icon');
                    this.classList.toggle('pink-icon');

                    // Make request
                    
                        let mediaId = $(_this).data('media-id');
                        let route = $('#info-media-id-' + mediaId).data('route');

                        let _data = {};

                        let csrf = $('meta[name="csrf-token"]').attr('content');

                        $.ajax({
                            type: "POST",
                            url: route,
                            headers: {
                                'X-CSRF-TOKEN': csrf
                            },
                            data: _data,
                            success: function(resp) {
                                console.log(resp);
                            },
                            error: function(err) {
                                console.log(err);
                            }
                        });
                    //}
                });

       
        }});

        function fixAnchorTagClick(e) { //Fara event listener nu se deschidea link-ul
            window.location.href = e.currentTarget.href;
        }

        function updateLike(e) {
            e.preventDefault();

            if(userId == -1)
                CKEDITOR.editorConfig = function( config ) {
                    //config.extraPlugins = 'emojione';
                };
                return;

            let _this = e.currentTarget;

            // Change like in viewF
            let liked = _this.dataset.liked;
            let likes_count = parseInt(_this.innerText);
            if(liked == 0) {
                likes_count = likes_count + 1;
            }
            else {
                likes_count = likes_count - 1;
            }
            _this.innerText = ' ' + likes_count;
            _this.dataset.liked = 1 - liked;
            _this.classList.toggle('icon-heart5');
            _this.classList.toggle('icon-heart6');


                CKEDITOR.replace('ck_placeholder', {
                    height: '200px',
                    removeButtons: 'Subscript,Superscript',
                    toolbarGroups: [
                        { name: 'styles' },
                        { name: 'links' },
                        { name: 'insert' },
                        { name: 'colors' },
                        { name: 'tools' },
                        { name: 'others' }
                    ]
                });

                
            // Make request
            let mediaId = $(_this).data('media-id');
            let route = $('#info-media-id-' + mediaId).data('route');

            let _data = {};

            let csrf = $('meta[name="csrf-token"]').attr('content');

            $.ajax({
                type: "POST",
                url: route,
                headers: {
                    'X-CSRF-TOKEN': csrf
                },
                data: _data,
                success: function(resp) {
                    console.log(resp);
                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
    </script>
@endpush
