<?php defined('BASEPATH') OR exit('No direct script access allowed');


class MY_Controller extends CI_Controller {
	
    protected $ref = '';
	
    protected $title = 'No Name';
        protected $includes = array();

	

        protected function check_access (){
            if (!$this->ion_auth->logged_in()){
                       redirect('auth/logout', 'refresh');
		}
		else
                    if ($this->ion_auth->is_group($this->ref)){
                        return true;
                    }
                    else {
                       redirect('auth/logout', 'refresh');
                    }
        }

        function index (){
            $this->check_access();

            $this->tloader->display(array(), $this->includes, $this->ref, $this->title, 'backend');
        }

        protected function show ($data){
            $this->check_access();

            $this->tloader->display($data, $this->includes, $this->ref, $this->title, 'backend');
        }

        function change_password (){
                $this->check_access();

                $this->form_validation->set_rules('old', 'Old password', 'required');
		$this->form_validation->set_rules('new', 'New Password', 'required|min_length[' . $this->config->item('min_password_length', 'ion_auth') . ']|max_length[' . $this->config->item('max_password_length', 'ion_auth') . ']|matches[new_confirm]');
		$this->form_validation->set_rules('new_confirm', 'Confirm New Password', 'required');

		$user = $this->ion_auth->get_user($this->session->userdata('user_id'));

		if ($this->form_validation->run() == false)
		{ 
			$this->data['message'] = (validation_errors()) ? validation_errors() : $this->session->flashdata('message');

			$this->data['old_password'] = array('name' => 'old',
				'id' => 'old',
				'type' => 'password',
			);
			$this->data['new_password'] = array('name' => 'new',
				'id' => 'new',
				'type' => 'password',
			);
			$this->data['new_password_confirm'] = array('name' => 'new_confirm',
				'id' => 'new_confirm',
				'type' => 'password',
			);
			$this->data['user_id'] = array('name' => 'user_id',
				'id' => 'user_id',
				'type' => 'hidden',
				'value' => $user->id,
			);
                        $this->data['ref'] = $this->ref;
			//render
                        $this->data['iviews'] = array ('/shared/change_password');

                        $this->show ($this->data);
		}
		else
		{
			$identity = $this->session->userdata($this->config->item('identity', 'ion_auth'));

			$change = $this->ion_auth->change_password($identity, $this->input->post('old'), $this->input->post('new'));

			if ($change)
			{ 
				$this->session->set_flashdata('message', $this->ion_auth->messages());
				redirect($this->ref);
			}
			else
			{
				$this->session->set_flashdata('message', $this->ion_auth->errors());
				$this->data['ref'] = $this->ref;
                                //render
                                $this->data['iviews'] = array ('/shared/change_password');

                                $this->show ($this->data);
			}
		}
        }

        // update session user profile
        function update_profile(){
                $this->check_access();

                $id = $this->session->userdata('user_id');

		$this->data['title'] = "Edit Profile";

		
		$this->form_validation->set_rules('first_name', 'First Name', 'required|xss_clean');
		

		if ($this->form_validation->run() == true)
		
		if ($this->form_validation->run() == true && $this->ion_auth->update_user($id, $additional_data))
		{ 
			$this->session->set_flashdata('message', "User Updated");
			redirect($this->ref, 'refresh');
		}
		else
		{
			 
			$this->data['message'] = (validation_errors() ? validation_errors() : ($this->ion_auth->errors() ? $this->ion_auth->errors() : $this->session->flashdata('message')));

                        $user = $this->ion_auth->get_user($id);

			$this->data['first_name'] = array('name' => 'first_name',
				'id' => 'first_name',
				'type' => 'text',
				'value' => $user->first_name,
			);
                        $this->data['user'] = $user;
                        $this->data['ref'] = $this->ref;
                        //render
                        $this->data['iviews'] = array ('/shared/update_profile');

                        $this->show ($this->data);			
		}
	}
       
}

?>
