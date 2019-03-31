@extends('shared.layout')

@section('content')
    <!-- Display photo -->
    <div id="mainContainer" class="content">
        <!-- Post -->
        <div class="panel">
            <div class="panel-body">
                <div class="content-group-lg">
                    <div class="content-group text-center">
                        <img src="{{ URL::asset($media["stylized_path"]) }}" class="img-responsive display-inline-block" alt="">
                    </div>
                </div>
            </div>
        </div>
        <!-- /post -->
        <!-- Comments -->
        <div class="panel panel-flat">
            <div class="panel-heading">
                <h6 class="panel-title text-semiold">Comments</h6>
                <div class="heading-elements">
                    <ul class="list-inline list-inline-separate heading-text text-muted">
                        <li>{{ sizeof($media["comments"]) }} comments</li>
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                @if(sizeof($media["comments"]) == 0)
                    <p>No comments yet. Be the first one to comment!</p>
                @else
                    <ul class="media-list stack-media-on-mobile">
                        @foreach($media["comments"] as $comment)
                            <li class="media">
                                <div class="media-left">
                                    <a href="#"><img src="{{ 'TODO' /* TODO: add avatar to user */ }}" class="img-circle img-sm" alt=""></a>
                                </div>

                                <div class="media-body">
                                    <div class="media-heading">
                                        <a href="#" class="text-semibold">{{ $comment->user->name }}</a>
                                        <span class="media-annotation dotted">
                                    @php
                                        $date = new DateTime($comment->created_at);
                                        echo $date->format('Y-m-d  H:i:s');
                                    @endphp
                                </span>
                                    </div>

                                    <p>{!! $comment->message /* parse the string as html */ !!}</p>

                                    <ul class="list-inline list-inline-separate text-size-small">
                                        <li>70 <a href="#"><i class="icon-arrow-up22 text-success"></i></a><a href="#"><i class="icon-arrow-down22 text-danger"></i></a></li>
                                        <li><a href="#">Reply</a></li>
                                        <li><a href="#">Edit</a></li>
                                    </ul>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <hr class="no-margin">

            <div class="panel-body">
                @if(Auth::guest())
                    <h6 class="no-margin-top content-group">You must be logged in to comment</h6>
                @else
                    <h6 class="no-margin-top content-group">Add a comment</h6>
                    <div class="text-right">
                        <div id="ck_placeholder"></div>
                        <button id="btnAddComment" type="button" class="btn bg-blue"><i class="icon-plus22"></i> Add comment</button>
                    </div>
                @endif
            </div>
        </div>
        <!-- /comments -->
    </div>
    <!-- /Display photos -->
@endsection

@push('css')
    <style type="text/css">
        #mainContainer > .panel:first-child { /* only the first child panel */
            background-color: inherit;
            border: none;
        }
    </style>
@endpush

@push('js')
    <!-- Theme JS files -->
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/ckeditor/ckeditor.js') }}"></script>
    <!-- /theme JS files -->

    <script type="text/javascript">
        $(function() { // On document ready
            // CKEditor
            var userId = @php if(Auth::guest()) echo -1; else echo $userId; @endphp;

            if (userId !== -1) {
                CKEDITOR.replace('ck_placeholder', {
                    height: '200px',
                    removeButtons: 'Subscript,Superscript',
                    toolbarGroups: [
                        { name: 'styles' },
                        { name: 'editing',     groups: [ 'find', 'selection' ] },
                        { name: 'basicstyles', groups: [ 'basicstyles' ] },
                        { name: 'paragraph',   groups: [ 'list', 'blocks', 'align' ] },
                        { name: 'links' },
                        { name: 'insert' },
                        { name: 'colors' },
                        { name: 'tools' },
                        { name: 'others' },
                        { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] }
                    ]
                });

                // add comment submit
                $('#btnAddComment').on('click', function(e) {
                    let _message = CKEDITOR.instances['ck_placeholder'].getData();

                    let mediaId = {{ $media["id"] }};
                    let route = "{{ route('comments.store') }}";

                    let _data = {
                        user_id: userId,
                        media_id: mediaId,
                        message: _message
                    };

                    console.log(_data);
                    console.log(route);

                    $.ajax({
                        type: "POST",
                        url: route,
                        data: _data,
                        success: function(resp) {
                            console.log(resp);
                            location.reload(); //reincarcam pagina
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                });
            }
        });
    </script>
@endpush
