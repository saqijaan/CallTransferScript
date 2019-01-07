<?php
namespace App;
use Twilio\Rest\Client;
use Twilio\TwiML\VoiceResponse;
use Twilio\Twiml;

class TwilioHelper{

    private $client;
    public function __construct(){
        $this->client = new Client(
            env('CLIENT_SID'),
            env('AUTH_TOKEN')
        );
    }
    
    public function enqueueCall($waiturl){
        $response = new VoiceResponse();
        $response->enqueue('waiting', ['waitUrl' => $waiturl]);
        return $response;
    }

    public function dialAgent($agentNumber,$from,$action){
        $call = $this->client->calls->create(
            $agentNumber,
            $from,
            array(
                "timeout"               => 15,
                "url"                   => url('?method=callConnected'),
                'statusCallback'        => $action,
                "statusCallbackEvent"   => array("completed")
                )
        );
        return $call;
    }

    public function connectAgent(){
        $response = new Twiml();
        $dial = $response->dial();
        $dial->queue("waiting");
        $response->redirect();
        return $response;
    }

    public function wait($mp3,$inputCaptureUrl){
        $response = new VoiceResponse();
        $gather = $response->gather(['numDigits'=> 1, 'action' => $inputCaptureUrl,'method'=>"POST"]);
        $gather->play($mp3, ['loop' => 1]);
        return $response;
    }
    
    public function sendAgentSms(array $data){
        $to     = $data['to'];
        $from   = $data['from'];
        $caller = $data['caller'];

        $q      = $data['q'];
        $cid    = $data['cid'];

        // Prepare Sms body to be sent to Agent
        date_default_timezone_set('America/New_York');
        $time = date('Y-m-d H:i:s');

        $body   =  "The number \"$caller\" Called you at $time";

        $message = $this->client->messages
                  ->create($to, // to
                    array("from" => $from, "body" => $body)
                  );

        $this->removeCallerFromQueue($q,$cid);

        return $message;
    }

    public function removeCallerFromQueue($queueSid,$callSid){
        $response = $this->client->queues($queueSid)
                 ->members($callSid)
                 ->update(url("?method=hangUp"), "GET");

        return $response;
    }

    public function getQueSize(){
        $queues = $this->client
                        ->queues
                        ->read();
        // foreach($queues as $queue){
        //     if ($queue->friendlyName === 'waiting'){
        //         return $queue->currentSize;
        //     }
        // }
        if (count($queues) > 0){
            return $queues[0]->currentSize;
        }
        return 0;
    }

    public function response($msg){
        $response = new TwiML;
        $response->say($msg);
        return $response;
    }

    public function hangUp($msg= null,$file=null){
        $response = new TwiML;
        if ($msg!=null)
            $response->say($msg);

        if($file!=null)
            $response->play($file);

        $response->hangUp();
        return $response;
    }
}