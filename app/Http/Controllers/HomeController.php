<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{

    function getIdTocen(){
        $abc = ['a','b','c','d','e','f','g','h','i','k','l','m','n','o','p','q','r','s','t','u','v','x','y','z'];
        $rundomText = '';
        for ($i = 0; $i < 15; $i++) {
            $n = rand(0,1);
            if ($n == 0) {
                $s = rand(0,9);
            } else {
                $s = $abc[rand(0,23)];
            }
            $rundomText .= $s;
        }
        $token = '';
        $sh = md5($rundomText);
        for ($i = 0; $i < 15; $i++) {
            $new_subwol = $sh[$i];
            $a = $rundomText[$i];
            $token .= $new_subwol . $a;
        }
        return $token;
    }


    //
    public function index()
    {
        if (session('id_token')){
            $token = session('id_token');
        } else {
            $token = $this->getIdTocen();
            session()->put('id_token', $token);
        }

        $products = Product::paginate(6);
        return  \view('home', compact('products','token'));
    }
}
