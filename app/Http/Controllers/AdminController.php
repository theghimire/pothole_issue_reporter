<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AdminController extends Controller
{
    // Show the login form for municipality staff
    public function loginForm()
    {
        // If already logged in, go straight to dashboard
        if (Session::has('admin_id')) {
            return redirect()->route('admin.dashboard');
        }
        return view('admin.login');
    }

    // Process the login request
    public function login(Request $request)
    {
        // Search for the admin in our database
        $admin = DB::table('admins')->where('username', $request->username)->first();

        // Check password using Bcrypt (Hash::check)
        if ($admin && Hash::check($request->password, $admin->password)) {
            // Save admin info in session
            Session::put('admin_id', $admin->id);
            Session::put('admin_username', $admin->username);
            return redirect()->route('admin.dashboard');
        }

        // Redirect back with error message
        return back()->with('error', 'Invalid username or password.');
    }

    // Logout and clear sessions
    public function logout()
    {
        Session::forget(['admin_id', 'admin_username']);
        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }

    // Main dashboard view with stats and pothole list
    public function dashboard(Request $request)
    {
        // Basic security check
        if (!Session::has('admin_id')) {
            return redirect()->route('admin.login')->with('error', 'Please login first.');
        }

        $query = DB::table('issues');

        // Apply filters if the user chose any in the dropdowns
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Get the latest reports first
        $reports = $query->orderBy('created_at', 'desc')->get();

        // Calculate counts for the stat boxes at the top
        $counts = [
            'Pending' => DB::table('issues')->where('status', 'Pending')->count(),
            'Ongoing' => DB::table('issues')->where('status', 'Ongoing')->count(),
            'Completed' => DB::table('issues')->where('status', 'Completed')->count(),
            'Rejected' => DB::table('issues')->where('status', 'Rejected')->count(),
        ];

        return response()
            ->view('admin.dashboard', compact('reports', 'counts'))
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sun, 02 Jan 1990 00:00:00 GMT');
    }

    // Update the status of a pothole (e.g. Mark as Completed)
    public function updateStatus(Request $request, $id)
    {
        if (!Session::has('admin_id'))
            return response()->json(['error' => 'Unauthorized'], 401);

        DB::table('issues')->where('id', $id)->update([
            'status' => $request->status,
            'admin_remark' => $request->admin_remark,
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Status updated successfully for Ticket ID: ' . $request->ticket_id);
    }

    // Delete a report permanently
    public function destroy($id)
    {
        // Simple security check
        if (!Session::has('admin_id')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Delete from database
        DB::table('issues')->where('id', $id)->delete();

        return back()->with('success', 'Report deleted successfully.');
    }

    // Export pothole data to a CSV file for office records
    public function exportCSV()
    {
        // Must be logged in to download data
        if (!Session::has('admin_id'))
            return redirect()->route('admin.login');

        $reports = DB::table('issues')->get();
        $filename = "ward5_pothole_reports_" . date('Y-m-d') . ".csv";

        $handle = fopen('php://output', 'w');
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Column Headers
        fputcsv($handle, ['Ticket ID', 'Ward', 'Status', 'Description', 'Latitude', 'Longitude', 'Created Date', 'Admin Remark']);

        // Fill data rows
        foreach ($reports as $report) {
            fputcsv($handle, [
                $report->ticket_id,
                $report->ward,
                $report->status,
                $report->description,
                $report->latitude,
                $report->longitude,
                $report->created_at,
                $report->admin_remark
            ]);
        }

        fclose($handle);
        exit;
    }
}
