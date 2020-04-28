<?php
//require (APPPATH.'/libraries/REST_Controller.php');
use Restserver \Libraries\REST_Controller ;

Class Customer extends REST_Controller{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('CustomerModel');
        $this->load->library('form_validation');
    }

    public function index_get(){
        return $this->returnData($this->db->get('customers')->result(), false);
    }

    public function index_post($id_customer = null){
        $validation = $this->form_validation;
        $rule = $this->CustomerModel->rules();
        /*if($id == null){
            array_push($rule, [
                'field' => 'password',
                'label' => 'password',
                'rules' => 'required'
            ],
            [
                'field' => 'email',
                'label' => 'email',
                'rules' => 'required|valid_email|is_unique[users.email]'
            ]);
        }
        else{
            array_push($rule, 
            [
                'field' => 'email',
                'label' => 'email',
                'rules' => 'required|valid_email'
            ]);
            }*/
        $validation->set_rules($rule);
        if(!$validation->run()){
            return $this->returnData($this->form_validation->error_array(), true);
        }
        $user = new CustomerData();
        $user->id_customer = $this->post('id_customer');
        $user->nama_customer = $this->post('nama_customer');
        $user->alamat_customer = $this->post('alamat_customer');
        $user->tglLahir_customer = $this->post('tglLahir_customer');
        $user->noTelp_customer = $this->post('noTelp_customer');
        $user->updateLog_by = $this->post('updateLog_by');


        if($id_customer == null){
            $response = $this->CustomerModel->store($user);
        }
        else{
            $response = $this->CustomerModel->update($user, $id_customer);
        }
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id_customer = null){
        if($id_customer == null){
            return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->CustomerModel->destroy($id_customer);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function returnData($msg, $error){
        $response['error'] = $error;
        $response['message'] = $msg;
        return $this->response($response);
    }
}

Class CustomerData{
    public $id_customer;
    public $nama_customer;
    public $alamat_customer;
    public $tglLahir_customer;
    public $noTelp_customer;
    public $updateLog_by;
    public $createLog_at;
    public $updateLog_at;
    public $deleteLog_at;
}

