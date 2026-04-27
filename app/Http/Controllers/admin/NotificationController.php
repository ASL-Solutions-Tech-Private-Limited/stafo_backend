<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyDetail;
use Google\Client as GoogleClient;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;

class NotificationController extends Controller
{


    public function index(Request $request)
    {
        

        return view('admin.notification.index');
    }

    public function create()
    {
        $companies = CompanyDetail::select('id','company_name','company_code')->where('status', '1')->orderBy('company_name','ASC')->get();
        return view('admin.notification.send', compact('companies'));
    }

    public function send(Request $request)
    {
        $request->validate([
            'notification' => 'required',
            'company_id' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $extension = $request->file('image')->getClientOriginalExtension();
            $imageName = 'notification_' . time() . '.' . $extension;
            $request->file('image')->move(public_path('uploads/notifications'), $imageName);
            $imageUrl = asset('uploads/notifications/' . $imageName);
        }
        

        //$path = $request->file('image')->store('public/uploads/notifications');
        //$imageUrl = url(Storage::url($path));
        $companies = $request->company_id;
        foreach($companies as $company_id){        
            $user = CompanyDetail::find($company_id);
            $fcm = $user->fcm_token;
            if ($fcm) {
                //return response()->json(['message' => 'User does not have a device token'], 400);
                //return redirect()->route('notification.send')
                //->with('false', 'User does not have a device token');
            

                $title = 'Stafo'; //$request->title;
                $description = $request->notification;
                $projectId = 'stafo-38601';//config('services.fcm.project_id'); # INSERT COPIED PROJECT ID

                $credentialsFilePath = Storage::path('json/fcm.json');
                $client = new GoogleClient();
                $client->setAuthConfig($credentialsFilePath);
                $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
                $client->refreshTokenWithAssertion();
                $token = $client->getAccessToken();

                $access_token = $token['access_token'];
                //dd($access_token);
                $headers = [
                    "Authorization: Bearer $access_token",
                    'Content-Type: application/json'
                ];
                
                $data = [
                    "message" => [
                        "token" => $fcm,
                        "notification" => [
                            "title" => $title,
                            "body" => $description,
                            "image" => $imageUrl,
                        ],
                    ]
                ];
                $payload = json_encode($data);
                //dd($payload);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send");
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_VERBOSE, true); // Enable verbose output for debugging
                $response = curl_exec($ch);
                $err = curl_error($ch);
                curl_close($ch);
                $response = json_decode($response);
            }
        }
            //dd($response);
            if (isset($response->error)) {
                return redirect()->route('notification.send')
                ->with('error', $response->error->message);
            } else {
                // return response()->json([
                //     'message' => 'Notification has been sent',
                //     'response' => json_decode($response, true)
                // ]);
                return redirect()->route('notification.send')
                ->with('success', 'Notification send successfully.');
            }
     
        return redirect()->route('notification.send')
            ->with('success', 'Notification send successfully.');
    }

}