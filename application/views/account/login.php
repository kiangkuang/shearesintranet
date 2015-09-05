<!DOCTYPE html>
<html>
  <?php require 'application/views/_layouts/head.php'; ?>
  <body class="hold-transition login-page">
    <div class="login-box">
      <div class="login-logo">
        <a href="/"><b>Sheares</b> Intranet</a>
      </div><!-- /.login-logo -->
      <div class="login-box-body">
        <p class="login-box-msg"><big>Sign In</big></p>
        <form action="account/processLogin" method="post">
          <div class="form-group has-feedback">
            <input name="user" class="form-control" placeholder="A1234567X">
            <span class="glyphicon glyphicon-user form-control-feedback"></span>
          </div>
          <div class="form-group has-feedback">
            <input name="password" type="password" class="form-control" placeholder="Password">
            <span class="glyphicon glyphicon-lock form-control-feedback"></span>
          </div>
          <?php if (isset($this->error)): ?>
          <div class="row">
            <div class="col-xs-12">
              <p class="text-center"><?= $this->error ?></p>
            </div>
          </div>
          <?php endif ?>
          <div class="row">
            <div class="col-xs-8">
              <a href="#">I forgot my password</a>
            </div><!-- /.col -->
            <div class="col-xs-4">
              <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
            </div><!-- /.col -->
          </div>
        </form>
      </div><!-- /.login-box-body -->
    </div><!-- /.login-box -->

    <?php include 'application/views/_layouts/javascript.php' ?>
  </body>
</html>
