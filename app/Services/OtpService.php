<?php

namespace App\Services;

use App\Models\Otp;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use  App\Jobs\OtpMailJob;


class OtpService{
     public function generateotp($userid,$type){
        $randomotp = rand(100000,999999);
        $expireset = Carbon::now()->addMinute(10);
        Otp::create([
            "user_id"=>$userid,
            "otp"=>$randomotp,
            'type'=> $type,
            "expires_at"=> $expireset
        ]);

        // Send OTP via to email / sms

        return $randomotp;
     }
     public function verifyotp($userid,$otp,$type){
          $checkotp = Otp::where("user_id",$userid)
                         ->where("otp",$otp)
                         ->where("expires_at",">",\Carbon\Carbon::now())
                         ->where("type",$type)
                         ->first();

          if($checkotp){
               // OTP valid

               $checkotp->delete(); // Delete OTP after verification.

               return true;
          }else{
               // OTP invalid
               return false;
          }
     }
}

?>
