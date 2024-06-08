<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDetails;
class UserDetailsController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $details = $user->details;

        return view('user.details', compact('user', 'details'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $details = $user->details;

        $request->validate([
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
        ]);

        if ($details) {
            $details->update($request->all());
        } else {
            $user->details()->create($request->all());
        }

        return redirect()->back()->with('success', 'Данные успешно обновлены.');
    }
}
