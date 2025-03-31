@extends('layouts.app')

@section('content')
<div class="content-page">
    <div class="container-fluid">
        <div class="row">
            {{-- <span id="testing">hello world</span> --}}

            {{-- @if (Auth::guard()->user()->can('view-dashboard-return-total') || Auth::guard()->user()->can('view-dashboard-return-finish') || Auth::guard()->user()->can('view-dashboard-return-pending') || Auth::guard()->user()->can('view-dashboard-exchange-total') || Auth::guard()->user()->can('view-dashboard-exchange-finish') || Auth::guard()->user()->can('view-dashboard-exchange-pending') || Auth::guard()->user()->can('view-dashboard-overdue-exchange-document') ) --}}
            <div class="col-lg-4">
                <div class="card card-transparent card-block card-stretch card-height border-none">
                    <div class="card-body p-0 mt-lg-2 mt-0">
                        <h3 class="mb-3">Hello , {{Auth::user()->name}}</h3>
                        <p class="mb-3"><strong>
                                @foreach ($branches as $branch)
                                {{ $branch->branches->branch_name }} <br>
                                @endforeach
                            </strong></p>
                    </div>
                </div>
            </div>
            {{-- @endif --}}




        <!-- Page end  -->
    </div>
</div>
{{-- <script>
    $(document).ready(function() {
        var $key = '';

        $(document).keypress(function(e) {
            if (e.key === 'Enter') {
                $('#testing').text($key);
                $key = '';
            } else {
                $key += e.key;
            }
            console.log($key);
        });
    });
</script> --}}

<script src="https://cdn.jsdelivr.net/npm/@pusher/push-notifications-web@1.1.0/dist/push-notifications-cdn.js"></script>
<script>
    navigator.serviceWorker.register('/service-worker.js')
    .then((registration) => {
        console.log("Service Worker registered with scope:", registration.scope);
    })
    .catch(console.error);

    {{-- const beamsClient = new PusherPushNotifications.Client({
        instanceId: "3c970f94-fe4f-491d-99ec-f82430cae1cb",
    });

    beamsClient.start()
        .then(() => beamsClient.addDeviceInterest("general")) // Subscribe to "general"
        .then(() => console.log("Successfully subscribed to push notifications!"))
        .catch(console.error); --}}



        const beamsClient = new PusherPushNotifications.Client({
            instanceId: "3c970f94-fe4f-491d-99ec-f82430cae1cb", // Replace with your Instance ID
        });

        beamsClient.start()
        .then(() => {
            // Get user ID dynamically from backend session or authentication system
            let userId = "1"; // Replace this dynamically

            return beamsClient.setUserId(userId, {
                fetchToken: () => {
                    return fetch("/api/pusher-auth", {
                        method: "POST",
                        body: JSON.stringify({ user_id: userId }),
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        }
                    })
                    .then(response => response.json())
                    .then((data)=>{
                        console.log(data.token)
                        return data.token}); // Corrected token extraction
                }
            });
        })
        .catch(console.error);
</script>

@endsection
