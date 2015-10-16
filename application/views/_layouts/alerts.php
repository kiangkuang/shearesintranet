        <!-- Alerts -->
        <?php if ($this->success || $this->error || $this->account->is_first_login): ?>
          <div class="content-header">
            <?php if ($this->success): ?>
              <div class="alert alert-success alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4>    <i class="icon fa fa-check"></i> Success!</h4>
                <?= $this->success ?>
              </div>
            <?php endif ?>

            <?php if ($this->error): ?>
              <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4>    <i class="icon fa fa-check"></i> Error!</h4>
                <?= $this->error ?>
              </div>
            <?php endif ?>

            <?php if ($this->account->is_first_login): ?>
              <div class="alert alert-warning alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4>    <i class="icon fa fa-check"></i> Warning!</h4>
                Change your password now to proceed!
              </div>
            <?php endif ?>
          </div>
        <?php endif ?>