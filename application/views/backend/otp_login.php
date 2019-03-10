<!doctype html>
<?php
//$system_title = $this->db->get_where('settings', array('type' => 'system_title'))->row()->description;
$system_name = $this->db->get_where('settings', array('type' => 'system_name'))->row()->description;
?>

<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>
        <?php echo get_phrase('forgot_password'); ?> | <?php echo $system_name; ?>
    </title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" href="<?php echo base_url('assets/login_page/img/favicon.png'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/login_page/css/font-awesome.min.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/login_page/css/normalize.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/login_page/css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/login_page/css/style.css'); ?>">
    <script src="<?php echo base_url('assets/login_page/js/vendor/modernizr-2.8.3.min.js'); ?>"></script>
    <link href="https://fonts.googleapis.com/css?family=Quicksand:300,400,500,700" rel="stylesheet">

</head>
<body>
<div class="main-content-wrapper">
    <div class="login-area">
        <div class="login-header">
            <a href="<?php echo site_url('login'); ?>" class="logo">
                <img src="<?php echo base_url('assets/login_page/img/logo.png'); ?>" height="60" alt="">
            </a>
                        <h2 class="title"><?php echo $system_name; ?></h2>
        </div>
        <div class="login-content">
            <form method="post" role="form" id="form_otp_login"
                  action="<?php echo site_url('login/login_with_otp'); ?>">
                <div class="form-group">
                    <input type="phone" id="phone" class="input-field" name="phone"
                           placeholder="<?php echo get_phrase('Mobile_No'); ?>"
                           required autocomplete="off">
                </div>
                <div class="form-group">
                    <input type="otp" class="input-field" name="otp" id="otp" style="display: none"
                           placeholder="<?php echo get_phrase('OTP'); ?>"
                           required autocomplete="off">
                </div>
                <div class="form-group" id="send_otp">
                    <span style="cursor: pointer"><i class="glyphicon glyphicon-phone"></i> Send OTP</span>
                </div>
                <div class="form-group" style="display: none" id="resend_otp">
                    <span style="cursor: pointer"><i class="fa fa-repeat"></i> Resend OTP</span>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo get_phrase('OTP_Login'); ?><i
                            class="fa fa-unlock"></i></button>
            </form>

            <div class="login-bottom-links">
                <a href="<?php echo site_url('login'); ?>" class="link"><i
                            class="fa fa-lock"></i><?php echo get_phrase('return_to_login_page'); ?></a>
            </div>
        </div>
    </div>
    <div class="image-area forgot-pass"></div>
</div>

<script src="<?php echo base_url('assets/login_page/js/vendor/jquery-1.12.0.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/login_page/js/vendor/bootstrap.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap-notify.js'); ?>"></script>


<?php if ($this->session->flashdata('reset_error') != '') { ?>
    <script type="text/javascript">
        $.notify({
            // options
            title: '<strong><?php echo get_phrase('error');?>!!</strong>',
            message: '<?php echo $this->session->flashdata('reset_error');?>'
        }, {
            // settings
            type: 'danger'
        });
    </script>
<?php } ?>
<?php if ($this->session->flashdata('reset_success') != '') { ?>
    <script type="text/javascript">
        $.notify({
            // options
            title: '<strong><?php echo get_phrase('success');?>!!</strong>',
            message: '<?php echo $this->session->flashdata('reset_success');?>'
        }, {
            // settings
            type: 'success'
        });
    </script>
<?php } ?>

<script type="application/javascript">
    $(document).ready(function () {
        $('#send_otp').click(function () {
            var mobile = $('#phone').val();

            if (!mobile) {
                $.notify({
                    message: 'Please enter the mobile number'
                }, {
                    type: 'danger',
                });
                return false;
            }
            $.ajax({
                type: "get", async: false,
                url: '<?php echo site_url('login/send_otp/'); ?>' + mobile,
                dataType: "json",
                success: function (response) {
                    var res = JSON.parse(response);
                    if (res.type == 'error') {
                        $.notify({
                            message: res.message
                        }, {
                            type: 'danger',
                        });
                    }
                    if (res.type == 'success') {
                        $('#send_otp').hide();
                        $('#resend_otp').show();
                        $('#otp').show();
                        $.notify({
                            message: 'OTP sent to your mobile, please check and try to login with this.Your current OTP will be valid for next 60 sec.'
                        }, {
                            type: 'success',
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);

                }
            });
        });

        $('#resend_otp').click(function () {
            var mobile = $('#phone').val();

            if (!mobile) {
                $.notify({
                    message: 'Please enter the mobile number'
                }, {
                    type: 'danger',
                });
                return false;
            }
            $.ajax({
                type: "get", async: false,
                url: '<?php echo site_url('login/resend_otp/'); ?>' + mobile,
                dataType: "json",
                success: function (response) {
                    var res = JSON.parse(response);
                    if (res.type == 'error') {
                        $.notify({
                            message: res.message
                        }, {
                            type: 'danger',
                        });
                    }
                    if (res.type == 'success') {
                        $('#send_otp').hide();
                        $('#resend_otp').show();
                        $('#otp').show();
                        $.notify({
                            message: 'OTP sent to your mobile, please check and try to login with this.Your current OTP will be valid for next 60 sec.'
                        }, {
                            type: 'success',
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);

                }
            });
        });
    });
</script>

</body>
</html>
