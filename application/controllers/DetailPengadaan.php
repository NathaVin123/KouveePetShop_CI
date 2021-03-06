<?php
//require (APPPATH.'/libraries/REST_Controller.php');
use Restserver \Libraries\REST_Controller ;

Class DetailPengadaan extends REST_Controller{
    public function __construct(){
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->model('DetailPengadaanModel');
        $this->load->library('form_validation');
    }

    public function getWithJoin_get() {
        $this->db->select('detailpengadaans.id_detailpengadaan,detailpengadaans.id_produk,detailpengadaans.no_order,detailpengadaans.jumlah_stok_pengadaan,detailpengadaans.harga, detailpengadaans.total_harga,
                         produks.nama_produk "nama_produk"');
        $this->db->from('detailpengadaans');
        $this->db->join('produks', 'detailpengadaans.id_produk = produks.id_produk');
        $this->db->order_by('detailpengadaans.id_detailpengadaan ASC');
        return $this->returnData($this->db->get()->result(), false);
    }
    public function index_get(){
        return $this->returnData($this->db->get('detailpengadaans')->result(), false);
    }

    public function getByIdPengadaan_get($id=null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        return $this->returnData($this->db->get_where('detailpengadaans', ["no_order" => $id])->result(), false);
    }

    public function search_get($id=null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        return $this->returnData($this->db->get_where('detailpengadaans', ["id_detailpengadaan" => $id])->row(), false);
    }

    public function index_post(){
        $validation = $this->form_validation;
        $rule = $this->DetailPengadaanModel->rules();
        array_push($rule,
            [
                'field' => 'no_order',
                'label' => 'no_order',
                'rules' => 'required'
            ],
            [
                'field' => 'id_produk',
                'label' => 'id_produk',
                'rules' => 'required'
            ],
            [
                'field' => 'jumlah_stok_pengadaan',
                'label' => 'jumlah_stok_pengadaan',
                'rules' => 'required'
            ],
            [
                'field' => 'harga',
                'label' => 'harga',
                'rules' => 'required'
            ]
           
        );
        $validation->set_rules($rule);
		if (!$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
        }

        $transaksi = new DetailPengadaanData();
        $transaksi->no_order = $this->post('no_order');
        $transaksi->id_produk = $this->post('id_produk');
        $transaksi->jumlah_stok_pengadaan = $this->post('jumlah_stok_pengadaan');
        $transaksi->harga = $this->post('harga');
        $transaksi->total_harga = $this->post('total_harga');

        $response = $this->DetailPengadaanModel->store($transaksi);
        return $this->returnData($response['msg'], $response['error']);
    }
    
    public function insertMultiple_post(){
        $data = $this->post('detailpengadaans');
        //if($id == null){
        $response = $this->DetailPengadaanModel->storeMultiple($data);
        //}
        return $this->returnData($response['msg'], $response['error']);
    }

    public function update_post($id = null){
        $validation = $this->form_validation;
        $rule = $this->DetailPengadaanModel->rules();
        array_push($rule,
            [
                'field' => 'id_produk',
                'label' => 'id_produk',
                'rules' => 'required'
            ],
            [
                'field' => 'jumlah_stok_pengadaan',
                'label' => 'jumlah_stok_pengadaan',
                'rules' => 'required'
            ],
            [
                'field' => 'harga',
                'label' => 'harga',
                'rules' => 'required'
            ]
        );
        $validation->set_rules($rule);
		if (!$validation->run()) {
			return $this->returnData($this->form_validation->error_array(), true);
        }

        $transaksi = new DetailPengadaanData();
        $transaksi->id_produk = $this->post('id_produk');
        $transaksi->jumlah_stok_pengadaan = $this->post('jumlah_stok_pengadaan');
        $transaksi->harga = $this->post('harga');
        $transaksi->total_harga = $this->post('total_harga');
        
        if($id == null){
            return $this->returnData('Parameter ID tidak ditemukan', true);
        }
        $response = $this->DetailPengadaanModel->update($transaksi,$id);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function updateMultiple_post(){
        $data = $this->post('detailpengadaans');
        //if($id == null){
        $response = $this->DetailPengadaanModel->updateMultiple($data);
        //}
        return $this->returnData($response['msg'], $response['error']);
    }

    public function index_delete($id = null){
        if($id == null){
			return $this->returnData('Parameter Id Tidak Ditemukan', true);
        }
        $response = $this->DetailPengadaanModel->destroy($id);
        return $this->returnData($response['msg'], $response['error']);
    }

    public function deleteMultiple_post(){
        $data = $this->post('id_detailpengadaan');
        //if($id == null){
        $response = $this->DetailPengadaanModel->deleteMultiple($data);
        //}
        return $this->returnData($response['msg'], $response['error']);
    }

    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}

Class DetailPengadaanData{
    public $id_detailpengadaan ;
    public $no_order;
    public $id_produk;
    public $jml_stok_pengadaan;
    public $status_pengadaan_produk;
    public $harga;
    public $total_harga;
}