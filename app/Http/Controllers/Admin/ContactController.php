<?php 
 
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use App\Mail\Contact;
use App\Models\User;

 
class ContactController extends Controller
{

    public function contactUser(Request $request, $id)
    {
        $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        try {
            Mail::to($user->email)->send(
                new Contact(
                    $request->input('message'),
                    $request->input('subject')
                )
            );
        } catch (\Exception $e) {
            \Log::error('Error sending contact email: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to send email'], 500);
        }

        return response()->json(['message' => 'Email sent successfully']);
    }
}