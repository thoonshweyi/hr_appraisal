<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OtpService;
class OtpsController extends Controller
{
    public function __construct(OtpService $otpservice){
        $this->otpservice = $otpservice;
    }
    public function create(Request $request){

        return view("otps.create");
    }

    public function generate(Request $request,$type){
        $userid = Auth::id();
        $getotp = $this->otpservice->generateotp($userid,$type);

        //  //Send OTP via to email / sms
        //   //dispatch(new OtpMailJob($user_email,$randomotp));
        // $token = "9LsE1Sl3ul3I89k4WDPdps_Ssx15jrHNgniRnIq5chFn0Pzrf0yYIN8xGYVDzstb";
        // $headers = [
        //     'Content-Type'=> 'application/json',
        //     'Authorization'=> "Bearer $token"
        // ];
        // $phoneNo = $installercard->phone;
        // $body = [
        //     "to"=> "+95$phoneNo",
        //     "message"=> "Your register OTP code is $getotp for PRO 1 Installer Benefit Program."
        // ];
        // $response = Http::withHeaders($headers)->post('https://smspoh.com/api/v2/send', $body);

        return response()->json([
            "message"=>"OTP generated successfully",
            "otp"=>$getotp,
            // 'opt_response'=>$response
        ]);
    }
}
