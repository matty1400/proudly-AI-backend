<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\company_leads;
use Illuminate\Http\Request;
use App\Models\filter_industries;
use App\Models\filter_headcount;
use App\Models\filter_headquarter_location;
use App\Models\filter_seniority;
use App\Models\filter_function;
use App\Models\people_search;
use App\Models\people_leads;
use App\Models\company_search;
use App\Models\users;
use Exception;
use GuzzleHttp\Client;



class DeviceController extends Controller
{
    // GET REQUESTS
    public function getUser(Request $request){

        $mail = $request->header('mail'); // Accessing the 'email' header
        $password = $request->header('password'); // Accessing the 'password' header

        if (!$mail || !$password) {
            // If header parameters are not provided, check query parameters
            $mail = $request->query('mail');
            $password = $request->query('password');
        }
        $password = hash("sha256", $password);
        $data = users::where('mail', $mail)
                     ->where('password', $password)
                     ->where('is_active', 1)
                     ->first();

        if ($data) {
            return response()->json($data);
        } else {
            return response()->json("user not found");
        }
    }


    public function getIdByIndustry(Request $request)
    {
        $name = $request->header('name'); // Accessing the 'name' header
        if (!$name) {
            // If header parameters are not provided, check query parameters
            $name = $request->query('name');
        }
        if ($name) {
            $data = filter_industries::where('industry_name', $name)->where('is_active', 1)->select('ID')->first();
        }
        else {
            $data = filter_industries::all('ID');
        }

        return response()->json($data);
    }

    public function getIndustryNames()
    {
        $data = filter_industries::all('industry_name', 'ID');
        return response()->json($data);
    }
    public function getHeadcount()
    {
        $data = filter_headcount::all('headcount_interval');
        return response()->json($data);
    }

    public function getIdByHeadcount(Request $request)
    {
        $interval = $request->header('interval'); // Accessing the 'interval' header
        if (!$interval) {
            // If header parameters are not provided, check query parameters
            $interval = $request->query('interval');
        }
        if ($interval) {
            $data = filter_headcount::where('headcount_interval', $interval)->where('is_active', 1)->select('ID')->first();
        }
        else {
            $data = filter_headcount::all('ID');
        }

        return response()->json($data);





    }

    public function getHeadquarters()
    {
        $data = filter_headquarter_location::all('industry_name');
        return response()->json($data);
    }

    public function getIdByHeadquarters(Request $request)
    {
        $name = $request->header('name'); // Accessing the 'name' header
        if (!$name) {
            // If header parameters are not provided, check query parameters
            $name = $request->query('name');
        }
        if ($name) {
            $data = filter_headquarter_location::where('industry_name', $name)->where('is_active', 1)->select('ID')->first();
        }
        else {
            $data = filter_headquarter_location::all('ID');
        }

        return response()->json($data);
    }


    public function getIdBySeniority(request $request)
    {
        $name = $request->header('name'); // Accessing the 'name' header
        if (!$name) {
            // If header parameters are not provided, check query parameters
            $name = $request->query('name');
        }
        if ($name) {
            $data = filter_seniority::where('seniority_name', $name)->where('is_active', 1)->select('ID')->first();
        }
        else {
            $data = filter_seniority::all('ID');
        }

        return response()->json($data);
    }

    public function getIdByFunction(Request $request)
    {
        $name = $request->header('name'); // Accessing the 'name' header
        if (!$name) {
            // If header parameters are not provided, check query parameters
            $name = $request->query('name');
        }
        if ($name) {
            $data = filter_function::where('function_name', $name)->where('is_active', 1)->select('ID')->first();
        }
        else {
            $data = filter_function::all('ID');
        }

        return response()->json($data);
    }


    public function getCompanySearchByUserId(request $request)
    {
        $user_id = $request->header('user_id'); // Accessing the 'user_id' header
        if (!$user_id) {
            // If header parameters are not provided, check query parameters
            $user_id = $request->query('user_id');
        }
        if ($user_id) {
            $data = company_search::all()->where('user_id', $user_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }


        return response()->json($data);
    }

    public function getCompanyLeadsBySearchId(request $request)
    {
        $search_id = $request->header('search_id'); // Accessing the 'search_id' header
        if (!$search_id) {
            // If header parameters are not provided, check query parameters
            $search_id = $request->query('search_id');
        }
        if ($search_id) {
            $data = company_leads::all()->where('search_id', $search_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "search not found";
            }
        }
        else{
            $data = "search not found";
        }

        return response()->json($data);
    }

    public function getPeopleSearchByUserId(request $request){
        $user_id = $request->header('user_id'); // Accessing the 'user_id' header
        if (!$user_id) {
            // If header parameters are not provided, check query parameters
            $user_id = $request->query('user_id');
        }

        if ($user_id) {
            $data = people_search::all()->where('user_id', $user_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "user not found";
            }
        }
        else{
            $data = "user not found";
        }

        return response()->json($data);
    }

    public function getPeopleLeadsBySearchId(request $request){
        $search_id = $request->header('search_id'); // Accessing the 'search_id' header
        if (!$search_id) {
            // If header parameters are not provided, check query parameters
            $search_id = $request->query('search_id');
        }
        if ($search_id) {
            $data = people_leads::all()->where('search_id', $search_id)->where('is_active', 1);
            if($data->isEmpty()) {
                $data = "search not found";
            }
        }
        else{
            $data = "search not found";
        }

        return response()->json($data);
    }

    // POST REQUESTS


    public function newCompanyLeads(Request $request){

        $search_id = $request->header('search_id');
        $name = $request->header('name');
        $company_url = $request->header('company_url');
        $description = $request->header('description');
        $company_id = $request->header('company_id');

        $headcount = $request->header('headcount');

        $data = new company_leads;
        $data->search_id = $search_id;
        $data->name = $name;
        $data->company_url = $company_url;
        $data->description = $description;
        $data->company_id = $company_id;

        $data->headcount = $headcount;
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;
        $data->save();

        return response()->json(['message' => 'Data added successfully']);
    }

    public function newPeopleLeads(Request $request){

            $full_name = $request->header('full_name');
            $company_name = $request->header('company_name');
            $company_id = $request->header('company_id');
            $regular_company_url = $request->header('regular_company_url');


            $title = $request->header('title');
            $mail = $request->header('mail');
            $person_url = $request->header('person_url');
            $connection_degree = $request->header('connection_degree');
            $company_location = $request->header('company_location');
            $person_location = $request->header('person_location');
            $search_id = $request->header('search_id');


            $data = new people_leads;
            $data->search_id = $search_id;
            $data->company_name = $company_name;
            $data->company_id = $company_id;
            $data->regular_company_url = $regular_company_url;
            $data->full_name = $full_name;
            $data->person_url = $person_url;
            $data->title = $title;
            $data->mail = $mail;
            $data->connection_degree = $connection_degree;
            $data->company_location = $company_location;
            $data->person_location = $person_location;
            $data->created_at = now();
            $data->updated_at = now();
            $data->is_active = 1;

            $data->save();

        return response()->json(['message' => 'Data added successfully']);
    }
   public function postUser(Request $request){

        $username = $request->header('username');
        $mail = $request->header('mail');
        $password = $request->header('password');

        $data = new users;

        $data->username = $username;
        $data->mail = $mail;
        $data->password = hash("sha256",$password);
        $data->is_admin = 0;
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;


        $data->save();

        return response()->json(['message' => 'Data added successfully']);
    }
    public function newCompanySearch(Request $request){

        $user_id = $request->header('user_id');
        $anual_revenue = $request->header('anual_revenue');
        $headcount = $request->header('headcount');
        $industry = $request->header('industry');
        $geography = $request->header('geography');

        $data = new company_search;

        $data->user_id = $user_id;
        $data->anual_revenue = $anual_revenue;
        $data->headcount = $headcount;
        $data->industry = $industry;
        $data->geography = $geography;
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;

        $data->save();

        $primary_key = $data->getKey();

        return response()->json(['company_search' => $primary_key]);
    }
    public function newPeopleSearch(Request $request){
        $user_id = $request->header('user_id');

        $job_function = $request->header('function');
        $job_seniority = $request->header('seniority');
        $current_company = $request->header('current_company');

        $data = new people_search;
        $data->user_id = $user_id;
        $data->function = $job_function;
        $data->seniority = $job_seniority;
        $data->current_company = $current_company;
        $data->created_at = now();
        $data->updated_at = now();
        $data->is_active = 1;
        $data->save();

        $primary_key = $data->getKey();

        return response()->json(['people_search_key' => $primary_key]);

    }

    //phantom Buster requests

    function updateAndLaunch(Request $request) {

        $address = $request->header('address');



        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://api.phantombuster.com/api/v2/agents/launch');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, '{"id":"5249917770822739","argument":{"numberOfProfiles":100,"extractDefaultUrl":false,"removeDuplicateProfiles":false,"sessionCookie":"AQEDATA3wxABdl0-AAABiGx9LIsAAAGIkImwi04AVzuGOsXLRtVw1kf4d-04DH2GLfYOXzRxBo5HulFrQVsBS74RCATsoRr_tpu9-lEVfk8VoLiV7NAG8_L0oG5mC4Vs6yFU4yTN2Rv62fYICPIRyuNt","searches":"' . $address . '"}}');

        $headers = array();
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'X-Phantombuster-Key: 056OL29RRtKkfikDIAslL7lytVsODwK3Z5xLsoTDy7Q';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close($ch);


    }


    // Fetcher function
    function fetcher(Request $request) {

       $search_id = $request->header('search_id');
       $type = $request->header('type');


        // require_once('vendor/autoload.php');

        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://api.phantombuster.com/api/v2/agents/fetch?id=5249917770822739', [
        'headers' => [
            'X-Phantombuster-Key' => '056OL29RRtKkfikDIAslL7lytVsODwK3Z5xLsoTDy7Q',
            'accept' => 'application/json',
        ],
        ]);

        $responseBody = json_decode($response->getBody(), true);

        // Store the s3Folder and orgs3Folder values in variables
        $s3Folder = $responseBody['s3Folder'];
        $orgs3Folder = $responseBody['orgS3Folder'];

        // You can do further processing or return the values as needed

        $url = "https://phantombuster.s3.amazonaws.com/{$orgs3Folder}/{$s3Folder}/result.json";

        $data = file_get_contents($url);

        if ($data === false) {
            // Error handling if the request fails
            echo "Failed to fetch data from the URL.";
          } else {
            $jsonData = json_decode($data);

            if ($jsonData === null) {
              // Error handling if JSON decoding fails
              echo "Failed to decode JSON data.";
            }
            else {
              // Process the decoded JSON data
              try {
                if($type=="companysearch"){
                foreach ($jsonData as $data) {
                    $companyId = $data->companyId;
                    $companyName = $data->companyName;
                    $description = $data->description;
                    $companyUrl = $data->companyUrl;
                    $headcount = $data->employeeCountRange;


                    $companyLead = new company_leads();
                    $companyLead->company_id = $companyId;
                    $companyLead->name = $companyName;
                    $companyLead->description = $description;
                    $companyLead->company_url = $companyUrl;
                    $companyLead->headcount = $headcount;
                    $companyLead->search_id = $search_id;
                    $companyLead->created_at = now();
                    $companyLead->updated_at = now();
                    $companyLead->is_active = 1;

                    $companyLead->save();
                }}
                if($type=="peoplesearch"){
                    foreach ($jsonData as $record) {


                        if (!isset($record->companyId) || !isset($record->regularCompanyUrl)) {
                            continue; // Skip this record if companyId or regularCompanyUrl is missing
                        }
                        $peopleLead = new people_leads();


                        $peopleLead->full_name = $record->fullName;
                        $peopleLead->company_name = $record->companyName;
                        $peopleLead->company_id = $record->companyId;
                        $peopleLead->regular_company_url = $record->regularCompanyUrl;
                        $peopleLead->title = $record->title;
                        $peopleLead->mail = isset($record->mail) ? $record->mail : null;
                        $peopleLead->person_url = $record->profileUrl;
                        $peopleLead->connection_degree = $record->connectionDegree;
                        $peopleLead->company_location = $record->companyLocation;
                        $peopleLead->person_location = $record->location;
                        $peopleLead->search_id = $search_id; // Change this value to the appropriate search ID
                        $peopleLead->created_at = now();
                        $peopleLead->updated_at = now();
                        $peopleLead->is_active = 1;

                        $peopleLead->save();
                }

            }
                else{
                    echo "No data found";
                }

                echo "Data inserted successfully!";
            }
            catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            }
          }


    }

}
