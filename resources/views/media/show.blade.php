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
                            <!-- Main comments -->
                            @if(is_null($comment->parent_id))
                                <li class="media">
                                    <div class="media-left">
                                        <a href="#"><img src="{{ url($comment->user->avatar) }}" class="img-circle img-sm" alt=""></a>
                                    </div>

                                    <div class="media-body">
                                        <div class="media-heading">
                                            <a href="#" class="text-semibold">{{ $comment->user->name }}</a>
                                            <span class="comment-date media-annotation dotted" style="display: none">
                                                {{ $comment->created_at }}
                                            </span>
                                        </div>

                                        <p>{!! $comment->message /* parse the string as html */ !!}</p>

                                        <div id="info-comment-id-{{ $comment->id }}"
                                             data-route="{{ route('comments.like', $comment->id) }}"></div>
                                        <ul class="list-inline list-inline-separate text-size-small">
                                            <li><a href="#"><i @if($comment->liked)
                                                                  class="icon-heart5 text-size-base text-pink position-left like-button"
                                                               @else
                                                                  class="icon-heart6 text-size-base text-pink position-left like-button"
                                                               @endif
                                                                  data-comment-id="{{ $comment->id }}"
                                                                  data-liked="{{ $comment->liked }}"> {{ $comment->likes_count }}</i></a></li>
                                            <li><a href="#" class="reply-a" data-comment-id="{{ $comment->id }}">Reply</a></li>
                                            <li style="visibility: hidden"><a href="#" class="reply-discard-a" data-comment-id="{{ $comment->id }}">Discard</a></li>
                                            <li style="visibility: hidden"><a href="#" class="reply-submit-a" data-comment-id="{{ $comment->id }}">Add reply</a></li>
                                        </ul>

                                        <div id="ck_placeholder_{{ $comment->id }}"></div>

                                        <!-- Replies -->
                                        @foreach($comment->replies as $reply)
                                            <div class="media">
                                                <div class="media-left">
                                                    <a href="#"><img src="{{ url($comment->user->avatar) }}" class="img-circle img-sm" alt=""></a>
                                                </div>

                                                <div class="media-body">
                                                    <div class="media-heading">
                                                        <a href="#" class="text-semibold">{{ $reply->user->name }}</a>
                                                        <span class="comment-date media-annotation dotted" style="display: none">
                                                            {{ $reply->created_at }}
                                                        </span>
                                                    </div>

                                                    <p>{!! $reply->message /* parse the string as html */ !!}</p>

                                                    <div id="info-comment-id-{{ $reply->id }}"
                                                         data-route="{{ route('comments.like', $reply->id) }}"></div>
                                                    <ul class="list-inline list-inline-separate text-size-small">
                                                        <li><a href="#"><i @if($reply->liked)
                                                                           class="icon-heart5 text-size-base text-pink position-left like-button"
                                                                           @else
                                                                           class="icon-heart6 text-size-base text-pink position-left like-button"
                                                                           @endif
                                                                           data-comment-id="{{ $reply->id }}"
                                                                           data-liked="{{ $reply->liked }}"> {{ $reply->likes_count }} </i></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        @endforeach
                                        <!-- /Replies -->
                                    </div>
                                </li>
                            @endif
                            <!-- /Main comments -->
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
    <script type="text/javascript" src="{{ URL::asset('limitless/assets/js/prettyDate.js') }}"></script>
    <!-- /theme JS files -->

    <script type="text/javascript">
        $(function() { // On document ready
            // Comment dates
            $('.comment-date').each(function(i) {
                this.innerText = prettyDate(this.innerText);
                this.style.display = 'inline-block';
            });

            // CKEditor
            var userId = @php if(Auth::guest()) echo -1; else echo $userId; @endphp;

            if (userId !== -1) {
                CKEDITOR.editorConfig = function( config ) {
                    //config.extraPlugins = 'emojione';
                };

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

                //add like functionality
                //like/unlike
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

                    // Make request
                    let commentId = $(this).data('comment-id');
                    let route = $('#info-comment-id-' + commentId).data('route');

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
                });

                //add reply functionality
                //when clicking on 'Reply'
                $('.reply-a').on('click', function(e) {
                    e.preventDefault();

                    let commentId = $(this).data('comment-id');

                    //add ckeditor instance
                    CKEDITOR.replace('ck_placeholder_' + commentId, {
                        height: '120px',
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
                            { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] }
                        ]
                    });

                    //make discard and submit li tags visible
                    let reply_a = this.parentNode.nextElementSibling.children[0];
                    let submit_a = reply_a.parentNode.nextElementSibling.children[0];

                    reply_a.parentNode.style.visibility = "visible";
                    submit_a.parentNode.style.visibility = "visible";
                });
                //when clicking on 'Discard'
                $('.reply-discard-a').on('click', function(e) {
                    e.preventDefault();

                    let commentId = $(this).data('comment-id');

                    //remove ckeditor instance
                    let ck_instance_name = 'ck_placeholder_' + commentId;
                    if(CKEDITOR.instances[ck_instance_name])
                        CKEDITOR.instances[ck_instance_name].destroy();

                    //make discard and submit li tags hidden
                    let reply_a = this;
                    let submit_a = this.parentNode.nextElementSibling.children[0];

                    reply_a.parentNode.style.visibility = "hidden";
                    submit_a.parentNode.style.visibility = "hidden";

                    //remove remainder text
                    $('#' + ck_instance_name).text('');
                });
                //when clicking on 'Add Reply'
                $('.reply-submit-a').on('click', function(e) {
                    e.preventDefault();

                    let commentId = $(this).data('comment-id');
                    let ck_instance_name = 'ck_placeholder_' + commentId;
                    let _message = CKEDITOR.instances[ck_instance_name].getData();

                    let mediaId = {{ $media->id }};
                    let route = "{{ route('comments.store') }}";

                    let _data = {
                        user_id: userId,
                        media_id: mediaId,
                        message: _message,
                        parent_id: commentId
                    };

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
                            location.reload(); //reincarcam pagina
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                });

                // add comment submit
                $('#btnAddComment').on('click', function(e) {
                    let _message = CKEDITOR.instances['ck_placeholder'].getData();

                    let mediaId = {{ $media->id }};
                    let route = "{{ route('comments.store') }}";

                    let _data = {
                        user_id: userId,
                        media_id: mediaId,
                        message: _message
                    };

                    let csrf = $('meta[name="csrf-token"]').attr('content');

                    console.log(_data);
                    console.log(route);
                    console.log(csrf);

                    $.ajax({
                        type: "POST",
                        url: route,
                        headers: {
                            'X-CSRF-TOKEN': csrf
                        },
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
