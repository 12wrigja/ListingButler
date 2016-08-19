<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class ButlerController extends Controller
{
	private $functions = ['next','liked','disliked','help','remaining'];

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
    	$userID = request()->input('user_id');
		if(count($argsSplit) == 0){
			return $this->help($userID);
		}
	
		$method = $argsSplit[0];
		if(in_array($method,$this->functions) && method_exists($this,$method)){
			if(count($argsSplit) == 1){
				return $this->$method($userID);
			} else {
				//TODO call methods with arguments here.
			}
		} else {
			return "I'm sorry: I don't know what to do with the command: ".$method;
		}
	}

	private function info($userID){
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
	
	private function next($userID){
		$history = $this->getHistory();
		if($history == null){
			return 'At this time, Channel History is not available. Please contact the developer.';
		}
		$userID = request()->input('user_id');
		$index = 0;
		$totalListings = count($history);
		$nextListing = null;
		while($index < $totalListings){
			$listing = $history[$index];
			$allReactions = $listing['reactions'];
			$didReact = false;
			foreach($allReactions as $reaction){
				$users = $reaction['users'];
				if(in_array($userID,$users)){
					$didReact = true;
					break;
				}
			}
			if(!$didReact){
			//Return the link here. For the time being, return the timestamp.
			$url = "https://mtv-engres-househunt.slack.com/archives/interest/p".str_replace('.','',$listing['ts']);
				return $url;
			} else {
				$index++;
			}
		}
		return 'It seems you have reacted to all the current listings. Please check back later!';
	}

	private function liked($userID){
		$history = $this->getHistory();
		if($history == null){
			return 'At this time, Channel History is not available. Please contact the developer.';
		}
		$userID = request()->input('user_id');
		$likedListings = [];
		foreach($history as $listing){
			$allReactions = $listing['reactions'];
			foreach($allReactions as $reaction){
				if($reaction['name'] == '+1'){
					$users = $reaction['users'];
					if(in_array($userID,$users)){
						$likedListings[] = $listing['ts'];
						break;
					}
					break;
				}
			}
		}
		
		if(count($likedListings) == 0){
			return "It seems you haven't liked any listings! Use the '+1' reaction to like a listing.";
		}
		$response = "";
		foreach($likedListings as $listing){
			$url = "https://mtv-engres-househunt.slack.com/archives/interest/p".str_replace('.','',$listing);
			$response = $response . $url . "\n";
		}
		//Return the links here.
		$respJson = [];
		$respJson['text'] = $response;
		return response()->json($respJson);
	}

	private function disliked($userID){
		return 'This functionality is not implemented yet! Check back later!';	
	}

	private function help($userID){
		return "Hello there! I'm the Listing Butler, and I'm here to make your life easier.\nCurrently, I support 5 commands:\n\nhelp: returns this message.\nnext: returns a link to the next listing that you haven't reacted to, or a message if you have already reacted to all listings.\nliked: returns links to all the listings you have liked.\ndisliked: returns links to all the listings you have disliked\nremaining: tells you the number of listings you have not reacted to.";
	}

	public function remaining($userID){
		$history = $this->getHistory();
		if($history == null){
			return 'At this time, Channel History is not available. Please contact the developer.';
		}
		$userID = request()->input('user_id');
		$leftToReactTo = 0;
		foreach($history as $listing){
			$allReactions = $listing['reactions'];
			$didReact = false;
			foreach($allReactions as $reaction){
				$users = $reaction['users'];
				if(in_array($userID,$users)){
					$didReact = true;
					break;
				}
			}
			if(!$didReact){
				$leftToReactTo++;
			}
		}
		return 'You have '.$leftToReactTo.' listings to react to.';	
	}

	public function getHistory(){
		$slackToken = env('SLACK_ACCESS_TOKEN');
		$historyURL = 'https://slack.com/api/channels.history?token='.$slackToken.'&channel=C1ZD2NTR8';
		$client = new \GuzzleHttp\Client();
		$res = json_decode($client->request('GET',$historyURL,[])->getBody()->getContents(),true);
		if(!$res['ok']){
			return null;
		}
		return $res['messages'];
	} 
}
