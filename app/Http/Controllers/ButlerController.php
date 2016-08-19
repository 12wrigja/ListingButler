<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ButlerController extends Controller
{
    public function index(){
	/*$response = '';
	foreach(request()->all() as $key=>$value){
		$response = $response . $key . '\t' . $value . '\n';
	}
	return $response;
	*/
	$token = request()->input('token');
	if($token == null){
		return response('Invalid Token',400);
	}

	$configToken = env('SLACK_TOKEN');
	if($configToken != $token){
		return response('Invalid Token',400);
	}

	$text = request()->input('text');

	$argsSplit = preg_split('#\s+#',$text,null,PREG_SPLIT_NO_EMPTY);

	if(count($argsSplit) == 0){
		return $this->help();
	}
	
	$method = $argSplit[0];
	if(method_exists($this,$method)){
		if(count($argSplit) == 1){
			return $this->$method();
		} else {
			//TODO call methods with arguments here.
		}
	}
	
	}
}
