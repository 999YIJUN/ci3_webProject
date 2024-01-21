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
    <main class="py-3 py-md-5">
        <div class="container">
            <div class="card">
                <div class="card-body">
                    <?= form_open('user/signout'); ?>
                    <div class="row g-3">
                        <h5 class="card-title fs-2 col-9 col-sm-10 col-md-10">帳號設定</h5>
                        <button type="submit" class="btn btn-outline-secondary col-3 col-sm-2 col-md-2">登出</button>
                    </div>
                    <?= form_close(); ?>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <?= form_label('信箱', 'email', ['class' => 'form-label']); ?>
                            <?= form_input([
                                'type' => 'email',
                                'name' => 'email',
                                'id' => 'email',
                                'class' => 'form-control',
                                'placeholder' => '',
                                'required' => 'required',
                                'value' => $user->email,
                                'disabled' => 'disabled'
                            ]) ?>
                        </div>
                        <div class="col-md-12">
                            <?= form_label('密碼', 'password', array('class' => 'form-label')); ?>
                            <div class="input-group">
                                <?= form_password([
                                    'name' => 'password',
                                    'id' => 'password',
                                    'class' => 'form-control',
                                    'value' => $user->password,
                                    'placeholder' => '',
                                    'required' => 'required',
                                    'disabled' => 'disabled'
                                ]) ?>
                                <div class="input-group-append">
                                    <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#passwordModal">修改</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <form class="row g-3" id="myForm">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <?= form_label('姓名', 'username', ['class' => 'form-label']); ?>
                                <input type="text" name="username" id="username" class="form-control" value="<?= $user->username ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?= form_label('電話', 'contact_number', ['class' => 'form-label']); ?>
                            <input type="text" name="contact_number" id="contact_number" class="form-control" value="<?= $user->contact_number ?>" maxlength="10">
                        </div>
                        <div class="col-md-6">
                            <?= form_label('生日', 'birthday', ['class' => 'form-label']); ?>
                            <input type="date" name="birthday" id="birthday" class="form-control" value="<?= $user->birthday ?>">
                        </div>
                        <hr>
                        <div class="col-md-6">
                            <?= form_label('縣市', 'city', ['class' => 'form-label']); ?>
                            <select name="city" id="city" class="form-select">
                                <option value="citySelect">選擇縣市</option>
                                <?php foreach ($tw_cities as $city) : ?>
                                    <?php
                                    // 判斷使用者選擇的縣市是否匹配當前迴圈的縣市
                                    $isSelectedCity = ($user->city === $city['name']);
                                    ?>
                                    <option value='<?= $city['name']; ?>' <?= ($isSelectedCity) ? 'selected' : ''; ?>>
                                        <?= $city['name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <?= form_label('區', 'district', ['class' => 'form-label']); ?>
                            <select name="district" id="district" class="form-select">
                                <option value="districtSelect">選擇區域</option>
                                <?php foreach ($tw_cities as $city) : ?>
                                    <?php
                                    $isSelectedCity = ($user->city === $city['name'])
                                    ?>
                                    <?php if ($isSelectedCity) : ?>
                                        <?php foreach ($city['districts'] as $district) : ?>
                                            <?php
                                            // 判斷使用者選擇的區域是否匹配當前迴圈的區域
                                            $isSelectedDistrict = ($user->district === $district['name']);
                                            ?>
                                            <option value="<?= $district['name']; ?>" <?= ($isSelectedDistrict) ? 'selected' : ''; ?>>
                                                <?= $district['name']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <?= form_label('郵遞區號', 'zip', ['class' => 'form-label']); ?>
                            <input type="text" name="zip" id="zip" class="form-control" value="<?= $user->zip ?>" readonly>
                        </div>
                        <div class="col-md-12">
                            <?= form_label('地址', 'address', ['class' => 'form-label']); ?>
                            <input type="text" name="address" id="address" class="form-control" value="<?= $user->address ?>">
                        </div>
                        <div class="col-md-12">
                            <div class="alert alert-success d-none" id="success_message">
                            </div>
                            <div class="text-end">
                                <button id="btnEdit" class="btn btn-primary" data-id="<?= $user->user_id; ?>">修改</button>
                            </div>
                        </div>
                    </form>
                    <?php $this->load->view('modal'); ?>
                </div>
            </div>
        </div>
    </main>
    <?php $this->load->view('script'); ?>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const citySelect = document.getElementById('city');
            const districtSelect = document.getElementById('district');
            const zipValue = document.getElementById('zip');
            const citiesJson = <?= json_encode($tw_cities); ?>;

            citySelect.addEventListener('change', function() {
                let citySelected = this.value;
                districtSelect.innerHTML = `<option value="districtSelect">選擇區域</option>`;
                zipValue.value = '';
                const cityData = citiesJson.find(city => city.name == citySelected);

                if (cityData) {
                    cityData.districts.forEach(district => {
                        const options = `<option value="${district.name}">${district.name}</option>`;
                        districtSelect.innerHTML += options;
                    });
                }
            });

            districtSelect.addEventListener('change', function() {
                let districtSelected = this.value;
                let citySelected = citySelect.value;
                const cityData = citiesJson.find(city => city.name == citySelected);
                zipValue.value = '';
                if (cityData) {
                    let districtData = cityData.districts.find(district => district.name == districtSelected);

                    if (districtData) {
                        const zip = districtData.zip;
                        zipValue.value = zip;
                    }
                }
            });
        });


        $(document).ready(function() {
            $("#btnEdit").click(function(event) {
                event.preventDefault();
                // var id = $(this).data("id");
                const postData = $('form').serialize();
                axios.post("<?= site_url('user/edit'); ?>", postData)
                    .then(function(response) {
                        const get_data = response.data;
                        const elementId = $('#success_message');
                        if (get_data.success) {
                            elementId.html(get_data.message);
                            elementId.removeClass('alert-danger').addClass('alert-success');
                        } else {
                            elementId.removeClass('alert-success').addClass('alert-danger');
                            elementId.html(get_data.message);
                        }

                        elementId.toggleClass('d-none', false);
                        console.log('成功:', response.data);
                    })
                    .catch(function(error) {
                        console.error('錯誤:', error);
                    });
            });
        });
    </script>

</body>

</html>