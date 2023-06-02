<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\http\Controllers\DeviceController;

class WebhookController extends Controller
{
    
    public function handle(Request $request)
    {
        $payload = $request->all();

        if($payload['exitMessage']=='finished'){
            $url = 'https://matijseraly.be/api/user';

            $headers = [
                'username' => 'JohnDoe',
                'mail' => 'john.doe@example.com',
                'password' => 'secretpassword'
            ];
            
            $options = [
                'http' => [
                    'method' => 'POST',
                    'header' => "Content-Type: application/json\r\n",
                    'content' => json_encode($headers)
                ]
            ];
            
            $context = stream_context_create($options);
            $response = file_get_contents($url, false, $context);
            
            if ($response === false) {
                // Handle error
                echo 'Error occurred while making the request.';
            } else {
                $responseData = json_decode($response, true);
                // Process the response data
                echo $responseData['message'];
            }
            
        }
        else{
            echo 'not done';
        }
    }
}
