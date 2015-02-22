<?php

class Outward_register extends Controller {

    var $outward_id;
    var $obj;
    var $data;

    function __construct() {
        parent::__construct();
        //$this->validate();
    }

    //----------------------------------------------------------------------

    function check_login_status() {
        if ($this->session->userdata('logged_in') != TRUE) {
            exit(0);
        }
    }

    //----------------------------------------------------------
    function index() {
        $this->check_login_status();
        if ($this->session->userdata('logged_in') == TRUE) {
            $this->display_data();
            $this->load->view('voutward_register', $this->data);
        } else {
            $this->load->view('vlogin');
        }
    }

    //----------------------------------------------------------------------

    function save_data() {
        $this->check_login_status();
        $this->obj = json_decode($this->input->post('jsarray', TRUE));
        $msg_validation['valid'] = $this->validate();
        if ($msg_validation['valid'] == "Success") {
            $this->outward_id = $this->generate_id();
            $this->db->set('id', $this->outward_id, TRUE);

            $out_date = date("Y-m-j", strtotime($this->obj->out_date));
            $this->db->set('date', $out_date, TRUE);

            $this->db->set('recipient', $this->obj->recipient, TRUE);
            $this->db->set('category_id', $this->obj->category_id, TRUE);
            $this->db->set('particulars', $this->obj->particulars, TRUE);
            $this->db->set('service_charge', $this->obj->service_charge, TRUE);
            $this->db->set('receipt_no', $this->obj->receipt_no, TRUE);
            $this->db->set('agent_id', $this->obj->agent_id, TRUE);
            $this->db->set('remarks', $this->obj->remarks, TRUE);
            $this->db->insert('tbl_outward_register');

            $msg_validation['outward_id'] = $this->outward_id;
            echo json_encode($msg_validation);
        } else {
            echo json_encode($msg_validation);
        }
    }

    //----------------------------------------------------------------------

    function generate_id() {
        $this->check_login_status();
        $cur_year = date('Y');
        $sql = "SELECT MAX(id) AS max_id FROM tbl_outward_register WHERE id LIKE 'OWR-" . $cur_year . "%'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $max_id = substr($row->max_id, 13);
            $new_id = $max_id + 1;
            if ($new_id < 10) {
                $new_id = "OWR-" . $cur_year . "-KHZ-00000" . $new_id;
            } elseif ($new_id < 100) {
                $new_id = "OWR-" . $cur_year . "-KHZ-0000" . $new_id;
            } elseif ($new_id < 1000) {
                $new_id = "OWR-" . $cur_year . "-KHZ-000" . $new_id;
            } elseif ($new_id < 10000) {
                $new_id = "OWR-" . $cur_year . "-KHZ-00" . $new_id;
            } elseif ($new_id < 100000) {
                $new_id = "OWR-" . $cur_year . "-KHZ-0" . $new_id;
            } else {
                $new_id = "OWR-" . $cur_year . "-KHZ-" . $new_id;
            }
            return $new_id;
        }

        if ($query->num_rows() < 0) {
            return "OWR-" . $cur_year . "-KHZ-00000" . $new_id;
        }
    }

    //----------------------------------------------------------------------

    function display_data() {
        $this->check_login_status();
        $this->load->library('table');
        $today = date('Y-m-d');
        $custom_field = "concat(\'<a class=\"updateBtn\" href=\"outward_register/update_data/', t1.id, \'\" >Update/Edit</a> \')";
        $custom_field = strip_slashes($custom_field);
        $sql = "SELECT t1.id, DATE_FORMAT(t1.Date, '%d-%m-%Y') AS Date, t1.recipient, t2.category_name, t1.particulars, t1.receipt_no, t3.name, $custom_field AS Action FROM tbl_outward_register t1 INNER JOIN tbl_despatch_category t2 ON t2.id = t1.category_id INNER JOIN tbl_courier_agents t3 ON t3.id = t1.agent_id WHERE t1.date BETWEEN '" . $today . "' AND '" . $today . "' ORDER BY t1.id";
        $query = $this->db->query($sql);

        if ($query->num_rows() >= 0) {
            $tmpl = array('table_open' => '<table id="records">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
            $this->table->set_template($tmpl);
            $this->table->set_heading('ID', 'Date', 'Recipient', 'Category', 'Particulars', 'Receipt No', 'Cour./Post. Agent', 'Action');
            $this->data['table_data'] = $this->table->generate($query);
        } else {
            $this->data['table_data'] = "No Information Found";
        }
    }

    //----------------------------------------------------------------------

    function validate() {
        $this->check_login_status();
        if (strlen($this->obj->recipient) < 1) {
            return "Recipient is Required";
        } elseif (strlen($this->obj->particulars) < 1) {
            return "Particulars is Required";
        } elseif (strlen($this->obj->category_id) < 1) {
            return "Category/Types is Required";
        } elseif (!is_numeric($this->obj->service_charge)) {
            return "Sub Total is not Numeric";
        }
        return "Success";
    }

    //----------------------------------------------------------------------
    function delete_data($id) {
        $this->check_login_status();
        if (is_null($id)) {
            echo 'Error: ID Not Provide';
            return;
        }


//                if($this->Model_db_integrity_check->check_integrity('tblCredit', 'CreditorID', $id ) == false ){
//                    echo 'This Branch cannot be deleted';
//		    return;
//                }
//                
//                if($this->Model_db_integrity_check->check_integrity('tblCreditorPayment', 'CreditorID', $id ) == false ){
//                    echo 'This Branch cannot be deleted';
//		    return;
//                }

        $this->db->where('id', $id);
        $this->db->delete('tbl_outward_register');
        echo "Records deleted successfully";
    }

    //----------------------------------------------------------------------
    function update_data() {
        $this->check_login_status();
        $this->obj = json_decode($this->input->post('jsarray', TRUE));
        $msg_validation['valid'] = $this->validate();
        if ($msg_validation['valid'] == "Success") {
            $this->outward_id = $this->obj->outward_id;
            
            $out_date = date("Y-m-j", strtotime($this->obj->out_date));
            $this->db->set('date', $out_date, TRUE);

            $this->db->set('recipient', $this->obj->recipient, TRUE);
            $this->db->set('category_id', $this->obj->category_id, TRUE);
            $this->db->set('particulars', $this->obj->particulars, TRUE);
            $this->db->set('service_charge', $this->obj->service_charge, TRUE);
            $this->db->set('receipt_no', $this->obj->receipt_no, TRUE);
            $this->db->set('agent_id', $this->obj->agent_id, TRUE);
            $this->db->set('remarks', $this->obj->remarks, TRUE);
            $this->db->where('id', $this->outward_id, TRUE);
            $this->db->update('tbl_outward_register');
            $msg_validation['outward_id'] = $this->outward_id;
            echo json_encode($msg_validation);
        } else {
            echo json_encode($msg_validation);
        }
    }

    //----------------------------------------------------------------------
    function getById($id) {
        $this->check_login_status();
        $sql = "SELECT t1.id, DATE_FORMAT(t1.date, '%d-%m-%Y') AS date, t1.recipient, t2.category_name, t1.category_id, t1.particulars, t1.service_charge, t1.receipt_no, t3.name as agent_name, t1.agent_id, t1.remarks FROM tbl_outward_register t1 INNER JOIN tbl_despatch_category t2 ON t1.category_id = t2.id INNER JOIN tbl_courier_agents t3 ON t3.id = t1.agent_id WHERE t1.id ='". $id . "'" ;
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            echo json_encode($query->row());
        } else {
            echo json_encode(array());
        }
    }
    
    //----------------------------------------------------------------------
    //Functions for showing the customer name in the auto complete text box
    function get_category_name() {
        $this->check_login_status();
        if (isset($_REQUEST['q'])) {
            $sql = "SELECT id, category_name as name FROM tbl_despatch_category WHERE category_name LIKE '%" . $_REQUEST['q'] . "%'";
            $query = $this->db->query($sql);
        } else {
            $sql = "SELECT id, category_name as name FROM tbl_despatch_category";
            $query = $this->db->query($sql);
        }

        if ($query->num_rows() > 0) {
            $result = json_encode($query->result_array());
            $result = '{"results":' . $result . '}';
            echo $result;
        } else {
            echo json_encode(array());
        }
    }
    //----------------------------------------------------------------------
    //Functions for showing the customer name in the auto complete text box
    function get_recipient_name() {
        $this->check_login_status();
        if (isset($_REQUEST['q'])) {
            $sql = "SELECT id, name FROM tbl_despatch_contacts WHERE name LIKE '%" . $_REQUEST['q'] . "%'";
            $query = $this->db->query($sql);
        } else {
            $sql = "SELECT id, name FROM tbl_despatch_contacts";
            $query = $this->db->query($sql);
        }

        if ($query->num_rows() > 0) {
            $result = json_encode($query->result_array());
            $result = '{"results":' . $result . '}';
            echo $result;
        } else {
            echo json_encode(array());
        }
    }
    //----------------------------------------------------------------------
    //Functions for showing the customer name in the auto complete text box
    function get_agent_name() {
        $this->check_login_status();
        if (isset($_REQUEST['q'])) {
            $sql = "SELECT id, name FROM tbl_courier_agents WHERE name LIKE '%" . $_REQUEST['q'] . "%'";
            $query = $this->db->query($sql);
        } else {
            $sql = "SELECT id, name FROM tbl_courier_agents";
            $query = $this->db->query($sql);
        }

        if ($query->num_rows() > 0) {
            $result = json_encode($query->result_array());
            $result = '{"results":' . $result . '}';
            echo $result;
        } else {
            echo json_encode(array());
        }
    }


    //----------------------------------------------------------------------
    function show_report() {
        $this->check_login_status();
        $sql = "SELECT id, name, address, phone, mobile, fax FROM tbl_outward_register ORDER BY id";
        $query = $this->db->query($sql);
        $this->load->library('table');
        if ($query->num_rows() > 0) {
            $tmpl = array('table_open' => '<table id="outward_register">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
            $this->table->set_template($tmpl);
            $this->table->set_heading('ID', 'Name', 'Address', 'Phone', 'Mobile', 'District');
            $this->data['table_data'] = $this->table->generate($query);
        } else {
            $this->data['table_data'] = "No Information Found";
        }

        $this->load->view('vrpt_outward_register', $this->data);
    }

    //-------------------------------------------------------------------------
    //Functions to filter the data in the Report List of First Tab based on Date
    function filter_data($date_filter, $date_filter1) {
        $this->check_login_status();
        $from_date = date("Y-m-j", strtotime($date_filter));
        $to_date = date("Y-m-j", strtotime($date_filter1));

        $custom_field = "concat(\'<a class=\"updateBtn\" href=\"outward_register/update_data/', t1.id, \'\" >Update/Edit</a> \')";
        $custom_field = strip_slashes($custom_field);
        //$sql = "SELECT t1.id, DATE_FORMAT(t1.date, '%d-%m-%Y') AS date, t1.recipient, t2.category_name, t1.particulars, t1.remarks, $custom_field AS Action FROM tbl_outward_register t1 INNER JOIN tbl_despatch_category t2 ON t2.id = t1.category_id WHERE t1.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' ORDER BY t1.id";
        
        $sql = "SELECT t1.id, DATE_FORMAT(t1.date, '%d-%m-%Y') AS date, t1.recipient, t2.category_name, t1.particulars, t1.receipt_no, t3.name, $custom_field AS Action FROM tbl_outward_register t1 INNER JOIN tbl_despatch_category t2 ON t2.id = t1.category_id INNER JOIN tbl_courier_agents t3 ON t3.id = t1.agent_id WHERE t1.date BETWEEN '" . $from_date . "' AND '" . $to_date . "' ORDER BY t1.id";
        
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            echo json_encode($query->result());
        } else {
            echo json_encode(array());
        }
    }
    //------------------------------------------------------------------------------
    //Functions to filter the data in the Report List of First Tab based on Invoice ID
    function filter_serial_id($serial_id) {
        $this->check_login_status();
        
        $custom_field = "concat(\'<a class=\"updateBtn\" href=\"outward_register/update_data/', t1.id, \'\" >Update/Edit</a> \')";
        $custom_field = strip_slashes($custom_field);
        $sql = "SELECT t1.id, DATE_FORMAT(t1.date, '%d-%m-%Y') AS date, t1.recipient, t2.category_name, t1.particulars, t1.receipt_no, t3.name, $custom_field AS Action FROM tbl_outward_register t1 INNER JOIN tbl_despatch_category t2 ON t2.id = t1.category_id INNER JOIN tbl_courier_agents t3 ON t3.id = t1.agent_id WHERE t1.ID LIKE '%" . $serial_id . "'";
        
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            echo json_encode($query->result());
        } else {
            echo json_encode(array());
        }
    }
    //------------------------------------------------------------------------------
    //Functions to filter the data in the Report List of First Tab based on Invoice ID
    function filter_receipt_no($receipt_no) {
        $this->check_login_status();
        
        $custom_field = "concat(\'<a class=\"updateBtn\" href=\"outward_register/update_data/', t1.id, \'\" >Update/Edit</a> \')";
        $custom_field = strip_slashes($custom_field);
        $sql = "SELECT t1.id, DATE_FORMAT(t1.date, '%d-%m-%Y') AS date, t1.recipient, t2.category_name, t1.particulars, t1.receipt_no, t3.name, $custom_field AS Action FROM tbl_outward_register t1 INNER JOIN tbl_despatch_category t2 ON t2.id = t1.category_id INNER JOIN tbl_courier_agents t3 ON t3.id = t1.agent_id WHERE t1.receipt_no LIKE '%" . $receipt_no . "'";
        
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            echo json_encode($query->result());
        } else {
            echo json_encode(array());
        }
    }

    //----------------------------------------------------------------------
}

?>