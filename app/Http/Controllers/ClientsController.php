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
            request()->validate([
                'first_name' => ['required'],
                'middle_name' => ['required'],
                'last_name' => ['required'],
                'borndate' => ['required','date'],
                'phone' => ['required','regex:/^\+7\d{10}/i'],
                'email' => ['required','email'],
                'iddoc' => ['required'],
                'idnum' => ['required','min:10'],
            ]);
            
            Client::create(request()->all());
            
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
        request()->validate([
            'first_name' => ['required'],
            'middle_name' => ['required'],
            'last_name' => ['required'],
            'borndate' => ['required','date'],
            'phone' => ['required','regex:/^\+7\d{10}/i'],
            'email' => ['required','email'],
            'iddoc' => ['required'],
            'idnum' => ['required','min:10'],
        ]);

        $client->update([
            'first_name' => request('first_name'),
            'middle_name' => request('middle_name'),
            'last_name' => request('last_name'),
            'borndate' => request('borndate'),
            'phone' => request('phone'),
            'email' => request('email'),
            'iddoc' => request('iddoc'),
            'idnum' => request('idnum'),
        ]);
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
