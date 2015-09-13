<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
  <?php include 'application/views/_layouts/head.php' ?>
  <body class="hold-transition skin-blue sidebar-mini">
    <div class="wrapper">

      <?php include 'application/views/_layouts/header.php' ?>
      <?php include 'application/views/_layouts/sidebar.php' ?>

      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">
        <?php include 'application/views/_layouts/alerts.php' ?>

        <!-- Content Header (Page header) -->
        <section class="content-header">
          <h1>
            Account
            <small>
              Change Password
            </small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="/account/view">Account</a></li>
            <li class="active">
              Change Password
            </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- column -->
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header with-border">
                  <h3 class="box-title">
                    Change Password
                  </h3>
                </div><!-- /.box-header -->

                <form role="form" method="post">
                  <div class="box-body">
                    <div class="row">
                      <div class="col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Current Password</label>
                          <input type="password" class="form-control" name="currentPassword" required>
                        </div>
                      </div>
                    </div>

                    <div class="row">
                      <div class="col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                          <label>New Password</label>
                          <input type="password" class="form-control" name="password" required>
                        </div>
                      </div>

                      <div class="col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Confirm Password</label>
                          <input type="password" class="form-control" name="password2" required>
                        </div>
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  <div class="box-footer">
                    <button class="btn btn-success" type="submit">
                      <i class="fa fa-save"></i> Change
                    </button>
                  </div>
                </form>
              </div><!-- /.box -->
            </div><!--/.col -->
          </div>   <!-- /.row -->
        </section><!-- /.content -->
      </div><!-- /.content-wrapper -->

      <?php include 'application/views/_layouts/footer.php' ?>

      <?php include 'application/views/_layouts/control-sidebar.php' ?>
    </div><!-- ./wrapper -->

    <?php include 'application/views/_layouts/javascript.php' ?>
    <!-- page script -->
    <script>
      $(function () {
        $(".data-table").DataTable();
      });
    </script>

  </body>
</html>
