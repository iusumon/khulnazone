<?php

class Welcome extends Controller {

	function Welcome()
	{
		parent::Controller();	
	}
	
           function check_login_status(){
                         if($this->session->userdata(logged_in) != TRUE){
                             exit(0);
                          }
            }
          //----------------------------------------------------------
	function index() {
		$this->load->view('welcome_message');
        }
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */