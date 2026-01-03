<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    /**
     * Display the neutral landing page for the project.
     * It shows a carousel of notices/ads and a table of all complaints.
     */
    public function index()
    {
        // Auto-logout: Clear admin session if user navigates back to portal
        if (Session::has('admin_id')) {
            Session::forget(['admin_id', 'admin_username']);
        }

        // Fetch all issues from the 'issues' table, newest first
        $issues = DB::table('issues')->orderBy('created_at', 'desc')->get();

        return view('home', compact('issues'));
    }
}
