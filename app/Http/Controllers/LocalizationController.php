<?php
namespace App\Http\Controllers;

use App;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\App as FacadesApp;

class LocalizationController extends Controller
{
    /**
     * @param $locale
     * @return RedirectResponse
     */
    public function index($locale)
    {
        FacadesApp::setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }
}
    ?>