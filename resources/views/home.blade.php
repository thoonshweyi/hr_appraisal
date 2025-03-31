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


    {{-- Start Firebase js2 --}}
    <script type="module">
        // Import the functions you need from the SDKs you need
        import { initializeApp } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-app.js";
        {{-- import { getAnalytics } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-analytics.js"; --}}
        import { getMessaging, getToken } from "https://www.gstatic.com/firebasejs/11.5.0/firebase-messaging.js";
        // TODO: Add SDKs for Firebase products that you want to use
        // https://firebase.google.com/docs/web/setup#available-libraries

        // Your web app's Firebase configuration
        // For Firebase JS SDK v7.20.0 and later, measurementId is optional
        const firebaseConfig = {
          apiKey: "AIzaSyBpHbI8rMw6pWjWalY5yF2jjOc2dOYwQ3Q",
          authDomain: "hr-appraisal.firebaseapp.com",
          projectId: "hr-appraisal",
          storageBucket: "hr-appraisal.firebasestorage.app",
          messagingSenderId: "254575584163",
          appId: "1:254575584163:web:66f30fcd9199f938eb7016",
          measurementId: "G-X1625951R8"
        };

        // Initialize Firebase
        const app = initializeApp(firebaseConfig);
        {{-- const analytics = getAnalytics(app); --}}
        console.log(app);
        const messaging = getMessaging(app);


        async function requestPermission() {
            try {
                const permission = await Notification.requestPermission();
                if (permission === "granted") {
                    console.log("Notification permission granted.");
                    getFCMToken();
                } else {
                    console.log("Notification permission denied.");
                }
            } catch (error) {
                console.error("Error requesting notification permission:", error);
            }
        }
        async function getFCMToken() {
            try {
                const vapidKey = "BNVN308vit0ouOpfUT-L7C6VQS0gRqOjPvUKIk99eYa1n4ce6dX9g0YEki3cB2vXEffZNOVK_HbHm2PD_p1zy8o"; // Replace with your actual VAPID key from Firebase
                const token = await getToken(messaging, { vapidKey });
                if (token) {
                    console.log("FCM Token:", token);
                    // Send this token to your server for later use
                } else {
                    console.log("No registration token available. Request permission first.");
                }
            } catch (error) {
                console.error("Error getting FCM token:", error);
            }
        }
        requestPermission();
        
    </script>

@endsection
