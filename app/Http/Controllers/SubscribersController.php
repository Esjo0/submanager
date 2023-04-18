<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Key;
class SubscribersController extends Controller
{
    //
    public function index($id){
        if(empty($id)){
            return redirect()->route('home');
        }
        
        return view('subscribers', compact('id'));
    }

    public function create_group(Request $request){

        $api_key = Key::select('api_key')->where('id', $request->api_id)->get()[0]->api_key;

        // dd($api_key);
        $groupsApi = (new \MailerLiteApi\MailerLite($api_key))->groups();

        $newGroup = $groupsApi->create(['name' => $request->group_name]); // creates group and returns it

        dd($newGroup);


    }
}
