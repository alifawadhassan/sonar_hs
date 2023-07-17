<?php

require(__DIR__ . '/vendor/autoload.php');

// 
//  this code is is for onfield change data in HubSpot
// 
$postdata = file_get_contents("php://input");
file_put_contents('logs_sonar/s_postdata.log', $postdata  . "\n", FILE_APPEND);


// 
// Event
$event =  isset(json_decode($postdata)->event) ? json_decode($postdata)->event : "";

$sonar_account_id = isset(json_decode($postdata)->object_id) ? json_decode($postdata)->object_id : "";

if ($event !== "account.updated") {
    $firstname = isset(json_decode($postdata)->current->name) ? json_decode($postdata)->current->name : "";
} else {
    $firstname = isset(json_decode($postdata)->original->name) ? json_decode($postdata)->original->name : "";
}

// 
// 
if ($event == "account.created") {
    account_created();
} else if ($event == "account.updated") {
    account_updated_attached();
} else if ($event == "account.attached") {
    account_updated_attached();
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

// **********************************
// Function For Account Details Sonar
function accountEmailSonar()
{
    global $sonar_account_id;
    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjU0NTcwNGZjNDUxOTdjNWZlM2ZmY2M0NjBkNjNiMjQ5NDA3OWJjOWE5N2NhNDEzZmFkYjc0MmY4YzJkMGNlYTY3NTZlYzlhMDA0ZTEwZjgiLCJpYXQiOjE2ODg5MzE5NzcuNTcxLCJuYmYiOjE2ODg5MzE5NzcuNTcxLCJleHAiOjIxNDU5MTY4MDAuMDcwNiwic3ViIjoiMyIsInNjb3BlcyI6W119.4zVGayiHtvV1vKho41OqRxJya5Es8UjM9oqMDkTpyLEa83euOas3_sgWvRkNbxhDedNBBbAZ3eL_ZlV73uvT5Y9FSIjE0aQSEJwdIKLVaeoMkGskx0LHgV9IOPFTRqBwFf0RYqLoLX9wKJvQha8gDo8LcscogPonVXCOjptnTjICNNC0QrdPKKZeib0vBvlG_vMt4930HC9fatBLcCSV9Zs8TtIspMh-85SI6bTEU898ew2uP0eJv6ijH_8EKv0KwN2glC_cG_6gS_N_SEr27abQLYPsXS-RwSThbcKaRGi83rz47IqrQO1tM-rB2SJJhubtMRnan_0RlIuMW_nSjvP6iOLeye-n5kfpeXs1bsFu8El-GgMvVoQKjFnaLlLWGzw-iH7UPKA1JAS0jF_c7DJVtuZ4YIliES2z3a8mHor0zKZ6vYv-bHD23NIg5PlwPV6oj6HoDJMxV6bIXlWuNGIPCukpoRaGuPh-1qIrGmpZBRTJqn9EVfmA2WEZxEWcMQzpr-AkP8j6BP371rZI4bnWol3L5l2GpOsbjx_hXrjTHAYW_IqCiXWJoQpy4sMi0lsY2_ARokpqlzTv-PChBctW9eX5UCjod09xpJJL0X3oLVCca_ugNCatZF1aeiT-MbJhIDW4gBYo5Coe7OE9QR-ATtrf57s1Juq72ahU5xI";

    $query = <<<GQL
    query MyCoolQuery {
      accounts(id: $sonar_account_id) {
      entities {
        id
        name
        account_status {
          id name
        }
        contacts {
            entities {
            email_address 
            }
        }
      }
     }
    }
    GQL;


    $client = new GuzzleHttp\Client;
    $response = $client->post('https://qflix.sonar.software/api/graphql', [
        'headers' => [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json',
        ],
        'json' => [
            'query' => $query,
        ]
    ]);

    $decodedResponse = json_decode($response->getBody()->getContents(), false, JSON_PRETTY_PRINT);
    file_put_contents('logs_sonar/s_SonarEmailRespo.log', json_encode($decodedResponse) . "\n", FILE_APPEND);

    $account = $decodedResponse['data']['accounts']['entities'][0];
    return $account['contacts']['entities'][0]['email_address'];
}


// **********************************
// Function For Account Details Sonar
function create_account_request()
{
    global $sonar_account_id;
    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjU0NTcwNGZjNDUxOTdjNWZlM2ZmY2M0NjBkNjNiMjQ5NDA3OWJjOWE5N2NhNDEzZmFkYjc0MmY4YzJkMGNlYTY3NTZlYzlhMDA0ZTEwZjgiLCJpYXQiOjE2ODg5MzE5NzcuNTcxLCJuYmYiOjE2ODg5MzE5NzcuNTcxLCJleHAiOjIxNDU5MTY4MDAuMDcwNiwic3ViIjoiMyIsInNjb3BlcyI6W119.4zVGayiHtvV1vKho41OqRxJya5Es8UjM9oqMDkTpyLEa83euOas3_sgWvRkNbxhDedNBBbAZ3eL_ZlV73uvT5Y9FSIjE0aQSEJwdIKLVaeoMkGskx0LHgV9IOPFTRqBwFf0RYqLoLX9wKJvQha8gDo8LcscogPonVXCOjptnTjICNNC0QrdPKKZeib0vBvlG_vMt4930HC9fatBLcCSV9Zs8TtIspMh-85SI6bTEU898ew2uP0eJv6ijH_8EKv0KwN2glC_cG_6gS_N_SEr27abQLYPsXS-RwSThbcKaRGi83rz47IqrQO1tM-rB2SJJhubtMRnan_0RlIuMW_nSjvP6iOLeye-n5kfpeXs1bsFu8El-GgMvVoQKjFnaLlLWGzw-iH7UPKA1JAS0jF_c7DJVtuZ4YIliES2z3a8mHor0zKZ6vYv-bHD23NIg5PlwPV6oj6HoDJMxV6bIXlWuNGIPCukpoRaGuPh-1qIrGmpZBRTJqn9EVfmA2WEZxEWcMQzpr-AkP8j6BP371rZI4bnWol3L5l2GpOsbjx_hXrjTHAYW_IqCiXWJoQpy4sMi0lsY2_ARokpqlzTv-PChBctW9eX5UCjod09xpJJL0X3oLVCca_ugNCatZF1aeiT-MbJhIDW4gBYo5Coe7OE9QR-ATtrf57s1Juq72ahU5xI";

    $query = <<<GQL
query MyCoolQuery {
    accounts(id: $sonar_account_id) {
        entities {
            id
            name
            account_status_id
            account_type_id
            next_bill_date
            activation_date
            contacts (primary: true) {
                entities {
                    name
                    username
                    email_address
                    phone_numbers{
                        entities {
                            number_formatted
                        }
                    }                    
                }
            }

            mailingAddresses: addresses (type: MAILING ) {
                entities {
                    line1
                    line2
                    city
                    subdivision
                    zip
                }
            }

            physicalAddresses: addresses (type: PHYSICAL ) {
                entities {
                    line1
                    line2
                    city
                    subdivision
                    zip
                }
            }

            dtv: custom_field_data (id: 192) {
                entities {
                    value
                }
            }

            ssid: custom_field_data (id: 193) {
                entities {
                    value
                }
            }

            ssid_sec: custom_field_data (id: 194) {
                entities {
                    value
                }
            }

            child_accounts {
                entities {
                    name
                }
            }


            ip_assignment_histories (ipassignmenthistoryable_type: Account ) {
                entities {
                    subnet
                    unique_identifier
                    updated_at
                }
            }

            
        }
    }
}
GQL;



    $client = new GuzzleHttp\Client;
    $response = $client->post('https://qflix.sonar.software/api/graphql', [
        'headers' => [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json',
        ],
        'json' => [
            'query' => $query,
        ]
    ]);

    $decodedResponse = json_decode($response->getBody()->getContents(), false, JSON_PRETTY_PRINT);


    echo json_encode($decodedResponse) . "<br><br><br>";
    $data = $decodedResponse;

    $accountCount = count($data->data->accounts->entities);
    $accounts = array();

    // ACCOUNTS DETAILS
    for ($i = 0; $i < $accountCount; $i++) {
        $entity = $data->data->accounts->entities[$i];
        $account = new stdClass();

        $account->accountId = $entity->id ?? "";
        $account->accountName = $entity->name ?? "";
        $account->accountStatusId = $entity->account_status_id ?? "";
        $account->accountTypeId = $entity->account_type_id ?? "";
        $account->activationDate = $entity->activation_date ?? "";
        $account->nextBillDate = $entity->next_bill_date ?? "";

        $latestIpHistory = end($entity->ip_assignment_histories->entities) ?? null;
        $account->subnet = $latestIpHistory->subnet ?? "";
        $account->uniqueIdentifier = $latestIpHistory->unique_identifier ?? "";

        $childAccounts = implode(" || ", array_column($entity->child_accounts->entities, 'id'));
        $account->childAccounts = $childAccounts;

        $mailingAddress = $entity->mailingAddresses->entities[0] ?? null;
        $account->mailingAddressLine1 = $mailingAddress->line1 ?? "";
        $account->mailingAddressLine2 = $mailingAddress->line2 ?? "";
        $account->mailingAddressCity = $mailingAddress->city ?? "";
        $account->mailingAddressState = $mailingAddress->subdivision ?? "";
        $account->mailingAddressZip = $mailingAddress->zip ?? "";

        $physicalAddress = $entity->physicalAddresses->entities[0] ?? null;
        $account->physicalAddressLine1 = $physicalAddress->line1 ?? "";
        $account->physicalAddressLine2 = $physicalAddress->line2 ?? "";
        $account->physicalAddressCity = $physicalAddress->city ?? "";
        $account->physicalAddressState = $physicalAddress->subdivision ?? "";
        $account->physicalAddressZip = $physicalAddress->zip ?? "";

        $contacts = $entity->contacts->entities[0] ?? null;
        $account->contactName = $contacts->name ?? "";
        $account->contactUsername = $contacts->username ?? "";
        $account->contactEmail = $contacts->email_address ?? "";
        $account->contactPhoneNumber = $contacts->phone_numbers->entities[0]->number_formatted ?? "";

        $dtv = $entity->dtv->entities[0] ?? null;
        $account->dtvAccount = $dtv->dtv_acct ?? "";

        $ssid = $entity->ssid->entities[0] ?? null;
        $account->ssid = $ssid->ssid ?? "";

        $ssidSec = $entity->ssid_sec->entities[0] ?? null;
        $account->ssidSecret = $ssidSec->ssid_secret ?? "";

        $accounts[] = $account;
    }

    for ($i = 0; $i < $accountCount; $i++) {

        $account = $accounts[$i];

        echo "Account ID: $account->accountId<br>";
        echo "firstname: $account->accountName<br>";
        echo "Account Status ID: $account->accountStatusId<br>";
        echo "Account Type ID: $account->accountTypeId<br>";
        echo "Activation Date: $account->activationDate<br>";
        echo "Next Bill Date: $account->nextBillDate<br>";
        echo "Subnet: $account->subnet<br>";
        echo "Unique Identifier: $account->uniqueIdentifier<br>";
        echo "Child Accounts: $account->childAccounts<br>";
        echo "Mailing Address Line 1: $account->mailingAddressLine1<br>";
        echo "Mailing Address Line 2: $account->mailingAddressLine2<br>";
        echo "Mailing Address City: $account->mailingAddressCity<br>";
        echo "Mailing Address Zip: $account->mailingAddressZip<br>";
        echo "Mailing Address State: $account->mailingAddressState<br>";
        echo "Physical Address Line 1: $account->physicalAddressLine1<br>";
        echo "Physical Address Line 2: $account->physicalAddressLine2<br>";
        echo "Physical Address City: $account->physicalAddressCity<br>";
        echo "Physical Address Zip: $account->physicalAddressZip<br>";
        echo "Physical Address State: $account->physicalAddressState<br>";
        echo "Contact Name: $account->contactName<br>";
        echo "Contact Username: $account->contactUsername<br>";
        echo "Contact Email: $account->contactEmail<br>";
        echo "Contact Phone Number: $account->contactPhoneNumber<br>";
        echo "DTV Account: $account->dtvAccount<br>";
        echo "SSID: $account->ssid<br>";
        echo "SSID Secret: $account->ssidSecret<br>";
        echo "----------------------<br><br><br>";


        // 
        // 
        // 
        // 
        // 
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => '{
        "properties": {
            "sonar_account_id": "' . $account->accountId . '",
            "firstname": "' . $account->accountName . '",
            "lastname": "",
            "email": "' . $account->contactEmail . '",
            "sonar_account_status": ' . $account->accountStatusId . ',
            "sonar_account_type": "' . $account->accountTypeId . '",
            "sonar_activation_date": "' . $account->activationDate . '",
            "sonar_next_bill_date": "' . $account->nextBillDate . '",
            "ip_address": "' . $account->subnet . '",
            "mac_address": "' . $account->uniqueIdentifier . '",
            "child_account_id": "' . $account->childAccounts . '",
            "line1_mail_address": "' . $account->mailingAddressLine1 . '",
            "line2_mail_address": "' . $account->mailingAddressLine2  . '",
            "city_mail_address": "' . $account->mailingAddressCity . '",
            "state_mail_address": "' . $account->mailingAddressState . '",
            "zip_mail_address": "' . $account->mailingAddressZip . '",
            "dtv_acct": "' . $account->dtvAccount . '",
            "email_primary_contact": "' . $account->contactEmail . '",
            "line1_serviceable_address": "' . $account->physicalAddressLine1 . '",
            "line2_serviceable_address": "' . $account->physicalAddressLine2 . '",
            "city_serviceable_address": "' . $account->physicalAddressCity . '",
            "state_serviceable_address": "' . $account->physicalAddressState . '",
            "zip_serviceable_address": "' . $account->physicalAddressZip . '",
            "name_primary_contact": "' . $account->contactName . '",
            "parent_account_id": "' . $account->accountName . '",
            "phone_primary_contact": "' . $account->contactPhoneNumber . '",
            "ssid": "' . $account->ssid . '",
            "ssid_secrets": "' . $account->ssidSecret . '",
            "username_primary_contact": "' . $account->contactUsername . '"
        }
    }',
            CURLOPT_HTTPHEADER => array(
                'authorization: Bearer ' . token_hubspot(),
                'content-type: application/json'
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        file_put_contents('logs_sonar/s_create_acc_in_hs.log',  $response . "\n", FILE_APPEND);
    }
}

// **********************************
// Function For Account Details Sonar
function update_account_request($contact_id)
{
    global $sonar_account_id;
    $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIxIiwianRpIjoiNjU0NTcwNGZjNDUxOTdjNWZlM2ZmY2M0NjBkNjNiMjQ5NDA3OWJjOWE5N2NhNDEzZmFkYjc0MmY4YzJkMGNlYTY3NTZlYzlhMDA0ZTEwZjgiLCJpYXQiOjE2ODg5MzE5NzcuNTcxLCJuYmYiOjE2ODg5MzE5NzcuNTcxLCJleHAiOjIxNDU5MTY4MDAuMDcwNiwic3ViIjoiMyIsInNjb3BlcyI6W119.4zVGayiHtvV1vKho41OqRxJya5Es8UjM9oqMDkTpyLEa83euOas3_sgWvRkNbxhDedNBBbAZ3eL_ZlV73uvT5Y9FSIjE0aQSEJwdIKLVaeoMkGskx0LHgV9IOPFTRqBwFf0RYqLoLX9wKJvQha8gDo8LcscogPonVXCOjptnTjICNNC0QrdPKKZeib0vBvlG_vMt4930HC9fatBLcCSV9Zs8TtIspMh-85SI6bTEU898ew2uP0eJv6ijH_8EKv0KwN2glC_cG_6gS_N_SEr27abQLYPsXS-RwSThbcKaRGi83rz47IqrQO1tM-rB2SJJhubtMRnan_0RlIuMW_nSjvP6iOLeye-n5kfpeXs1bsFu8El-GgMvVoQKjFnaLlLWGzw-iH7UPKA1JAS0jF_c7DJVtuZ4YIliES2z3a8mHor0zKZ6vYv-bHD23NIg5PlwPV6oj6HoDJMxV6bIXlWuNGIPCukpoRaGuPh-1qIrGmpZBRTJqn9EVfmA2WEZxEWcMQzpr-AkP8j6BP371rZI4bnWol3L5l2GpOsbjx_hXrjTHAYW_IqCiXWJoQpy4sMi0lsY2_ARokpqlzTv-PChBctW9eX5UCjod09xpJJL0X3oLVCca_ugNCatZF1aeiT-MbJhIDW4gBYo5Coe7OE9QR-ATtrf57s1Juq72ahU5xI";

    $query = <<<GQL
query MyCoolQuery {
    accounts(id: $sonar_account_id) {
        entities {
            id
            name
            account_status_id
            account_type_id
            next_bill_date
            activation_date
            contacts (primary: true) {
                entities {
                    name
                    username
                    email_address
                    phone_numbers{
                        entities {
                            number_formatted
                        }
                    }                    
                }
            }

            mailingAddresses: addresses (type: MAILING ) {
                entities {
                    line1
                    line2
                    city
                    subdivision
                    zip
                }
            }

            physicalAddresses: addresses (type: PHYSICAL ) {
                entities {
                    line1
                    line2
                    city
                    subdivision
                    zip
                }
            }

            dtv: custom_field_data (id: 192) {
                entities {
                    value
                }
            }

            ssid: custom_field_data (id: 193) {
                entities {
                    value
                }
            }

            ssid_sec: custom_field_data (id: 194) {
                entities {
                    value
                }
            }

            child_accounts {
                entities {
                    name
                }
            }


            ip_assignment_histories (ipassignmenthistoryable_type: Account ) {
                entities {
                    subnet
                    unique_identifier
                    updated_at
                }
            }

            
        }
    }
}
GQL;



    $client = new GuzzleHttp\Client;
    $response = $client->post('https://qflix.sonar.software/api/graphql', [
        'headers' => [
            'Authorization' => "Bearer $token",
            'Accept' => 'application/json',
        ],
        'json' => [
            'query' => $query,
        ]
    ]);

    $decodedResponse = json_decode($response->getBody()->getContents(), false, JSON_PRETTY_PRINT);


    echo json_encode($decodedResponse) . "<br><br><br>";
    $data = $decodedResponse;


    $accountCount = count($data->data->accounts->entities);
    $accounts = array();



    // ACCOUNTS DETAILS
    for ($i = 0; $i < $accountCount; $i++) {
        $entity = $data->data->accounts->entities[$i];
        $account = new stdClass();

        $account->accountId = $entity->id ?? "";
        $account->accountName = $entity->name ?? "";
        $account->accountStatusId = $entity->account_status_id ?? "";
        $account->accountTypeId = $entity->account_type_id ?? "";
        $account->activationDate = $entity->activation_date ?? "";
        $account->nextBillDate = $entity->next_bill_date ?? "";

        $latestIpHistory = end($entity->ip_assignment_histories->entities) ?? null;
        $account->subnet = $latestIpHistory->subnet ?? "";
        $account->uniqueIdentifier = $latestIpHistory->unique_identifier ?? "";

        $childAccounts = implode(" || ", array_column($entity->child_accounts->entities, 'id'));
        $account->childAccounts = $childAccounts;

        $mailingAddress = $entity->mailingAddresses->entities[0] ?? null;
        $account->mailingAddressLine1 = $mailingAddress->line1 ?? "";
        $account->mailingAddressLine2 = $mailingAddress->line2 ?? "";
        $account->mailingAddressCity = $mailingAddress->city ?? "";
        $account->mailingAddressState = $mailingAddress->subdivision ?? "";
        $account->mailingAddressZip = $mailingAddress->zip ?? "";

        $physicalAddress = $entity->physicalAddresses->entities[0] ?? null;
        $account->physicalAddressLine1 = $physicalAddress->line1 ?? "";
        $account->physicalAddressLine2 = $physicalAddress->line2 ?? "";
        $account->physicalAddressCity = $physicalAddress->city ?? "";
        $account->physicalAddressState = $physicalAddress->subdivision ?? "";
        $account->physicalAddressZip = $physicalAddress->zip ?? "";

        $contacts = $entity->contacts->entities[0] ?? null;
        $account->contactName = $contacts->name ?? "";
        $account->contactUsername = $contacts->username ?? "";
        $account->contactEmail = $contacts->email_address ?? "";
        $account->contactPhoneNumber = $contacts->phone_numbers->entities[0]->number_formatted ?? "";

        $dtv = $entity->dtv->entities[0] ?? null;
        $account->dtvAccount = $dtv->dtv_acct ?? "";

        $ssid = $entity->ssid->entities[0] ?? null;
        $account->ssid = $ssid->ssid ?? "";

        $ssidSec = $entity->ssid_sec->entities[0] ?? null;
        $account->ssidSecret = $ssidSec->ssid_secret ?? "";

        $accounts[] = $account;
    }

    for ($i = 0; $i < $accountCount; $i++) {

        $account = $accounts[$i];

        echo "Account ID: $account->accountId<br>";
        echo "firstname: $account->accountName<br>";
        echo "Account Status ID: $account->accountStatusId<br>";
        echo "Account Type ID: $account->accountTypeId<br>";
        echo "Activation Date: $account->activationDate<br>";
        echo "Next Bill Date: $account->nextBillDate<br>";
        echo "Subnet: $account->subnet<br>";
        echo "Unique Identifier: $account->uniqueIdentifier<br>";
        echo "Child Accounts: $account->childAccounts<br>";
        echo "Mailing Address Line 1: $account->mailingAddressLine1<br>";
        echo "Mailing Address Line 2: $account->mailingAddressLine2<br>";
        echo "Mailing Address City: $account->mailingAddressCity<br>";
        echo "Mailing Address Zip: $account->mailingAddressZip<br>";
        echo "Mailing Address State: $account->mailingAddressState<br>";
        echo "Physical Address Line 1: $account->physicalAddressLine1<br>";
        echo "Physical Address Line 2: $account->physicalAddressLine2<br>";
        echo "Physical Address City: $account->physicalAddressCity<br>";
        echo "Physical Address Zip: $account->physicalAddressZip<br>";
        echo "Physical Address State: $account->physicalAddressState<br>";
        echo "Contact Name: $account->contactName<br>";
        echo "Contact Username: $account->contactUsername<br>";
        echo "Contact Email: $account->contactEmail<br>";
        echo "Contact Phone Number: $account->contactPhoneNumber<br>";
        echo "DTV Account: $account->dtvAccount<br>";
        echo "SSID: $account->ssid<br>";
        echo "SSID Secret: $account->ssidSecret<br>";
        echo "----------------------<br><br><br>";


        // 
        // 
        // 
        // 

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.hubapi.com/crm/v3/objects/contacts/' . $contact_id,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'PATCH',
            CURLOPT_POSTFIELDS => '{
  "properties": {
    "sonar_account_id": "' . $account->accountId . '",
    "firstname": "' . $account->accountName . '",
    "lastname": "",
    "email": "' . $account->contactEmail . '",
    "sonar_account_status": ' . $account->accountStatusId . ',
    "sonar_account_type": "' . $account->accountTypeId . '",
    "sonar_activation_date": "' . $account->activationDate . '",
    "sonar_next_bill_date": "' . $account->nextBillDate . '",
    "ip_address": "' . $account->subnet . '",
    "mac_address": "' . $account->uniqueIdentifier . '",
    "child_account_id": "' . $account->childAccounts . '",
    "line1_mail_address": "' . $account->mailingAddressLine1 . '",
    "line2_mail_address": "' . $account->mailingAddressLine2  . '",
    "city_mail_address": "' . $account->mailingAddressCity . '",
    "state_mail_address": "' . $account->mailingAddressState . '",
    "zip_mail_address": "' . $account->mailingAddressZip . '",
    "dtv_acct": "' . $account->dtvAccount . '",
    "email_primary_contact": "' . $account->contactEmail . '",
    "line1_serviceable_address": "' . $account->physicalAddressLine1 . '",
    "line2_serviceable_address": "' . $account->physicalAddressLine2 . '",
    "city_serviceable_address": "' . $account->physicalAddressCity . '",
    "state_serviceable_address": "' . $account->physicalAddressState . '",
    "zip_serviceable_address": "' . $account->physicalAddressZip . '",
    "name_primary_contact": "' . $account->contactName . '",
    "parent_account_id": "' . $account->accountName . '",
    "phone_primary_contact": "' . $account->contactPhoneNumber . '",
    "ssid": "' . $account->ssid . '",
    "ssid_secrets": "' . $account->ssidSecret . '",
    "username_primary_contact": "' . $account->contactUsername . '"
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
        file_put_contents('logs_sonar/s_update_acc_in_hs.log',  $response . "\n\n", FILE_APPEND);

        // echo $response;
    }
}


// **********************************
// Function, Account Create ( Event ) 
function account_created()
{

    // Global Variables
    global $sonar_account_id;

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
                "value": "' . $sonar_account_id . '"
            }
        ]
    }',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . token_hubspot()
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);


    if (json_decode($response)->total == 1) {
        // 
        $contact_id  = json_decode($response)->results[0]->id;
        update_account_request($contact_id);
    } else {
        // Request to Create Contact
        create_account_request();
    }
}


// **********************************
// Function, Account attached ( Event ) 
function account_updated_attached()
{
    // Global Variables
    global $sonar_account_id;
    global $firstname;

    if (1) {
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
                       "value": "' . $sonar_account_id . '"
                   }
               ]
           }',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . token_hubspot()
            ),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        // 

        if (json_decode($response)->total != 1) {
            $emailSon = accountEmailSonar();

            if ($emailSon != null && $emailSon != "") {

                // 
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
               "filters": [{
                       "propertyName": "email",
                       "operator": "EQ",
                       "value": "' . $emailSon . '" } ]
               }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . token_hubspot()
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                // 
            } else {

                // Request to Search and Get contact ID
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
               "query": "' . $firstname . '"
               }',
                    CURLOPT_HTTPHEADER => array(
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . token_hubspot()
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
            }
        }
        // 
        // 
        // If 1 Contact Found Then Update it
        if (json_decode($response)->total == 1) {
            // 
            $contact_id  = json_decode($response)->results[0]->id;
            update_account_request($contact_id);
            // 
        } else if (json_decode($response)->total == 0) {
            create_account_request();
        } else {
            file_put_contents('logs_sonar/s_issue_account.log', $sonar_account_id . "\n", FILE_APPEND);
        }
    }
}
