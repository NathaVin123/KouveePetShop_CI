<?php
//require (APPPATH.'/libraries/REST_Controller.php');
use Restserver \Libraries\REST_Controller ;

Class DetailLayanan extends REST_Controller{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('DetailLayananModel');
        $this->load->library('form_validation');
    }

    public function index_get(){
        return $this->returnData($this->db->get('detaillayanans')->result(), false);
    }

    public function index_post($id_detail_layanan = null){
        $validation = $this->form_validation;
        $rule = $this->DetailLayananModel->rules();
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
        $user = new DetailLayananData();
        $user->kode_layanan = $this->post('kode_layanan');
        $user->tgl_transaksi_layanan = $this->post('tgl_transaksi_layanan');
        $user->jml_transaksi_layanan = $this->post('jml_transaksi_layanan');
        $user->subtotal = $this->post('subtotal');
        if($id_detail_layanan == null){
            $response = $this->DetailLayananModel->store($user);
        }
        else{
            $response = $this->DetailLayananModel->update($user, $id_detail_layanan);
        }
        return $this->returnData($response['msg'], $response['error']);
    }


    public function index_delete($id_detail_layanan = null){
        if($id_detail_layanan == null){
            return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->DetailLayananModel->destroy($iid_detail_layanan);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function returnData($msg, $error){
        $response['error'] = $error;
        $response['message'] = $msg;
        return $this->response($response);
    }
}

Class DetailLayananData{
    public $kode_layanan;
    public $tgl_transaksi_layanan;
    public $jml_transaksi_layanan;
    public $subtotal;
}