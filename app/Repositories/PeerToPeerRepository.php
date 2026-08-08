<?php

namespace App\Repositories;

use App\Interfaces\PeerToPeerRepositoryInterface;
use App\Models\AppraisalForm;
use App\Models\AppraisalFormAssesseeUser;
use App\Models\AssFormCat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class PeerToPeerRepository implements PeerToPeerRepositoryInterface
{
    public function sendAppraisalForm($peertopeer,array $request){

        $assessor_user_id = $request['assessor_user_id'];
        $ass_form_cat_id = $peertopeer->ass_form_cat_id;
        $appraisal_cycle_id = $request['appraisal_cycle_id'];    
        $user_id = getUserData()?->id;

        $assessee_user_id = $peertopeer->assessee_user_id;

        $appraisalform = AppraisalForm::updateOrCreate([
            "assessor_user_id"=> $assessor_user_id,
            "ass_form_cat_id"=> $ass_form_cat_id,
            "appraisal_cycle_id"=> $appraisal_cycle_id,
        ],[
            "user_id"=> $user_id,
            'assessed' => false,
        ]);

        if ($appraisalform->status_id === 19) {
            $appraisalform->update([
                'status_id' => 20,
            ]);
        }

        AppraisalFormAssesseeUser::firstOrcreate([
            "appraisal_form_id" => $appraisalform->id,
            "assessee_user_id" => $assessee_user_id,
        ],[
            "user_id" => Auth::guard()->user()->id
        ]);

        $assessor = User::find($assessor_user_id);
        $assformcat = AssFormCat::where('id',$ass_form_cat_id)->first();
        if ($assessor) {
            $title = "You received new Appraisal Form \"$assformcat?->name\"";
            sendNotification($assessor,$appraisalform,$title);
        }
    }

}


// Repository biding in laravel

// If you mean Repository Binding in Laravel, it is the pattern where you tell Laravel's Service Container:

// "Whenever my application asks for this interface, give it this repository implementation."

// This is useful when you want to separate Controller → Service → Repository → Model/DB.

// 1. Create the interface
// namespace App\Repositories\Contracts;

// interface ProductRepositoryInterface
// {
//     public function find(int $id);

//     public function getAll();
// }
// 2. Create the repository
// namespace App\Repositories;

// use App\Models\Product;
// use App\Repositories\Contracts\ProductRepositoryInterface;

// class ProductRepository implements ProductRepositoryInterface
// {
//     public function find(int $id)
//     {
//         return Product::findOrFail($id);
//     }

//     public function getAll()
//     {
//         return Product::all();
//     }
// }
// 3. Bind it in a Service Provider

// For newer Laravel versions, you can put it in AppServiceProvider:

// use App\Repositories\ProductRepository;
// use App\Repositories\Contracts\ProductRepositoryInterface;

// public function register(): void
// {
//     $this->app->bind(
//         ProductRepositoryInterface::class,
//         ProductRepository::class
//     );
// }

// The important part is:

// $this->app->bind(
//     ProductRepositoryInterface::class,
//     ProductRepository::class
// );

// It means:

// ProductRepositoryInterface
//         ↓
// ProductRepository
// 4. Inject the interface into Controller
// use App\Repositories\Contracts\ProductRepositoryInterface;

// class ProductController extends Controller
// {
//     public function __construct(
//         private ProductRepositoryInterface $productRepository
//     ) {}

//     public function show($id)
//     {
//         $product = $this->productRepository->find($id);

//         return view('products.show', compact('product'));
//     }
// }

// Laravel sees:

// ProductRepositoryInterface $productRepository

// and automatically resolves it to:

// ProductRepository

// because of your binding.