<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Announcement;
use Illuminate\Validation;
use Illuminate\Support\Facades\Auth;

require_once app_path('Http/Helpers/APIResponse.php');
date_default_timezone_set('Asia/Kolkata');

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the announcements
     *
     * @method GET
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /announcements/all
     * @authentication Requires user authentication
     * @middleware sanctum:auth
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $currDateTime = now();

            $request->validate([
                'term' => 'string|nullable|in:past,upcoming',
                'per_page' => 'nullable|integer',
                'page_number' => 'nullable|integer',
            ]);

            $announcements = Announcement::query();

            if ($request->has('term')) {
                if ($request->input('term') === 'past') {
                    $announcements->where('date', '<', $currDateTime->toDateString())->orWhere(function ($query) use ($currDateTime) {
                        $query->where('date', $currDateTime->toDateString())->where('time', '<', $currDateTime->toTimeString());
                    });
                } else {
                    $announcements->where('date', '>=', $currDateTime->toDateString())->orWhere(function ($query) use ($currDateTime) {
                        $query->where('date', $currDateTime->toDateString())->where('time', '>=', $currDateTime->toTimeString());
                    });
                }
            }

            $perPage = $request->input('per_page', 10);
            $page = $request->input('page_number', 1);

            $announcements = $announcements->paginate($perPage, ['*'], 'page', $page);

            return ok('Announcements fetched successfully!', $announcements);
        } catch (\Throwable $th) {
            return error('Failed to fetch the Announcements! ' . $th->getMessage());
        }
    }

    /**
     * create a new announcement
     *
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /announcements/create
     * @authentication Requires user authentication
     * @middleware checkRole:admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'message' => 'required|string|min:5',
                'time' => 'required|string',
                'date' => 'required|date_format:Y-m-d',
            ]);

            $announcement = Announcement::create($request->only('message', 'time', 'date'));
            return ok('Announcement created successfully', $announcement);
        } catch (\Throwable $th) {
            return error('Error creating the Announcement: ' . $th->getMessage());
        }
    }

    /**
     * update an existing announcement
     *
     * @method POST
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /announcements/update/{id}
     * @authentication Requires user authentication
     * @middleware checkRole:admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $currTime = date('h:i');
            $currDate = date('Y-m-d');
            $request->validate([
                'message' => 'required|string|min:5',
                'time' => 'required|string',
                'date' => 'required|date_format:Y-m-d',
            ]);
            $announcement = Announcement::findOrFail($id);
            if ($announcement->date >= $currDate) {
                $announcement->update($request->only('message', 'time', 'date'));
                return ok('Announcement updated successfully', $announcement);
            } else {
                return error('Cannot update an past Announcement');
            }
        } catch (\Throwable $th) {
            return error('Error updating the Announcement: ' . $th->getMessage());
        }
    }

    /**
     * view an single announcement
     *
     * @method GET
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /announcements/{id}
     * @authentication Requires user authentication
     * @middleware sanctum:auth
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            $announcement = Announcement::findOrFail($id);
            $user = Auth()->user()->role;
            if ($user !== 'admin') {
                $announcement->update(['status' => 'seen']);
                return ok('Announcement seen !');
            } else {
                return ok('Requested announcement found !', $announcement);
            }
        } catch (\Throwable $th) {
            return error('failed to fetch the Announcement ! ' . $th->getMessage());
        }
    }

    /**
     * delet an announcement
     *
     * @method DELETE
     * @author Rohit Vispute (Zignuts Technolab)
     * @route /announcements/{id}
     * @authentication Requires user authentication
     * @middleware checkRole:admin
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request, $id)
    {
        $currTime = date('h:i');
        $currDate = date('Y-m-d');
        $announcement = Announcement::findOrFail($id);
        if ($announcement->date >= $currDate) {
            $announcement->delete();
            return ok('Announcement deleted successfully  !');
        } else {
            return error('Cannot delete an past Announcement');
        }
    }
}
