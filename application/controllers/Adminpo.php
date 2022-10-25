<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Adminpo extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->mkits = $this->load->database('mkits', TRUE);
        is_logged_in();
    }

    public function index()
    {
        $data['title'] = 'P/T MKITS';
        $data['user'] = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('adminpo/dataco', $data);
        $this->load->view('templates/footer');
    }

    public function getDatamwk($idlix)
    {
        $this->load->model("m_mkits");
        $mwk = $this->m_mkits->dataMwk($idlix);
        $data = array();

        foreach ($mwk->result() as $mk) {
            $data[] = array(
                'id' => $mk->No_Co,
                'nobl' => $mk->no_bl,
                'tglorder' => $mk->tgl_order,
                'tglkirim' => $mk->tgl_krm,
                'namacust' => $mk->customer_nama,
                'totalhrg' => $mk->uang,
                'status' => $mk->status . "|" . $mk->status_delivery,
                'sales' => $mk->sales,
                'keterangan' => $mk->keterangan,
                'tgldelivery' => date_format(date_create($mk->tgl_delivery), "d/m/Y H:i")
            );
        }
        print_r(json_encode($data));
    }

    public function detailCo()
    {
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        $pt = $this->mkits->get_where('customerorder_hdr', ['No_Co' => $id])->result_array();


        $skus = $this->mkits->get_where('customerorder_dtl', ['No_Co' => $id])->result_array();

        $this->load->model("m_mkits");
        $total = $this->m_mkits->getTotal($id);
        $rw = $total->row_array();

        $output = '';

        foreach ($pt as $p) {
            $output .= '<div class="card">
            <div class="card-body">
            <div class="row">
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-4">No CO</div>
                        <div class="col-1">:</div>
                        <div class="col-7">' . $p["No_Co"] . '</div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-3">Customer</div>
                        <div class="col-1">:</div>
                        <div class="col-8">' . $p["customer_nama"] . '</div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-4">Sales</div>
                        <div class="col-1">:</div>
                        <div class="col-7">' . $p["sales"] . '</div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-3">Alamat</div>
                        <div class="col-1">:</div>
                        <div class="col-8">' . $p["alamat_krm"] . '</div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-4">Tgl. Invoice</div>
                        <div class="col-1">:</div>
                        <div class="col-7">' . $p["tgl_inv"] . '</div>
                    </div>
                </div>
                <div class="col-md-7">
                    <div class="row">
                        <div class="col-3">Tgl. Order</div>
                        <div class="col-1">:</div>
                        <div class="col-8">' . $p["tgl_order"] . '</div>
                    </div>
                </div>
            </div>
        </div></div></br>';
        }
        $output .= '<div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>';
        foreach ($skus as $sk) {
            $output .= "<tr>
                            <td>" . $sk['model'] . "</td>
                            <td>" . number_format($sk['qty_kirim'], 0, ".", ".") . "</td>
                            <td>Rp. " . number_format($sk['price'], 0, ".", ".") . "</td>
                            <td>Rp. " . number_format($sk['amount'], 0, ".", ".") . "</td>
                        </tr>";
        }
        $output .= '</tbody>
        <tfoot>
            <tr>
                <th>Total</th>
                <th>' . $rw["total_qty"] . '</th>
                <th>Rp. ' . number_format($rw["totalprice"], 0, ".", ".") . '</th>
                <th>Rp. ' . number_format($rw["totalamount"], 0, ".", ".") . '</th>
            </tr>
            <tr>
                <th colspan="3">Diskon</th>
                <th>Rp. ' . number_format($rw["diskons"], 0, ".", ".") . '</th>
            </tr>
            <tr>
                <th colspan="3">PPN</th>
                <th>Rp. ' . number_format($rw["ppns"], 0, ".", ".") . '</th>
            </tr>
            <tr>
                <th colspan="3">Harga Total (inc.)</th>
                <th>Rp. ' . number_format($rw["total_semua"], 0, ".", ".") . '</th>
            </tr>
        </tfoot>
        </table>';
        print_r($output);
    }

    public function poxb()
    {
        $data['title']  = 'PO XB';
        $data['user']   = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['pt']     = $this->db->get('tb_perusahaan')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('adminpo/po-xb', $data);
        $this->load->view('templates/footer');
    }
    public function export()
    {
        var_dump($this->input->post());
    }

    public function tambahpo()
    {
        $data['title']  = 'PO XB';
        $data['user']   = $this->db->get_where('tb_user', ['email' => $this->session->userdata('email')])->row_array();
        $data['pt']     = $this->db->get('tb_perusahaan')->result_array();
        $data['bilom']  = $this->db->get('tb_bom')->result_array();

        $this->load->view('templates/header', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/topbar', $data);
        $this->load->view('adminpo/po-xbadd', $data);
        $this->load->view('templates/footer');
    }
    public function getSkuXb()
    {
        $id = $this->input->post('id');

        $kets = $this->db->get_where('tb_bom', ['id' =>$id])->result_array();
        $data = array();

        foreach ($kets as $k) {
            $data = array(
                'harga' => $k['harga']
            );
        }
        print_r(json_encode($data));

    }

}