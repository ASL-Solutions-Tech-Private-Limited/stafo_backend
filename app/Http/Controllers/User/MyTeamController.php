<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MyTeamController extends Controller
{
    // Show all the team members
    public function index()
    {
        // Fetch all team members (or any data you need for the index view)
        // $teamMembers = TeamMember::all();  // Assuming you have a TeamMember model
        return view('user.myteam.index');
    }

    // Show the form to create a new team member
    public function create()
    {
        return view('user.myteam.create');
    }

    // Store a new team member in the database
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'role' => 'required|string|max:255',
    //         // Add other fields as needed
    //     ]);

    //     // Save the new team member
    //     TeamMember::create($request->all());

    //     return redirect()->route('myteam.index')->with('success', 'Team member added successfully!');
    // }

    // Display a specific team member
    // public function show($id)
    // {
    //     $teamMember = TeamMember::findOrFail($id);
    //     return view('user.myteam.show', compact('teamMember'));
    // }

    // Show the form to edit a specific team member
    // public function edit($id)
    // {
    //     $teamMember = TeamMember::findOrFail($id);
    //     return view('user.myteam.edit', compact('teamMember'));
    // }

    // Update the specific team member
    // public function update(Request $request, $id)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'role' => 'required|string|max:255',
    //         // Add other fields as needed
    //     ]);

    //     $teamMember = TeamMember::findOrFail($id);
    //     $teamMember->update($request->all());

    //     return redirect()->route('myteam.index')->with('success', 'Team member updated successfully!');
    // }

    // Delete a specific team member
    // public function destroy($id)
    // {
    //     $teamMember = TeamMember::findOrFail($id);
    //     $teamMember->delete();

    //     return redirect()->route('myteam.index')->with('success', 'Team member deleted successfully!');
    // }
}