<?php

class Despatch_contacts extends Controller {
    var $contact_id;
    var $obj;
    var $data;
    
	function __construct() {
		parent::__construct();
		//$this->validate();
	}

	//----------------------------------------------------------------------

           function check_login_status(){
                         if($this->session->userdata('logged_in') != TRUE){
                             exit(0);
                          }
            }
          //----------------------------------------------------------
	function index() {
            $this->check_login_status();
                if($this->session->userdata('logged_in') == TRUE){
                    $this->display_data();
                    $this->load->view('vdespatch_contacts', $this->data);
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
			$this->contact_id = $this->generate_id();
			$this->db->set('id', $this->contact_id, TRUE);
			$this->db->set('name', $this->obj->contact_name, TRUE);
                        $this->db->set('address', $this->obj->address, TRUE);
                        $this->db->set('phone', $this->obj->phone, TRUE);
                        $this->db->set('mobile', $this->obj->mobile, TRUE);
                        $this->db->set('fax', $this->obj->fax, TRUE);
                        $this->db->set('email', $this->obj->email, TRUE);
                        $this->db->set('remarks', $this->obj->remarks, TRUE);
			$this->db->insert('tbl_despatch_contacts');
			$msg_validation['contact_id'] = $this->contact_id;
                        echo json_encode($msg_validation);
		} else {
                        echo json_encode($msg_validation);
		}
	}

	//----------------------------------------------------------------------

	function generate_id() {
            $this->check_login_status();
		$query = $this->db->select_max('id')->get('tbl_despatch_contacts');
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$max_id = substr($row->id, 2);
			$new_id = $max_id + 1;
			if ($new_id < 10) {
				$new_id = "K00000" . $new_id;
			} elseif ($new_id < 100) {
				$new_id = "K0000" . $new_id;
			} elseif ($new_id < 1000) {
				$new_id = "K000" . $new_id;
			} elseif ($new_id < 10000) {
				$new_id = "K00" . $new_id;
			} elseif ($new_id < 100000) {
				$new_id = "K0" . $new_id;
			} else {
				$new_id = "K" . $new_id;
			}
			return $new_id;
		}

		if ($query->num_rows() < 0) {
			return "K000001";
		}
	}

	//----------------------------------------------------------------------

	function display_data() {
            $this->check_login_status();
                $this->load->library('table');
                $custom_field = "concat(\'<a class=\"updateBtn\" href=\"contacts_info/update_data/', tbl_despatch_contacts.id, \'\" >Update/Edit</a> \')";
                $custom_field = strip_slashes($custom_field);
                $sql = "SELECT id, name, address, phone, mobile, fax, email, remarks, $custom_field AS Action FROM tbl_despatch_contacts ORDER BY id";
                $query = $this->db->query($sql);
                
		if ($query->num_rows() > 0) {
                        $tmpl = array ('table_open'=> '<table id="records">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
			$this->table->set_template($tmpl);
                        $this->table->set_heading('ID', 'Name', 'Address', 'Phone', 'Mobile', 'Fax', 'Email', 'Remarks', 'Action');
                        $this->data['table_data'] = $this->table->generate($query);
		} else {
                        $this->data['table_data'] = "No Information Found";
		}
	}

	//----------------------------------------------------------------------

	function validate() {
            $this->check_login_status();
                if(strlen($this->obj->contact_name) < 1) {
                    return "Name is Required";
                } elseif(strlen($this->obj->address) < 1) {
                    return "Address is Required";
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
		$this->db->delete('tbl_despatch_contacts');
		echo "Records deleted successfully";
	}

	//----------------------------------------------------------------------
	function update_data() {
            $this->check_login_status();
		$this->obj = json_decode($this->input->post('jsarray', TRUE));
		$msg_validation['valid'] = $this->validate();
		if ($msg_validation['valid'] == "Success") {
                        $this->contact_id = $this->obj->contact_id;
                        $this->db->set('name', $this->obj->contact_name, TRUE);
			$this->db->set('address', $this->obj->address, TRUE);
			$this->db->set('phone', $this->obj->phone, TRUE);
			$this->db->set('mobile', $this->obj->mobile, TRUE);
			$this->db->set('fax', $this->obj->fax, TRUE);
			$this->db->set('email', $this->obj->email, TRUE);
			$this->db->set('remarks', $this->obj->remarks, TRUE);
			$this->db->where('ID', $this->contact_id, TRUE);
			$this->db->update('tbl_despatch_contacts');
                        $msg_validation['contact_id'] = $this->contact_id;
			echo json_encode($msg_validation);
		} else {
			echo json_encode($msg_validation);
                }
	}
	//----------------------------------------------------------------------
	function getById($id) {
            $this->check_login_status();
		$query = $this->db->where('id', $id)->limit(1)->get('tbl_despatch_contacts');
		if ($query->num_rows() > 0) {
			echo json_encode($query->row());
		} else {
			echo json_encode(array());
		}
	}
	//----------------------------------------------------------------------
        function show_report(){
            $this->check_login_status();
		$sql = "SELECT id, name, address, phone, mobile, fax, email, remarks FROM tbl_despatch_contacts ORDER BY id";
                $query = $this->db->query($sql);
                $this->load->library('table');
		if ($query->num_rows() > 0) {
                        $tmpl = array ('table_open'=> '<table id="branches">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
			$this->table->set_template($tmpl);
                        $this->table->set_heading('ID', 'Name', 'designation', 'Phone', 'Mobile', 'Fax', 'Email', 'Remarks');
                        $this->data['table_data'] = $this->table->generate($query);
		} else {
                        $this->data['table_data'] = "No Information Found";
		}
                
                $this->load->view('vrpt_contacts_info', $this->data);
	}
        //----------------------------------------------------------------------
}
?>