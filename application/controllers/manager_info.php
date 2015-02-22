<?php

class Manager_info extends Controller {

    var $manager_id;
    var $obj;
    var $data;

    //----------------------------------------------------------------------
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
            $this->load->view('vmanager_info', $this->data);
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
            $this->manager_id = $this->generate_id();
            $this->db->set('id', $this->manager_id, TRUE);
            $this->db->set('name', $this->obj->manager_name, TRUE);
            $this->db->set('designation_id', $this->obj->designation_id, TRUE);
            $this->db->set('branch_id', $this->obj->place_of_posting_id, TRUE);
            $this->db->set('mobile_no', $this->obj->mobile, TRUE);
            $this->db->set('remarks', $this->obj->remarks, TRUE);
            $this->db->insert('tbl_managers');
            $msg_validation['manager_id'] = $this->manager_id;
            echo json_encode($msg_validation);
        } else {
            echo json_encode($msg_validation);
        }
    }

    //----------------------------------------------------------------------
    function generate_id() {
        $this->check_login_status();
        $query = $this->db->select_max('id')->get('tbl_managers');
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $max_id = substr($row->id, 2);
            $new_id = $max_id + 1;
            if ($new_id < 10) {
                $new_id = "M00000" . $new_id;
            } elseif ($new_id < 100) {
                $new_id = "M0000" . $new_id;
            } elseif ($new_id < 1000) {
                $new_id = "M000" . $new_id;
            } elseif ($new_id < 10000) {
                $new_id = "M00" . $new_id;
            } elseif ($new_id < 100000) {
                $new_id = "M0" . $new_id;
            } else {
                $new_id = "M" . $new_id;
            }
            return $new_id;
        }

        if ($query->num_rows() < 0) {
            return "M000001";
        }
    }

    //----------------------------------------------------------------------

    function display_data() {
        $this->check_login_status();
        $this->load->library('table');
        $custom_field = "concat(\'<a class=\"updateBtn\" href=\"branch_info/update_data/', tbl_managers.id, \'\" >Update/Edit</a> \')";
        $custom_field = strip_slashes($custom_field);
        $sql = "SELECT tbl_managers.id, tbl_managers.name, tbl_designation.designation, tbl_branches.name as branch, tbl_managers.mobile_no, tbl_managers.remarks, $custom_field AS Action FROM tbl_managers INNER JOIN tbl_designation ON tbl_managers.designation_id = tbl_designation.id INNER JOIN tbl_branches ON tbl_branches.id = tbl_managers.branch_id ORDER BY tbl_managers.id";
        $query = $this->db->query($sql);

        if ($query->num_rows() > 0) {
            $tmpl = array('table_open' => '<table id="records">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
            $this->table->set_template($tmpl);
            $this->table->set_heading('ID', 'Name', 'Designation', 'Place of Posting', 'Mobile', 'Remarks', 'Action');
            $this->data['table_data'] = $this->table->generate($query);
        } else {
            $this->data['table_data'] = "No Information Found";
        }
    }

    //----------------------------------------------------------------------

    function validate() {
        $this->check_login_status();
        if (strlen($this->obj->manager_name) < 1) {
            return "Name is Required";
        } 
        
        $sql = "SELECT id, designation FROM tbl_designation WHERE id ='" . $this->obj->designation_id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
//            $this->cust_name = $row->Name;
        } else {
            return "Designation is Required";
        }
        
        $sql = "SELECT id, name FROM tbl_branches WHERE id ='" . $this->obj->place_of_posting_id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            $row = $query->row();
//            $this->cust_name = $row->Name;
        } else {
            return "Branch Name is Required";
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

        $this->db->where('id', $id);
        $this->db->delete('tbl_managers');
        echo "Records deleted successfully";
    }

    //----------------------------------------------------------------------
    function update_data() {
        $this->check_login_status();
        $this->obj = json_decode($this->input->post('jsarray', TRUE));
        $msg_validation['valid'] = $this->validate();
        if ($msg_validation['valid'] == "Success") {
            $this->manager_id = $this->obj->manager_id;
            $this->db->set('name', $this->obj->manager_name, TRUE);
            $this->db->set('designation_id', $this->obj->designation_id, TRUE);
            $this->db->set('branch_id', $this->obj->place_of_posting_id, TRUE);
            $this->db->set('mobile_no', $this->obj->mobile, TRUE);
            $this->db->set('remarks', $this->obj->remarks, TRUE);
            $this->db->where('id', $this->manager_id, TRUE);
            $this->db->update('tbl_managers');
            $msg_validation['manager_id'] = $this->manager_id;
            echo json_encode($msg_validation);
        } else {
            echo json_encode($msg_validation);
        }
    }

    //----------------------------------------------------------------------
    function getById($id) {
        $this->check_login_status();
        $sql = "SELECT tbl_managers.id, tbl_managers.name, tbl_designation.id as designation_id, tbl_designation.designation, tbl_branches.id as place_of_posting_id, tbl_branches.name as place_of_posting, tbl_managers.mobile_no, tbl_managers.remarks FROM tbl_managers INNER JOIN tbl_designation ON tbl_managers.designation_id = tbl_designation.id INNER JOIN tbl_branches ON tbl_branches.id = tbl_managers.branch_id WHERE tbl_managers.ID ='" . $id . "'";
        $query = $this->db->query($sql);
        if ($query->num_rows() > 0) {
            echo json_encode($query->row());
        } else {
            echo json_encode(array());
        }
    }

    //----------------------------------------------------------------------
    //Functions for showing the group name in the auto complete text box
    function get_designation() {
        $this->check_login_status();
        if (isset($_REQUEST['q'])) {
            $sql = "SELECT id, designation as name FROM tbl_designation WHERE designation LIKE '%" . $_REQUEST['q'] . "%' ORDER BY id";
            $query = $this->db->query($sql);
        } else {
            $sql = "SELECT id, designation as name FROM tbl_designation";
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
    //Functions for showing the group name in the auto complete text box
    function get_place_of_posting() {
        $this->check_login_status();
        if (isset($_REQUEST['q'])) {
            $sql = "SELECT id, name FROM tbl_branches WHERE name LIKE '%" . $_REQUEST['q'] . "%' ORDER BY id";
            $query = $this->db->query($sql);
        } else {
            $sql = "SELECT id, name FROM tbl_branches";
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
        $sql = "SELECT id, name, designation, place_of_posting, mobile_no, remarks FROM tbl_managers ORDER BY id";
        $query = $this->db->query($sql);
        $this->load->library('table');
        if ($query->num_rows() > 0) {
            $tmpl = array('table_open' => '<table id="branches">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
            $this->table->set_template($tmpl);
            $this->table->set_heading('ID', 'Name', 'designation', 'place_of_posting', 'Mobile', 'District');
            $this->data['table_data'] = $this->table->generate($query);
        } else {
            $this->data['table_data'] = "No Information Found";
        }

        $this->load->view('vrpt_manager_info', $this->data);
    }

    //----------------------------------------------------------------------
}

?>