<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notice;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class NoticeController extends Controller
{
    // Display a listing of the resource
    public function index()
    {
        $notices = Notice::all();

        return view('admin.report.notice.notices', compact('notices'));
    }

    // Show the form for creating a new resource
    public function create()
    {
        return view('admin.report.notice.create');
    }

    // Store a newly created resource in storage
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'file' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048', // Adjust mime types and max size as needed
        ]);

        $filePath = null;

        // Check if a file is uploaded
        if ($request->hasFile('file')) {
            // Store the file in the 'public/notices' folder
            $filePath = $request->file('file')->store('notices', 'public');
        }

        // Create a new notice
        Notice::create([
            'title' => $request->input('title'),
            'description' => $request->input('description'),
            'file' => $filePath, // Store the file path in the database
        ]);

        return redirect()->route('urgent.notices')->with('message', 'Notice created successfully.');
    }

    // Display the specified resource
    public function show(Notice $notice)
    {
        return view('notices.show', compact('notice'));
    }

    // Show the form for editing the specified resource
    public function edit(Notice $notice)
    {
        return view('notices.edit', compact('notice'));
    }

    // Update the specified resource in storage
    public function update(Request $request, Notice $notice)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);

        $notice->update($request->all());
        return redirect()->route('notices.index')->with('success', 'Notice updated successfully.');
    }

    // Remove the specified resource from storage
    public function destroy(Notice $notice)
    {
        $notice->delete();
        return redirect()->route('notices.index')->with('success', 'Notice deleted successfully.');
    }
}
