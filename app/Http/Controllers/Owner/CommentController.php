<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $id)
    {
        // Placeholder – later you'll save the comment
        return back()->with('info', 'Comment feature coming soon.');
    }

    public function reply(Request $request, $commentId)
    {
        return back()->with('info', 'Reply feature coming soon.');
    }
}