<?php 
class Login extends Controller {
		function __construct(){
			parent::__construct();
		}
//------------------------------------------------------------------------------
           function check_login_status(){
                         if($this->session->userdata('logged_in') != TRUE){
                             exit(0);
                          }
            }
          //----------------------------------------------------------
		function index() {
                        $this->session->unset_userdata('logged_in');
			$this->load->view('vlogin');
		}
//------------------------------------------------------------------------------
		
		function checkLogin() {
			$user = $this->input->post('user', true);
			$passwd = $this->input->post('passwd', true);
			$this->db->where('UserName', $user);
			$query = $this->db->get('tblUsers');
			
			if($query->num_rows()>0) {
				foreach($query->result() as $row) {
					if($row->UserName == $user AND $row->Password == $passwd AND $row->user_type == 'admin') {
                                                
                                                $arr_login_data = array(
                                                    'user' => $user,
                                                    'logged_in' => TRUE,
                                                    'user_type' => $row->user_type,
                                                    'emp_id' => $row->emp_id,
                                                    'prj_name' => "IBBL Zonal Office, Khulna"
                                                );
                                                $this->session->set_userdata($arr_login_data);
                                                
						echo true;
						return;
					} elseif($row->UserName == $user AND $row->Password == $passwd AND $row->user_type == 'receiver') {
                                                
                                                $arr_login_data = array(
                                                    'user' => $user,
                                                    'logged_in' => TRUE,
                                                    'user_type' => $row->user_type,
                                                    'emp_id' => $row->emp_id,
                                                    'prj_name' => "IBBL Zonal Office, Khulna"
                                                );
                                                $this->session->set_userdata($arr_login_data);
                                                
						echo 'receiver';
						return;
					} else {
						echo false;
						return;
					}
				}
			}
			
			if($query->num_rows() < 1) {
				$this->load->view('vlogin');
			}
		}
//------------------------------------------------------------------------------
		function load_main() {
                        $this->check_login_status();
                        if($this->session->userdata('user_type') == 'admin'){
                            //check session user id and password
                            $this->load->view('vmain');
                        }elseif($this->session->userdata('user_type') == 'receiver'){
                            $this->load->view('vreceive_docs');
                        }
		}
//------------------------------------------------------------------------------
                function exit_all(){
                    $this->check_login_status();
                    $this->session->sess_destroy();
                    $this->load->view('vlogin');
                }
}
?>
