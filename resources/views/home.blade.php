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



@endsection


@section('js')
<!-- Firebase scripts -->
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/9.6.10/firebase-messaging-compat.js"></script>
<script>
    navigator.serviceWorker.register('/firebase-messaging-sw.js')
    .then((registration) => {
        console.log("Service Worker registered with scope:", registration.scope);
    })
    .catch(console.error);
    const firebaseConfig = {
        apiKey: "AIzaSyBpHbI8rMw6pWjWalY5yF2jjOc2dOYwQ3Q",
        authDomain: "hr-appraisal.firebaseapp.com",
        projectId: "hr-appraisal",
        storageBucket: "hr-appraisal.firebasestorage.app",
        messagingSenderId: "254575584163",
        appId: "1:254575584163:web:66f30fcd9199f938eb7016",
        measurementId: "G-X1625951R8"
      };


    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function initFirebaseMessagingRegistration() {
        Notification.requestPermission().then((permission) => {
          if (permission === "granted") {
            messaging.getToken({ vapidKey: "BNVN308vit0ouOpfUT-L7C6VQS0gRqOjPvUKIk99eYa1n4ce6dX9g0YEki3cB2vXEffZNOVK_HbHm2PD_p1zy8o" })
              .then((token) => {
                console.log("FCM Token:", token);

                // Send token to backend
                $.ajax({
                  url: '/save-fcm-token',
                  method: 'POST',
                  data: {
                    fcm_token: token,
                    _token: '{{ csrf_token() }}'
                  },
                  success: function(response) {
                    console.log("Token saved");
                  }
                });
              }).catch((err) => {
                console.error("Error getting token", err);
              });
          } else {
            console.error("Permission not granted for notifications");
          }
        });
      }

      initFirebaseMessagingRegistration();

    // Handle foreground messages
    {{-- messaging.onMessage(function(payload) {
      console.log("Message received: ", payload);
      alert(payload.notification.title + "\n" + payload.notification.body);
    }); --}}
  </script>

@endsection
