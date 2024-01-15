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
                    <h5 class="card-title fs-2">帳號設定</h5>
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
                            <?= form_password([
                                'name' => 'password',
                                'id' => 'password',
                                'class' => 'form-control',
                                'value' => $user->password,
                                'placeholder' => '',
                                'required' => 'required',
                                'disabled' => 'disabled'
                            ]) ?>
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
                            <input type="text" name="contact_number" id="contact_number" class="form-control" value="<?= $user->contact_number ?>">
                        </div>
                        <div class="col-md-6">
                            <?= form_label('生日', 'birthday', ['class' => 'form-label']); ?>
                            <input type="date" name="birthday" id="birthday" class="form-control" value="<?= $user->birthday ?>">
                        </div>
                        <hr>
                        <div class="col-md-6">
                            <?= form_label('縣市', 'city', ['class' => 'form-label']); ?>
                            <select id="city" class="form-select">
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
                            <select id="district" class="form-select">
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
                            <input type="text" name="zip" id="zip" class="form-control" value="<?= $user->zip ?>">
                        </div>
                        <div class="col-md-12">
                            <?= form_label('地址', 'address', ['class' => 'form-label']); ?>
                            <input type="text" name="address" id="address" class="form-control" value="<?= $user->address ?>">
                        </div>
                        <div class="text-end">
                            <button id="btnEdit" class="btn btn-primary" data-id="<?= $user->user_id; ?>">修改</button>
                        </div>
                    </form>
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
                districtSelect.innerHTML = `<option value="${'選擇縣市'}">選擇縣市</option>`;
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
                var id = $(this).data("id");
                var postData = {
                    id: id,
                    username: $('#username').val(),
                    contact_number: $('#contact_number').val(),
                    birthday: $('#birthday').val(),
                    city: $('#city').val(),
                    district: $('#district').val(),
                    zip: $('#zip').val(),
                    address: $('#address').val()
                };

                axios.post("<?= site_url('user/edit'); ?>", postData)
                    .then(function(response) {
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