<?php
defined('BASEPATH') or exit('No direct script access allowed');
/**
 * controller untuk semua hal yang berkaitan dengan data/model/views gudang yang akan ditampilkan
 */
class Warehouse extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->mkits = $this->load->database('mkits', TRUE);
        is_logged_in();
    }

    public function index()
    {
        $data['title'] = 'Data Barang';
        $data['user'] = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('warehouse/dataBarang', $data);
        $this->load->view('templates/footer');
    }
    //Ambil data barang dari database - print / echo dalam bentuk json 
    public function stockList()
    {
        $this->load->model("m_gudang");
        $list = $this->m_gudang->listBarang();

        $data = array();

        foreach ($list->result() as $ls) { //data di masukkan dalam sebuah array $data
            $data[] = array(
                'id'        => $ls->code_barang,
                'model'     => $ls->model,
                'stok75'    => $ls->stok_g75,
                'stok50'    => $ls->stok_a50,
                'minus_75'  => $ls->minus_75,
                'plus_75'   => $ls->plus_75,
                'minus50'   => $ls->minus50,
                'plus_50'   => $ls->plus_50,
                'g75_to_a50' => $ls->g75_to_a50,
                'a50_to_g75' => $ls->a50_to_g75
            );
        }

        print_r(json_encode($data));
    }

    public function iMutation()
    {
        $this->load->model("m_gudang");
        $mt = $this->m_gudang->listMutasi();

        $data = array();

        foreach ($mt->result() as $row) {
            $data[] = array(
                'id'        => $row->id_mutasi,
                'mutasiby'  => $row->mutasi_by,
                'model'     => $row->model,
                'jenis'     => $row->jenis_mutasi,
                'qty'       => $row->qty_mutasi,
                'source'    => $row->source,
                'status'    => $row->status,
                'tujuan'    => $row->destination,
                'tgl'       => date("d/m/Y", $row->created_date)
            );
        }
        print_r(json_encode($data));
    }

    public function mutasi()
    {
        $data['title'] = 'Mutasi Barang';
        $data['user'] = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('warehouse/mutasiBarang', $data);
        $this->load->view('templates/footer');
    }
    /*
    # menambah data barang, di view ini hanya boleh dilakukan oleh user tertentu
    # jika ingin menambahkan field baru, tentukan dan alter table database terlebih dahulu
*/
    public function addItem()
    {
        $this->form_validation->set_rules('keterangan', 'Keterangan', 'trim');
        $this->form_validation->set_rules('sg75', 'Stok G-75', 'numeric|required', [
            'required' => 'Setidaknya 0'
        ]);
        $this->form_validation->set_rules('sa50', 'Stok A-50', 'numeric|required', [
            'required' => 'Setidaknya 0'
        ]);
        $this->form_validation->set_rules('model', 'SKU barang', 'trim|required|is_unique[tb_barang.model]', [
            'is_unique' => 'SKU ini sudah ada!'
        ]);
        if ($this->form_validation->run() == FALSE) {
            $data['title'] = 'Data Barang';
            $data['user'] = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('warehouse/addBarang', $data);
            $this->load->view('templates/footer');
        } else {
            $data = [
                'code_barang'   => $this->input->post('code'),
                'model'         => $this->input->post('model'),
                'stok_g75'      => $this->input->post('sg75'),
                'stok_a50'      => $this->input->post('sa50'),
                'keterangan' => $this->input->post('keterangan'),
                'created_by' => $this->input->post('createdby'),
                'created_at' => time()
            ];
            $this->db->insert('tb_barang', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Item/ barang baru ditambahkan!</div>');
            redirect('warehouse/addItem');
        }
    }

    /** 
     * menampilkan detail barang dalam bentuk php->html yang akan d load dari ajax~jQuery
     */
    public function detailBarang()
    {
        $itemCode = $this->input->post('id');

        $this->load->model("m_gudang");
        $item = $this->m_gudang->getdataItem($itemCode);

        $output = '';

        foreach ($item->result() as $itm) {
            $skus = $itm->model;
            $this->load->model("m_gudang");
            $mts = $this->m_gudang->get_mutasi($skus);

            $output .= '<div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-striped" width="100%">
                                <tr>
                                    <th>Model</th>
                                    <td>:</td>
                                    <th>' . $itm->model . '</th>
                                    <td rowspan="5" class="table-warning" width="30%"><img src="https://horekadepot.com/image/catalog/img/' . str_replace("-", "", $itm->model) . '.jpeg" width="200px"></td>
                                </tr>
                                <tr>
                                    <th>Ketarangan</th>
                                    <td>:</td>
                                    <th>' . $itm->keterangan . '</th>
                                </tr>
                                <tr>
                                    <th>Stok G-75</th>
                                    <td>:</td>
                                    <th>' . $itm->stok_g75 . '</th>
                                </tr>
                                <tr>
                                    <th>Stok A-50</th>
                                    <td>:</td>
                                    <th>' . $itm->stok_a50 . '</th>
                                </tr>
                                <tr>
                                    <th>Created Date</th>
                                    <td>:</td>
                                    <th>' . date("d/m/Y", $itm->created_at) . '</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                
                <hr class="sidebar-divider mt-3">
                <div class="col-md-12 mt-3">
                    <div class="card">
                        <h5 class="card-header card-title">Mutasi Aktif</h5>
                    
                        <div class="card-body">
                            <table class="table table-bordered table-striped tableM" width="100%" data-advanced-search="false">
                                <thead>
                                    <tr>
                                        <th>Jenis Mutasi</th>
                                        <th>Qty</th>
                                        <th>Perpindahan </th>
                                        <th>Tanggal Buat</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>';
            foreach ($mts->result() as $mt) {
                if ($mt->jenis_mutasi == 'plus') {
                    $a = "Penambahan Stok";
                } elseif ($mt->jenis_mutasi == 'minus') {
                    $a = "Pengurangan Stok";
                } else {
                    $a = "Perpindahan Stok";
                }
                $output .= '<tr>
                                        <td>' . $a . '</td>
                                        <td>' . $mt->qty_mutasi . '</td>
                                        <td>' . $mt->source . ' <span class="fas fa-fw fa-arrow-right"></span> ' . $mt->destination . '</td>
                                        <td>' . date("d/m/Y", $mt->created_date) . '</td>
                                        <td><span class="fas fa-fw fa-arrow-right"></span>' . $mt->keterangan . '</td>
                                    </tr>';
            }
            $output .= '</tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>';
        }

        echo $output;
    }
/**
 * create mutasi baru disini
 */
    public function mutasiBaru()
    {
        $this->form_validation->set_rules('code', 'UNIQUE Code', 'trim|required');
        $this->form_validation->set_rules('uname', 'Nama Pengguna', 'trim|required');
        $this->form_validation->set_rules('mods', 'Model', 'trim|required');
        $this->form_validation->set_rules('jmutasi', 'Jenis Mutasi', 'trim|required');
        $this->form_validation->set_rules('pickticket', 'Pick Ticket', 'is_unique[tb_mutasi.keterangan]', [
            'is_unique' => 'Pickticket ini sudah pernah dimutasikan'
        ]);

        if ($this->form_validation->run() == FALSE) {
            $data['title']  = 'Mutasi Barang';
            $data['user']   = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();
            $data['gdg']    = $this->db->get('tb_gudang')->result_array();
            $data['sj']     = $this->mkits->get_where('customerorder_hdr', ['status_delivery' => '0'])->result_array();
            $data['sku']    = $this->db->get('tb_barang')->result_array();;

            $this->load->view('templates/header', $data);
            $this->load->view('templates/sidebar', $data);
            $this->load->view('templates/topbar', $data);
            $this->load->view('warehouse/addMutasi', $data);
            $this->load->view('templates/footer');
        } else {
            $id             = $this->input->post('code');
            $userName       = $this->input->post('uname');
            $sku            = $this->input->post('mods');
            $jenis_mutasi   = $this->input->post('jmutasi');

            if ($jenis_mutasi == 'plus') { //jika jenis mutasi adalah penambahan stok
                $data = [
                    'id_mutasi'     => $id,
                    'mutasi_by'     => $userName,
                    'model'         => $sku,
                    'jenis_mutasi'  => $jenis_mutasi,
                    'qty_mutasi'    => $this->input->post('qtys'),
                    'destination'   => $this->input->post('gtujuan'),
                    'status'        => '0',
                    'keterangan'    => $this->input->post('nlpb'),
                    'created_date'  => time()
                ];
            } elseif ($jenis_mutasi == 'minus') { //Jika Jenis muasi adalah pengurangan stok
                $data = [
                    'id_mutasi'     => $id,
                    'mutasi_by'     => $userName,
                    'model'         => $sku,
                    'jenis_mutasi'  => $jenis_mutasi,
                    'qty_mutasi'    => $this->input->post('qtys'),
                    'source'        => $this->input->post('gasal'),
                    'status'        => '0',
                    'keterangan'    => $this->input->post('pickticket'),
                    'created_date'  => time()
                ];
            } elseif ($jenis_mutasi == 'mutation') { //Jika jenis mutasi ada perpindahan stok
                $data = [
                    'id_mutasi'     => $id,
                    'mutasi_by'     => $userName,
                    'model'         => $sku,
                    'jenis_mutasi'  => $jenis_mutasi,
                    'qty_mutasi'    => $this->input->post('qtys'),
                    'source'        => $this->input->post('gasal'),
                    'destination'   => $this->input->post('gtujuan'),
                    'status'        => '0',
                    'keterangan'    => $this->input->post('nlpb'),
                    'created_date'  => time()
                ];
            }
            $this->db->insert('tb_mutasi', $data);
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">Mutasi baru terecord!</div>');
            redirect('warehouse/mutasiBaru');
        }
    }
    /**
     * ambil data sku dari database berdasarkan post Search nama pada select2
     * masukkan dalam array json
     * kirim response ke javascript
     */
    public function getSkus()
    {
        $search = $this->input->post('searchNama');

        $this->load->model("m_mkits");
        $sku    = $this->m_mkits->dataSku($search);
        $data   = array();

        foreach ($sku->result() as $s) {
            $data[] = array(
                "id"    => $s->model,
                "text"  => $s->model
            );
        }
        echo json_encode($data);
    }
    /**
     * menampilkan data stok dari databse ~ berdasakan ID/ SKU/ Model yang di kirim lewat ajax
     * pada bagian ini kita boleh tambahkan data apa yang akan d post lwt ajax, sesuai kebutuhan
     */
    public function getStoks()
    {
        $id = $this->input->post('id');

        $sku = $this->db->get_where('tb_barang', ['model' => $id])->result_array();
        $data = array();

        foreach ($sku as $s) {
            $data = array(
                'stok75' => $s['stok_g75'],
                'stok50' => $s['stok_a50']
            );
        }
        print_r(json_encode($data));
    }

    public function detailMutasi()
    {
        $id_mutasi  = $this->input->post('id');
        $muts       = $this->db->get_where('tb_mutasi', ['id_mutasi' => $id_mutasi])->result_array();

        $html = '';
        foreach ($muts as $mt) {
            if ($mt["jenis_mutasi"] == 'plus') {
                $kindm = 'Penambahan Stok <i class="fas fa-fw fa-arrow-right"></i> ' . $mt['destination'];
            } else if ($mt["jenis_mutasi"] == 'minus') {
                $kindm = 'Pengurangan Stok <i class="fas fa-fw fa-arrow-right"></i> ' . $mt['source'];
            } else {
                $kindm = 'Pindah Stok ' . $mt["source"] . ' <i class="fas fa-fw fa-arrow-right"></i> ' . $mt["destination"];
            }

            if ($mt["status"] == '0') {
                $sts = '<span class="badge badge-info">Pending</span>';
            } else if ($mt["status"] == '1') {
                $sts = '<span class="badge badge-success">Approved</span><i class="fas fa-fw fa-arrow-right"></i> ' . $mt["approved_by"];
            } else {
                $sts = '<span class="badge badge-danger">Cancel</span><i class="fas fa-fw fa-arrow-right"></i> ' . $mt["approved_by"];
            }
            $html .= '<div class="row">
            <div class="col-md-12">
                <div class="card border-primary">
                    <div class="card-body table-responsive">
                        <table class="table table-bordered table-hover" width="100%">
                            <tr>
                                <th>Jenis Mutasi</th>
                                <th width="40%">: ' . $kindm . '</th>
                                <td rowspan="7" width="30%" style="vertical-align: center; text-align: center;"><img src="https://horekadepot.com/image/catalog/img/' . str_replace("-", "", $mt["model"]) . '.jpeg" width="200px"></td>
                            </tr>
                            <tr>
                                <th >Sku/ Model</th>
                                <th width="40%">: ' . $mt["model"] . '</th>
                            </tr>
                            <tr>
                                <th >Tgl. Mutasi</th>
                                <th width="40%">: ' . date("d/m/Y", $mt["created_date"]) . '</th>
                            </tr>
                            <tr>
                                <th >Referensi</th>
                                <th width="40%">: ' . $mt["keterangan"] . '</th>
                            </tr>
                            <tr>
                                <th >Status</th>
                                <th width="40%">: ' . $sts . '</th>
                            </tr>
                            <tr>
                                <th >Dibuat Oleh</th>
                                <th width="40%">: ' . $mt["mutasi_by"] . '</th>
                            </tr>
                            <tr>
                                <th >Note</th>
                                <th width="40%">: ' . $mt["note"] . '</th>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            </div>';
        }
        print_r($html);
    }

    public function apm($id = NULL)
    {
        $data['title']  = 'Mutasi Barang';
        $data['user']   = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['muts']   = $this->db->get_where('tb_mutasi', ['id_mutasi' => $id])->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('warehouse/mutasi-proses', $data);
        $this->load->view('templates/footer');
    }

    public function appsProc()
    {
        // var_dump($this->input->post());
        // die();
        $id         = $this->input->post('code');
        $j_mutasi   = $this->input->post('njenis');
        $action     = $this->input->post('act');
        $qty_mutasi = $this->input->post('qtys');
        $model      = $this->input->post('nmodel');

        $stok_database = $this->db->get_where('tb_barang', ['model' => $this->input->post('nmodel')])->row_array();

        if ($action == 'terima') {
            if ($j_mutasi == 'minus') {
                // pengurangan stok disini ya
                $source = $this->input->post('nsource');
                /**
                 * Jika stok yang akan dikurangi adalah dari gudang Garuda 75
                 */
                if ($source == 'g75') {
                    $stok75 = $stok_database['stok_g75'];

                    $data_min = [
                        'stok_g75'  => $stok75 - $qty_mutasi,
                        'update_by' => $this->input->post('mutby'),
                        'update_at' => time()
                    ];

                    $this->db->set($data_min);
                    $this->db->where('model', $model);
                    $this->db->update('tb_barang');

                    // proses update database tb_mutasi
                    $data_b = [
                        'status'        => '1',
                        'approved_by'   => $this->input->post('nuser'),
                        'approved_date' => time(),
                        'note'          => $this->input->post('note')
                    ];

                    $this->db->set($data_b);
                    $this->db->where('id_mutasi', $id);
                    $this->db->update('tb_mutasi');

                    $data_history = [
                        'code_barang' => $stok_database['code_barang'],
                        'locator'     => 'stok_g75',
                        'kategori'    => 'minus',
                        'qty_awal'    => $stok75,
                        'qty_update'  => $qty_mutasi,
                        'qty_akhir'   => $stok75 - $qty_mutasi,
                        'update_by'   => $this->input->post('mutby'),
                        'approval_by' => $this->input->post('nuser'),
                        'approval_at' => time(),
                        'keterangan'  => $this->input->post('nketerangan')
                    ];

                    $this->db->insert('tb_history_mutasi', $data_history);

                    echo "Pengurangan Stok Berhasil";
                } else if ($source == 'a50') {
                    /**
                     * jika pengurangan adalah dari gudang a50
                     */
                    $stok50 = $stok_database['stok_a50'];

                    $data_min = [
                        'stok_a50'  => $stok50 - $qty_mutasi,
                        'update_by' => $this->input->post('mutby'),
                        'update_at' => time()
                    ];

                    $this->db->set($data_min);
                    $this->db->where('model', $model);
                    $this->db->update('tb_barang');

                    // proses update database tb_mutasi
                    $data_b = [
                        'status'        => '1',
                        'approved_by'   => $this->input->post('nuser'),
                        'approved_date' => time(),
                        'note'          => $this->input->post('note')
                    ];

                    $this->db->set($data_b);
                    $this->db->where('id_mutasi', $id);
                    $this->db->update('tb_mutasi');

                    $data_history = [
                        'code_barang' => $stok_database['code_barang'],
                        'locator'     => 'stok_a50',
                        'kategori'    => 'minus',
                        'qty_awal'    => $stok50,
                        'qty_update'  => $qty_mutasi,
                        'qty_akhir'   => $stok50 - $qty_mutasi,
                        'update_by'   => $this->input->post('mutby'),
                        'approval_by' => $this->input->post('nuser'),
                        'approval_at' => time(),
                        'keterangan'  => $this->input->post('nketerangan')
                    ];

                    $this->db->insert('tb_history_mutasi', $data_history);

                    echo "Pengurangan Stok Berhasil";
                }
            } else if ($j_mutasi == 'plus') {
                // penambahan stok disini ya
                $dest = $this->input->post('ndestination');
                if ($dest == 'g75') {
                    $stok75 = $stok_database['stok_g75'];

                    $data_plus = [
                        'stok_g75'  => $stok75 + $qty_mutasi,
                        'update_by' => $this->input->post('mutby'),
                        'update_at' => time()
                    ];

                    $this->db->set($data_plus);
                    $this->db->where('model', $model);
                    $this->db->update('tb_barang');

                    // proses update database tb_mutasi
                    $data_b = [
                        'status'        => '1',
                        'approved_by'   => $this->input->post('nuser'),
                        'approved_date' => time(),
                        'note'          => $this->input->post('note')
                    ];

                    $this->db->set($data_b);
                    $this->db->where('id_mutasi', $id);
                    $this->db->update('tb_mutasi');

                    $data_history = [
                        'code_barang' => $stok_database['code_barang'],
                        'locator'     => 'stok_g75',
                        'kategori'    => 'plus',
                        'qty_awal'    => $stok75,
                        'qty_update'  => $qty_mutasi,
                        'qty_akhir'   => $stok75 + $qty_mutasi,
                        'update_by'   => $this->input->post('mutby'),
                        'approval_by' => $this->input->post('nuser'),
                        'approval_at' => time(),
                        'keterangan'  => $this->input->post('nketerangan')
                    ];

                    $this->db->insert('tb_history_mutasi', $data_history);

                    echo "Penambahan Stok Berhasil";
                } else if ($dest == 'a50') { //tambah stok gudang 75
                    $stok50 = $stok_database['stok_a50'];

                    $data_plus = [
                        'stok_a50'  => $stok50 + $qty_mutasi,
                        'update_by' => $this->input->post('mutby'),
                        'update_at' => time()
                    ];

                    $this->db->set($data_plus);
                    $this->db->where('model', $model);
                    $this->db->update('tb_barang');

                    // proses update database tb_mutasi
                    $data_b = [
                        'status'        => '1',
                        'approved_by'   => $this->input->post('nuser'),
                        'approved_date' => time(),
                        'note'          => $this->input->post('note')
                    ];

                    $this->db->set($data_b);
                    $this->db->where('id_mutasi', $id);
                    $this->db->update('tb_mutasi');

                    $data_history = [
                        'code_barang' => $stok_database['code_barang'],
                        'locator'     => 'stok_a50',
                        'kategori'    => 'plus',
                        'qty_awal'    => $stok50,
                        'qty_update'  => $qty_mutasi,
                        'qty_akhir'   => $stok50 + $qty_mutasi,
                        'update_by'   => $this->input->post('mutby'),
                        'approval_by' => $this->input->post('nuser'),
                        'approval_at' => time(),
                        'keterangan'  => $this->input->post('nketerangan')
                    ];

                    $this->db->insert('tb_history_mutasi', $data_history);

                    echo "Penambahan Stok Berhasil";
                }
            } else if ($j_mutasi == 'mutation') {
                /**
                 * Perpidahan Stok
                 * Jika ada penambahan gudang silahkan input dsni
                 * tambahan condition dengan IF Else, Else IF
                 */
                $source = $this->input->post('nsource');
                $destination = $this->input->post('ndestination');
                /**
                 * Jika Sumber dari Gudang garuda 75 ke Gudang Arjuna 50 
                 * Maka gudang 75 akan berkurang dan gudang a50 akan bertambah
                 */
                if ($source == 'g75' && $destination == 'a50') {

                    $stok75 = $stok_database['stok_g75'];
                    $stok50 = $stok_database['stok_a50'];

                    // proses update databse tb_barang
                    $data_a = [
                        'stok_g75'  => $stok75 - $qty_mutasi,
                        'stok_a50'  => $stok50 + $qty_mutasi,
                        'update_by' => $this->input->post('mutby'),
                        'update_at' => time()
                    ];
                    $this->db->set($data_a);
                    $this->db->where('model', $model);
                    $this->db->update('tb_barang');

                    // proses update database tb_mutasi
                    $data_b = [
                        'status'        => '1',
                        'approved_by'   => $this->input->post('nuser'),
                        'approved_date' => time(),
                        'note'          => $this->input->post('note')
                    ];

                    $this->db->set($data_b);
                    $this->db->where('id_mutasi', $id);
                    $this->db->update('tb_mutasi');

                    $data_history_a = [
                        'code_barang' => $stok_database['code_barang'],
                        'locator'     => 'stok_g75',
                        'kategori'    => 'mutation',
                        'qty_awal'    => $stok75,
                        'qty_update'  => $qty_mutasi,
                        'qty_akhir'   => $stok75 - $qty_mutasi,
                        'update_by'   => $this->input->post('mutby'),
                        'approval_by' => $this->input->post('nuser'),
                        'approval_at' => time(),
                        'keterangan'  => $this->input->post('nketerangan')
                    ];
                    $this->db->insert('tb_history_mutasi', $data_history_a);

                    $data_history_b = [
                        'code_barang' => $stok_database['code_barang'],
                        'locator'     => 'stok_a50',
                        'kategori'    => 'mutation',
                        'qty_awal'    => $stok50,
                        'qty_update'  => $qty_mutasi,
                        'qty_akhir'   => $stok50 + $qty_mutasi,
                        'update_by'   => $this->input->post('mutby'),
                        'approval_by' => $this->input->post('nuser'),
                        'approval_at' => time(),
                        'keterangan'  => $this->input->post('nketerangan')
                    ];
                    $this->db->insert('tb_history_mutasi', $data_history_b);

                    echo "Pindah Stok Berhasil";

                    /**
                     * Jika proses perindahan gudanga dari Arjuna 50 ke Gudang garuda 75
                     * Gudang 50 akan berkurang dan gudang 75 akan bertamba
                     * stoknya berpindah
                     */
                } else if ($source == 'a50' && $destination == 'g75') {
                    $stok75 = $stok_database['stok_g75'];
                    $stok50 = $stok_database['stok_a50'];

                    // proses update databse tb_barang
                    $data_a = [
                        'stok_g75'  => $stok75 + $qty_mutasi,
                        'stok_a50'  => $stok50 - $qty_mutasi,
                        'update_by' => $this->input->post('mutby'),
                        'update_at' => time()
                    ];
                    $this->db->set($data_a);
                    $this->db->where('model', $model);
                    $this->db->update('tb_barang');

                    // proses update database tb_mutasi
                    $data_b = [
                        'status'        => '1',
                        'approved_by'   => $this->input->post('nuser'),
                        'approved_date' => time(),
                        'note'          => $this->input->post('note')
                    ];

                    $this->db->set($data_b);
                    $this->db->where('id_mutasi', $id);
                    $this->db->update('tb_mutasi');

                    $data_history_a = [
                        'code_barang' => $stok_database['code_barang'],
                        'locator'     => 'stok_g75',
                        'kategori'    => 'mutation',
                        'qty_awal'    => $stok75,
                        'qty_update'  => $qty_mutasi,
                        'qty_akhir'   => $stok75 + $qty_mutasi,
                        'update_by'   => $this->input->post('mutby'),
                        'approval_by' => $this->input->post('nuser'),
                        'approval_at' => time(),
                        'keterangan'  => $this->input->post('nketerangan')
                    ];
                    $this->db->insert('tb_history_mutasi', $data_history_a);

                    $data_history_b = [
                        'code_barang' => $stok_database['code_barang'],
                        'locator'     => 'stok_a50',
                        'kategori'    => 'mutation',
                        'qty_awal'    => $stok50,
                        'qty_update'  => $qty_mutasi,
                        'qty_akhir'   => $stok50 - $qty_mutasi,
                        'update_by'   => $this->input->post('mutby'),
                        'approval_by' => $this->input->post('nuser'),
                        'approval_at' => time(),
                        'keterangan'  => $this->input->post('nketerangan')
                    ];
                    $this->db->insert('tb_history_mutasi', $data_history_b);

                    echo "Pindah Stok Berhasil";
                }
            }
        } else if ($action == 'tolak') {
            $data_b = [
                'status'        => '2',
                'approved_by'   => $this->input->post('nuser'),
                'approved_date' => time(),
                'note'          => $this->input->post('note')
            ];

            $this->db->set($data_b);
            $this->db->where('id_mutasi', $id);
            $this->db->update('tb_mutasi');

            echo "Pengajuan Mutasi ditolak";
        }
    }

    public function historyBarang($id = null)
    {
        $data['title']  = 'Data Barang';
        $data['user']   = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['brg']    = $this->db->get_where('tb_barang', ['code_barang' => $id])->row_array();
        $data['gdg']    = $this->db->get('tb_gudang')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('warehouse/history-item', $data);
        $this->load->view('templates/footer');
    }

    public function listMutasi($id = null)
    {
        $this->load->model("m_gudang");
        $muts = $this->m_gudang->spesifikMutasi($id);
        $data = array();

        foreach ($muts->result() as $m) {
            $data[] = array(
                'tgl_update'    => date("d/m/Y", $m->approval_at),
                'kategori'      => $m->kategori,
                'qty_awal'      => $m->qty_awal,
                'qty_update'      => $m->qty_update,
                'qty_akhir'      => $m->qty_akhir,
                'locator'       => $m->locator,
                'updateBy'      => $m->update_by,
                'keterangan'    => $m->keterangan
            );
        }
        print_r(json_encode($data));
    }
}
