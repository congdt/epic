<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\Models\Picture;
use App\Models\User;
use App\Models\Like;
use App\Models\Album;
use App\Models\Comment;
class GuestController extends Controller
{
	public function loadComment(Request $request){
		$data = $request->all();
		if (!isset($data['picture_id']) ){
			return Response::json(["title" => "Wrong input"]);
		}
		$picture = Picture::getPicture($data['picture_id']);
		if($picture == NULL){
			return Response::json(['title' => "Invalid Picture"]);
		}
		$comments = Picture::getComment($picture->id)->toArray();
		return Response::json(["title" => "success", "picture_id" => $picture->id, "comments" => $comments]);
	}
	
    //
	public function showIndex(){
		$pictures = Picture::getAllPublicPicture()->toArray();
		
		
		for($i = 0; $i < count($pictures); $i++){
			$user = User::getUser($pictures[$i]['user_id']);
			$album = Album::getAlbum($pictures[$i]['album_id']);
			$like = Picture::getNumberOfLike($pictures[$i]['id']);
			$comment = Picture::getAllComment($pictures[$i]['id']);
			$users_comment = Comment::getUserComment($pictures[$i]['id']);
			$pictures[$i]['comment'] = $comment;
			$pictures[$i]['users_comment'] = $users_comment;
			$pictures[$i]['user_name'] = $user->name;
			$pictures[$i]['user_avatar'] = $user->avatar;
			$pictures[$i]['album_name'] = $album->name;
			$pictures[$i]['numLike'] = $like;
			if(Auth::check()){
				$user_id = Auth::user()->id;
				$button = Like::where("picture_id", $pictures[$i]['id'])
							->where("user_id", $user_id)->first();
				if($button == NULL){
					$pictures[$i]['button'] = "Like";
				}
				else 
					$pictures[$i]['button'] = "Liked";
				
			}
		}
		
		return view("index", ["pictures" => $pictures]);
	}
	
	public function showUserPage(Request $request, $user_id){
		
		$user = User::getUser($user_id);
		
		if($user==NULL){
			return "Wrong user id";
		}
		$pictures = User::getPublicPicture($user->id)->toArray();
		for($i = 0; $i < count($pictures); $i++){
			$user = User::getUser($pictures[$i]['user_id']);
			$album = Album::getAlbum($pictures[$i]['album_id']);
			$like = Picture::getNumberOfLike($pictures[$i]['id']);
			$pictures[$i]['user_name'] = $user->name;
			$pictures[$i]['album_name'] = $album->name;
			$pictures[$i]['numLike'] = $like;
			if(Auth::check()){
				$user_id = Auth::user()->id;
				$button = Like::where("picture_id", $pictures[$i]['id'])
							->where("user_id", $user_id)->first();
				if($button == NULL){
					$pictures[$i]['button'] = "Like";
				}
				else 
					$pictures[$i]['button'] = "Liked";
			
			}
		}
		
		return view("userPage", [ "pictures" => $pictures]);
	}
	
	public function search(Request $request){
		
		
	}
}
