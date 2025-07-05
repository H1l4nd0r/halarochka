<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class ClientsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('clients.index',[
            'clients' => Client::orderBy('last_name', 'asc')->get()
        ] );
    }

    /**
     * Create a new instance.
     */
    public function create(){
        return view('clients.create');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view('clients.show', ['client' => $client ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(){
        try {
            $validated = request()->validate([
                'first_name' => ['required'],
                'middle_name' => ['required'],
                'last_name' => ['required'],
                'borndate' => ['required','date'],
                'phone' => ['required','regex:/^\+7\d{10}/i'],
                'email' => ['required','email'],
                'iddoc' => ['required'],
                'idnum' => ['required','min:10'],
                'files.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120'
            ]);

            $fileData = [];
    
            foreach (request()->file('files') as $file) {
                $path = $file->store('public/uploads');
                
                $fileData[] = [
                    'path' => str_replace('public/', '', $path),
                    'name' => $file->getClientOriginalName(),
                    'type' => $file->getClientMimeType(),
                    'size' => $file->getSize(),
                    'uploaded_at' => now()->toDateTimeString(),
                ];
            }
            $validated['files'] = \json_encode($fileData);
            
            Client::create($validated);
            
            return redirect('/clients');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }
    }

    /**
     * Display edit form.
     */
    public function edit(Client $client)
    {
        return view('clients.edit', ['client' => $client ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Client $client)
    {
        $validated = request()->validate([
            'first_name' => ['required'],
            'middle_name' => ['required'],
            'last_name' => ['required'],
            'borndate' => ['required','date'],
            'phone' => ['required','regex:/^\+7\d{10}/i'],
            'email' => ['required','email'],
            'iddoc' => ['required'],
            'idnum' => ['required','min:10'],
            'files.*' => 'file|mimes:jpg,jpeg,png,pdf|max:5120'
        ]);

        $fileData = [];
    
        foreach (request()->file('files') as $file) {
            $path = $file->store('public/uploads');
            
            $fileData[] = [
                'path' => str_replace('public/', '', $path),
                'name' => $file->getClientOriginalName(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
                'uploaded_at' => now()->toDateTimeString(),
            ];
        }
        $validated['files'] = \json_encode($fileData);

        $client->update($validated);
        return redirect('/clients/' . $client->id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        //
    }
}
