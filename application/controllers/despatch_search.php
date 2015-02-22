<?php

class Despatch_search extends Controller {
    var $category_id;
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
                $this->display_data();
                $this->load->view('vdespatch_search', $this->data);
//                $this->check_login_status();
//                    if($this->session->userdata('logged_in') == TRUE){
//                        $this->display_data();
//                        $this->load->view('vdespatch_search', $this->data);
//                    } else {
//                        $this->load->view('vlogin');
//                    }

            }

	//----------------------------------------------------------------------
	function search_data() {
//          $this->check_login_status();
            
            $this->obj = json_decode($this->input->post('jsarray', TRUE));
//            $this->obj = json_decode($jsarry);
            $sWhere_inward = "";
            
            if ($this->obj->from_date != "") {
                $sWhere_inward .= "date BETWEEN '" . date("Y-m-j", strtotime($this->obj->from_date)) . "' AND ";
            }
            
            if ($this->obj->to_date != "") {
                $sWhere_inward .= " '" . date("Y-m-j", strtotime($this->obj->to_date)) . "' AND ";
            }
            
            if ($this->obj->category_name != "") {
                $sWhere_inward .= "tbl_despatch_category.category_name = '" . $this->obj->category_name . "' AND ";
            }
            
            if ($this->obj->particulars != "") {
                $sWhere_inward .= "particulars LIKE '%" . $this->obj->particulars . "%' AND ";
            }
            
            if ($this->obj->sender != "") {
                $sWhere_inward .= "sender LIKE '%" . $this->obj->sender . "%' AND ";
            }
            
            if ($this->obj->agent != "") {
                $sWhere_inward .= "tbl_employees.name LIKE '%" . $this->obj->agent . "%' AND ";
            }

            $sWhere_inward = "WHERE (" . $sWhere_inward;
            $sWhere_inward = substr_replace($sWhere_inward, "", -4);
            $sWhere_inward .= ')';
            
            $sWhere_outward = "";
            
            if ($this->obj->from_date != "") {
                $sWhere_outward .= "date BETWEEN '" . date("Y-m-j", strtotime($this->obj->from_date)) . "' AND ";
            }
            
            if ($this->obj->to_date != "") {
                $sWhere_outward .= " '" . date("Y-m-j", strtotime($this->obj->to_date)) . "' AND ";
            }
            
            if ($this->obj->category_name != "") {
                $sWhere_outward .= "tbl_despatch_category.category_name = '" . $this->obj->category_name . "' AND ";
            }
            
            if ($this->obj->particulars != "") {
                $sWhere_outward .= "particulars LIKE '%" . $this->obj->particulars . "%' AND ";
            }
            
            if ($this->obj->sender != "") {
                $sWhere_outward .= "recipient LIKE '%" . $this->obj->sender . "%' AND ";
            }
            
            if ($this->obj->agent != "") {
                $sWhere_outward .= "tbl_courier_agents.name LIKE '%" . $this->obj->agent . "%' AND ";
            }

            $sWhere_outward = "WHERE (" . $sWhere_outward;
            $sWhere_outward = substr_replace($sWhere_outward, "", -4);
            $sWhere_outward .= ')';
            
            $sql = "SELECT tbl_inward_register.id, DATE_FORMAT(tbl_inward_register.date, '%d-%m-%Y') as date, tbl_inward_register.sender, tbl_despatch_category.category_name, particulars, tbl_employees.name FROM tbl_inward_register INNER JOIN tbl_despatch_category ON tbl_despatch_category.id = tbl_inward_register.category_id LEFT JOIN tbl_employees ON tbl_employees.emp_id = tbl_inward_register.emp_id $sWhere_inward";
            $sql .= " UNION SELECT tbl_outward_register.id, DATE_FORMAT(tbl_outward_register.date, '%d-%m-%Y') as date, tbl_outward_register.recipient, tbl_despatch_category.category_name, tbl_outward_register.particulars, tbl_courier_agents.name FROM tbl_outward_register INNER JOIN tbl_despatch_category ON tbl_despatch_category.id = tbl_outward_register.category_id INNER JOIN tbl_courier_agents ON tbl_courier_agents.id = tbl_outward_register.agent_id $sWhere_outward";
            
//            $sql = "SELECT id, category_name, remarks FROM tbl_despatch_category ORDER BY id";
                $query = $this->db->query($sql);
                $this->load->library('table');
		if ($query->num_rows() > 0) {
//                        $tmpl = array ('table_open'=> '<table id="search_despatch">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
//			$this->table->set_template($tmpl);
//                        $this->table->set_heading('ID', 'Date', 'Sender/Receiver', 'Category', 'Particulars', 'Employee ID/Agent Name');
//                        $this->data['table_data'] = $this->table->generate($query);
                        echo json_encode($query->result_array());
		} else {
                        $this->data['table_data'] = "No Information Found";
		}
                //echo $this->data['table_data'];
//                $this->load->view('vrpt_despatch_search', $this->data);
	}
	//----------------------------------------------------------------------
        function show_report(){
            $this->check_login_status();
		$sql = "SELECT id, category_name, remarks FROM tbl_despatch_category ORDER BY id";
                $query = $this->db->query($sql);
                $this->load->library('table');
		if ($query->num_rows() > 0) {
//                        $tmpl = array ('table_open'=> '<table id="branches">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
//			$this->table->set_template($tmpl);
//                        $this->table->set_heading('ID', 'Category Name', 'Remarks');
//                        $this->data['table_data'] = $this->table->generate($query);
                        echo json_encode($query->rows());
		} else {
                        $this->data['table_data'] = "No Information Found";
		}
                
                $this->load->view('vrpt_despatch_category', $this->data);
		
	}
	//----------------------------------------------------------------------
	function display_data() {
//            $this->check_login_status();
                $this->load->library('table');
//                $custom_field = "concat(\'<a class=\"updateBtn\" href=\"despatch_category/update_data/', tbl_despatch_category.id, \'\" >Update/Edit</a> \')";
//                $custom_field = strip_slashes($custom_field);
//                $sql = "SELECT id, category_name, remarks, $custom_field AS Action FROM tbl_despatch_category ORDER BY id";
                $sql = "SELECT tbl_inward_register.id, tbl_inward_register.date, tbl_inward_register.sender, tbl_despatch_category.category_name, particulars, emp_id FROM tbl_inward_register INNER JOIN tbl_despatch_category ON tbl_despatch_category.id = tbl_inward_register.category_id WHERE tbl_inward_register.id < 0";
           
                $query = $this->db->query($sql);
                
		if ($query->num_rows() >= 0) {
                        $tmpl = array ('table_open'=> '<table id="records">', 'heading_row_start' => '<thead><tr>', 'heading_row_end' => '</tr></thead><tbody>');
			$this->table->set_template($tmpl);
                        $this->table->set_heading('ID', 'Date', 'Sender/Receiver', 'Category', 'Particulars', 'Emp/Agent');
                        $this->data['table_data'] = $this->table->generate($query);
		} else {
                        $this->data['table_data'] = "No Information Found";
		}
	}

	//----------------------------------------------------------------------
	function validate() {
            $this->check_login_status();
                if(strlen($this->obj->category_name) < 1) {
                    return "Category Name is Required";
                } 
                return "Success";
        }
	//----------------------------------------------------------------------
        //Functions for showing the customer name in the auto complete text box
        function get_category_name() {
//            $this->check_login_status();
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
        function get_sender_name() {
//            $this->check_login_status();
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
}
?>