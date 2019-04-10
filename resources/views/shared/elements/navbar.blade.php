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

        <ul class="nav navbar-nav navbar-right">
            @if(Auth::guest())
                <li><a href="{{ route('login') }}">Log in</a></li>
                <li><a href="{{ route('register') }}">Register</a></li>
            @else
                <li class="dropdown dropdown-user">
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        <img src="{{ URL::asset('limitless/assets/images/placeholder.jpg') /*TODO: add avatar to users*/ }}" alt="">
                        <span>{{ Auth::user()->name }}</span>
                        <i class="caret"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-right">
                       <li>
                            <a href="{{ route('media.photosByUserId', Auth::user()->id) }}">
                                <i class=""></i> My photos
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                                        document.getElementById('logout-form').submit();">
                                <i class="icon-switch2"></i> Logout
                            </a>
                        </li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST">
                            @csrf
                        </form>
                    </ul>
                </li>
            @endif
        </ul>
    </div>
</div>
<!-- /main navbar -->
