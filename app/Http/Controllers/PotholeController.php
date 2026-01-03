<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Session;

class PotholeController extends Controller
{
    /**
     * Helper to clear admin session when they navigate to citizen routes
     */
    private function clearAdminSession()
    {
        if (Session::has('admin_id')) {
            Session::forget(['admin_id', 'admin_username']);
        }
    }
    // Show the pothole reporting form
    public function index()
    {
        $this->clearAdminSession();
        return view('pothole.report');
    }

    // Save the pothole report data to database
    public function store(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'description' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'ward' => 'required|integer',
            'photos' => 'required|array|min:1',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
            'capture_lat' => 'nullable|numeric',
            'capture_lng' => 'nullable|numeric',
        ]);

        $paths = [];
        if ($request->has('photos')) {
            foreach ($request->photos as $photoData) {
                // Determine file extension from base64 header
                $extension = 'jpg';
                if (str_contains($photoData, 'image/png'))
                    $extension = 'png';
                if (str_contains($photoData, 'image/webp'))
                    $extension = 'webp';

                // Decode base64
                $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $photoData));
                $fileName = 'pothole_' . uniqid() . '.' . $extension;
                $path = 'potholes/' . $fileName;

                Storage::disk('public')->put($path, $imageData);
                $paths[] = $path;
            }
        }

        // Create the issue report
        $ticketId = 'PTH-' . strtoupper(substr(uniqid(), -6));

        DB::table('issues')->insert([
            'ticket_id' => $ticketId,
            'name' => $request->name,
            'description' => $request->description,
            'ward' => $request->ward,
            'photo' => json_encode($paths),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'capture_lat' => $request->capture_lat,
            'capture_lng' => $request->capture_lng,
            'status' => 'Pending',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Go to success page with the ticket ID
        return redirect()->route('report.success', ['ticket_id' => $ticketId]);
    }

    public function success($ticket_id)
    {
        $this->clearAdminSession();
        return view('pothole.success', compact('ticket_id'));
    }

    public function track()
    {
        $this->clearAdminSession();
        return view('pothole.track');
    }

    public function checkStatus(Request $request)
    {
        $ticket_id = $request->ticket_id;
        $report = DB::table('issues')->where('ticket_id', $ticket_id)->first();

        if (!$report) {
            return back()->with('error', 'Ticket ID not found. Please check and try again.');
        }

        return view('pothole.track_result', compact('report'));
    }

    // NEW: Check if there are many reports in a small radius
    public function checkDensity(Request $request)
    {
        $lat = $request->lat;
        $lng = $request->lng;

        if (!$lat || !$lng)
            return response()->json(['count' => 0]);

        // Define a small radius (approx 100m = ~0.001 degrees)
        $threshold = 0.001;

        $count = DB::table('issues')
            ->whereBetween('latitude', [$lat - $threshold, $lat + $threshold])
            ->whereBetween('longitude', [$lng - $threshold, $lng + $threshold])
            ->whereIn('status', ['Pending', 'Ongoing'])
            ->count();

        return response()->json(['count' => $count]);
    }
}
