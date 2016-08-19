<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ButlerController extends Controller
{
    public function index(){
	$token = request()->input('token');
	if($token == null){
		return response('Invalid Token',400);
	}

	$configToken = env('SLACK_VALID_REQUEST_TOKEN');
	if($configToken != $token){
		return response('Invalid Token',400);
	}

	$text = request()->input('text');

	$argsSplit = preg_split('#\s+#',$text,null,PREG_SPLIT_NO_EMPTY);

	if(count($argsSplit) == 0){
		return $this->help();
	}
	
	$method = $argsSplit[0];
	if(method_exists($this,$method)){
		if(count($argsSplit) == 1){
			return $this->$method();
		} else {
			//TODO call methods with arguments here.
		}
	} else {
		return "I'm sorry: I don't know what to do with the command: ".$method;
	}
	
	}

	private function info(){
		if(request()->input('user_name') == '12wrigja'){
			$response = '';
			foreach(request()->all() as $key=>$value){
				$response = $response . $key . '\t' . $value . '\n';
			}
			return $response;
		} else {
			return response('Invalid User',400);
		}
	}
	
	private function next(){
		return 'This functionality is not implemented yet! Check back later!';
	}

	private function liked(){	
		return 'This functionality is not implemented yet! Check back later!';
	}

	private function disliked(){
		return 'This functionality is not implemented yet! Check back later!';	
	}

	private function help(){
		return "Hello there! I'm the Listing Butler, and I'm here to make your life easier.\nCurrently, I support 4 commands:\n\nhelp: returns this message\nnext: returns a link to the next listing that you haven't reacted to, or a message if you have already reacted to all listings.\nliked: returns links to all the listings you have liked.\ndisliked: returns links to all the listings you have disliked";
	}
}
