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
        <?php echo get_phrase('login'); ?> | <?php echo $system_name; ?>
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
            <form method="post" role="form" id="form_login"
                  action="<?php echo site_url('login/validate_login'); ?>">
                <div class="form-group">
                    <input type="text" class="input-field" name="email"
                           placeholder="<?php echo get_phrase('email') . '/' . get_phrase('username'); ?>"
                           required autocomplete="off">
                </div>
                <div class="form-group">
                    <input type="password" class="input-field" name="password"
                           placeholder="<?php echo get_phrase('password'); ?>"
                           required>
                </div>

                <button type="submit" class="btn btn-primary"><?php echo get_phrase('login'); ?><i
                            class="fa fa-lock"></i></button>
            </form>
            <div class="login-bottom-links">
                <a href="<?php echo site_url('login/forgot_password'); ?>" class="link">
                    <?php echo get_phrase('forgot_your_password'); ?> ?
                </a>
                <a href="#" class="link" id="otp_login">
                    <i class="fa fa-lock"></i> <?php echo get_phrase('Login with OTP'); ?>
                </a>
            </div>
        </div>
    </div>
    <div class="image-area"></div>
</div>

<div class="modal fade" id="myModalHorizontal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    Modal title
                </h4>
            </div>

            <!-- Modal Body -->
            <div class="modal-body">

                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="inputEmail3">Email</label>
                        <div class="col-sm-10">
                            <input type="email" class="form-control"
                                   id="inputEmail3" placeholder="Email"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label"
                               for="inputPassword3">Password</label>
                        <div class="col-sm-10">
                            <input type="password" class="form-control"
                                   id="inputPassword3" placeholder="Password"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <div class="checkbox">
                                <label>
                                    <input type="checkbox"/> Remember me
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-default">Sign in</button>
                        </div>
                    </div>
                </form>


            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        data-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary">
                    Save changes
                </button>
            </div>
        </div>
    </div>
</div>


<!-- Button trigger modal -->
<button class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModalNorm">
    Launch Normal Form
</button>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close"
                        data-dismiss="modal">
                    <span aria-hidden="true">&times;</span>
                    <span class="sr-only">Close</span>
                </button>
                <h2 class="modal-title" style="text-align: center" id="myModalLabel">
                    OTP Login
                </h2>
            </div>
            <!-- Modal Body -->
            <div class="modal-body">
                <form role="form">
                    <div class="form-group">
                        <label for="exampleInputEmail1">Mobile No</label>
                        <input type="phone" class="form-control"
                               id="phone" required placeholder="Enter Mobile"/>
                    </div>
                    <div class="form-group" id="otp" style="display: none;">
                        <label for="exampleInputPassword1">OTP</label>
                        <input type="otp" class="form-control"
                               id="otp" placeholder="OTP"/>
                    </div>
                    <div class="form-group" style="cursor: pointer" id="send_otp">
                        <span><i class="fa fa-mail-forward"></i> &nbsp;Send OTP</span>
                    </div>
                    <div style="cursor: pointer;display: none" id="resend_otp">
                        <span><i class="fa fa-refresh"></i> &nbsp;Resend OTP</span>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer">
                <button type="button" id="login_with_otp" class="btn btn-primary">
                    Login
                </button>
            </div>
        </div>
    </div>
</div>


<script src="<?php echo base_url('assets/login_page/js/vendor/jquery-1.12.0.min.js'); ?>"></script>
<script src="<?php echo base_url('assets/login_page/js/vendor/bootstrap.js'); ?>"></script>
<script src="<?php echo base_url('assets/js/bootstrap-notify.js'); ?>"></script>


<?php if ($this->session->flashdata('login_error') != '') { ?>
    <script type="text/javascript">
        $.notify({
            // options
            title: '<strong><?php echo get_phrase('error');?>!!</strong>',
            message: '<?php echo $this->session->flashdata('login_error');?>'
        }, {
            // settings
            type: 'danger'
        });
    </script>
<?php } ?>

<script type="application/javascript">
    $(document).ready(function () {
        $('#otp_login').click(function (event) {
            $('#myModal').modal('show');
        });

        // $('#myModal').modal({
        //     backdrop: 'static',
        //     keyboard: false
        // });

        $('#send_otp').click(function () {
            var mobile = $('#phone').val();
            $.ajax({
                type: "get", async: false,
                url: '<?php echo site_url('teacher/send_otp/'); ?>' + mobile,
                dataType: "json",
                success: function (response) {
                    var res = JSON.parse(response);
                    if (res.type == 'error') {
                        $.notify({
                            message: res.message
                        }, {
                            type: 'danger',
                            z_index: 1800
                        });
                    }
                    if (res.type == 'success') {
                        $('#send_otp').hide();
                        $('#resend_otp').show();
                        $('#otp').show();
                        $.notify({
                            message: 'OTP sent to your mobile, please check and try to login with this.This OTP will be valid for next 60 sec.'
                        }, {
                            type: 'success',
                            z_index: 1800
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    console.log(textStatus, errorThrown);

                }
            });
        });

        $('#login_with_otp').click(function () {
            var mobile = $('#phone').val();
            var otp = $('#otp').val();
            $.ajax({
                url: '<?php echo site_url('teacher/login_with_otp'); ?>',
                type: "post",
                data: {mobile: mobile, otp: otp},
                success: function (response) {
                    var res = JSON.parse(response);
                    console.log(res);
                    if (res.type == 'error') {
                        $.notify({
                            message: res.message
                        }, {
                            type: 'danger',
                            z_index: 3000
                        });
                    }
                    if (res.type == 'success') {
                        $.notify({
                            message: res.message
                        }, {
                            type: 'success',
                            z_index: 3000
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
