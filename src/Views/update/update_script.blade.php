<script type="text/javascript">
    var updateAreaDiv = $('#update-area');
    var refreshPercent = 0;
    var checkInstall = true;

    $('#update-app').click(function () {
        if ($('#update-frame').length) {
            return false;
        }

        Swal.fire({
            title: 'هشدار',
            type: 'info',
            text: 'آیا از بروزرسانی اسکریپت اطمینان دارید؟',
            showCancelButton: true,
            confirmButtonColor: '#00a018',
            cancelButtonColor: '#d33',
            confirmButtonText: 'بله',
            cancelButtonText: 'خیر'
        }).then((result) => {
            if (result.value) {

                $.ajax({
                    type: 'GET',
                    url: '{!! route("admin.updateVersion.update") !!}',
                    success: function (response) {
                        if (response.status == 'success') {
                            updateAreaDiv.show();
                            updateAreaDiv.html("<strong>بروزرسانی ها:-</strong><br> " + response.description);
                            toastr.success(response.message);
                            downloadScript();
                            downloadPercent();
                        } else {
                            toastr.warning(response.message);
                        }
                    }
                });
            }
        });


    });

    function downloadScript() {
        $.ajax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.download") !!}',
            success: function (response) {
                clearInterval(refreshPercent);
                $('#percent-complete').css('width', '100%');
                $('#percent-complete').html('100%');
                $('#download-progress').append("<i><span class='text-success'>دانلود کامل شد.</span> در حال نصب...(لطفا صبر کنید! ممکن است چند دقیقه طول بکشد.)</i>");
                toastr.success('دانلود کامل شد. در حال نصب ...');
                window.setInterval(function () {
                    /// call your function here
                    if (checkInstall == true) {
                        checkIfFileExtracted();
                    }
                }, 1500);

                installScript();

            }
        });
    }

    function getDownloadPercent() {
        $.ajax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.downloadPercent") !!}',
            success: function (response) {
                $('#percent-complete').css('width', response + '%');
                $('#percent-complete').html(response + '%');
            }
        });
    }

    function checkIfFileExtracted() {
        $.ajax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.checkIfFileExtracted") !!}',
            success: function (response) {
                checkInstall = false;
                if (response.status == 'success') {
                    toastr.success(response.message);
                    setTimeout(function () {
                        location.reload();
                    }, 8000);
                }
            }
        });
    }

    function downloadPercent() {
        updateAreaDiv.append('<hr><div id="download-progress">' +
            'در حال دانلود ... <br><div class="progress progress-lg">' +
            '<div class="progress-bar progress-bar-success active progress-bar-striped" role="progressbar" id="percent-complete" role="progressbar""></div>' +
            '</div>' +
            '</div>'
        );
        //getting data
        refreshPercent = window.setInterval(function () {
            getDownloadPercent();
            /// call your function here
        }, 1500);
    }

    function installScript() {
        $.ajax({
            type: 'GET',
            url: '{!! route("admin.updateVersion.install") !!}',
            success: function (response) {
                if (response.status == 'success') {
                    toastr.success(response.message);
                }
            }, error: function (xhr) {
                location.reload();
            }
        });
    }

    function getPurchaseData() {
        var token = "{{ csrf_token() }}";
        $.ajax({
            type: 'POST',
            url: "{{ route('purchase-verified') }}",
            data: {'_token': token},
            container: "#support-div",
            messagePosition: 'inline',
            success: function (response) {
                window.location.reload();
            }
        });
        return false;
    }
</script>
