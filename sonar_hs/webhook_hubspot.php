<?php

require(__DIR__ . '/vendor/autoload.php');

use GuzzleHttp\Client;

// Token From My Account
// $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiMGYwNTM3NzgwYmYzNmQ1MWU4NTI2OGM3OGI2OTRlY2Y3MDA3OTBkNDQ2ODQ1ZGI4NmI0YTc1M2QwYmU1YzNlYzI5YWIxZWMwMGY4NmJkMGUiLCJpYXQiOjE2ODgxMDgyOTQuMzU3OSwibmJmIjoxNjg4MTA4Mjk0LjM1NzksImV4cCI6MjE0NTkxNjgwMC4wOTMxLCJzdWIiOiI4Iiwic2NvcGVzIjpbXX0.ar0xFyKwtLNoErODTSh5Q_3G7DS09t1t5QuesShke4vnWBv4nihUgaxlMCCQL7NrFMhPcypCGLmaxte239R_Zm6_UlgHz9EeBpyL8eRtPdPamTHQ5mPTbDuywIDPJ_2QjpOflKWle_8VnUfHjB_7BOura37BnvGgrh56NS2FJrxHkHQDAfS5ljv0j627Au5IbZbTiIFOX2dI_x31LgNEQpGM71EZ0TzhZqKINEDFxrSN5HDCTqKWXsETm9pqp4sY4yZBwNw-0Gdp5aHFP1iSM2FdYTW3_mtJqIW7360MeWKMt3IXTvEADoxPKsriG37K_MW7mXEthYb522JkcnEzsOKCNjKpMGBVRUjMWQdYX1TDjpTLbJVfq6APNf--1MZrWK_No567N02i8H_qV1jxp6TunESWXqzFmLH0QSEIRJPuNHv-SfIHVwgHHDnyLdh4nwqWBKB3ioAGrqmz-Brpd_FH4hBUyD8kkQBBcbc2-1OGbQhdW6VgYMbO2KmQE3i2G40V4wdau9NYkeBBjA77ZfcAwgHMx7vbtso5ll6M6SJP5BQ6LOqzPwVExm1m83YGqKqqBsx_IvopinC9LTkuipVPUKvUmu_pcQKadNL3wT7n2XNIqw7WBFT_XT96cs85WuVg4emdvO1bcfAfekOfR8cXBvMjHODxlkBpPCKT6Fc";


// // Token From Client Account
$token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjU0NTcwNGZjNDUxOTdjNWZlM2ZmY2M0NjBkNjNiMjQ5NDA3OWJjOWE5N2NhNDEzZmFkYjc0MmY4YzJkMGNlYTY3NTZlYzlhMDA0ZTEwZjgiLCJpYXQiOjE2ODg5MzE5NzcuNTcxLCJuYmYiOjE2ODg5MzE5NzcuNTcxLCJleHAiOjIxNDU5MTY4MDAuMDcwNiwic3ViIjoiMyIsInNjb3BlcyI6W119.4zVGayiHtvV1vKho41OqRxJya5Es8UjM9oqMDkTpyLEa83euOas3_sgWvRkNbxhDedNBBbAZ3eL_ZlV73uvT5Y9FSIjE0aQSEJwdIKLVaeoMkGskx0LHgV9IOPFTRqBwFf0RYqLoLX9wKJvQha8gDo8LcscogPonVXCOjptnTjICNNC0QrdPKKZeib0vBvlG_vMt4930HC9fatBLcCSV9Zs8TtIspMh-85SI6bTEU898ew2uP0eJv6ijH_8EKv0KwN2glC_cG_6gS_N_SEr27abQLYPsXS-RwSThbcKaRGi83rz47IqrQO1tM-rB2SJJhubtMRnan_0RlIuMW_nSjvP6iOLeye-n5kfpeXs1bsFu8El-GgMvVoQKjFnaLlLWGzw-iH7UPKA1JAS0jF_c7DJVtuZ4YIliES2z3a8mHor0zKZ6vYv-bHD23NIg5PlwPV6oj6HoDJMxV6bIXlWuNGIPCukpoRaGuPh-1qIrGmpZBRTJqn9EVfmA2WEZxEWcMQzpr-AkP8j6BP371rZI4bnWol3L5l2GpOsbjx_hXrjTHAYW_IqCiXWJoQpy4sMi0lsY2_ARokpqlzTv-PChBctW9eX5UCjod09xpJJL0X3oLVCca_ugNCatZF1aeiT-MbJhIDW4gBYo5Coe7OE9QR-ATtrf57s1Juq72ahU5xI";

// 
//  this code is is for onfield change data in HubSpot
$postdata = file_get_contents("php://input");
file_put_contents('logs_hubspot/h_postdata.log', $postdata  . "\n", FILE_APPEND);


// 
// 
$hs_contact_id = json_decode($postdata)[0]->objectId;
$subscriptionType = json_decode($postdata)[0]->subscriptionType;
file_put_contents('logs_hubspot/h_hs_contact_id.log', $hs_contact_id  . "\n", FILE_APPEND);

// 
// 
if ($subscriptionType == "contact.creation" || $subscriptionType == "contact.propertyChange") {
    account_created_updated();
}


// **********************************
// Function For Latest Access Token
function token_hubspot()
{

    $refresh_token = "32108243-a497-4719-8c0e-74fbf3a2fef2";
    $client_id = "3e067572-9d52-469b-a8bb-82467a18b84e";
    $client_secret = "0e51f06a-0e1a-47ef-99ae-661e0bbab132";

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.hubapi.com/oauth/v1/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=refresh_token&client_id=' . $client_id . '&client_secret=' . $client_secret . '&refresh_token=' . $refresh_token . '',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);

    // 
    return json_decode($response)->access_token;
}

// *************************************************
// Function Return email and Sonar ID of contact ( If Exists )
function email_sonar_account_id()
{

    global $hs_contact_id;

    // 
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts/' . $hs_contact_id . '?properties=email,sonar_account_id',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . token_hubspot()
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    // echo $response;

    if (json_decode($response)->properties) {
        return [json_decode($response)->properties->email, json_decode($response)->properties->sonar_account_id];
    }
}


// ***********************************************
// Function To Search Account in Sonar using email
function search_sonar_via_email($email)
{

    global $token;

    $query = '{
        accounts(reverse_relation_filters: {
          relation: "contacts"
          search: {
            string_fields: {
                attribute:"email_address"
                match: true,
                search_value:"' . $email . '"
              }
          }
        }) {
          entities {
            id
          }
        }
      }';


    $client = new Client;
    $response = $client->post('https://qflix.sonar.software/api/graphql', [
        'headers' => [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json',
        ],
        'json' => [
            'query' => $query,
        ]
    ]);

    $data = json_decode($response->getBody(), true);
    if (isset($data['data']['accounts']['entities'][0]['id'])) {
        return $data['data']['accounts']['entities'][0]['id'];
    }
}


// ***********************
function update_account_request_query($query)
{
    global $token;


    $client = new Client;
    $response = $client->post('https://qflix.sonar.software/api/graphql', [
        'headers' => [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json',
        ],
        'json' => [
            'query' => $query,
        ]
    ]);

    $data = json_decode($response->getBody(), true);
    echo "<br>" . json_encode($data);
}

// ************************************
// Function To Update New Sonar Account
function update_sonar_account_req($sonar_id)
{

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts/search',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "filters": [
        {
            "propertyName": "sonar_account_id",
            "operator": "EQ",
            "value": "' . $sonar_id . '"
        }
    ],
    "properties": [
        "firstname",
        "sonar_account_id",
        "email",
        "sonar_account_status",
        "sonar_account_type",
        "sonar_activation_date",
        "sonar_next_bill_date",
        "ip_address",
        "mac_address",
        "child_account_id",
        "line1_mail_address",
        "line2_mail_address",
        "city_mail_address",
        "state_mail_address",
        "zip_mail_address",
        "dtv_acct",
        "line1_serviceable_address",
        "line2_serviceable_address",
        "city_serviceable_address",
        "state_serviceable_address",
        "zip_serviceable_address",
        "name_primary_contact",
        "phone_primary_contact",
        "ssid",
        "ssid_secrets",
        "username_primary_contact"
    ]
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . token_hubspot()
        ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;

    $response = json_decode($response)->results[0]->properties;

    if (isset($response)) {
        $properties = $response;


        // 
        // If name not null then update
        if ($properties->firstname != null) {

            // 
            echo '<br>firstname: ' . $properties->firstname . PHP_EOL;

            $query = <<<GQL
            mutation {
              updateAccount(id: "{$sonar_id}", input: {
                name: "{$properties->firstname}",
              }) {
                id
                name
              }
            }
            GQL;
            update_account_request_query($query);
        }

        // 
        // If sonar_account_status not null then update
        if ($properties->sonar_account_status != null) {

            // 
            echo '<br>sonar_account_status: ' . $properties->sonar_account_status . PHP_EOL;

            $sonar_account_status = intval($properties->sonar_account_status);

            $query = <<<GQL
            mutation {
              updateAccount(id: "{$sonar_id}", input: {
                account_status_id: {$sonar_account_status},
              }) {
                id
                account_status_id
              }
            }
            GQL;
            update_account_request_query($query);
        }

        // If sonar_account_type not null then update
        if ($properties->sonar_account_type != null) {

            // 
            echo '<br>sonar_account_type: ' . $properties->sonar_account_type . PHP_EOL;

            $sonar_account_type = intval($properties->sonar_account_type);

            $query = <<<GQL
            mutation {
              updateAccount(id: "{$sonar_id}", input: {
                account_type_id: {$sonar_account_type},
              }) {
                id
                account_type_id
              }
            }
            GQL;
            update_account_request_query($query);
        }

        // If sonar_next_bill_date not null then update
        // if ($properties->sonar_next_bill_date != null) {

        //     // 
        //     echo '<br>sonar_next_bill_date: ' . $properties->sonar_next_bill_date . PHP_EOL;

        //     $query = <<<GQL
        //     mutation {
        //         updateEntityCustomFields(id: 193, input: {
        //             next_bill_date: "{$properties->sonar_next_bill_date}",
        //       }) {
        //         id
        //         next_bill_date
        //       }
        //     }
        //     GQL;
        //     update_account_request_query($query);
        // }

        // If ssid not null then update
        if ($properties->ssid != null) {

            // 
            echo '<br>ssid: ' . $properties->ssid . PHP_EOL;


            $query = <<<GQL
            mutation {
                updateAccount(id: "{$sonar_id}", input: {
                    custom_field_data: 
                    [
                       {
                          custom_field_id: 193,
                          value: "{$properties->ssid}"
                       }
                    ]
                  }) {
                    id
                    account_status_id
                  }
            }
            GQL;
            update_account_request_query($query);
        }

        // If ip_address not null then update
        if ($properties->ip_address != null) {

            // 
            echo '<br>ip_address: ' . $properties->ip_address . PHP_EOL;

            $ip_address = json_decode($properties->ip_address);

            $query = <<<GQL
            mutation {
            updateIpAssignment(id: "{$sonar_id}", input: {
                subnet: "{$ip_address}"
              }) {
                id
                subnet
              }
            }
            GQL;
            update_account_request_query($query);
        }

        // If line1_serviceable_address not null then update
        if ($properties->line1_mail_address != null) {

            // 
            echo '<br>line1_mail_address: ' . $properties->line1_mail_address . PHP_EOL;

            $line1_mail_address = $properties->line1_mail_address;

            $query = <<<GQL
            mutation {
                updateServiceableAddress(id: 2205793, input: {
                    line1: "{$line1_mail_address}"
              }) {
                id
              }
            }
            GQL;
            update_account_request_query($query);
        }
    }
}

// $hs_contact_id = "306653";
// update_sonar_account_req("2204280");

// create_sonar_account_req();


// ************************************
// Function To Create New Sonar Account
function create_sonar_account_req()
{
    global $token;
    global $hs_contact_id;


    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts/search',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
    "filters": [
        {
            "propertyName": "hs_object_id",
            "operator": "EQ",
            "value": "' . $hs_contact_id . '"
        }
    ],
    "properties": [
        "firstname",
        "sonar_account_id",
        "email",
        "sonar_account_status",
        "sonar_account_type",
        "sonar_activation_date",
        "sonar_next_bill_date",
        "ip_address",
        "mac_address",
        "child_account_id",
        "line1_mail_address",
        "line2_mail_address",
        "city_mail_address",
        "state_mail_address",
        "zip_mail_address",
        "dtv_acct",
        "line1_serviceable_address",
        "line2_serviceable_address",
        "city_serviceable_address",
        "state_serviceable_address",
        "zip_serviceable_address",
        "name_primary_contact",
        "phone_primary_contact",
        "ssid",
        "ssid_secrets",
        "username_primary_contact"
    ]
}',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . token_hubspot()
        ),
    ));

    $response = curl_exec($curl);
    curl_close($curl);
    echo "<br><br><br>" . $response;

    $response = json_decode($response)->results[0]->properties;

    if (isset($response)) {
        $properties = $response;

        $numericPhoneNumber = preg_replace('/[^0-9]/', '', $properties->phone_primary_contact);
        // $customFieldData = [];

        // if ($properties->dtv_acct !== null) {
        //     $customFieldData[] = [
        //         "custom_field_id" => 192,
        //         "value" => json_encode($properties->dtv_acct)
        //     ];
        // }

        // if ($properties->ssid !== null) {
        //     $customFieldData[] = [
        //         "custom_field_id" => 193,
        //         "value" => json_encode($properties->ssid)
        //     ];
        // }

        // if ($properties->ssid_secrets !== null) {
        //     $customFieldData[] = [
        //         "custom_field_id" => 194,
        //         "value" => json_encode($properties->ssid_secrets)
        //     ];
        // }

        // $customFieldDataInput = '';

        // if (!empty($customFieldData)) {
        //     $customFieldDataInput = 'custom_field_data: ' . json_encode($customFieldData);
        // }

        // custom_field_data: [
        //     {
        //         custom_field_id: 192
        //         value: "{$properties->dtv_acct}"
        //     },
        //     custom_field_data:
        //     {
        //         custom_field_id: 193
        //         value: "{$properties->ssid}"
        //     },
        //     custom_field_data:
        //     {
        //         custom_field_id: 194
        //         value: "{$properties->ssid_secrets}"
        //     }
        // ]



        $query = <<<GQL
    mutation {
      createAccount(input: {
        name: "{$properties->firstname}",
        account_status_id: "{$properties->sonar_account_status}",
        account_type_id: "{$properties->sonar_account_type}",
        company_id: 1,
        primary_contact: 
        {
            name: "{$properties->firstname}"
            email_address: "{$properties->email}"
            phone_numbers: 
            {
                number: {$numericPhoneNumber}
                country: US
                phone_number_type_id: 1
            }
        }
      }) {
        id
        name
      }
    }
    GQL;


        $client = new Client;
        $response = $client->post('https://qflix.sonar.software/api/graphql', [
            'headers' => [
                'Authorization' => "Bearer $token",
                'Accept' => 'application/json',
            ],
            'json' => [
                'query' => $query,
            ]
        ]);

        $data = json_decode($response->getBody(), true);
        echo "<br><br>" . json_encode($data);
        // 
        // 
        $id = $data['data']['createAccount']['id'];

        if ($id != null) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts/' . $hs_contact_id,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'PATCH',
                CURLOPT_POSTFIELDS => '{
      "properties": {
        "sonar_account_id": "' . $id . '"
    }
    }',
                CURLOPT_HTTPHEADER => array(
                    'authorization: Bearer ' . token_hubspot(),
                    'content-type: application/json'
                ),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            // 
            echo $response;
            file_put_contents('logs_hubspot/s_update_acc_in_hs.log',  $response . "\n\n", FILE_APPEND);

            //         $query = <<<GQL
            // mutation {
            //     createMailingAddress(input: {
            //         account_id: "{$id}",
            //         line1: "{$properties->line1_mail_address}",
            //         line2: "{$properties->line2_mail_address}",
            //         city: "{$properties->city_mail_address}",
            //         subdivision: "{$properties->state_mail_address}",
            //         zip: "{$properties->zip_mail_address}",
            //   }) {
            //     id
            //     name
            //   }
            // }
            // GQL;

            //         $client = new Client;
            //         $response = $client->post('https://qflix.sonar.software/api/graphql', [
            //             'headers' => [
            //                 'Authorization' => "Bearer $token",
            //                 'Accept' => 'application/json',
            //             ],
            //             'json' => [
            //                 'query' => $query,
            //             ]
            //         ]);

            //         $data = json_decode($response->getBody(), true);
            //         echo  "<br><br>" . json_encode($data);
        }
    }
}

// ********************************************
// Function When Evenet Contact Creation Occur
function account_created_updated()
{
    // if id exist ->else if email exist -> else create
    if (email_sonar_account_id()[1] != null) {
        // If Id exist in HubSpot Then Update
        $id = email_sonar_account_id()[1];

        file_put_contents('logs_hubspot/hs_sonar_id.log', $id  . "\n", FILE_APPEND);


        update_sonar_account_req($id);
    } else if (email_sonar_account_id()[0] != null) {
        // Search Account VIA email if Exist then Update otherwise create
        $email = email_sonar_account_id()[0];
        $id = search_sonar_via_email($email);
        if ($id != null) {
            update_sonar_account_req($id);
        } else {
            create_sonar_account_req();
        }
    } else {
        create_sonar_account_req();
    }
}
