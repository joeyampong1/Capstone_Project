<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function create($id)
    {
        // Temporary redirect back to sitter profile
        return redirect()->route('owner.sitter.profile', $id)
            ->with('info', 'Messaging feature coming soon.');
    }
}