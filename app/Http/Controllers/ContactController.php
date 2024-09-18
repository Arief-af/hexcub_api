<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ContactController extends Controller
{
    public function index()
    {
        $contacts = Contact::latest()->paginate(10);

        return response()->json([
            'message' => 'Contacts Retrieved Successfully',
            'data' => $contacts
        ], Response::HTTP_OK);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'message' => 'required|string',
            'image' => 'required|string|max:255', // Assuming image is stored as a path or URL
        ]);

        try {
            $contact = Contact::create($validated);
            return response()->json([
                'message' => 'Contact Created Successfully',
                'data' => $contact
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to create contact',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show(int $id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json([
                'message' => 'Contact not found'
            ], Response::HTTP_NOT_FOUND);
        }
        return response()->json([
            'message' => 'Contact Retrieved Successfully',
            'data' => $contact
        ], Response::HTTP_OK);
    }

    public function update(Request $request, int $id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json([
                'message' => 'Contact not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone_number' => 'required|string|max:20',
            'message' => 'required|string',
            'image' => 'required|string|max:255',
        ]);

        $contact->update($validated);
        return response()->json([
            'message' => 'Contact Updated Successfully',
            'data' => $contact
        ], Response::HTTP_OK);
    }

    public function destroy(int $id)
    {
        $contact = Contact::find($id);
        if (!$contact) {
            return response()->json([
                'message' => 'Contact not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $contact->delete();
        return response()->json([
            'message' => 'Contact Deleted Successfully'
        ], Response::HTTP_OK);
    }
}