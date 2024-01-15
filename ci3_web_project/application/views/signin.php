<?php

use Google\Service\CloudSearch\Id;
?>
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

<body class="d-flex flex-column bg-light">
    <section class="py-3 py-md-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5 col-xxl-4">
                    <div class="card border border-light-subtle rounded-3 shadow-sm">
                        <div class="card-body p-3 p-md-4 p-xl-5">
                            <div class="text-center mb-3">
                            </div>
                            <h1 class="fs-3 fw-normal text-center text-secondary mb-4">登入</h1>
                            <?= form_open("user/user_check"); ?>
                            <div class="row gy-2 overflow-hidden">
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <?= form_input(array(
                                            'type' => 'email',
                                            'name' => 'email',
                                            'id' => 'email',
                                            'class' => 'form-control',
                                            'placeholder' => '',
                                            'required' => 'required'
                                        )) ?>
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
                                            'placeholder' => '',
                                            'required' => 'required'
                                        )) ?>
                                        <?= form_label('密碼', 'password', array('class' => 'form-label')); ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex gap-2 justify-content-between">
                                        <div class="form-check">
                                            <?= form_checkbox(array(
                                                'name' => 'rememberMe',
                                                'id' => 'rememberMe',
                                                'class' => 'form-check-input',
                                                'value' => '1',
                                                'checked' => isset($_POST['rememberMe']) && $_POST['rememberMe'] == '1',
                                            )); ?>
                                            <?= form_label('記住密碼', 'rememberMe', array('class' => 'form-check-label text-secondary')); ?>
                                        </div>
                                        <?= anchor('', '忘記密碼', array('class' => 'link-primary text-decoration-none')); ?>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="col-md-12">
                                        <?php if ($this->session->flashdata('user_check')) : ?>
                                            <div class="alert alert-danger">
                                                <?= $this->session->flashdata('user_check'); ?>
                                            </div>
                                        <?php endif; ?>
                                        <?php if ($this->session->flashdata('password_check')) : ?>
                                            <div class="alert alert-danger">
                                                <?= $this->session->flashdata('password_check'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="d-grid my-3">
                                        <?= form_submit(array(
                                            'name' => 'btnSignIn',
                                            'id' => 'btnSignIn',
                                            'class' => 'btn btn-primary btn-lg',
                                            'value' => '登入',
                                        )); ?>
                                    </div>
                                </div>
                            </div>
                            <?= form_close(); ?>
                            <div class="col-12 text-center mb-3">
                                <span class="text-secondary">沒有帳號?</span>
                                <?= anchor('user/signup', '註冊', array('class' => 'link-primary text-decoration-none')); ?>
                            </div>
                            <div class="col-12 col-lg-2 d-flex align-items-center justify-content-center gap-3 flex-lg-column">
                                <div class="bg-dark h-100 d-none d-lg-block" style="width: 1px; --bs-bg-opacity: .1;">
                                </div>
                                <div class="bg-dark w-100 d-lg-none" style="height: 1px; --bs-bg-opacity: .1;"></div>
                                <div class="">or</div>
                                <div class="bg-dark h-100 d-none d-lg-block" style="width: 1px; --bs-bg-opacity: .1;">
                                </div>
                                <div class="bg-dark w-100 d-lg-none" style="height: 1px; --bs-bg-opacity: .1;"></div>
                            </div>
                            <div class="col-12 col-lg-5 d-flex align-items-center pt-3">
                                <div class="d-flex gap-3 flex-column w-100">
                                    <button href="#!" class="btn btn-lg btn-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 256 262" fill="currentColor" class="bi bi-google text-danger">
                                            <path fill="#4285F4" d="M255.878 133.451c0-10.734-.871-18.567-2.756-26.69H130.55v48.448h71.947c-1.45 12.04-9.283 30.172-26.69 42.356l-.244 1.622l38.755 30.023l2.685.268c24.659-22.774 38.875-56.282 38.875-96.027" />
                                            <path fill="#34A853" d="M130.55 261.1c35.248 0 64.839-11.605 86.453-31.622l-41.196-31.913c-11.024 7.688-25.82 13.055-45.257 13.055c-34.523 0-63.824-22.773-74.269-54.25l-1.531.13l-40.298 31.187l-.527 1.465C35.393 231.798 79.49 261.1 130.55 261.1" />
                                            <path fill="#FBBC05" d="M56.281 156.37c-2.756-8.123-4.351-16.827-4.351-25.82c0-8.994 1.595-17.697 4.206-25.82l-.073-1.73L15.26 71.312l-1.335.635C5.077 89.644 0 109.517 0 130.55s5.077 40.905 13.925 58.602z" />
                                            <path fill="#EB4335" d="M130.55 50.479c24.514 0 41.05 10.589 50.479 19.438l36.844-35.974C195.245 12.91 165.798 0 130.55 0C79.49 0 35.393 29.301 13.925 71.947l42.211 32.783c10.59-31.477 39.891-54.251 74.414-54.251" />
                                        </svg>
                                        <span class="ms-2 fs-6">Log in with Google</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php $this->load->view('script'); ?>
    <script>

    </script>
</body>

</html>