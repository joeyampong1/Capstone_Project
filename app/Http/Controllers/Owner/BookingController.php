<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function create($id)
    {
        return redirect()->route('owner.sitter.profile', $id)
            ->with('info', 'Booking feature coming soon.');
    }
}