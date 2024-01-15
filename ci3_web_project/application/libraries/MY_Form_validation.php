<?php
class MY_Form_validation extends CI_Form_validation
{
    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->set_custom_error_messages();
    }

    public function set_custom_error_messages()
    {
        $this->set_message('required', '請填寫 %s');
        $this->set_message('max_length', '%s 長度不可超過 %s 個字元');
        $this->set_message('min_length', '%s 長度不可少於 %s 個字元');
        $this->set_message('matches', '%s 與密碼不一致');
        $this->set_message('valid_email', '請填寫有效的 %s');
        $this->set_message('check_email', '請填寫有效的 %s');
    }

    public function check_email($email)
    {
        $allowedDomains = array('.com', '.net', '.org', '.edu', '.gov', '.info', '.co', '.io');

        // 提取郵箱地址的領域部分
        $domain = substr(strrchr($email, "@"), 1);

        // 檢查郵箱地址的領域名稱是否在允許的領域名稱清單中
        if (!in_array($domain, $allowedDomains)) {
            return FALSE;
        }
        return TRUE;
    }
}
