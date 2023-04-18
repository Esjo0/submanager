<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MailerLiteApi\MailerLite;
use App\Models\Key;

class KeysController extends Controller
{
    public function index(){
        $keys = Key::where('status', 'active')->get();
        return view('welcome', compact('keys'));
    }

    //
    public function check_key(Request $request){

        $exist = Key::where('api_key', $request->api_key)->get()->count();
        if($exist > 0){
            return redirect()->route('home')->with('error', 'The Key you entered already exists. Kindly click on the title to Manage Subscribers');
        }else{
            $url = "https://api.mailerlite.com/api/v2/groups";

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("X-MailerLite-ApiKey: $request->api_key"));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);
            
            if ($http_code == 200) {
                $add = Key::create([
                    'title' => $request->title,
                    'api_key' => $request->api_key,
                    'status' => 'active'
                ]);
                if ($add) {
                    return redirect()->route('home')->with('success', 'New Key Successfully Created');
                } else {
                    return redirect()->route('home')->with('error', 'Error Adding API Key, kindly try again.');
                }
            } else {
                return redirect()->route('home')->with('error', 'You entered an incorrect API key. Kindly check and try again.');
            }
        }
        
    }
}
