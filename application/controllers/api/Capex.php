<?php
defined('BASEPATH') or exit('No direct script acess allowed');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Headers: origin, content-type, accept');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS, DELETE, PUT');
require(APPPATH . 'libraries/RestController.php');
require(APPPATH . 'libraries/Format.php');

use chriskacerguis\RestServer\RestController;

class Capex extends RestController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('capexmodel');
    }
    public function capex_get()
    {
        $capexID = $this->get('capexID');
        $division = $this->get('division');
        $capexStatusID = $this->get('capexStatusID');
        $status = $this->get('status');
        $flowID = $this->get('flowID');
        $year = $this->get('year');

        //$capexStatus = $this->get('capexStatusID');

        if ($status == "all") {   //1.	Show Capex All   //แสดงข้อมูล capex ทั้งหมด
            $result = $this->capexmodel->getCapex();
        } else if ($status == "one") {   //2.	Show Capex One // แสดงข้อมูล capex เฉพาะ
            $arr = array(
                "capexID" => $capexID,
            );
            $result = $this->capexmodel->getCapexSelect($arr);
        } else if ($status == "division") {  //9.	show Capex for each Division  // แสดงข้อมูล Capex เฉพาะแผนก
            $arr = array(
                "division" => $division,
            );
            $arr = "division = '$division' AND capexNo != ''";
            $result = $this->capexmodel->getCapexSelect($arr);
        } else if ($status == "approval") {    //5.	show Capex Approv  // แสดงข้อมูล capex ที่ approv แล้ว
            $arr = array(
                "capexStatusID" => $capexStatusID
            );
            $result = $this->capexmodel->getCapexSelect($arr);
        } else if ($status == "flow") {
            $arr = array(
                "capexFlow.flowID" => $flowID
            );
            $result = $this->capexmodel->getCapexApproval($arr);
        } else if ($status == "status") {
            $where = "capex.capexStatusID  = '$capexStatusID' AND capex.division = '$division'";
            $result = $this->capexmodel->getCapexSelect($where);
        } else if ($status == "year") {
            $where = "capex.capexYear  = '$year' AND capex.division = '$division' AND capexNo != ''";
            $result = $this->capexmodel->getCapexApproval($where);
        } else if ($status == "capexcenter") {
            if ($year != "") {
                $where = "capex.capexYear  = '$year' AND capexNo != ''";
            } else {
                $where = "capexNo != ''";
            }

            $result = $this->capexmodel->getCapexApproval($where);
        } else {   // status ไม่ตรงกับเงื่อนไข
            $result = array(
                "Status" => "This status is not available"
            );
        }
        $this->response($result, 200);
    }

    public function capex_put()    //4.	edit Capex // แก้ไข capex   
    {
        $capexID            = $this->put('capexID');
        $capexNo            = $this->put('capexNo');
        $capexName          = $this->put('capexName');
        $classificationID   = $this->put('classificationID');
        $priorityID         = $this->put('priorityID');
        $division           = $this->put('division');
        $capexYear          = $this->put('capexYear');
        $totalPlan          = $this->put('totalPlan');
        $h1Plan             = $this->put('h1Plan');
        $h2Plan             = $this->put('h2Plan');
        $goal               = $this->put('goal');
        $mainComponents     = $this->put('mainComponents');
        $expectation        = $this->put('expectation');
        $capexStatusID      = $this->put('capexStatusID');

        $arr = array(
            "capexName"         => $capexName,
            "capexNo"           => $capexNo,
            "classificationID"  => $classificationID,
            "priorityID"        => $priorityID,
            "division"          => $division,
            "capexYear"         => $capexYear,
            "totalPlan"         => $totalPlan,
            "h1Plan"            => $h1Plan,
            "h2Plan"            => $h2Plan,
            "goal"              => $goal,
            "mainComponents"    => $mainComponents,
            "expectation"       => $expectation,
            "capexStatusID"     => $capexStatusID

        );
        $where = "capexID = $capexID";

        $this->capexmodel->updateCapex($arr, $where);
        $result = array(
            "status" => "success",
            "detail" => "update capex completed"
        );
        $this->response($result, 200);
        //$this->response($arr, 200);
    }
    public function capex_post()    //3.	create Capex  // สร้าง capex
    {
        $capexName          = $this->post('capexName');
        $capexNo            = $this->post('capexNo');
        $classificationID   = $this->post('classificationID');
        $priorityID         = $this->post('priorityID');
        $division           = $this->post('division');
        $capexYear          = $this->post('capexYear');
        $totalPlan          = $this->post('totalPlan');
        $h1Plan             = $this->post('h1Plan');
        $h2Plan             = $this->post('h2Plan');
        $goal               = $this->post('goal');
        $mainComponents     = $this->post('mainComponents');
        $expectation        = $this->post('expectation');
        $capexStatusID      = $this->post('capexStatusID');
        $type               = $this->post('type');

        $id = 0;

        $arr = array(
            "capexName"         => $capexName,
            "capexNo"           => $capexNo,
            "classificationID"  => $classificationID,
            "priorityID"        => $priorityID,
            "division"          => $division,
            "capexYear"         => $capexYear,
            "totalPlan"         => $totalPlan,
            "h1Plan"            => $h1Plan,
            "h2Plan"            => $h2Plan,
            "goal"              => $goal,
            "mainComponents"    => $mainComponents,
            "expectation"       => $expectation,
            "capexStatusID"     => $capexStatusID,
            "capexType"         => $type
        );
        $id = $this->capexmodel->insertCapex($arr);

        if ($id != 0) {
            $result = array(
                "status" => "success",
                "detail" => "create capex success",
                "capexID" => $id
            );
        } else {
            $result = array(
                "status" => "error",
                "detail" => "can' not create capex"
            );
        }
        $this->response($result, 200);
    }
    public function capex_delete()
    {
        $capexID = $this->delete('capexID');
        $where = "capexID = " . $capexID;
        $this->queuecapcapexmodelexmodel->deleteCapex($where);

        $result = array(
            "status" => "success",
            "detail" => "delete Capex completed"
        );
        $this->response($result, 200);
    }

    public function capexstatus_put()
    {
        $capexID = $this->put('capexID');
        $status = $this->put('status');

        $arr = array(
            'capexStatusID' => $status
        );

        $where = "capexID = '$capexID'";

        $this->capexmodel->updateCapex($arr, $where);
        $result = array(
            "status" => "success",
            "detail" => "update capex completed"
        );
        $this->response($result, 200);
    }

    public function info_get()
    {
        $division = $this->get('division');

        // $total = 0;
        // $inprogress = 0;
        // $success  = 0;
        // $defer = 0;
        $total = 0;

        $data = $this->capexmodel->getInfo("capex.division = '$division'");

        $result = array(
            'total' => $total,
            'data'  => $data
        );
        $this->response($result, 200);
    }

    public function count_get()
    {
        $year = $this->get('year');

        $where = "capexYear = '$year'";
        $result = $this->capexmodel->getCapexCount($where);
        $this->response($result, 200);
    }
}


/*
  //    $result = array(
    //        "status" => "success"
    //    );
       

        // foreach ($capex->result() as $row){
        //     echo $row->capexName."<br>";
        // }
// public function capexID_get() //2.	Show Capex One // แสดงข้อมูล capex เฉพาะ
    // {      
    //     $capexID = $this->get('capexID');
    //     $arr = array(
    //         "capexID" => $capexID,   
    //     );
    //     $result = $this->capexmodel->getCapexOne($arr);
    //     $this->response($result,200);
        
    // }
    // public function capexDivision_get($division) //9.	show Capex for each Division  // แสดงข้อมูล Capex เฉพาะแผนก
    // {      
    //     $result = $this->CapexModel->getCapex();
    //     $this->response($result,200);
        
    // }
// $arr = array(
        //     "capexName" => "Test1"
        // );
        // $where = "capexID = 1";
        // $this->CapexMpdel->updateCapex($arr,$where);
{
     "capexName": "Test4",
     "$capexNo" : "",
    "classificationID": 101, 
    "priorityID": 1,
    "division": "IT",   
    "capexYear":"400000",     
    "totalPlan": 400000 , 
    "h1Plan": 200000,     
    "h2Plan": 200000,
    "goal": "goal",
    "mainComponents": "main",
    "expectation": "exp",
	"capexStatusID": 2

}


        $name = $this->post('name');
        echo "Hello".$name;
*/
