<?php
require __DIR__ . '/vendor/autoload.php';

use App\TwilioHelper;
use App\DB;
use Twilio\Twiml;

$twilio =new TwilioHelper;

if (isset($_GET['method'])){
    call_user_func($_GET['method'],$twilio);
}else
    exit($twilio->response("Invalid Method"));


// This is first method which twilio hits when any Caller Calls
function incomming($twilio){

    $response = $twilio->EnqueueCall(url('?method=wait'));
    $db= new DB;
    if ($db->isAgentFree)
        $twilio->dialAgent(env('AGENT_NUMBER'),env('TWILIO_NUMBER'),url('?method=dialAction'));
    exit($response);
}

// This method is hit when Callback action called on agent Dialing
function dialAction($twilio){

    // Check if Call status is Sent
    if (isset($_POST['CallStatus'])){
        $status = $_POST['CallStatus'];

        // If call status sent Check its type
        switch($status){
            case "busy": 
            case "failed": 
            case "no-answer":{

                // If Buyers does not answers Call again and again until no caller available in the queue
                if ($twilio->getQueSize()>0){
                    $twilio->dialAgent(env('AGENT_NUMBER'),env('TWILIO_NUMBER'),url('?method=dialAction'));
                    exit($twilio->response("Dialing Agent for next caller"));
                }
                // Else skip swith to next Instruction
                else{
                    exit(
                        $twilio->response('No More callers')
                    );
                }
            }
            break;
        }
    }

    // if no Condition matches than Exit with no Callers Message
    exit(
        $twilio->response('')
    );
}

// This Method is called when Twilio hits StatusCallback Url of Number
function callConnected($twilio){
    //  If Agent Picked Up the Call. Confirm if there is any 
    //  caller in the queue than connect else move forward
    $db= new DB;

    $status = $_REQUEST['CallStatus'];
    
    if ($twilio->getQueSize()>0 && $db->isAgentFree )
    {
        $db->isAgentFree = false;
        $db->save();
        exit(
            $twilio->connectAgent()
        );
    }else {
        $db->isAgentFree = true;
        $db->save();
        // Edit if no caller in queue
        exit(
            $twilio->response('')
        );
    }
}

// This method is called when wait url from queue is called
function wait($twilio){
    $queue      =   $_POST['QueueSid'];
    $callSid    =   $_POST['CallSid'];
    $caller     =   $_POST['Caller'];

    exit( $twilio->wait(
        url('assets/audio/Roviin_IVR_greeting.mp3'),                                     // Mp3 File Url
        url("?method=captureInput&q=$queue&cid=$callSid&caller=$caller")                // Callback Url for Capturing User Input,
        ) 
    );
}

// This method is hit by twilio when user inputs something from Dialpad
function captureInput($twilio){
    $queue      =   $_GET["q"];
    $callSid    =   $_GET["cid"];
    $caller     =   $_GET['caller'];

    if (isset($_POST['Digits'])){

        if ($_POST['Digits']==='1')
        {
            $data = [
                "to"        =>    env('AGENT_NUMBER'),
                "from"      =>    env('TWILIO_NUMBER'),
                "q"         =>    $queue,
                "cid"       =>    $callSid,
                'caller'    =>    $caller,
            ];
            exit(
                $twilio->sendAgentSms( $data)
            );
        }
    }
    $response = new TwiML;
    $response->redirect(url('?method=wait'), ['method' => 'POST']);
    exit($response);
}

// Hangs Up the Caller if He Inputs Something
function hangUp($twilio){
    exit(
        $twilio->hangUp(null,url('assets/audio/Roviin_IVR_place_saved.mp3'))
    );
}