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
        $allowed_domains = array('.com', '.net', '.org', '.edu', '.gov', '.info', '.co', '.io');

        // 尋找 '@' 符號的索引
        $at_position = strrpos($email, '@');

        // 取得郵箱結尾 / substr(string, start, ?length)
        $domain = substr($email, $at_position + 1);

        // 檢查是否在允許的域名清單中
        $valid_domain = false;
        foreach ($allowed_domains as $allowed) {
            if (strtolower(substr($domain, -strlen($allowed))) === $allowed) {
                $valid_domain = true;
                break;
            }
        }

        return $valid_domain;
    }
}
