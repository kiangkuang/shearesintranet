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
              <?php if (isset($ccaclassification)): ?>
                Edit
              <?php else: ?>
                Add
              <?php endif ?>
            </small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="/ccaclassification/view">CCA Classification</a></li>
            <li class="active">
              <?php if (isset($ccaclassification)): ?>
                Edit
              <?php else: ?>
                Add
              <?php endif ?>
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
                    <?php if (isset($ccaclassification)): ?>
                      Edit Information
                    <?php else: ?>
                      Add new CCA Classification
                    <?php endif ?>
                  </h3>
                </div><!-- /.box-header -->

                <form role="form" action="/ccaclassification/update" method="post">
                  <div class="box-body">
                    <div class="row">
                      <?php if (isset($ccaclassification)): ?>
                        <input type="hidden" name="id" value="<?= $ccaclassification->id ?>">
                      <?php endif ?>
                      <div class="col-md-12">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Name</label>
                          <input type="text" class="form-control" name="name" placeholder="Name" value="<?= (isset($ccaclassification))? $ccaclassification->name : '' ?>">
                        </div>
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  <div class="box-footer">
                    <button class="btn btn-success" type="submit">
                      <i class="fa fa-save"></i> <?= (isset($ccaclassification))? 'Save' : 'Add' ?>
                    </button>
                    <a class="btn btn-default" href="/ccaclassification/view">
                      <i class="fa fa-undo"></i> Back
                    </a>
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

  </body>
</html>
