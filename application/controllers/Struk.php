<?php
//require (APPPATH.'/libraries/REST_Controller.php');
use Restserver \Libraries\REST_Controller ;

Class Struk extends REST_Controller{
    function __construct() {
        header('Access-Control-Allow-Origin: *');
        header("Access-Control-Allow-Methods: GET, OPTIONS, POST, DELETE");
        header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
        parent::__construct();
        $this->load->library('pdf');
        include_once APPPATH . '/third_party/fpdf/fpdf.php';
    }
    
    function transaksiLayanan_get($param){
        // $this->load->helper('directory'); //load directory helper
        $dir = "controllers/PDF/"; // Your Path to folder
        // $map = directory_map($dir); /* This function reads the directory path specified in the first parameter and builds an array representation of it and all its contained files. */
        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();
    
        $dataTransaksi = null;
        $dataDetailTransaksi = null;

        $nama_kasir = "-";
        $nama_customer_service = "-";
        $nama_jenis_hewan = "-";
        $nama_hewan = "Guest";
        $nama_pelanggan = "Guest";
        $no_telp = "-";

        $resultTransaksi = $this->db->get_where('transaksipenjualanlayanans', ["kode_penjualan_layanan" => $param]);
        if($resultTransaksi->num_rows()!=0){
            $dataTransaksi = $resultTransaksi->row();
            $dataDetailTransaksi = $this->db->get_where('detailtransaksilayanans', ["kode_penjualan_layanan" => $param])->result();
            
            
            if($dataTransaksi->id_kasir!=null){
                $id_kasir = $dataTransaksi->id_kasir;
                $kasir = $this->db->get_where('pegawais', ["NIP" => $id_kasir])->row();
                $nama_kasir = $kasir->nama_pegawai;
            }
            if($dataTransaksi->id_cs!=null){
                $id_cs = $dataTransaksi->id_cs;
                $customer_service = $this->db->get_where('pegawais', ["NIP" => $id_cs])->row();
                $nama_customer_service = $customer_service->nama_pegawai;
            }
            if($dataTransaksi->id_hewan!=null){
                $id_hewan = $dataTransaksi->id_hewan;
                $hewan = $this->db->get_where('hewans', ["id_hewan" => $id_hewan])->row();
                $id_customer = $hewan->id_customer;
                $id_jenisHewan = $hewan->id_jenisHewan;

                $jenis_hewan = $this->db->get_where('jenishewans', ["id_jenisHewan" => $id_jenisHewan])->row();
                $pelanggan = $this->db->get_where('customers', ["id_customer" => $id_customer])->row();

                $nama_jenis_hewan = $jenis_hewan->nama_jenisHewan;
                $nama_hewan = $hewan->nama_hewan;
                $nama_pelanggan = $pelanggan->nama_customer;
                $no_telp = $pelanggan->noTelp_customer;
            }
        }else{
            $this->returnData("ID Transaksi Layanan tidak ditemukan!",true);
        }
        
        $subtotal = $dataTransaksi->subtotal;
        $id_transaksi = $dataTransaksi->kode_penjualan_layanan;
        $total = $dataTransaksi->total;
        $tanggal_lunas = "-";
        $diskon = "0";
        $tgl = $dataTransaksi->createLog_at;
        if($dataTransaksi->diskon!=null){
            $diskon = $dataTransaksi->diskon;
        }
        if($dataTransaksi->tanggal_lunas!=null){
            $tanggal_lunas = date("j F Y H:i",strtotime($dataTransaksi->tanggal_lunas));
        }

        $month_name = array("Januari", "Februari", "Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
        $nowDate = date("d");
        $nowMonth = date("m");
        $nowYear = date("Y");
        //setlocale(LC_TIME, 'id');
        //$month_name = date('F', mktime(0, 0, 0, $nowMonth));
        $id_p = sprintf( $id_transaksi);
        $newDate = date("Y-m-d", strtotime($tgl));
        // setting jenis font yang akan digunakan
        $pdf->Image(APPPATH.'controllers/PDF/Logo/kouvee.png',10,10,-200);
        // $pdf->Image(APPPATH.'controllers/PDF/Logo/kouveelogo.png',20,25,-800);
        $pdf->Cell(10,50,'',0,1);
        // $pdf->Image(APPPATH.'controllers/PDF/Logo/kotak.jpg',5,80,-700);
        // Memberikan space kebawah agar tidak terlalu rapat
        $pdf->Cell(70);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(50,7,'Nota Lunas',0,1,'C');
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(140);
        $pdf->Cell(30,8,'NO : '.$id_p,0,1);
        $pdf->Cell(140);
        $pdf->Cell(30,8,'Tanggal : '.$tanggal_lunas,0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(45,6,'Kasir  ',0,0);
        $pdf->Cell(45,6,':  '.$nama_kasir,0,1);
        $pdf->Cell(45,6,'Customer Service ',0,0);
        $pdf->Cell(45,6,':  '.$nama_customer_service,0,1);
        $pdf->Cell(45,6,'Member  ',0,0);
        $pdf->Cell(45,6,':  '.$nama_pelanggan,0,1);
        $pdf->Cell(45,6,'Telp  ',0,0);
        $pdf->Cell(45,6,':  '.$no_telp,0,1);
        $pdf->Cell(45,6,'Nama Hewan  ',0,0);
        $pdf->Cell(45,6,':  '.$nama_hewan.' - '.'('.$nama_jenis_hewan.')',0,1);
        // $pdf->Cell(30,6,$alamat_supplier,0,1);
        // $pdf->Cell(30,6,$no_telp,0,1);
        $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(70);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(50,7,'Jasa Layanan',0,1,'C');
        $pdf->Cell(180,7,'_________________________________________________________________',0,1,'C');
    
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,6,'NO',1,0,'C');
        $pdf->Cell(55,6,'NAMA LAYANAN',1,0,'C');
        $pdf->Cell(35,6,'UKURAN',1,0,'C');
        $pdf->Cell(30,6,'HARGA',1,0,'C');
        $pdf->Cell(20,6,'JUMLAH',1,0,'C');
        $pdf->Cell(30,6,'TOTAL',1,1,'C');
        $pdf->SetFont('Arial','',10);
        $i = 1;

        foreach ($dataDetailTransaksi as $loop){
            if($loop->kode_penjualan_layanan == $dataTransaksi->kode_penjualan_layanan)
            {

                $id_harga_layanan = $loop->id_layananHarga;
                $harga_layanan = $this->db->get_where('layananhargas', ["id_layananHarga" => $id_harga_layanan])->row();
                $harga = $harga_layanan->harga;
                $id_layanan = $harga_layanan->id_layanan;
                $layanan = $this->db->get_where('layanans', ["id_layanan" => $id_layanan])->row();
                $nama_layanan = $layanan->nama_layanan;
                $id_ukuran_hewan = $harga_layanan->id_ukuranHewan;
                $ukuran = $this->db->get_where('ukuranhewans', ["id_ukuranHewan" => $id_ukuran_hewan])->row();
                $nama_ukuran = $ukuran->nama_ukuranHewan;       
                
                $pdf->Cell(10,10,$i,1,0,'C');
                $pdf->Cell(55,10,$layanan->nama_layanan,1,0,'C');
                $pdf->Cell(35,10,$ukuran->nama_ukuranHewan,1,0,'C');
                $pdf->Cell(30,10,'Rp '.$harga_layanan->harga,1,0,'C');
                $pdf->Cell(20,10,$loop->jml_transaksi_layanan,1,0,'C');
                $pdf->Cell(30,10,'Rp '.$loop->total_harga,1,1,'C');
            }
            $i++;
        }
        $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(45,6,'Sub Total :Rp   '.$subtotal,0,1);
        $pdf->Cell(45,6,'Diskon     :Rp   '.$diskon,0,1);
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(60,10,'Total       :Rp '.$total,1,1);

        $now = date("d-m-Y");
        $pdf->Cell(10,20,'',0,1);
        $pdf->Cell(135);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,7,'Dicetak tanggal '.$nowDate.' '.$month_name[intval($nowMonth)-1].' '.$nowYear,0,1,'C');
        $pdf->Output($id_transaksi.'.pdf','D');
        //.$param
    }

    function transaksiProduk_get($param){
        // $this->load->helper('directory'); //load directory helper
        $dir = "controllers/PDF/"; // Your Path to folder
        // $map = directory_map($dir); /* This function reads the directory path specified in the first parameter and builds an array representation of it and all its contained files. */
        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();
    
        $dataTransaksi = null;
        $dataDetailTransaksi = null;

        $nama_kasir = "-";
        $nama_customer_service = "-";
        $nama_jenis_hewan = "-";
        $nama_hewan = "Guest";
        $nama_pelanggan = "Guest";
        $no_telp = "-";

        $resultTransaksi = $this->db->get_where('transaksipenjualanproduks', ["kode_penjualan_produk" => $param]);
        if($resultTransaksi->num_rows()!=0){
            $dataTransaksi = $resultTransaksi->row();
            $dataDetailTransaksi = $this->db->get_where('detailtransaksiproduks', ["kode_penjualan_produk" => $param])->result();

            if($dataTransaksi->id_kasir!=null){
                $id_kasir = $dataTransaksi->id_kasir;
                $kasir = $this->db->get_where('pegawais', ["NIP" => $id_kasir])->row();
                $nama_kasir = $kasir->nama_pegawai;
            }
            if($dataTransaksi->id_cs!=null){
                $id_cs = $dataTransaksi->id_cs;
                $customer_service = $this->db->get_where('pegawais', ["NIP" => $id_cs])->row();
                $nama_customer_service = $customer_service->nama_pegawai;
            }
            if($dataTransaksi->id_hewan!=null){
                $id_hewan = $dataTransaksi->id_hewan;
                $hewan = $this->db->get_where('hewans', ["id_hewan" => $id_hewan])->row();
                $id_customer = $hewan->id_customer;
                $id_jenisHewan = $hewan->id_jenisHewan;

                $jenis_hewan = $this->db->get_where('jenishewans', ["id_jenisHewan" => $id_jenisHewan])->row();
                $pelanggan = $this->db->get_where('customers', ["id_customer" => $id_customer])->row();

                $nama_jenis_hewan = $jenis_hewan->nama_jenisHewan;
                $nama_hewan = $hewan->nama_hewan;
                $nama_pelanggan = $pelanggan->nama_customer;
                $no_telp = $pelanggan->noTelp_customer;
            }
        }else{
            $this->returnData("ID Transaksi Produk tidak ditemukan!",true);
        }

        $subtotal = $dataTransaksi->subtotal;
        $id_transaksi = $dataTransaksi->kode_penjualan_produk;
        $total = $dataTransaksi->total;
        $tanggal_lunas = "-";
        $diskon = "0";
        $tgl = $dataTransaksi->createLog_at;
        if($dataTransaksi->diskon!=null){
            $diskon = $dataTransaksi->diskon;
        }
        if($dataTransaksi->tanggal_lunas!=null){
            $tanggal_lunas = date("j F Y H:i",strtotime($dataTransaksi->tanggal_lunas));
        }

        $month_name = array("Januari", "Februari", "Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
        $nowDate = date("d");
        $nowMonth = date("m");
        $nowYear = date("Y");
        //setlocale(LC_TIME, 'id');
        //$month_name = date('F', mktime(0, 0, 0, $nowMonth));
        $id_p = sprintf( $id_transaksi);
        $newDate = date("Y-m-d", strtotime($tgl));
        // setting jenis font yang akan digunakan
        $pdf->Image(APPPATH.'controllers/PDF/Logo/kouvee.png',10,10,-200);
        // $pdf->Image(APPPATH.'controllers/PDF/Logo/kouveelogo.png',20,25,-800);
        $pdf->Cell(10,50,'',0,1);
        // $pdf->Image(APPPATH.'controllers/PDF/Logo/kotak.jpg',5,80,-700);
        // Memberikan space kebawah agar tidak terlalu rapat
        $pdf->Cell(70);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(50,7,'Nota Lunas',0,1,'C');
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(140);
        $pdf->Cell(30,8,'NO : '.$id_p,0,1);
        $pdf->Cell(140);
        $pdf->Cell(30,8,'Tanggal : '.$tanggal_lunas,0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(45,6,'Kasir  ',0,0);
        $pdf->Cell(45,6,':  '.$nama_kasir,0,1);
        $pdf->Cell(45,6,'Customer Service ',0,0);
        $pdf->Cell(45,6,':  '.$nama_customer_service,0,1);
        $pdf->Cell(45,6,'Member  ',0,0);
        $pdf->Cell(45,6,':  '.$nama_pelanggan,0,1);
        $pdf->Cell(45,6,'Telp  ',0,0);
        $pdf->Cell(45,6,':  '.$no_telp,0,1);
        $pdf->Cell(45,6,'Nama Hewan  ',0,0);
        $pdf->Cell(45,6,':  '.$nama_hewan.'-'.'('.$nama_jenis_hewan.')',0,1);
        // $pdf->Cell(30,6,$alamat_supplier,0,1);
        // $pdf->Cell(30,6,$no_telp,0,1);
        $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(70);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(50,7,'Pembelian Produk',0,1,'C');
        $pdf->Cell(180,7,'_________________________________________________________________',0,1,'C');

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,6,'NO',1,0,'C');
        $pdf->Cell(70,6,'NAMA PRODUK',1,0,'C');
        $pdf->Cell(40,6,'HARGA',1,0,'C');
        $pdf->Cell(20,6,'JUMLAH',1,0,'C');
        $pdf->Cell(40,6,'TOTAL',1,1,'C');
        $pdf->SetFont('Arial','',10);
        $i = 1;

        foreach ($dataDetailTransaksi as $loop){
            if($loop->kode_penjualan_produk == $dataTransaksi->kode_penjualan_produk)
            {
                
                $id_produk = $loop->id_produkHarga;
                $produk = $this->db->get_where('produks', ["id_produk" => $id_produk])->row();
                $pdf->Cell(10,10,$i,1,0,'C');
                $pdf->Cell(70,10,$produk->nama_produk,1,0,'L');
                $pdf->Cell(40,10,'Rp '.$produk->harga_produk,1,0,'C');
                $pdf->Cell(20,10,$loop->jml_transaksi_produk,1,0,'C');
                $pdf->Cell(40,10,'Rp '.$loop->total_harga,1,1,'C');
            }
            $i++;
        }
        $pdf->Cell(10,10,'',0,1);
        $pdf->Cell(45,6,'Sub Total :Rp   '.$subtotal,0,1);
        $pdf->Cell(45,6,'Diskon     :Rp   '.$diskon,0,1);
        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(65,10,'Total :Rp '.$total,1,1);

        $now = date("d-m-Y");
        $pdf->Cell(10,20,'',0,1);
        $pdf->Cell(135);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,7,'Dicetak tanggal '.$nowDate.' '.$month_name[intval($nowMonth)-1].' '.$nowYear,0,1,'C');
        $pdf->Output($id_transaksi.'.pdf','D');
        //.$param
    }
    function pengadaanProduk_get($param){
        // $this->load->helper('directory'); //load directory helper
        $dir = "controllers/PDF/"; // Your Path to folder
        // $map = directory_map($dir); /* This function reads the directory path specified in the first parameter and builds an array representation of it and all its contained files. */
        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();
    
        $dataPengadaan = null;
        $dataDetailPengadaan = null;

        $nama_supplier = "-";
        $nama_produk = "-";
        $jumlah = "-";
        $harga = "-";
        $total = "-";
        $total_harga = "-";

        $resultPengadaan = $this->db->get_where('pengadaans', ["no_order" => $param]);
        if($resultPengadaan->num_rows()!=0){
            $dataPengadaan = $resultPengadaan->row();
            $dataDetailPengadaan = $this->db->get_where('detailpengadaans', ["no_order" => $param])->result();

            if($dataPengadaan->id_supplier!=null){
                $id_supplier = $dataPengadaan->id_supplier;
                $supplier = $this->db->get_where('suppliers', ["id_supplier" => $id_supplier])->row();
                $nama_supplier = $supplier->nama_supplier;
                $telp_supplier = $supplier->telepon_supplier;
                $alamat_supplier = $supplier->alamat_supplier;
            }
            // if($dataPengadaan->id_produk!=null){
            //     $id_produk = $dataPengadaan->id_produk;
            //     $produk = $this->db->get_where('produk', ["id_produk" => $id_produk])->row();
            //     $nama_produk = $produk->nama;
            // }
           
        }else{
            $this->returnData("ID Pengadaan Produk tidak ditemukan!",true);
        }

        $total = $dataPengadaan->total_harga;
        $no_order = $dataPengadaan->no_order;
        $tgl = $dataPengadaan->createLog_at;
        

        $month_name = array("Januari", "Februari", "Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember");
        $nowDate = date("d");
        $nowMonth = date("m");
        $nowYear = date("Y");
        //setlocale(LC_TIME, 'id');
        //$month_name = date('F', mktime(0, 0, 0, $nowMonth));
        $id_p = sprintf( $no_order);
        $newDate = date("Y-m-d", strtotime($tgl));
        // setting jenis font yang akan digunakan
        $pdf->Image(APPPATH.'controllers/PDF/Logo/kouvee.png',10,10,-200);
        // $pdf->Image(APPPATH.'controllers/PDF/Logo/kouveelogo.png',20,25,-800);
        $pdf->Cell(10,50,'',0,1);
        // $pdf->Image(APPPATH.'controllers/PDF/Logo/kotak.jpg',5,80,-700);
        // Memberikan space kebawah agar tidak terlalu rapat
        $pdf->Cell(70);
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(50,7,'Surat Pemesanan',0,1,'C');
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(140);
        $pdf->Cell(15,8,'NO',0,0);
        $pdf->Cell(15,8,': '.$id_p,0,1);
        $pdf->Cell(140);
        $pdf->Cell(15,8,'Tanggal',0,0);
        $pdf->Cell(15,8,': '.$tgl,0,0);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(10,10,'',0,1);
        $pdf->Image(APPPATH.'controllers/PDF/kotak.jpg',5,80,-600);
        // $pdf->Cell(45,6,'Supplier  ',0,0);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(45,6,'Kepada Yth.',0,1);
        $pdf->Cell(5,5,'',0,1);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(45,6,$nama_supplier,0,1);
        // $pdf->Cell(45,6,'Alamat ',0,0);
        $pdf->Cell(45,6,$alamat_supplier,0,1);
        // $pdf->Cell(45,6,'Telp  ',0,0);
        $pdf->Cell(45,6,$telp_supplier,0,1);
        $pdf->Cell(10,10,'',0,1);
        // $pdf->Cell(70);
        $pdf->SetFont('Arial','',10);
        $pdf->Cell(45,7,'Mohon disediakan produk-produk berikut :',0,1,'L');
        $pdf->SetFont('Arial','B',14);
        $pdf->Cell(180,7,'_________________________________________________________________',0,1,'C');

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,6,'NO',1,0,'C');
        $pdf->Cell(70,6,'NAMA PRODUK',1,0,'C');
        $pdf->Cell(40,6,'Satuan',1,0,'C');
        $pdf->Cell(60,6,'JUMLAH',1,1,'C');

        $pdf->SetFont('Arial','',10);
        $i = 1;

        foreach ($dataDetailPengadaan as $loop){
            if($loop->no_order == $dataPengadaan->no_order)
            {
                
                $id_produk = $loop->id_produk;
                $jumlah = $loop->jumlah_stok_pengadaan;
                $produk = $this->db->get_where('produks', ["id_produk" => $id_produk])->row();
                $satuan = $produk->satuan_produk;
                $pdf->Cell(10,10,$i,1,0,'C');
                $pdf->Cell(70,10,$produk->nama_produk,1,0,'L');
                $pdf->Cell(40,10,$satuan,1,0,'C');
                $pdf->Cell(60,10,$loop->jumlah_stok_pengadaan,1,1,'C');

            }
            $i++;
        }


        $now = date("d-m-Y");
        $pdf->Cell(10,20,'',0,1);
        $pdf->Cell(135);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(30,7,'Dicetak tanggal '.$nowDate.' '.$month_name[intval($nowMonth)-1].' '.$nowYear,0,1,'C');
        $pdf->Output($no_order.'.pdf','D');
        //.$param
    }


    public function returnData($msg,$error){
        $response['error']=$error;
        $response['message']=$msg;
        return $this->response($response);
    }
}