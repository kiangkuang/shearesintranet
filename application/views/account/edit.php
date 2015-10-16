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
              <?php if (isset($account)): ?>
                Edit
              <?php else: ?>
                Add
              <?php endif ?>
            </small>
          </h1>
          <ol class="breadcrumb">
            <li><a href="/"><i class="fa fa-home"></i> Home</a></li>
            <li><a href="/account/view">Account</a></li>
            <li class="active">
              <?php if (isset($account)): ?>
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
                    <?php if (isset($account)): ?>
                      Edit Information
                    <?php else: ?>
                      Add new Account
                    <?php endif ?>
                  </h3>
                </div><!-- /.box-header -->

                <form role="form" action="/account/update" method="post">
                  <div class="box-body">
                    <div class="row">
                      <?php if (isset($account)): ?>
                        <input type="hidden" name="id" value="<?= $account->id ?>">
                      <?php endif ?>
                      <div class="col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Name</label>
                          <input type="text" class="form-control" name="name" placeholder="Name" value="<?= (isset($account))? $account->name : '' ?>">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Username</label>
                          <input type="text" class="form-control" name="user" placeholder="Username" value="<?= (isset($account))? $account->user : '' ?>">
                        </div>
                      </div>

                      <div class="col-md-4">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Room</label>
                          <input type="text" class="form-control" name="room" placeholder="Room" value="<?= (isset($account))? $account->room : '' ?>">
                        </div>
                      </div>

                      <div class="col-md-4">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Email</label>
                          <input type="email" class="form-control" name="email" placeholder="Email" value="<?= (isset($account))? $account->email : '' ?>">
                        </div>
                      </div>

                      <div class="col-md-4">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Contact</label>
                          <input type="text" class="form-control" name="contact" placeholder="Contact" value="<?= (isset($account))? $account->contact : '' ?>">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Password</label>
                          <input type="password" class="form-control" name="password" placeholder="<?= (isset($account))? 'Leave blank if not changing' : 'Password' ?>">
                        </div>
                      </div>

                      <div class="col-md-6">
                        <!-- text input -->
                        <div class="form-group">
                          <label>Confirm Password</label>
                          <input type="password" class="form-control" name="password2" placeholder="<?= (isset($account))? 'Leave blank if not changing' : 'Confirm Password' ?>">
                        </div>
                      </div>
                    </div>
                  </div><!-- /.box-body -->
                  <div class="box-footer">
                    <button class="btn btn-success" type="submit">
                      <i class="fa fa-save"></i> <?= (isset($account))? 'Save' : 'Add' ?>
                    </button>
                    <a class="btn btn-default" href="/account/view">
                      <i class="fa fa-undo"></i> Back
                    </a>
                  </div>
                </form>
              </div><!-- /.box -->

              <?php if (isset($account)): ?>
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">
                      Add CCAs
                    </h3>
                  </div><!-- /.box-header -->

                  <form role="form" action="/membership/addMembership" method="post">
                    <div class="box-body">
                      <div class="row">
                        <?php if (isset($account)): ?>
                          <input type="hidden" name="account_id" value="<?= $account->id ?>">
                        <?php endif ?>
                        <div class="col-md-12">
                          <div class="form-group">
                            <label>CCA Names</label>
                            <select class="form-control select2" multiple="multiple" data-placeholder="Names of new members" name="cca_ids[]">
                              <?php foreach ($ccas as $cca): ?>
                                <option value="<?= $cca->id ?>"><?= $cca->name ?></option>
                              <?php endforeach ?>
                            </select>
                          </div><!-- /.form-group -->
                        </div>
                      </div>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                      <button class="btn btn-success" type="submit">
                        <i class="fa fa-plus"></i> Add
                      </button>
                    </div>
                  </form>
                </div><!-- /.box -->
              <?php endif; ?>

              <?php if (isset($account)): ?>
                <div class="box box-primary">
                  <div class="box-header with-border">
                    <h3 class="box-title">
                      Membership
                    </h3>
                  </div><!-- /.box-header -->
                  <form role="form" action="/membership/updateMemberships" method="post">
                    <input type="hidden" name="account_id" value="<?= $account->id ?>">
                    <div class="box-body">
                      <table class="table table-bordered table-striped data-table">
                        <thead>
                          <tr>
                            <th>CCA</th>
                            <th>Role</th>
                            <th>Points</th>
                            <th style="width: 50px;"></th>
                          </tr>
                        </thead>
                        <tbody>
                          <?php if ($memberships): ?>
                            <?php foreach ($memberships as $membership): ?>
                              <tr>
                                <td>
                                  <a href="/cca/edit/<?= $membership->cca->id ?>"><?= $membership->cca->name ?></a>
                                  <input type="hidden" name="memberships[<?= $membership->id ?>][id]" value="<?= $membership->id ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="memberships[<?= $membership->id ?>][role]" placeholder="Role" value="<?= $membership->role ?>">
                                </td>
                                <td>
                                  <input type="text" class="form-control" name="memberships[<?= $membership->id ?>][points]" placeholder="Points" value="<?= $membership->points ?>">
                                </td>
                                <th class="text-center">
                                  <a href="/membership/delete/<?= $membership->id ?>" class="btn btn-sm btn-default">
                                    <i class="fa fa-trash"></i> Delete
                                  </a>
                                </th>
                              </tr>
                            <?php endforeach; ?>
                          <?php endif; ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <th>CCA</th>
                            <th>Role</th>
                            <th>Points</th>
                            <th></th>
                          </tr>
                        </tfoot>
                      </table>
                    </div><!-- /.box-body -->
                    <div class="box-footer">
                      <button class="btn btn-success" type="submit">
                        <i class="fa fa-save"></i> Save
                      </button>
                    </div>
                  </form>
                </div><!-- /.box -->
              <?php endif ?>
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
        //Initialize Select2 Elements
        $(".select2").select2();
      });
    </script>

  </body>
</html>
