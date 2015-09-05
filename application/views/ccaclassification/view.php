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
            CCA Classification
            <small>
              All
            </small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
            <li class="active">CCA Classification</li>
          </ol>
        </section>

        <!-- Main content -->
        <section class="content">
          <div class="row">
            <div class="col-xs-12">
              <div class="box box-primary">
                <div class="box-header">
                  <h3 class="box-title">CCA Classification</h3>
                </div><!-- /.box-header -->
                <div class="box-body">
                  <table class="table table-bordered table-striped data-table">
                    <thead>
                      <tr>
                        <th>Name</th>
                        <th style="width: 110px;"></th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php foreach ($ccaclassifications as $ccaclassification): ?>
                        <tr>
                          <td><?= $ccaclassification->name ?></td>
                          <th class="text-center">
                            <a href="/ccaclassification/edit/<?= $ccaclassification->id ?>" class="btn btn-sm btn-default">
                              <i class="fa fa-edit"></i> Edit
                            </a>
                            <a href="/ccaclassification/delete/<?= $ccaclassification->id ?>" class="btn btn-sm btn-default">
                              <i class="fa fa-trash"></i> Delete
                            </a>
                          </th>
                        </tr>
                      <?php endforeach ?>
                    </tbody>
                    <tfoot>
                      <tr>
                        <th>Name</th>
                        <th></th>
                      </tr>
                    </tfoot>
                  </table>
                </div><!-- /.box-body -->
              </div><!-- /.box -->
            </div><!-- /.col -->
          </div><!-- /.row -->
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
