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

        p {
            margin-bottom: 0;
        }
    </style>
</head>

<body class="d-flex flex-column bg-light">
    <section class="py-3 py-md-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                    <div class="card border border-light-subtle rounded-3 shadow-sm">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="text-center mb-3">
                            </div>
                            <h1 class="fs-3 fw-normal text-center text-secondary mb-4">註冊</h1>
                            <?= form_open("user/insert"); ?>
                            <div class="row gy-2 overflow-hidden">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <?= form_input(array(
                                            'type' => 'email',
                                            'name' => 'email',
                                            'id' => 'email',
                                            'placeholder' => 'name@example.com',
                                            'class' => 'form-control',
                                            'required' => 'required'
                                        )); ?>
                                        <?= form_label('信箱', 'email', array('class' => 'form-label')); ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <?= form_password(array(
                                            'name' => 'password',
                                            'id' => 'password',
                                            'class' => 'form-control',
                                            'value' => '',
                                            'placeholder' => 'Password',
                                            'required' => 'required'
                                        )); ?>
                                        <?= form_label('密碼', 'password', array('class' => 'form-label')); ?>
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
                                <div class="col-12">
                                    <div class="g-recaptcha" data-sitekey="6LewFjYpAAAAAK7gzKhx3Zyb5TJtxY1d0qOuRgZO"></div>
                                </div>
                                <div class="col-12">
                                    <?php //echo validation_errors('<div class="alert alert-danger" role="alert">', '</div>');
                                    ?>
                                    <?php if ($this->session->flashdata('email_check')) : ?>
                                        <div class="alert alert-danger">
                                            <?= $this->session->flashdata('email_check'); ?>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($this->session->flashdata('password_error')) : ?>
                                        <div class="alert alert-danger">
                                            <?= $this->session->flashdata('password_error'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($this->session->flashdata('password_confirm_error')) : ?>
                                        <div class="alert alert-danger">
                                            <?= $this->session->flashdata('password_confirm_error'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($this->session->flashdata('email_error')) : ?>
                                        <div class="alert alert-danger">
                                            <?= $this->session->flashdata('email_error'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($this->session->flashdata('captcha_success')) : ?>
                                        <div class="alert alert-success">
                                            <?= $this->session->flashdata('captcha_success'); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($this->session->flashdata('captcha_fail')) : ?>
                                        <div class="alert alert-danger">
                                            <?= $this->session->flashdata('captcha_fail'); ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div class=" col-12">
                                    <div class="d-grid my-3">
                                        <?= form_submit(array(
                                            'name' => 'btnSignUp',
                                            'id' => 'btnSignUp',
                                            'class' => 'btn btn-primary btn-lg',
                                            'value' => '註冊',
                                        )); ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <p class="m-0 text-secondary text-center">已有帳號?
                                        <?= anchor('user/signin', '登入', array('class' => 'link-primary text-decoration-none')); ?>
                                    </p>
                                </div>
                            </div>
                            <?= form_close(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php $this->load->view('script'); ?>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>

    </script>
</body>

</html>