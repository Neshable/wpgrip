<?php

namespace App\Http\Controllers;

use App\Models\WaitlistEntry;
use Illuminate\Http\Request;

class WaitlistController extends Controller
{
    public function show()
    {
        return view('auth.register-waitlist');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:waitlist_entries,email'],
        ], [
            'email.unique' => 'This email is already on the waitlist!',
        ]);

        WaitlistEntry::create([
            'email' => $request->email,
        ]);

        return back()->with('success', 'You\'re on the list! We\'ll notify you when WPGrip launches.');
    }
}
