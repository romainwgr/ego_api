<?php 
 
namespace App\Http\Controllers\Admin;

use App\Http\Requests\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Contact;
 
class ContactController extends Controller
{

    public function contactUser(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        Mail::to($user->email)->send(
            new Contact(
                $request->input('message'),
                $request->input('subject')
            )
        );
        return response()->json(['message' => 'Email sent successfully']);
    }
}