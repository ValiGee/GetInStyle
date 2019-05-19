@extends('shared.layout')

@section('content')
    <!-- Display photos -->
    <div id="sort-container">
        <form id="sort-form" action="{{ route('media.search') }}" method="GET">
            @csrf
            <input id="searchInputHidden" type="hidden" name="tags[]">
            <label for="sortColumn">Sort by</label>
            <select name="sortColumn" id="sort-column-select">
                @foreach($sortColumn as $display => $val)
                    @if($val == $sortByColumn)
                        <option value="{{ $val }}" selected>{{ $display }}</option>
                    @else
                        <option value="{{ $val }}">{{ $display }}</option>
                    @endif
                @endforeach
            </select>
            <label for="sortOrder">Order</label>
            <select name="sortOrder" id="sort-order-select">
                @foreach($sortOrder as $display => $val)
                    @if($val == $sortByOrder)
                        <option value="{{ $val }}" selected>{{ $display }}</option>
                    @else
                        <option value="{{ $val }}">{{ $display }}</option>
                    @endif
                @endforeach
            </select>
        </form>
    </div>
    <div id="photos-container" class="infinite-scroll">
        @foreach($media as $_media)
            <div class="panel panel-flat">
                <div class="panel-body">
                    <a href="{{ route('media.show', $_media->id) }}" onclick="fixAnchorTagClick(event)"><img src={{ URL::asset($_media["stylized_path"]) }}></a>
                </div>
                <div id="info-media-id-{{ $_media->id }}"
                     data-route="{{ route('media.like', $_media->id) }}"></div>
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
                                                                  data-liked="{{ $_media->liked }}"> {{ $_media->likes_count }}</i></a>
                            </li>
                        </ul>
                        <a href="{{ route('media.show', $_media->id) }}" onclick="fixAnchorTagClick(event)" class="heading-text pull-right"><i class="icon-comments position-right"></i> {{ sizeof($_media->comments) }}</a>
                    </div>
                </div>
            </div>
        @endforeach
        {{ $media->appends(['tags' => $tags, 'sortColumn' => $sortByColumn, 'sortOrder' => $sortByOrder])->links() }}
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

        -webkit-column-count: 4;
        -webkit-column-gap:   5px;
        -moz-column-count:    4;
        -moz-column-gap:      5px;
        column-count:         4;
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
        opacity: 0.8;
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
@endpush

@push('js')
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/libraries/jquery_ui/interactions.min.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/core/libraries/jquery_ui/touch.min.js') }}"></script>

    <script src="//unpkg.com/jscroll/dist/jquery.jscroll.min.js"></script>
    <script type="text/javascript">
        /*
        $('ul.pagination').hide();
        $(function() {
            $('.infinite-scroll').jscroll({
                autoTrigger: true,
                loadingHtml: '<img class="center-block" src="/images/loading.gif" alt="Loading..." />',
                padding: 0,
                nextSelector: '.pagination li.active + li a',
                contentSelector: 'div.infinite-scroll',
                callback: function() {
                    $('ul.pagination').remove();
                }
            });
        });*/
    </script>

    <script type="text/javascript">
        $(function() {
            //IN SEARCH PAGE
            $('#searchInput').val('{{ $searchPlaceholder }}');

            $('#searchInputHidden').val('{{ $searchPlaceholder }}');

            $('#sort-form select').on('change', function(e) {
                let sortColumnSelect = $('#sort-column-select');
                let sortOrderSelect = $('#sort-order-select');

                let selectedColumn = sortColumnSelect.children("option:selected").val();
                let selectedOrder = sortOrderSelect.children("option:selected").val();

                console.log('Selected Column : ' + selectedColumn + ' ; Selected Order : ' + selectedOrder);

                $('#sort-form').submit();
            });
        });

        window.onload = function(e) {
            userId = @php if(Auth::guest()) echo -1; else echo $userId; @endphp;
        };

        function fixAnchorTagClick(e) { //Fara event listener nu se deschidea link-ul
            window.location.href = e.currentTarget.href;
        }

        function updateLike(e) {
            e.preventDefault();

            if(userId == -1)
                return;

            let _this = e.currentTarget;

            // Change like in view
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