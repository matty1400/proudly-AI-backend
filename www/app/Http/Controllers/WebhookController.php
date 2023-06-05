<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\http\Controllers\DeviceController;
use App\Models\company_leads;
use App\Models\jobs;
use App\Models\people_leads;
use Exception;


class WebhookController extends Controller
{
    public function updateJobStatus(){
        $data = jobs::all()->where("ID",max("ID"))->select()->first();
        $data->status = "completed";
        $data->save();
    }
    
    public function handle(Request $request)
    {
        $data = $request->all();
       
        $data = jobs::all()->where("ID",max("ID"))->select()->first();
        if($data->company_search_id != null){
            $search_id = $data->company_search_id;
            $type = "company";
        }
        else{
            $search_id = $data->people_search_id;
            $type = "people";
        }


     


        // require_once('vendor/autoload.php');

        $client = new \GuzzleHttp\Client();

        $response = $client->request('GET', 'https://api.phantombuster.com/api/v2/agents/fetch?id=8697827096363829', [
        'headers' => [
            'X-Phantombuster-Key' => 'tvKJdE1a7UnxDkVpbj6p4Ju6wOlbP4LVhVgitqfPCEc',
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
                if($type=="company"){
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
                if($type=="people"){
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
                $this->updateJobStatus();
            }
            catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }
            }
          }


    }
    
    }


