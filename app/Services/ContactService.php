<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Contact;
use Illuminate\Http\Request;

final class ContactService
{
    public static function save(Request $request)
    {
        $me = auth()->user();

        $contact = new Contact;
        $contact->first_name = $request->first_name;
        $contact->last_name = $request->last_name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->user_id = $me->id;
        $contact->save();
    }

    public static function get($id)
    {
        $me = auth()->user();

        return Contact::where('user_id', $me->id)->find($id);
    }

    public static function update(Request $request, $id)
    {
        $me = auth()->user();

        $contact = Contact::where('user_id', $me->id)->find($id);
        $contact->first_name = $request->first_name;
        $contact->last_name = $request->last_name;
        $contact->email = $request->email;
        $contact->phone = $request->phone;
        $contact->save();
    }

    public static function delete($id)
    {
        $me = auth()->user();
        Contact::where('user_id', $me->id)->find($id)->delete();
    }
}
