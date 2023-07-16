<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.0/css/all.min.css" integrity="sha512-xh6O/CkQoPOWDdYTDqeRdPCVd1SpvCA9XXcUnZS2FmJNp1coAFzvtCN9BmamE+4aHK8yyUHUSCcJHgXloTyT2A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<?php


// 
//  this code is used to authencticate app and install it in out selected account
// 
$code = $_GET['code'];
if ($code != "") {

  $CLIENT_ID = "3e067572-9d52-469b-a8bb-82467a18b84e";
  $CLIENT_SECRET = "0e51f06a-0e1a-47ef-99ae-661e0bbab132";
  $REDIRECT_URI = 'https://emeraldsoft.uk/projects/ali/test/sonar';


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
    CURLOPT_POSTFIELDS => 'grant_type=authorization_code&client_id=' . $CLIENT_ID . '&client_secret=' . $CLIENT_SECRET . '&redirect_uri=' . $REDIRECT_URI . '&code=' . $code . '',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/x-www-form-urlencoded'
    ),
  ));

  $response = curl_exec($curl);
  curl_close($curl);

  file_put_contents('logs/01I_responseAfterHubSpotAuthorization.log', $response . "\n", FILE_APPEND);


  if (json_decode($response)->access_token) {
    echo "<div style='width:100%; height:100vh; background-image: linear-gradient(rgb(249,241,2), yellow);'><h1 style='text-align: center; width:fit-content; margin:auto auto; transform: translateY(30vh);'><i class='fa fa-check fa-3x' aria-hidden='true'></i> <br> You have successfully installed the app <br> Now you now close this window<h1></div>";
  } else {
    echo "<div style='width:100%; height:100vh; background-image: linear-gradient(rgb(249,241,2), yellow);'><h1 style='text-align: center; width:fit-content; margin:auto auto; transform: translateY(30vh);'><i class='fa fa-check fa-3x' aria-hidden='true'></i> <br> There is some error in installing app <br> You can try it again <br> For again installation , please head to <a href='https://app-eu1.hubspot.com/oauth/authorize?client_id=8cb953ba-e9ee-4c0b-8597-7f586911b081&redirect_uri=https://www.resista.it/hubspot/hubspot.php&scope=oauth%20forms%20crm.objects.contacts.read%20crm.objects.contacts.write%20crm.schemas.contacts.read%20crm.schemas.contacts.write'>Install App for Webinar!</a><h1></div>";
  }
}






?>