<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        require_once(APPPATH . 'libraries/ssp.class.php');
        $this->load->model("user_model");
    }

    public function index()
    {
        $user_info = $this->session->userdata('user_info');

        if ($user_info) {
            $data['user_info'] = $user_info;

            $json_data = file_get_contents(APPPATH . 'views/twcities.json');
            $data['tw_cities'] = json_decode($json_data, true);
            $this->load->view('user_info', $data);
        } else {
            redirect('user/signin');
        }
    }

    public function signup()
    {
        $this->load->view("user_signup");
    }

    public function user_signup()
    {
        $this->form_validation->set_rules("username", "Username", "required");
        $this->form_validation->set_rules("password", "密碼", "required|min_length[8]|max_length[20]");
        $this->form_validation->set_rules("password_confirm", "密碼確認", "required|matches[password]");
        $this->form_validation->set_rules('email', 'Email', 'required|valid_email|is_unique[users.email]');
        $this->form_validation->set_custom_error_messages();

        if ($this->form_validation->run() == false) {
            $data['validation_errors'] = validation_errors();

            $data['password'] = $this->input->post('password');
            $data['password_confirm'] = $this->input->post('password_confirm');
            $this->load->view('user_signup', $data);
        } else {
            $unique_code = uniqid();
            $userData = array(
                'username' => $this->input->post('username'),
                'email' => $this->input->post('email'),
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
                'unique_code' => $unique_code
            );
            $this->user_model->registerUser($userData);
            $link = base_url('user/verify/' . $unique_code);
            $send_email = $this->send_email_setting(
                $userData["email"],
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
            //redirect("user/signin");
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
        $this->load->view("user_signin");
    }

    public function user_signin()
    {
        $this->form_validation->set_rules("email", "Email", "required");
        $this->form_validation->set_rules("password", "Password", "required|min_length[8]|max_length[20]");
        $this->form_validation->set_custom_error_messages();

        if ($this->form_validation->run() == false) {
            $this->load->view('user_signin');
        } else {
            $email = $this->input->post("email");
            $password = $this->input->post("password");

            $user = $this->user_model->getUserByEmail($email);
            if ($user && password_verify($password, $user['password'])) {
                if ($this->validate()) {
                    if ($user['is_verified'] == 1) {
                        $this->session->set_userdata('user_info', $user);
                        redirect('user/index');
                    } else {
                        $this->session->set_flashdata('message', '帳戶尚未完成驗證，請先確認您的郵件以完成註冊確認。');
                        $this->load->view('user_signin');
                    }
                } else {
                    redirect('user/signin');
                }
            } else {
                $this->session->set_flashdata('message', '登入失敗，請檢查您的 Email 和密碼。');
                $this->load->view('user_signin');
            }
        }
    }

    // reCAPTCHA 驗證
    public function validate()
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
                // $storeData = array(
                //     'username'    =>    $this->input->post('username'),
                // );

                // $this->user_model->registerUser($storeData);

                $this->session->set_flashdata('success_message', '驗證成功');
                return true;
                //redirect('welcome');
            } else {
                $this->session->set_flashdata('message', '驗證失敗');
                return false;
                //redirect('welcome');
            }
        } else {
            $this->session->set_flashdata('message', '驗證失敗');
            return false;
            //redirect('welcome');
        }
    }

    public function editPassword($user_id)
    {
        $this->form_validation->set_rules("password", "密碼", "required|min_length[8]|max_length[20]");
        $this->form_validation->set_rules("password_confirm", "密碼確認", "required|matches[password]");

        if ($this->form_validation->run() == false) {
            $errors = $this->form_validation->error_array();
            print_r($errors);
        } else {
            $new_data = array(
                'password' => password_hash($this->input->post('password'), PASSWORD_DEFAULT),
            );
            $result = $this->user_model->update_user($user_id, $new_data);
            if ($result) {
                echo "User updated successfully.";
            } else {
                echo "Failed to update user.";
            }
        }
    }

    public function edit($user_id)
    {
        $this->form_validation->set_rules('birthday', 'Birthday', 'required|callback_date_check');
        if ($this->form_validation->run() == false) {
            $errors = $this->form_validation->error_array();
            print_r($errors);
        } else {
            $new_data = array(
                'username' => $this->input->post("username"),
                'city' => $this->input->post('city'),
                'district' => $this->input->post('district'),
                'zip' => $this->input->post('zip'),
                'address' => $this->input->post('address'),
                'birthday' => $this->input->post('birthday'),
            );
            $result = $this->user_model->update_user($user_id, $new_data);
            if ($result) {
                echo "User updated successfully.";
            } else {
                echo "Failed to update user.";
            }
        }
    }

    public function date_check($date)
    {
        $timestamp = strtotime($date);
        $lower_bound = strtotime('1930-01-01');
        $upper_bound = strtotime('2010-12-31');

        if ($timestamp > $lower_bound && $timestamp < $upper_bound) {
            return true;
        } else {
            $this->form_validation->set_message('date_check', 'The Birthday field must contain a date greater than 1930-01-01 and less than 2010-12-31.');
            return false;
        }
    }

    public function indexes()
    {
        $this->load->view('datatables'); // 载入 View
    }

    public function get_data()
    {
        $data = $this->user_model->fetch_data();
        echo json_encode($data);
    }

    public function delete_user()
    {
        if ($this->input->server('REQUEST_METHOD') !== 'POST') {
            show_404();
        }

        $user_id = $this->input->post('user_id');
        var_dump($user_id);
        $deleted = $this->user_model->delete($user_id);

        if ($deleted) {
            echo json_encode(array('success' => true));
        } else {
            echo json_encode(array('success' => false));
        }
    }

    public function indexess()
    {
        $this->load->view('data'); // 载入 View
    }

    public function get_dataa()
    {
        // Database connection info 
        $dbDetails = array(
            'host' => $this->db->hostname,
            'user' => $this->db->username,
            'pass' => $this->db->password,
            'db'   => $this->db->database
        );

        $table = 'users';
        $primaryKey = 'user_id';

        $columns = array(
            array(
                'db' => 'user_id',
                'dt' => 0
            ),
            array(
                'db' => 'username',
                'dt' => 1
            ),
            array(
                'db' => 'user_id',
                'dt' => 2,
                'formatter' => function ($d, $row) {
                    return "<div class='btn-group'>
                                    <button class='btn btn-sm btn-primary' id='btnUpdate'>Update</button>
                                    <button class='btn btn-sm btn-danger' id='btnDelete'>Delete</button>
                            </div>";
                }
            )
        );

        $searchFilter = array();
        if (!empty($this->input->get('search_keywords'))) {
            $searchFilter['search'] = array(
                'user_id' => $this->input->get('search_keywords')
            );
        }
        if (!empty($this->input->get('filter_option'))) {
            $searchFilter['filter'] = array(
                'username' => $this->input->get('filter_option')
            );
        }

        echo json_encode(
            SSP::simple($this->input->get(), $dbDetails, $table, $primaryKey, $columns, $searchFilter)
        );
    }
}
