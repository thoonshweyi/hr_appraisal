<div class="iq-top-navbar">
    <div class="iq-navbar-custom">
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <div class="iq-navbar-logo d-flex align-items-center justify-content-between">
                <i class="ri-menu-line wrapper-menu"></i>
                <a href="{{ route('home') }}" class="header-logo">
                    <img src="{{ asset('images/hrlogo.jpg') }}" class="img-fluid rounded-normal" alt="logo">
                    <h5 class="logo-title ml-3">{{ config('app.name', 'Laravel') }}</h5>

                </a>
            </div>
            <div class="iq-search-bar device-search">

            </div>
            <div class="d-flex align-items-center" id="notification">
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-label="Toggle navigation">
                    <i class="ri-menu-3-line"></i>
                </button>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto navbar-list align-items-center">
                        <li class="nav-item">
                            <a href="{{ route('user.profile') }}" class="nav-link" style="line-height: 22px">
                                <div class="d-flex justify-content-center align-items-center">
                                    <img src="{{ asset('images/user/' . Auth::user()->roles->pluck('name')->first() .'.png') }}" alt="profile-img" class="rounded profile-img img-fluid avatar-70 me-3">
                                    <div class="ms-3">
                                        <span class="d-inline-block mb-2">{{Auth::user()->name}}</span>
                                        <p class="p-0 m-0">{{Auth::user()->roles->pluck('name')->first()}}</p>
                                    </div>
                                </div>
                            </a>
                        </li>
                        <li class="nav-item nav-icon dropdown">
                            <a href="#" class="search-toggle dropdown-toggle btn border add-btn" id="dropdownMenuButton02" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                               @php $locale = session()->get('locale'); @endphp
                                @switch($locale)
                                    @case('en')
                                        <img src="{{asset('images/small/flag-01.png')}}" alt="img-flag" class="img-fluid image-flag mr-2">English
                                    @break
                                    @case('mm')
                                        <img src="{{asset('images/small/flag-02.png')}}" alt="img-flag" class="img-fluid image-flag mr-2">Myanmar
                                    @break
                                    @default
                                        <img src="{{asset('images/small/flag-01.png')}}" alt="img-flag" class="img-fluid image-flag mr-2">English
                                @endswitch
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                <div class="card shadow-none m-0">

                                    <div class="card-body p-3">
                                        <a class="iq-sub-card" href="{{ route('lang','mm')}}"><img src="{{asset('images/small/flag-02.png')}}" alt="img-flag" class="img-fluid mr-2">Myanmar</a>
                                    </div>
                                    <div class="card-body p-3">
                                        <a class="iq-sub-card" href="{{ route('lang','en')}}"><img src="{{asset('images/small/flag-01.png')}}" alt="img-flag" class="img-fluid mr-2">English</a>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item nav-icon dropdown">
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>
                                @if (number_convert(count(Auth::user()->unreadNotifications) > 0))
                                {{-- <span class="bg-secondary badge-card p-2" id="notification_count">{{number_convert(count(Auth::user()->unreadNotifications))}}</span> --}}
                                @endif
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0 ">
                                        <div class="cust-title p-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <h5 class="mb-0">Notifications</h5>
                                                <a class="badge badge-secondary badge-card" href="#">{{-- number_convert(count(Auth::user()->unreadNotifications)) --}}</a>
                                            </div>
                                        </div>
                                        <div class="px-3 pt-0 pb-0 sub-card">

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li class="nav-item nav-icon dropdown caption-content">
                            <a href="#" class="search-toggle dropdown-toggle" id="dropdownMenuButton4" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{-- <img src="{{ asset('images/user/' . Auth::user()->roles->pluck('name')->first() .'.png') }}" class="img-fluid rounded" alt="user"> --}}
                                <i class="fas fa-cog fa-lg"></i>
                            </a>
                            <div class="iq-sub-dropdown dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <div class="card shadow-none m-0">
                                    <div class="card-body p-0 text-center">
                                        <div class="media-body profile-detail text-center">
                                            <img src="{{ asset('images/PRO-1-Global-Logo.png') }}" alt="profile-bg" class="rounded-top img-fluid mb-4">
                                            <img src="{{ asset('images/user/' . Auth::user()->roles->pluck('name')->first() .'.png') }}" alt="profile-img" class="rounded profile-img img-fluid avatar-70">
                                        </div>
                                        <div class="p-3">
                                            <h5 class="mb-1">{{Auth::user()->name}}</h5>
                                            <p class="mb-0">{{Auth::user()->roles->pluck('name')->first()}}</p>
                                            <div class="d-flex align-items-center justify-content-center mt-3">
                                                <a href="{{ route('user.profile') }}" class="btn border mr-2">Profile</a>
                                                <a class="btn border" href="{{ route('logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">Sign Out
                                                </a>


                                                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                                    @csrf
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
        </nav>
    </div>
</div>
