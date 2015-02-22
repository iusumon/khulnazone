<?php

class Employee_details extends Controller {
    var $employee_id;
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
                    $this->load->view('vemployee_details', $this->data);
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
			$this->employee_id = $this->generate_id();
			$this->db->set('id', $this->employee_id, TRUE);
			$this->db->set('emp_id', $this->obj->personal_id, TRUE);
			$this->db->set('name', $this->obj->name, TRUE);
                        $this->db->set('designation', $this->obj->designation, TRUE);
                        
                        $join_date = date("Y-m-j", strtotime($this->obj->join_date));
                        $this->db->set('join_date', $join_date, TRUE);
                        
                        $last_prom_date = date("Y-m-j", strtotime($this->obj->last_prom_date));
                        $this->db->set('last_prom_date', $last_prom_date, TRUE);
                        
                        $this->db->set('remarks', $this->obj->remarks, TRUE);
			$this->db->insert('tbl_employees');
                        
			$msg_validation['employee_id'] = $this->employee_id;
                        echo json_encode($msg_validation);
		} else {
                        echo json_encode($msg_validation);
		}
	}

	//----------------------------------------------------------------------

	function generate_id() {
            $this->check_login_status();
		$query = $this->db->select_max('id')->get('tbl_employees');
		if ($query->num_rows() > 0) {
			$row = $query->row();
			$max_id = substr($row->id, 2);
			$new_id = $max_id + 1;
			if ($new_id < 10) {
				$new_id = "E00000" . $new_id;
			} elseif ($new_id < 100) {
				$new_id = "E0000" . $new_id;
			} elseif ($new_id < 1000) {
				$new_id = "E000" . $new_id;
			} elseif ($new_id < 10000) {
				$new_id = "E00" . $new_id;
			} elseif ($new_id < 100000) {
				$new_id = "E0" . $new_id;
			} else {
				$new_id = "E" . $new_id;
			}
			return $new_id;
		}

		if ($query->num_rows() < 0) {
			return "E000001";
		}
	}

	//----------------------------------------------------------------------

	function display_data() {
            $this->check_login_status();
                $this->load->library('table');
                $custom_field = "concat(\'<a class=\"updateBtn\" href=\"employee_details/update_data/', tbl_employees.id, \'\" >Update/Edit</a> \')";
                $custom_field = strip_slashes($custom_field);
                $sql = "SELECT id, emp_id, name, designation, join_date, remarks, $custom_field AS Action FROM tbl_employees ORDER BY id";
                $query = $this->db->query($sql);
                
		if ($query->num_rows() > 0) {
                        $tmpl = array ('table_open'=> '<table id="records">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
			$this->table->set_template($tmpl);
                        $this->table->set_heading('ID', 'Personal ID', 'Name', 'Designation', 'Joining Date', 'Remarks', 'Action');
                        $this->data['table_data'] = $this->table->generate($query);
		} else {
                        $this->data['table_data'] = "No Information Found";
		}
	}

	//----------------------------------------------------------------------

	function validate() {
            $this->check_login_status();
                if(strlen($this->obj->name) < 1) {
                    return "Name is Required";
                } elseif(strlen($this->obj->designation) < 1) {
                    return "Designation is Required";
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
		$this->db->delete('tbl_employees');
		echo "Records deleted successfully";
	}

	//----------------------------------------------------------------------
	function update_data() {
            $this->check_login_status();
		$this->obj = json_decode($this->input->post('jsarray', TRUE));
		$msg_validation['valid'] = $this->validate();
		if ($msg_validation['valid'] == "Success") {
                        $this->employee_id = $this->obj->employee_id;
                        $this->db->set('emp_id', $this->obj->personal_id, TRUE);
                        $this->db->set('name', $this->obj->name, TRUE);
			$this->db->set('designation', $this->obj->designation, TRUE);
                        
                        $join_date = date("Y-m-j", strtotime($this->obj->join_date));
                        $this->db->set('join_date', $join_date, TRUE);
                        
                        $last_prom_date = date("Y-m-j", strtotime($this->obj->last_prom_date));
                        $this->db->set('last_prom_date', $last_prom_date, TRUE);
                        
			$this->db->set('remarks', $this->obj->remarks, TRUE);
			$this->db->where('id', $this->employee_id, TRUE);
			$this->db->update('tbl_employees');
                        $msg_validation['employee_id'] = $this->employee_id;
			echo json_encode($msg_validation);
		} else {
			echo json_encode($msg_validation);
                }
	}
	//----------------------------------------------------------------------
	function getById($id) {
            $this->check_login_status();
		$query = $this->db->where('id', $id)->limit(1)->get('tbl_employees');
		if ($query->num_rows() > 0) {
			echo json_encode($query->row());
		} else {
			echo json_encode(array());
		}
	}
	//----------------------------------------------------------------------
        function show_report(){
            $this->check_login_status();
		$sql = "SELECT id, name, address, phone, mobile, fax FROM tbl_employees ORDER BY id";
                $query = $this->db->query($sql);
                $this->load->library('table');
		if ($query->num_rows() > 0) {
                        $tmpl = array ('table_open'=> '<table id="employee_details">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
			$this->table->set_template($tmpl);
                        $this->table->set_heading('ID', 'Name', 'Address', 'Phone', 'Mobile', 'District');
                        $this->data['table_data'] = $this->table->generate($query);
		} else {
                        $this->data['table_data'] = "No Information Found";
		}
                
                $this->load->view('vrpt_employee_details', $this->data);
		
	}
        //----------------------------------------------------------------------
}
?>