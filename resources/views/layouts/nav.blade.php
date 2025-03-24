 <div class="data-scrollbar" data-scroll="1">
    <nav class="iq-sidebar-menu">
        <ul id="iq-sidebar-toggle" class="iq-menu">
            <li class="{{ (strpos(Route::currentRouteName(), 'home') === 0) ? 'active' : ''}}">
                <a href="{{ route('home') }}" class="svg-icon">
                    <svg  class="svg-icon" id="p-dash1" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>
                    </svg>
                    <span class="ml-4">  {{__('nav.dashboards')}}</span>
                </a>
            </li>


            @can('view-fixed-analysis')

            <li class=" ">
                <a href="#fixed" class="collapsed" data-toggle="collapse" aria-expanded="false">
                    <i class="ri-pie-chart-line"></i>
                    <span class="ml-4">{{__('nav.fixed')}}</span>
                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                    </svg>
                </a>
                <ul id="fixed" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    <li class="{{ (request()->routeIs('ratingscales.index')) ? 'active' : '' }}">
                        <a href="{{route('ratingscales.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.rating_scale_list')}}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->routeIs('grades.index')) ? 'active' : '' }}">
                        <a href="{{route('grades.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.grade_list')}}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->routeIs('statuses.index')) ? 'active' : '' }}">
                        <a href="{{route('statuses.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.status_list')}}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->routeIs('divisions.index')) ? 'active' : '' }}">
                        <a href="{{route('divisions.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.division_list')}}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->routeIs('agiledepartments.index')) ? 'active' : '' }}">
                        <a href="{{route('agiledepartments.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.department_list')}}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->routeIs('subdepartments.index')) ? 'active' : '' }}">
                        <a href="{{route('subdepartments.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.sub_department_list')}}</span>
                        </a>
                    </li>

                    <li class="{{ (request()->routeIs('sections.index')) ? 'active' : '' }}">
                        <a href="{{route('sections.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.section_list')}}</span>
                        </a>
                    </li>

                    <li class="{{ (request()->routeIs('positions.index')) ? 'active' : '' }}">
                        <a href="{{route('positions.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.position_list')}}</span>
                        </a>
                    </li>

                    <li class="{{ (request()->routeIs('genders.index')) ? 'active' : '' }}">
                        <a href="{{route('genders.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.gender_list')}}</span>
                        </a>
                    </li>

                    <li class="{{ (request()->routeIs('positionlevels.index')) ? 'active' : '' }}">
                        <a href="{{route('positionlevels.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.positionlevel_list')}}</span>
                        </a>
                    </li>


                    <li class="{{ (request()->routeIs('attachformtypes.index')) ? 'active' : '' }}">
                        <a href="{{route('attachformtypes.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.attachformtype_list')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            @can('view-add-on')

            <li class=" ">
                <a href="#addon" class="collapsed" data-toggle="collapse" aria-expanded="false">
                    <i class="ri-add-box-line"></i>
                    <span class="ml-4">{{__('nav.addon')}}</span>
                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                    </svg>
                </a>
                <ul id="addon" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    <li class="{{ (request()->routeIs('employees.index')) ? 'active' : '' }}">
                        <a href="{{route('employees.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.employee_list')}}</span>
                        </a>
                    </li>

                    <li class="{{ (request()->routeIs('assformcats.index')) ? 'active' : '' }}">
                        <a href="{{route('assformcats.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.assformcat_list')}}</span>
                        </a>
                    </li>

                    <li class="{{ (request()->routeIs('appraisalcycles.index')) ? 'active' : '' }}">
                        <a href="{{route('appraisalcycles.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.appraisalcycle_list')}}</span>
                        </a>
                    </li>

                </ul>
            </li>
            @endcan


            <li class="{{ (strpos(Route::currentRouteName(), 'appraisalforms.index') === 0) ? 'active' : ''}}">
                <a href="{{ route('appraisalforms.index') }}" class="svg-icon">
                    <i class="far fa-folder-open"></i>
                    <span class="ml-4">  {{__('nav.appraisalform_list')}}</span>
                </a>
            </li>

            @can('edit-faqs')
            <li class=" ">
                <a href="#faq" class="collapsed" data-toggle="collapse" aria-expanded="false">
                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                    viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" fill="none" stroke="currentColor" stroke-width="20" stroke-linecap="round" width="20px" height="20px"   xml:space="preserve">
                    <g>
                        <g>
                            <path d="M256,0C114.842,0,0,114.842,0,256s114.842,256,256,256s256-114.842,256-256S397.158,0,256,0z M256,487.619
                                C128.284,487.619,24.381,383.716,24.381,256S128.284,24.381,256,24.381S487.619,128.284,487.619,256S383.716,487.619,256,487.619z
                                "/>
                        </g>
                    </g>
                    <g>
                        <g>
                            <path d="M256,48.762C141.729,48.762,48.762,141.729,48.762,256S141.729,463.238,256,463.238S463.238,370.271,463.238,256
                                S370.271,48.762,256,48.762z M256,438.857c-100.827,0-182.857-82.03-182.857-182.857S155.173,73.143,256,73.143
                                S438.857,155.173,438.857,256S356.827,438.857,256,438.857z"/>
                        </g>
                    </g>
                    <g>
                        <g>
                            <path d="M279.162,112.152h-46.324c-25.879,0-47.543,20.445-47.543,46.324v22.552c0,6.733,5.458,12.19,12.19,12.19
                                s12.19-5.458,12.19-12.19v-22.552c0-12.435,10.726-21.943,23.162-21.943h46.324c12.435,0,21.943,9.507,21.943,21.943v37.79
                                c0,12.436-10.117,22.552-22.552,22.552c-25.879,0-46.933,21.054-46.933,46.933v34.743c0,6.733,5.458,12.19,12.19,12.19
                                s12.19-5.458,12.19-12.19v-34.743c0-12.435,10.117-22.552,22.552-22.552c25.879,0,46.933-21.054,46.933-46.933v-37.79
                                C325.486,132.597,305.041,112.152,279.162,112.152z"/>
                        </g>
                    </g>
                    <g>
                        <g>
                            <path d="M245.029,337.067c-20.165,0-36.571,16.406-36.571,36.571s16.406,36.571,36.571,36.571s36.571-16.406,36.571-36.571
                                S265.194,337.067,245.029,337.067z M245.029,385.829c-6.722,0-12.19-5.469-12.19-12.19c0-6.722,5.469-12.19,12.19-12.19
                                c6.722,0,12.19,5.469,12.19,12.19C257.219,380.36,251.75,385.829,245.029,385.829z"/>
                        </g>
                    </g>
                </svg>

                    <span class="ml-4">FAQ</span>
                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                    </svg>
                </a>
                <ul id="faq" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    <li class="">
                        <a href="{{route('faqs.create')}}">
                            <i class="las la-minus"></i><span> FAQ Create </span>
                        </a>
                    </li>
                    <li class="">
                        <a href="{{route('faqs.index')}}">
                            <i class="las la-minus"></i><span>FAQ List</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan

            @can('view-users')
            <li class=" ">
                <a href="#member" class="collapsed" data-toggle="collapse" aria-expanded="false">
                    <svg version="1.1" id="Capa_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                        class="svg-icon" id="p-dash2" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" width="20px" height="20px"
                        viewBox="0 0 60 60" style="enable-background:new 0 0 60 60;" xml:space="preserve">
                        <path d="M55.517,46.55l-9.773-4.233c-0.23-0.115-0.485-0.396-0.704-0.771l6.525-0.005c0.114,0.011,2.804,0.257,4.961-0.67
                            c0.817-0.352,1.425-1.047,1.669-1.907c0.246-0.868,0.09-1.787-0.426-2.523c-1.865-2.654-6.218-9.589-6.354-16.623
                            c-0.003-0.121-0.397-12.083-12.21-12.18c-1.739,0.014-3.347,0.309-4.81,0.853c-0.319-0.813-0.789-1.661-1.488-2.459
                            C30.854,3.688,27.521,2.5,23,2.5s-7.854,1.188-9.908,3.53c-2.368,2.701-2.148,5.976-2.092,6.525v5.319c-0.64,0.729-1,1.662-1,2.625
                            v4c0,1.217,0.553,2.352,1.497,3.109c0.916,3.627,2.833,6.36,3.503,7.237v3.309c0,0.968-0.528,1.856-1.377,2.32l-8.921,4.866
                            C1.801,46.924,0,49.958,0,53.262V57.5h44h2h14v-3.697C60,50.711,58.282,47.933,55.517,46.55z M44,55.5H2v-2.238
                            c0-2.571,1.402-4.934,3.659-6.164l8.921-4.866C16.073,41.417,17,39.854,17,38.155v-4.019l-0.233-0.278
                            c-0.024-0.029-2.475-2.994-3.41-7.065l-0.091-0.396l-0.341-0.22C12.346,25.803,12,25.176,12,24.5v-4c0-0.561,0.238-1.084,0.67-1.475
                            L13,18.728V12.5l-0.009-0.131c-0.003-0.027-0.343-2.799,1.605-5.021C16.253,5.458,19.081,4.5,23,4.5
                            c3.905,0,6.727,0.951,8.386,2.828c0.825,0.932,1.24,1.973,1.447,2.867c0.016,0.07,0.031,0.139,0.045,0.208
                            c0.014,0.071,0.029,0.142,0.04,0.21c0.013,0.078,0.024,0.152,0.035,0.226c0.008,0.053,0.016,0.107,0.022,0.158
                            c0.015,0.124,0.027,0.244,0.035,0.355c0.001,0.009,0.001,0.017,0.001,0.026c0.007,0.108,0.012,0.21,0.015,0.303
                            c0,0.018,0,0.033,0.001,0.051c0.002,0.083,0.002,0.162,0.001,0.231c0,0.01,0,0.02,0,0.03c-0.004,0.235-0.02,0.375-0.02,0.378
                            L33,18.728l0.33,0.298C33.762,19.416,34,19.939,34,20.5v4c0,0.873-0.572,1.637-1.422,1.899l-0.498,0.153l-0.16,0.495
                            c-0.669,2.081-1.622,4.003-2.834,5.713c-0.297,0.421-0.586,0.794-0.837,1.079L28,34.123v4.125c0,0.253,0.025,0.501,0.064,0.745
                            c0.008,0.052,0.022,0.102,0.032,0.154c0.039,0.201,0.091,0.398,0.155,0.59c0.015,0.045,0.031,0.088,0.048,0.133
                            c0.078,0.209,0.169,0.411,0.275,0.605c0.012,0.022,0.023,0.045,0.035,0.067c0.145,0.256,0.312,0.499,0.504,0.723l0.228,0.281h0.039
                            c0.343,0.338,0.737,0.632,1.185,0.856l9.553,4.776C42.513,48.374,44,50.78,44,53.457V55.5z M58,55.5H46v-2.043
                            c0-3.439-1.911-6.53-4.986-8.068l-6.858-3.43c0.169-0.386,0.191-0.828,0.043-1.254c-0.245-0.705-0.885-1.16-1.63-1.16h-2.217
                            c-0.046-0.081-0.076-0.17-0.113-0.256c-0.05-0.115-0.109-0.228-0.142-0.349C30.036,38.718,30,38.486,30,38.248v-3.381
                            c0.229-0.28,0.47-0.599,0.719-0.951c1.239-1.75,2.232-3.698,2.954-5.799C35.084,27.47,36,26.075,36,24.5v-4
                            c0-0.963-0.36-1.896-1-2.625v-5.319c0.026-0.25,0.082-1.069-0.084-2.139c1.288-0.506,2.731-0.767,4.29-0.78
                            c9.841,0.081,10.2,9.811,10.21,10.221c0.147,7.583,4.746,14.927,6.717,17.732c0.169,0.24,0.22,0.542,0.139,0.827
                            c-0.046,0.164-0.178,0.462-0.535,0.615c-1.68,0.723-3.959,0.518-4.076,0.513h-6.883c-0.643,0-1.229,0.327-1.568,0.874
                            c-0.338,0.545-0.37,1.211-0.086,1.783c0.313,0.631,0.866,1.474,1.775,1.927l9.747,4.222C56.715,49.396,58,51.482,58,53.803V55.5z"/>
                        <g>
                    </svg>
                    <span class="ml-4">{{__('nav.user')}}</span>
                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                    </svg>
                </a>
                <ul id="member" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    <li class="{{ (request()->routeIs('users.create')) ? 'active' : '' }}">
                        <a href="{{route('users.create')}}">
                            <i class="las la-minus"></i><span>{{__('nav.create_user')}}  </span>
                        </a>
                    </li>
                    <li class="{{ (request()->routeIs('users.index')) ? 'active' : '' }}">
                        <a href="{{route('users.index')}}">
                            <i class="las la-minus"></i><span>{{__('nav.user_list')}} </span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan
            @can('view-branches')
            <li class="{{ (request()->is('branches')) ? 'active' : '' }}">
                <a href="{{route('branches.index')}}" class="svg-icon">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                    class="svg-icon" id="p-dash2" fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round" width="20px" height="20px"
                    >
                        <defs>
                            <style>.cls-1,.cls-2{fill:none;stroke:"currentColor";stroke-linecap:round;stroke-linejoin:round;stroke-width:1.5px;}.cls-2{fill-rule:evenodd;}
                            </style>
                        </defs>
                        <g id="ic-places-castle">
                            <path class="cls-1" d="M2,7H22a0,0,0,0,1,0,0V20.8a.2.2,0,0,1-.2.2H2.2a.2.2,0,0,1-.2-.2V7A0,0,0,0,1,2,7Z"/>
                            <path class="cls-2" d="M7.94,21V15a2,2,0,0,1,2-2H14a2,2,0,0,1,2,2v6"/>
                            <path class="cls-2" d="M2,7V3.2A.2.2,0,0,1,2.2,3H5.8a.2.2,0,0,1,.2.2V7"/>
                            <path class="cls-2" d="M10,7V3.2a.2.2,0,0,1,.2-.2h3.6a.2.2,0,0,1,.2.2V7"/>
                            <path class="cls-2" d="M18,7V3.2a.2.2,0,0,1,.2-.2h3.6a.2.2,0,0,1,.2.2V7"/>
                        </g>
                    </svg>
                    <span class="ml-4">{{__('nav.branch')}}</span>
                </a>
            </li>
            @endcan
            @can('view-roles')
            <li class=" ">
                <a href="#role" class="collapsed" data-toggle="collapse" aria-expanded="false">
                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                    class="svg-icon" id="p-dash2" fill="none" stroke="currentColor" stroke-width="10" stroke-linecap="round" width="20px" height="20px"
                    >
                        <defs>
                            <style>.cls-1,.cls-2{fill:none;stroke:"currentColor";stroke-linecap:round;stroke-linejoin:round;stroke-width:1.5px;}.cls-1{fill-rule:evenodd;}
                            </style>
                        </defs>
                        <g id="ic-security-secured-profile">
                            <path class="cls-1" d="M22,8.44c0-1.4-.16-2.64-.21-3.11a1.15,1.15,0,0,0-1.3-1c-.3,0-.95.12-1.68.15a7.35,7.35,0,0,1-2-.16,7.46,7.46,0,0,1-2.19-1.19A14.91,14.91,0,0,1,13,1.81a1.15,1.15,0,0,0-1.57,0A18.08,18.08,0,0,1,9.89,3.1a7.77,7.77,0,0,1-2.2,1.22,8,8,0,0,1-2.28.18,17.22,17.22,0,0,1-1.87-.18,1.14,1.14,0,0,0-1.3,1C2.19,5.8,2.06,7.05,2,8.44a16.94,16.94,0,0,0,.26,4.15,13,13,0,0,0,3.85,5.85,32.09,32.09,0,0,0,4.62,3.62,2.65,2.65,0,0,0,3,0,31.88,31.88,0,0,0,4.36-3.67,13.3,13.3,0,0,0,3.63-5.76A17.34,17.34,0,0,0,22,8.44Z"/>
                            <path class="cls-1" d="M17,19.33V18a5,5,0,0,0-5-5h0a5,5,0,0,0-5,5v1.33"/>
                            <circle class="cls-2" cx="12" cy="9.5" r="2.5"/>
                        </g>
                    </svg>
                    <span class="ml-4">{{__('nav.role')}}</span>
                    <svg class="svg-icon iq-arrow-right arrow-active" width="20" height="20" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="10 15 15 20 20 15"></polyline><path d="M4 4h7a4 4 0 0 1 4 4v12"></path>
                    </svg>
                </a>
                <ul id="role" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                    <li class="{{ (request()->routeIs('roles.create')) ? 'active' : '' }}">
                        <a href="{{route('roles.create')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.create_role')}}</span>
                        </a>
                    </li>
                    <li class="{{ (request()->routeIs('roles.index')) ? 'active' : '' }}">
                        <a href="{{route('roles.index')}}">
                            <i class="las la-minus"></i><span>{{ __('nav.role_list')}}</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endcan


            {{-- <li>
            <a href="{{ asset('/download/Imported Claim System (Operation User Guide).pdf') }}" class="btn btn-large pull-right" target="_blank">
                    <?xml version="1.0" encoding="iso-8859-1"?>
                    <!-- Uploaded to: SVG Repo, www.svgrepo.com, Generator: SVG Repo Mixer Tools -->
                    <svg fill="#000000" height="20px" width="20px" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                         viewBox="0 0 489.104 489.104" xml:space="preserve">
                    <g>
                        <path d="M411.55,100.9l-94.7-94.7c-4.2-4.2-9.4-6.2-14.6-6.2H92.15c-11.4,0-20.8,9.4-20.8,20.8v330.8c0,11.4,9.4,20.8,20.8,20.8
                            h132.1V421l-16.6-15.2c-8.3-7.3-21.8-7.3-29.1,1s-7.3,21.8,1,29.1l52,47.9c3.1,3.1,14.6,10.2,29.1,0l52-47.9
                            c8.3-8.3,8.3-20.8,1-29.1c-8.3-8.3-20.8-8.3-29.1-1l-18.7,17.2v-50.5h132.1c11.4,0,19.8-9.4,19.8-19.8V115.5
                            C417.85,110.3,415.75,105.1,411.55,100.9z M324.15,70.4l39.3,38.9h-39.3V70.4z M265.95,331.9v-130c0-11.4-9.4-20.8-20.8-20.8
                            c-11.4,0-20.8,9.4-20.8,20.8v130h-111.3V41.6h169.6v86.3c0,11.4,9.4,20.8,20.8,20.8h74.9v183.1h-112.4V331.9z"/>
                    </g>
                    </svg>
                    <span class="ml-4">Import Claim User Guide</span>
                </a>
            </li> --}}
        </ul>
    </nav>
    <div class="p-3"></div>
</div>
