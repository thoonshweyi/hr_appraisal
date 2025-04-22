@extends("layouts.onepage.index")



@section("content")

<div class="card shadow">
    <div class="card-body text-center">
        <form id="verify-otp-form" action="" method="POST">
            @csrf
            <div class="mb-4">
                <img src="{{ asset('images/otpsend.png') }}" alt="OTP Icon" width="80" height="80">
                <h5 class="mt-3">Verification</h5>
                <p>
                    We will send you a One Time Password on your phone number
                    <br/>
                    <strong>
                        {{-- {{ $user->employee->phone }} --}}
                        {{-- {{ dd(substr($user->employee->phone,0,2)) }} --}}
                        @php
                            $phone = $user->employee->phone;
                            $prefixLength = 2;
                            $suffixLength = 4;

                            $prefix = substr($phone, 0, $prefixLength);
                            $suffix = substr($phone, -$suffixLength);
                            $middleLength = strlen($phone) - ($prefixLength + $suffixLength);
                            $maskedMiddle = str_repeat('*', $middleLength);
                        @endphp

                        {{ $prefix . ' ' . $maskedMiddle . $suffix }}
                    </strong>
                </p>
            </div>

            <div class="mb-3">
                <label for="otp_number" class="form-label">Enter OTP</label>
                <input type="number" id="otp_number" name="otp_number" class="form-control otp_number" min="100000" max="999999" placeholder="Enter 6-digit OTP" oninput="enforceSixDigits(this)">
            </div>

            <div class="input-container row align-items-center">
                <h5><a href="javascript:void(0);" id="get-otp-btn" class="flex-fill text-nowrap text-primary font-weight-bold generate-otp-btns">GET OTP</a></h5>
            </div>
            <p id="otptimer-text" class="d-none"><span id="otptimer"></span></p>
            <p id="resend-otp" class="d-none mt-1">Didn't receive verification OTP? <a href="javascript:void(0);" class="text-primary fw-bold generate-otp-btns">Resend Again</a></p>

            <div class="d-grid">
                <button type="submit" id="verify-otp-btn" class="btn btn-primary">Verify</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('css')

<style>
    /* .sign-banner {
        background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
            url('images/banner/banner1.jpg') no-repeat center center;
        background-size: cover;
    }
    .sign-banner img {
        object-fit: cover;
    } */
     .otp_number{
        appearance: none;
     }
</style>
@endsection




@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script type="text/javascript">
    $(document).ready(function(){


        let timerActive = false; // Flag to track if a timer is running
        $(".generate-otp-btns").click(function(){
            if (timerActive) {
                // Prevent further clicks while the timer is active
                return;
            }
           clearInterval();

            Swal.fire({
                title: "Processing....",
                // html: "I will close in <b></b> milliseconds.",
                text: "Please wait while we send your OTP",
                allowOutsideClick:false,
                didOpen: () => {
                     Swal.showLoading();
                }
           });


            const otp_number = $("#otp_number").val();
            {{-- console.log(otp_number); --}}

            $.ajax({
                url:"/generateotps/signin",
                type:"GET",
                success:function(response){
                     console.log(response);
                     Swal.close();

                     {{-- $("#otpmessage").text("Your OTP code is "+response.otp); --}}
                     {{-- $("#otpmodal").modal("show"); --}}

                     startotptimer(60); // OTP will expires in 120s (2 minute);

                     timerActive = true; // Mark timer as active
                },
                error:function(response){
                     console.error("Error: ",response);
                }
           })
           $('#get-otp-btn').addClass('d-none');


        });



        function startotptimer(duration){

            let timer = duration,minutes,seconds;
            // console.log(timer,minutes,seconds); // 60 undefined undefined


            let setinv = setInterval(dectimer,1000);

            function dectimer(){
                minutes = parseInt(timer/60);
                seconds = parseInt(timer%60);

                minutes = minutes < 10 ? "0"+minutes : minutes;
                seconds = seconds < 10 ? "0"+seconds : seconds;

                $("#otptimer-text").removeClass('d-none');
                $("#otptimer").text(`Expires in ${minutes}:${seconds}.`);

                if(timer-- <= 0){
                    clearInterval(setinv);
                    timerActive = false; // Mark timer as inactive

                    $("#otptimer-text").addClass('d-block');
                    $("#otptimer").text(`Your OTP is expired. You can request again.`);

                    $('#resend-otp').removeClass('d-none');

                }
            }
        }
    });

    {{-- Start OTP Input --}}
    function enforceSixDigits(input) {
        // Limit the input value to 6 digits
        if (input.value.length > 6) {
            input.value = input.value.slice(0, 6);
        }
    }
    {{-- End OTP Input --}}


    $("#verify-otp-form").on("submit",function(e){
        e.preventDefault();
        $.ajax({
             url:"/verifyotps/signin",
             type:"POST",
             data:$(this).serialize(),
             success: function(response){
                console.log(response);
                if(response.message && response.valid == "true"){


                    window.location.href = "{{ route('home') }}";
                }else{
                    Swal.fire({
                        title: "Invalid OTP",
                        text: "Can't Go Forward",
                        icon: "error"
                   });
                }
             },
             error:function(response){
                  console.log("Error OTP: ",response);
                  Swal.fire({
                       title: "Invalid OTP",
                       text: "Can't Go Forward",
                       icon: "error"
                  });
             }
        });
   });



</script>
@endsection
