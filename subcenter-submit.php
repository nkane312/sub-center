<?php
ini_set('display_errors', 1);

$headers = array();
$headers[] = 'x-functions-key: vS1cVk5ANnfgMPpsvhY1OSrxES7CUwa6qRYUDMqQ42ysyg96jGggMg==';
$headers[] = 'Cache-Control: no-cache';
$headers[] = 'Content-Type: application/json';

if (isset($_POST)) {
    $postdata = file_get_contents('php://input');
    $request = json_decode($postdata);
    $ch = curl_init();

    if ($request->Type == "create") {
        $recaptchaToken = $request->RecaptchaToken;

        if (isset($recaptchaToken)) {
            $secret = '6Lc1-xkUAAAAAHxP6V-VA1o4sDqyjwnsnsQEyHC8';
            $remoteip = $_SERVER['REMOTE_ADDR'];
            $url = 'https://www.google.com/recaptcha/api/siteverify';

            $curl = curl_init();
      
            $recaptchaArray = array(
                'secret' => $secret,
                'response' => $recaptchaToken,
                'remoteip' => $remoteip
            );

            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $recaptchaArray);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            
            $curlData = curl_exec($curl);
            curl_close($curl);
            
            
        
            if ($curlData["success"]){
                $ch = curl_init();

                $endpoint = 'https://anannet.azurewebsites.net/api/CreateSubscription';
                $dataArray = array(
                    "EmailAddress"=>$request->EmailAddress,
                    "EmailId"=>$request->EmailId,
                    "FirstName"=>$request->FirstName,
                    "LastName"=>$request->LastName,
                    "InteractionsRecord"=>$request->InteractionsRecord,
                    "SubscriptionList"=>$request->SubscriptionList
                );

                $subData = json_encode($dataArray);

                curl_setopt($ch, CURLOPT_URL, $endpoint);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS,$subData);  //Post Fields
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $server_output_obj = curl_exec ($ch);

                if ($server_output_obj === 'true') {
                    echo 'created';
                } 

                curl_close ($ch);
            } else {
                //fwrite($myfile, "\n Recaptcha Verification Failure! ".PHP_EOL);
                echo(json_encode(array("Success"=>"false")));
            }
        } else {
            echo(json_encode(array("Success"=>"false")));
        } //  end (isset($recaptchaToken))
    } else {
        if ($request->Type == "update") {
            $endpoint = 'https://anannet.azurewebsites.net/api/UpdateSubscription';
            $dataArray = array(
                "EmailId"=>$request->EmailId,
                "SubscriberKey"=>$request->SubscriberKey,
                "SubscriptionList"=>$request->SubscriptionList
            );
            $subData = json_encode($dataArray);
        } else if ($request->Type == "unsubscribe") {
            $endpoint = 'https://anannet.azurewebsites.net/api/UpdateDoNotEmail';
            $dataArray = array(
                "SubscriberKey"=>$request->SubscriberKey,
                "EmailId"=>$request->EmailId
            );
            $subData = json_encode($dataArray);
        } else {
            $endpoint = 'https://anannet.azurewebsites.net/api/GetSubscription';
            $subData = $request->SubscriberKey; 
        }

        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $subData);  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $server_output_obj = curl_exec ($ch);

        echo $server_output_obj;

        curl_close ($ch);


    } 
} else {
    echo(json_encode(array("Success"=>"false")));
}// End of POST[]
?>