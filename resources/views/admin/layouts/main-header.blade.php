<!-- main-header opened -->
<div class="main-header sticky side-header nav nav-item" id="main-header">
    <div class="container-fluid">
        <div class="main-header-left ">
            <div class="responsive-logo">
                <a href="{{ url('/admin/' . $page='home') }}">
                    <img src="{{URL::asset('assets/img/logo.png')}}"
                         class="logo-1" alt="logo"></a>
                <a href="{{ url('/admin/' . $page='home') }}">
                    <img src="{{URL::asset('assets/img/logo.png')}}"
                         class="logo-2" alt="logo"></a>
            </div>
            
            <div class="app-sidebar__toggle" data-toggle="sidebar">
                <a class="open-toggle" href="#"><i class="header-icon fe fe-align-left"></i></a>
                <a class="close-toggle" href="#"><i class="header-icons fe fe-x"></i></a>
            </div>
        </div>
        
        <div class="main-header-center justify-content-center">
            <h4>@yield('title')</h4> 
        </div>

        <div class="main-header-right">

            <div class="nav nav-item  navbar-nav-right ml-auto">
               @php
                    app(\App\Services\AlertService::class)->sync();
                    $alertUnreadCount = \App\Models\Alert::unread()->count();
                    $latestAlerts = \App\Models\Alert::unread()->latest()->take(5)->get();
               @endphp
               <div class="dropdown nav-item main-header-notification">
                    <a class="new nav-link" href="#" data-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-bell"></i>
                        @if($alertUnreadCount > 0)
                            <span class="badge badge-danger nav-link-badge">{{ $alertUnreadCount }}</span>
                        @endif
                    </a>
                    <div class="dropdown-menu">
                        <div class="menu-header-content bg-primary text-white">
                            <p class="menu-header-title mb-0">{{ __('main.alerts_center') }}</p>
                            <p class="menu-header-subtitle mb-0">{{ __('main.alerts_unread_count', ['count'=>$alertUnreadCount]) }}</p>
                        </div>
                        <div class="main-notification-list Notification-scroll">
                            @forelse($latestAlerts as $alert)
                                <a class="d-flex p-3 border-bottom" href="{{ route('alerts.index') }}">
                                    <div class="mr-3 notifyimg bg-{{ $alert->severity === 'danger' ? 'danger' : 'warning' }}">
                                        <i class="fa fa-bell text-white"></i>
                                    </div>
                                    <div>
                                        <h6 class="notification-label mb-0">{{ $alert->title }}</h6>
                                        <span class="notification-subtext text-muted">{{ $alert->created_at->diffForHumans() }}</span>
                                    </div>
                                </a>
                            @empty
                                <div class="p-3 text-center text-muted">{{ __('main.no_alerts_found') }}</div>
                            @endforelse
                        </div>
                        <div class="dropdown-footer text-center">
                            <a href="{{ route('alerts.index') }}">{{ __('main.show_all') }}</a>
                        </div>
                    </div>
               </div>
               <div class="dropdown main-profile-menu nav nav-item nav-link"> 
                    <a href="#" type="button" id="btnFullScreen" name="btnFullScreen" role="button">
                        <i class="fa fa-expand"></i> 
                    </a>
                </div>
                <div class="dropdown main-profile-menu nav nav-item nav-link">
                    <a class="profile-user d-flex" href="#">
                        @if (isset(Auth::user()->profile_pic) && !empty(Auth::user()->profile_pic) )
                            <img src="{{asset(Auth::user()->profile_pic)}}" alt="avatar"><i></i>
                        @else
                            <img src="{{asset('assets/img/avatar.png')}}" alt="avatar"><i></i>
                        @endif
                    </a> 
                    <div class="dropdown-menu">
                        <div class="main-header-profile bg-primary p-3">
                            <div class="d-flex wd-100p">
                                <div class="main-img-user">
                                    @if (isset(Auth::user()->profile_pic) && !empty(Auth::user()->profile_pic) )
                                        <img src="{{asset(Auth::user()->profile_pic)}}" alt="avatar"><i></i>
                                    @else
                                        <img src="{{asset('assets/img/avatar.png')}}" alt="avatar"><i></i>
                                    @endif
                                </div>
                                
                                <div class="mr-3 my-auto">
                                    <h6>{{Auth::user()->name}}</h6>
                                    <span>
                                        {{Auth::user()->role_name}}
                                    </span>
                <span>
                    @if(!empty(Auth::user()->branch_id) && optional(Auth::user()->branch)->branch_name)
                        {{ optional(Auth::user()->branch)->branch_name }}
                    @endif
                </span>
                                </div>
                            </div>
                        </div>
                        <a class="dropdown-item" href="{{route('admin.profile.edit',Auth::user()->id)}}"><i
                                class="bx bx-cog"></i> تعديل الملف الشخصى </a> 
                        <a class="dropdown-item" href="{{ route('admin.logout') }}"
                           onclick="event.preventDefault();document.getElementById('logout-form').submit();">
                            <i class="fa fa-power-off"></i> تسجيل الخروج
                        </a>
                        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST"
                              style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div> 
            </div>
        </div>
    </div>
</div>
<!-- /main-header -->
