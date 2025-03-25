<?php

namespace App\Policies;

use App\Models\User;
use App\Models\AppraisalForm;
use Illuminate\Auth\Access\HandlesAuthorization;

class AppraisalFormPol
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
//     public function viewany(User $user){
//         // check if the user has the 'Admin' role
//         return $user->hasRoles(['Admin']);
//    }

   // Users can view their own leave datas
    public function view(User $user,AppraisalForm $appraisalform){

            return $user->can("view-all-appraisal-form") || $this->isOwner($user,$appraisalform);
    }

    public function edit(User $user,AppraisalForm $appraisalform){
        return ($user->can("edit-add-on")  || $this->isOwner($user,$appraisalform)) && !$appraisalform->assessed;
    }

    public function isOwner($user,$appraisalform){
        return $user->id == $appraisalform->assessor_user_id;
    }
}
