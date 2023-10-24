<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\comments;
use App\Models\topics;
use App\Models\follows;
use App\Models\stories;
use App\Models\users;
use App\Models\likes;


use Illuminate\Http\Request;



use Exception;
use App\Mail\WelcomeMail;
use GuzzleHttp\Client;

use Illuminate\Support\Facades\Mail;
$salt = "letsuprise";



class DeviceController extends Controller
{
    public $salt = "letsuprise";
    
    // GET REQUESTS
    public function getUser(Request $request){

        $username = $request->header('username'); // Accessing the 'email' header
        $password = $request->header('password'); // Accessing the 'password' header

        if (!$username || !$password) {
            // If header parameters are not provided, check query parameters
            $username = $request->query('username');
            $password = $request->query('password');
        }
        $password = hash("sha256", $password.$this->salt);
        $data = users::where('username', $username)
                     ->where('password', $password)
                     ->where('is_active', 1)
                     ->first();

        if ($data) {
            return response()->json($data);
        } else {
            return response()->json("user not found");
        }
    }

    public function getUserById(Request $request){

        $user_id = $request->header('userId'); // Accessing the 'user_id' header

        if (!$user_id) {
            // If header parameters are not provided, check query parameters
            $user_id = $request->query('userId');
        }
        if ($user_id) {
            $data = users::all()->where('id', $user_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }

        return response()->json($data);
    }

    public function getUserByName(Request $request){

        $username = $request->header('username'); // Accessing the 'username' header

        if (!$username) {
            // If header parameters are not provided, check query parameters
            $username = $request->query('username');
        }
        if ($username) {
            $data = users::all()->where('username', $username)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }

        return response()->json($data);
    }

    public function getStoryByUserId(request $request){
        $user_id = $request->header('userId'); // Accessing the 'user_id' header
        if (!$user_id) {
            // If header parameters are not provided, check query parameters
            $user_id = $request->query('userId');
        }
        if ($user_id) {
            $data = stories::all()->where('user_id', $user_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }

        return response()->json($data);
    }

    public function getLikesByStoryId(request $request){
        $story_id = $request->header('storyId'); // Accessing the 'story_id' header
        if (!$story_id) {
            // If header parameters are not provided, check query parameters
            $story_id = $request->query('storyId');
        }
        if ($story_id) {
            $data = likes::all()->where('liked_story', $story_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "story not found";
            }
        }
        else{
            $data = "story not found";
        }

        return response()->json($data);

        


    }

    public function getFollowsByUserId(request $request){
        $user_id = $request->header('userId'); // Accessing the 'user_id' header
        if (!$user_id) {
            // If header parameters are not provided, check query parameters
            $user_id = $request->query('userId');
        }
        if ($user_id) {
            $data = follows::all('followed_user_id')->where('following_user_id', $user_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }

        return response()->json($data);
    }

    public function getCommentsByStoryId(Request $request){

        $story_id = $request->header('storyId'); // Accessing the 'story_id' header

        if (!$story_id) {
            // If header parameters are not provided, check query parameters
            $story_id = $request->query('storyId');
        }
        if ($story_id) {
            $data = comments::all()->where('story_id', $story_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "story not found";
            }
        }
        else{
            $data = "story not found";
        }

    }

    public function getTopicById(Request $request){

        $topic_id = $request->header('topicId'); // Accessing the 'topic_id' header

        if (!$topic_id) {
            // If header parameters are not provided, check query parameters
            $topic_id = $request->query('topicId');
        }
        if ($topic_id) {
            $data = topics::all()->where('id', $topic_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "topic not found";
            }
        }
        else{
            $data = "topic not found";
        }

    }

    public function getTopics(Request $request){

        $data = topics::all()->where('is_active', 1);
        if($data->isEmpty()) {
            $data = "topics not found";
        }
        return response()->json($data);
    }




    
 

   


    

    // POST REQUESTS


   

   
   public function postUser(Request $request){

        $username = $request->header('username');
        $password = $request->header('password');
        $mail = $request->header('mail');
        

        $data = new users;

        $data->username = $username;
        $data->mail = $mail;
        $data->password = hash("sha256",$password.$this->salt);
        $data->is_admin = 0;
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;


        $data->save();
        // $this->sendWelcomeEmail($mail,$username);

        return response()->json(['message' => 'Data added successfully']);
    }

    public function postStory(Request $request){

        $title = $request->header('title');
        $body = $request->header('body');
        $user_id = $request->header('userId');
        $topic_id = $request->header('topicId');
       
        $data = new stories;

        $data->title = $title;
        $data->body = $body;
        $data->user_id = $user_id;
        $data->topic_id = $topic_id;
        
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;
    }

    public function postComment(Request $request){

        $message = $request->header('message');
        $author_id = $request->header('authorId');
        $author_name = $request->header('authorName');
        $story_id = $request->header('storyId');
       
        $data = new comments;

        $data->message = $message;
        $data->author_id = $author_id;
        $data->author_name = $author_name;
        $data->story_id = $story_id;
        
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;
    }

    public function postLike(Request $request){

       
        $liked_story = $request->header('likedStory');
       
        $data = new likes;

        $data->liked_story = $liked_story;
        
        
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;
    }


    //DElete LEADS

    
    

    public function sendWelcomeEmail($email, $name)
    {
        $data = [
            'name' => $name,
        ];

        Mail::to($email)->send(new WelcomeMail($data));
    }
}
