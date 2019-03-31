<!-- Main navbar -->
<div class="navbar navbar-inverse navbar-transparent">
    <div class="navbar-header">
        <a class="navbar-brand" href="{{ route('media.index') }}"><img src="{{ URL::asset('limitless/assets/images/logo_light.png') }}" alt=""></a>

        <ul class="nav navbar-nav pull-right visible-xs-block">
            <li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-grid3"></i></a></li>
        </ul>
    </div>

    <div class="navbar-collapse collapse" id="navbar-mobile">

        <ul class="nav navbar-nav">
            <li><a href="{{ route('media.create') }}">Stylize</a></li>
        </ul>

        <div class="navbar-right">
            <ul class="nav navbar-nav">

                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ URL::asset('limitless/assets/images/placeholder.jpg') }}" alt="">
                        <span>Victoria</span>
                        <i class="caret"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right">
                        {{--<li><a href="{{ url('../media/photosByUserId/'. ['user_id' => $user->id]) }}"><i class="icon-user-plus"></i> My profile</a></li>--}}
                        <li><a href="#"><i class="icon-cog5"></i> Account settings</a></li>
                        <li><a href="#"><i class="icon-switch2"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</div>
<!-- /main navbar -->
