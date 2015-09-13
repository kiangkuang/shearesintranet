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
            CCA
            <small>
              <?= $this->account->name ?>
            </small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
            <li class="active">
              CCA
            </li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <!-- column -->
            <div class="col-md-6 col-md-offset-3 col-lg-4 col-lg-offset-4">
              <!-- Profile Image -->
              <div class="box box-primary">
                <div class="box-body box-profile">
                  <img class="profile-user-img img-responsive img-circle" src="/assets/AdminLTE-2.3.0/dist/img/default.png" alt="User profile picture">
                  <h3 class="profile-username text-center"><?= $this->account->name ?></h3>
                  <p class="text-muted text-center"><?= $this->account->user ?></p>
                  
                  <?php if ($memberships): ?>
                    <ul class="list-group list-group-unbordered">
                      <?php foreach ($memberships as $membership): ?>
                        <li class="list-group-item">
                          <b><?= $membership->cca->name ?></b> <span class="pull-right text-light-blue"><?= $membership->points ?></span><br>
                          <small class="text-muted"><?= $membership->role ?></small>
                        </li>
                      <?php endforeach ?>
                    </ul>
                    <p class="lead text-light-blue"><b>Total Points <span class="pull-right"><?= $totalPoints ?></span></b></p>
                  <?php endif ?>
                </div><!-- /.box-body -->
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
