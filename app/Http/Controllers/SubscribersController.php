<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use yajra\DataTables\DataTables;

use App\Models\Key;
use App\Models\Group;
use App\Models\Subscriber;
use GuzzleHttp\Client;


class SubscribersController extends Controller
{
    //
    public function index($id){
        if(empty($id)){
            return redirect()->route('home');
        }

        $api = key::where('id', $id)->first();
        if(empty($api)){
            return redirect()->route('home');
        }
        
        return view('subscribers', compact('api'));
    }

    public function create_group(Request $request){
        
        $api_key = Key::select('api_key')->where('id', $request->api_id)->get()[0]->api_key;

        $groupsApi = (new \MailerLiteApi\MailerLite($api_key))->groups();

        $newGroup = $groupsApi->create(['name' => $request->group_name]); // creates group and returns it

        if(is_object($newGroup)){
            $add = Group::create([
                'group_id_online' => $newGroup->id,
                'name' => $newGroup->name,
                'key_id' => $request->api_id
            ]);

            if ($add) {
                return redirect()->route('subscribers', ['id' => $request->api_id])->with('success', 'New Group Successfully Created');
            } else {
                return redirect()->route('subscribers', ['id' => $request->api_id])->with('error', 'Error Creating New Group, kindly try again.');
            }
        }else{
            return redirect()->route('subscribers', ['id' => $request->api_id])->with('error', $newGroup);
        }


    }

    public function store(Request $request)
    {
        if( Subscriber::where('email', $request->email)->get()->count() > 0){
            return redirect()->back()->with('error', 'Subscriber Already Exists');
        }else{
            $api_key = Key::select('api_key')->where('id', $request->api_id)->get()[0]->api_key;
        
            $client = new Client([
                'base_uri' => 'https://api.mailerlite.com/api/v2/',
                'headers' => [
                    'X-MailerLite-ApiKey' => $api_key,
                    'Content-Type' => 'application/json',
                ],
            ]);

            
            $group_id = (integer)Group::select('group_id_online')->where('id', $request->group_id)->get()[0]->group_id_online;
                
            $response = $client->post('groups/'.$group_id.'/subscribers', [
                'json' => [
                    'email' => $request->email,
                    'name' => $request->name,
                    'fields' => [
                        'country' => $request->country
                    ]
                ],
            ]);

            if ($response->getStatusCode() === 200) {

                    Subscriber::create([
                    'email' => $request->email,
                    'name' => $request->name,
                    'country' => $request->country,
                    'group_id' => $request->group_id
                    ]);
                return redirect()->back()->with('success', 'Subscriber added successfully');
            } else {
                return redirect()->back()->with('error', 'Failed to add subscriber');
            }
        }
        
    }

    public function list(){
        $data = Subscriber::select(['id', 'email', 'name', 'country', 'created_at']);

        return DataTables::of($data)->make(true);

    }

    public function edit(Request $request)
    {

        $subscriber = Subscriber::where('id', $request->id)->first();


        // Set up the API endpoint URL and data
        $url = "https://api.mailerlite.com/api/v2/subscribers/$subscriber->email";
        $data = [
            'name' => $request->name,
            'fields' => [
                'country' => $request->country
            ]
        ];

        // Make the API call using Laravel's HTTP Client
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-MailerLite-ApiKey' => $subscriber->group->api->api_key
        ])->put($url, $data);

        // Check the response status code to determine if the update was successful
        if ($response->status() == 200) {
            $data = Subscriber::findOrFail($request->id);
            $data->update([
                'name' => $request->name,
                'country' => $request->country
            ]);
            return response()->json([
                'success' => true,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'error' => 'There was an error updating the subscriber.',
            ]);
        }
    }

    public function delete($id)
    {
        $subscriber = Subscriber::where('id', $id)->first();

        $api_key = $subscriber->group->api->api_key;
        // Create a new Guzzle HTTP client
        $client = new Client([
            'base_uri' => 'https://api.mailerlite.com/api/v2/',
            'headers' => [
                'Content-Type' => 'application/json',
                'X-MailerLite-ApiKey' => $api_key,
            ],
        ]);
        
        // Send the API request to delete the subscriber
        $response = $client->delete("subscribers/$subscriber->email");
        
        // Check if the request was successful
        if ($response->getStatusCode() == 204) {
            // The subscriber was deleted successfully
            $data = Subscriber::findOrFail($id);
            $data->delete();
            return response()->json([
                'success' => true,
            ]);
        } else {
            // There was an error deleting the subscriber
            return response()->json([
                'success' => false,
                'error' => 'There was an error deleting the subscriber.',
            ]);
        }
        
    }
}
