<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
        }
    </style>
</head>

<body>
    <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="password_change" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title" id="password_change">密碼修改</h5>
                        <button type="button" class="btn-close" id="btnClose" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <?= form_password(array(
                                        'name' => 'password_reset',
                                        'id' => 'password_reset',
                                        'class' => 'form-control',
                                        'value' => '',
                                        'placeholder' => 'Password',
                                        'required' => 'required'
                                    )); ?>
                                    <?= form_label('密碼', 'password_reset', array('class' => 'form-label')); ?>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <?= form_password(array(
                                        'name' => 'password_confirm',
                                        'id' => 'password_confirm',
                                        'class' => 'form-control',
                                        'value' => '',
                                        'placeholder' => 'password_confirm',
                                        'required' => 'required'
                                    )); ?>
                                    <?= form_label('密碼確認', 'password_confirm', array('class' => 'form-label')); ?>
                                </div>
                            </div>
                        </div>
                        <div class="container">
                            <div class="alert alert-danger d-none" id="password_error">
                            </div>
                            <div class="alert alert-danger d-none" id="password_confirm_error">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" id="btnExit" data-bs-dismiss="modal">關閉</button>
                        <button type="submit" class="btn btn-primary" id="save_password">儲存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php $this->load->view('script'); ?>

    <script>
        $(document).ready(function() {
            $('#save_password').click(function(e) {
                e.preventDefault();

                // 獲取表單數據
                const formData = $('form').serialize();

                // 發送 POST 請求
                axios.post('<?= site_url("user/edit_password"); ?>', formData)
                    .then(response => {
                        const get_data = response.data;
                        console.log(get_data);
                        if (get_data.error) {
                            errors(get_data);

                            function errors(data) {
                                errorMessage('#password_error', data.password);
                                errorMessage('#password_confirm_error', data.password_confirm);
                            }

                            // 處理個別錯誤消息
                            function errorMessage(selector, errorData) {
                                const errorElement = $(selector);
                                if (errorData !== '') {
                                    const errorMessageText = $('<div/>').html(errorData).text();
                                    errorElement.text(errorMessageText);
                                    errorElement.toggleClass('d-none', false);
                                } else {
                                    errorElement.empty().toggleClass('d-none', true);
                                }
                            }
                        }

                        if (get_data.success) {
                            $('#btnExit').click();

                            // 使用 setTimeout 延遲顯示 alert
                            setTimeout(function() {
                                Swal.fire({
                                    title: "成功!",
                                    text: "已完成密碼更新!",
                                    icon: "success"
                                });
                            }, 100);
                            console.log('成功:', response.data);
                        }
                    })
                    .catch(error => {
                        console.error('Axios Error:', error);
                    });
            });

            $('#btnExit, #btnClose').click(function(e) {
                init();
            });

            function init() {
                // 初始化
                $('#password_reset, #password_confirm').val('');
                $('#password_error, #password_confirm_error').html('').toggleClass('d-none', true);
            }
        });
    </script>

</body>

</html>