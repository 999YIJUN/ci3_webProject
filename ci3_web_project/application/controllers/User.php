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
        $this->form_validation->set_rules('email', '信箱', 'required|valid_email|check_email');

        //$this->form_validation->set_message('required', '請填寫 %s');
        $this->form_validation->set_custom_error_messages();
        $captcha_check = $this->captcha_validate();

        if (!$this->form_validation->run() || !$captcha_check) {
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
                    // password_hash 字串長度為60
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

    // 
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

    // 發信驗證
    public function verify($code)
    {
        $user = $this->user_model->getUserByUniqueCode($code);

        if ($user && $user->verified == 0) {

            $this->user_model->update_user_verified($user->user_id);
            redirect("user/signin");
        } elseif ($user && $user->verified == 1) {
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
            if ($user->verified == 1) {
                $this->session->set_userdata('user_info', $user);

                redirect("user/index");
            } else {
                $this->session->set_flashdata('verified_checked', '尚未完成驗證，請先確認您的郵件以完成註冊確認。');
                redirect('user/signin');
            }
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

    public function edit_password()
    {
        $this->form_validation->set_rules('password_reset', '密碼', 'required|max_length[20]|min_length[8]');
        $this->form_validation->set_rules('password_confirm', '密碼確認', 'required|matches[password_reset]');

        $this->form_validation->set_custom_error_messages();
        $data = $this->session->userdata('user_info');
        $id = $data->user_id;

        if (!$this->form_validation->run()) {
            $error_data = [
                'error' => true,
                'password' => form_error('password_reset'),
                'password_confirm' => form_error('password_confirm')
            ];
        } else {
            $user_data = [
                'password' => password_hash($this->input->post('password_reset'), PASSWORD_DEFAULT)
            ];
            $error_data = [
                'success' => true,
            ];

            $this->user_model->update_user($id, $user_data);
        }

        echo json_encode($error_data);
    }

    public function edit()
    {
        // 使用 file_get_contents('php://input') 來接收 JSON 資料
        // $receivedData = json_decode(file_get_contents('php://input'), true);

        $user = $this->session->userdata('user_info');
        $data = [];
        // 檢查並設置非空值
        $id = $user->user_id;
        $username = $this->input->post('username');
        $contact_number = $this->input->post('contact_number');
        $birthday = $this->input->post('birthday');
        $city = $this->input->post('city');
        $district = $this->input->post('district');
        $zip = $this->input->post('zip');
        $address = $this->input->post('address');
        if (!empty($username)) {
            $data['username'] = $username;
        }
        if (!empty($contact_number)) {
            $data['contact_number'] = $contact_number;
        }
        if (!empty($birthday)) {
            $data['birthday'] = $birthday;
        }
        if (!empty($city) && $city !== 'citySelect' && $district !== 'districtSelect') {
            $data['city'] = $city;
        }
        if (!empty($district) && $district !== 'districtSelect') {
            $data['district'] = $district;
        }
        if (!empty($zip)) {
            $data['zip'] = $zip;
        }
        if (!empty($address)) {
            $data['address'] = $address;
        }
        $beforeData = $this->user_model->get_user_by_id($id);
        if (!empty($data)) {

            $this->user_model->update_user($id, $data);
            $afterData = $this->user_model->get_user_by_id($id);
            if ($beforeData != $afterData) {
                // 保持在用戶最新資訊
                $this->session->set_userdata('user_info', $afterData);

                $response = array(
                    'success' => true,
                    'message' => '完成更新',
                    'user' => $beforeData,
                    'users' => $afterData
                );
            } else {
                $response = array('success' => false, 'message' => '沒有更新的內容', 'user' => $beforeData, 'users' => $afterData);
            }
        } else {
            $response = array('success' => false, 'message' => '沒有更新的內容');
        }

        echo json_encode($response);
    }

    public function signout()
    {
        $this->session->unset_userdata('user_info');

        redirect('user/signin');
    }

    public function user_data()
    {
        $this->load->view('user_data');
    }

    public function get_data()
    {
        $this->load->library('ssp');
        $dbDetails = array(
            'host' => $this->db->hostname,
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database
        );
        $table = 'users';
        $primaryKey = 'user_id';
        // DataTables 設定
        $columns = array(
            array(
                'db' => 'user_id', 'dt' => 0
            ),
            array(
                'db' => 'username', 'dt' => 1
            ),
            array(
                'db' => 'sort', 'dt' => 2,
                'formatter' => function ($data, $row) {
                    return '<input type="text" class="form-control sort-input" value="' . $row['sort'] . '" data-user-id="' . $row['user_id'] . '" />';
                }
            ),
            array(
                'db' => 'user_id',
                'dt' => 3,
                'formatter' => function ($data, $row) {
                    return '<button class="btn btn-sm btn-primary btnEdit" data-user-id="' . $row['user_id'] . '" data-sort="' . $row['sort'] . '">按鈕</button>';
                }
            )
        );

        $output = $this->ssp->simple($this->input->get(), $dbDetails, $table, $primaryKey, $columns);

        echo json_encode($output);
    }

    public function edit_sort()
    {
        $user_id = $this->input->post('user_id');
        $new_sort_value = $this->input->post('new_sort_value');
        $response = [];
        $current_sort_value = $this->user_model->get_sort_value($user_id);

        if ($new_sort_value == $current_sort_value) {
            $response = ['status' => 'error', 'message' => '新的排序值與原本的值相同'];
        } else {
            $data = ['sort' => $new_sort_value];

            if ($this->user_model->is_sort_value_exists($new_sort_value)) {
                $check_users = $this->user_model->checked_sort($new_sort_value);

                foreach ($check_users as $check_user) {
                    $check_user_id = $check_user['user_id'];
                    $check_sort = $check_user['sort'];

                    $this->user_model->update_user($check_user_id, ['sort' => $check_sort + 1]);
                }

                $this->user_model->update_user($user_id, $data);
                $response = ['status' => 'error', 'message' => '已有重複值'];
            } else {
                $this->user_model->update_user($user_id, $data);
                $response = ['status' => 'success'];
            }
        }

        $this->output->set_content_type('application/json')->set_output(json_encode($response));
        //     header('Content-Type: application/json');
        //     echo json_encode($response);
    }

    // public function edit_sort()
    // {
    //     $user_id = $this->input->post('user_id');
    //     $new_sort_value = $this->input->post('new_sort_value');
    //     $response = [];
    //     $current_sort_value = $this->user_model->get_sort_value($user_id);

    //     if ($new_sort_value == $current_sort_value) {
    //         $response = ['status' => 'error', 'message' => '新的排序值與原本的值相同'];
    //     } else {
    //         $data = ['sort' => $new_sort_value];

    //         if ($this->user_model->is_sort_value_exists($new_sort_value)) {

    //             $original_sort_value = $this->user_model->get_sort_value($user_id);

    //             $this->user_model->swap_sort_values($original_sort_value, $new_sort_value);
    //             $this->user_model->update_user($user_id, $data);
    //             $response = ['status' => 'error', 'message' => '已有重複值'];
    //         } else {
    //             $this->user_model->update_user($user_id, $data);
    //             $response = ['status' => 'success'];
    //         }
    //     }

    //     $this->output->set_content_type('application/json')->set_output(json_encode($response));
    // }



    // $searchFilter = array();
    // if (!empty($this->input->get('search_keywords'))) {
    //     $searchFilter['search'] = array(
    //         'user_id' => $this->input->get('search_keywords')
    //     );
    // }
    // if (!empty($this->input->get('filter_option'))) {
    //     $searchFilter['filter'] = array(
    //         'username' => $this->input->get('filter_option')
    //     );
    // }
}
