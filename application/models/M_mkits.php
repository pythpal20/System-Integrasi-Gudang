<?php
defined('BASEPATH') or exit('No direct script access allowed');

class M_mkits extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->mkits = $this->load->database('mkits', TRUE);
    }

    public function getdataSku($search = NULL)
    {
        if (!isset($search)) {
            $this->mkits->select('*');
            $this->mkits->from('master_produk');
            $this->mkits->order_by('model', 'desc');
            $query = $this->mkits->get();

            return $query;
        } else {
            $this->mkits->select('*');
            $this->mkits->from('master_produk');
            $this->mkits->like('model', $search);
            $this->mkits->or_like('barcode', $search);
            $this->mkits->order_by('model', 'desc');
            $query = $this->mkits->get();

            return $query;
        }
    }

    public function dataMwk($idlix)
    {
        $sql = "SELECT 
            a.No_Co,
            a.no_bl,
            a.tgl_order,
            a.tgl_krm,
            c.customer_nama,
            SUM(b.harga_total) AS uang,
            a.status,
            a.sales,
            a.keterangan,
            cd.status_delivery,
            cd.tgl_delivery
        FROM customerorder_hdr a
        JOIN customerorder_dtl b ON a.No_Co = b.No_Co
        JOIN master_customer c ON a.customer_id = c.customer_id
        LEFT JOIN customerorder_hdr_delivery cd ON a.No_Co = cd.No_Co
        WHERE a.id_perusahaan = '$idlix'
        GROUP BY b.No_Co ORDER BY a.No_Co DESC";
        $query = $this->mkits->query($sql);

        return $query;
    }

    public function getTotal($id)
    {
        $sql = "SELECT SUM(qty_kirim) AS total_qty, SUM(price) AS totalprice, SUM(amount) AS totalamount, SUM(diskon) AS diskons, SUM(ppn) as ppns, SUM(harga_total) AS total_semua
        FROM customerorder_dtl
        WHERE No_Co = '$id'";

        $query = $this->mkits->query($sql);

        return $query;
    }

    public function getPicktTicket($id)
    {
        $this->mkits->SELECT('*');
        $this->mkits->FROM('customerorder_hdr');
        $this->mkits->WHERE('id_perusahaan', $id);
        $this->mkits->order_by('No_Co', 'DESC');

        $query = $this->mkits->get();
        return $query;
    }
}
