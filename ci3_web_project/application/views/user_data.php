<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">
</head>

<body>
    <table id="datatable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>user_id</th>
                <th>username</th>
                <th>sort</th>
                <th></th>
            </tr>
            </tr>
        </thead>
        <tbody>
        </tbody>
    </table>

    <?php $this->load->view('script'); ?>
    <script>
        $(document).ready(function() {
            var table = $('#datatable').DataTable({
                "pagingType": "full_numbers",
                "processing": true,
                "serverSide": true,
                "lengthMenu": [
                    [10, 25, 50, -1],
                    [10, 25, 50, "All"]
                ],
                "order": [
                    [2, "asc"]
                ],
                // "searching": false,
                "ajax": {
                    "url": "<?php echo site_url('user/get_data'); ?>",
                },
            });
            // $('#search_column1').on('keyup', function() {
            //     table.column(0).search(this.value).draw();
            // });
            $('#datatable').on('click', '.btnEdit', function() {
                const id = $(this).data('user-id');
                var sortInput = $(this).closest('tr').find('.sort-input');
                var newSortValue = sortInput.val();

                $.ajax({
                    url: '<?php echo site_url('user/edit_sort'); ?>',
                    type: 'POST',
                    data: {
                        user_id: id,
                        new_sort_value: newSortValue
                    },
                    success: function(response) {
                        table.ajax.reload(null, false);

                    },
                    error: function(error) {
                        // 處理錯誤的回應
                    }
                });
            });

        });
    </script>
</body>

</html>