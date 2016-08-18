<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ButlerController extends Controller
{
    public function index(){
	$response = '';
	foreach(request()->all() as $key=>$value){
		$response = $response . $key . '\t' . $value . '\n';
	}
	return $response;
	$token = request()->input('token');
	if($token == null){
		return response('Invalid Token',400);
	}

	$configToken = env('SLACK_TOKEN');
	dd($configToken);
	if(config('SLACK_TOKEN') == request()->get('token')){
		return 'OK';
	} else {
		return 'Not OK';
	}
	}
}
