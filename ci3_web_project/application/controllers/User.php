<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
    }

    public function index()
    {
        $user_info = $this->session->userdata('user_info');
        if ($user_info) {
            $data['user'] = $user_info;

            $json_data = file_get_contents(APPPATH . 'json/taiwan_cities.json');
            $data['tw_cities'] = json_decode($json_data, true);
            $this->load->view('user_info', $data);
        } else {
            redirect('user/signin');
        }
    }

    public function signup()
    {
        $this->load->view('signup');
    }

    public function insert()
    {
        $this->form_validation->set_rules('password', '密碼', 'required|max_length[20]|min_length[8]');
        $this->form_validation->set_rules('password_confirm', '密碼確認', 'required|matches[password]');
        $this->form_validation->set_rules('email', '信箱', 'required|valid_email|callback_check_email');

        //$this->form_validation->set_message('required', '請填寫 %s');
        $this->form_validation->set_custom_error_messages();
        $captcha_check = $this->captcha_validate();

        if (!$this->form_validation->run() || $captcha_check) {
            // $error_message = validation_errors(); // 取得表單驗證錯誤訊息

            if (form_error('password')) {
                $this->session->set_flashdata('password_error', form_error('password'));
            }
            if (form_error('password_confirm')) {
                $this->session->set_flashdata('password_confirm_error', form_error('password_confirm'));
            }
            if (form_error('email')) {
                $this->session->set_flashdata('email_error', form_error('email'));
            }
            redirect('user/signup');
        } else {
            $email = $this->input->post("email");
            $email_check = $this->user_model->get_user_by_email($email);
            if ($email_check) {
                $this->session->set_flashdata('email_check', "信箱已註冊過");
                redirect('user/signup');
            } else {
                $unique_code = uniqid();
                $user_data = [
                    "username" => $this->input->post('username'),
                    "password" => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                    "email" => $email,
                    'unique_code' => $unique_code
                ];

                $this->user_model->insert_user($user_data);
                $link = base_url('user/verify/' . $unique_code);
                $send_email = $this->send_email_setting(
                    $user_data["email"],
                    "註冊確認信",
                    "<div>
                    <p>HELLO</p>
                    <h1>請按確認: <a href='" . $link . "'>確認連結</a></h1>
                    </div>"
                );
                if ($send_email) {
                    echo "成功";
                } else {
                    echo "失敗";
                }
                redirect('user/signin');
            }
        }
    }

    // reCAPTCHA 驗證
    public function captcha_validate()
    {
        $captcha_response = trim($this->input->post('g-recaptcha-response'));

        if ($captcha_response != '') {
            $keySecret = '6LewFjYpAAAAAMt3Hs0zv1D7r5xwzgHOuqZj1QaY';

            $check = array(
                'secret' => $keySecret,
                'response' => $this->input->post('g-recaptcha-response')
            );

            $startProcess = curl_init();

            curl_setopt($startProcess, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");

            curl_setopt($startProcess, CURLOPT_POST, true);

            curl_setopt($startProcess, CURLOPT_POSTFIELDS, http_build_query($check));

            curl_setopt($startProcess, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($startProcess, CURLOPT_RETURNTRANSFER, true);

            $receiveData = curl_exec($startProcess);

            $finalResponse = json_decode($receiveData, true);

            if ($finalResponse['success']) {

                $this->session->set_flashdata('captcha_success', '驗證成功');
                return true;
                //redirect('welcome');
            } else {
                $this->session->set_flashdata('captcha_fail', '驗證失敗');
                return false;
                //redirect('welcome');
            }
        } else {
            $this->session->set_flashdata('captcha_fail', '驗證失敗');
            return false;
            //redirect('welcome');
        }
    }

    public function send_email_setting($to, $subject, $message)
    {
        $this->load->config("email");
        $from = $this->config->item("smtp_user");

        $this->email->from($from);
        $this->email->to($to);
        $this->email->subject($subject);
        $this->email->message($message);

        return $this->email->send();
    }

    public function verify($code)
    {
        $user = $this->user_model->getUserByUniqueCode($code);

        if ($user && $user['is_verified'] == 0) {

            $this->user_model->update_user_verified($user['user_id']);
            redirect("user/signin");
        } elseif ($user && $user['is_verified'] == 1) {
            echo "您的帳戶已經驗證過，請直接登入。";
        } else {
            echo "驗證失敗";
        }
    }

    public function signin()
    {
        $this->load->view("signin");
    }

    public function user_check()
    {
        $email = $this->input->post('email');
        $password = $this->input->post('password');
        $user = $this->user_model->get_user_by_email($email);
        if ($user && password_verify($password, $user->password)) {
            $this->session->set_userdata('user_info', $user);

            redirect("user/index");
        } else {
            if (!$user) {
                $this->session->set_flashdata('user_check', '無此用戶,請先註冊');
                redirect('user/signin');
            } else {
                $this->session->set_flashdata('password_check', '密碼有誤,請重新輸入');
                redirect('user/signin');
            }
        }
    }

    public function edit()
    {
        // 使用 file_get_contents('php://input') 來接收 JSON 資料
        $receivedData = json_decode(file_get_contents('php://input'), true);

        // $user = $this->session->userdata('user_info');
        // $id = $user->user_id;
        $id = $receivedData['id'];
        $data = [];
        // 檢查並設置非空值
        if (!empty($receivedData['username'])) {
            $data['username'] = $receivedData['username'];
        }
        if (!empty($receivedData['contact_number'])) {
            $data['contact_number'] = $receivedData['contact_number'];
        }
        if (!empty($receivedData['birthday'])) {
            $data['birthday'] = $receivedData['birthday'];
        }
        if (!empty($receivedData['city'])) {
            $data['city'] = $receivedData['city'];
        }
        if (!empty($receivedData['district'])) {
            $data['district'] = $receivedData['district'];
        }
        if (!empty($receivedData['zip'])) {
            $data['zip'] = $receivedData['zip'];
        }
        if (!empty($receivedData['address'])) {
            $data['address'] = $receivedData['address'];
        }
        if (!empty($data)) {
            $this->user_model->update_user($id, $data);
            // 更新成功後，獲取最新的用戶資訊
            $updatedUserData = $this->user_model->get_user_by_id($id);
            // 將最新的用戶資訊設置到 session 中
            $this->session->set_userdata('user_info', $updatedUserData);

            $response = array(
                'success' => true,
                'message' => 'User information updated successfully',
                'user' => $updatedUserData
            );
        } else {
            $response = array('success' => false, 'message' => 'No specific data to update');
        }

        echo json_encode($response);
    }
}
