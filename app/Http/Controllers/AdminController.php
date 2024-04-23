<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\View\View;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{


    //основная страница
    public function index (Request $request, $page = 1)
    {
        $title = 'Панель управление';
        $page = $request->route('page', 1);

        //$products = Product::paginate(3);
        $userId = auth()->id();
        return \view('admin.home',compact('title' , 'userId' ));
    }

    public function setAdmin(Request $request)
    {
        $userId = $request->id;
        $user = User::find($userId);
        $user->assignRole('admin');

    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('home');
    }

}
