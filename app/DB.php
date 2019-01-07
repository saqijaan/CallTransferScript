<?php
namespace App;

class DB{

    private $file;

    public $queueSid        ;
    public $isAgentFree     ;
    public $connectedCallSid;
    public $caller;


    public function __construct($file=null){
        if ($file==null)
            $this->file = 'db.json';
        else
            $this->file=$file;

        $data = json_decode(file_get_contents($this->file));

        if ($data!=null){
            $this->queueSid         = $data->queueSid;
            $this->isAgentFree      = $data->isAgentFree;
            $this->connectedCallSid = $data->connectedCallSid;
            $this->caller           = $data->caller;
        }
    }

    public function save(){
        file_put_contents($this->file,json_encode($this));
    }

}