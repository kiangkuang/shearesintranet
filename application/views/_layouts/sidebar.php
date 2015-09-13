      <!-- Left side column. contains the logo and sidebar -->
      <aside class="main-sidebar">

        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">

          <!-- Sidebar user panel (optional) -->
          <div class="user-panel">
            <div class="pull-left image">
              <img src="/assets/AdminLTE-2.3.0/dist/img/default.png" class="img-circle" alt="User Image">
            </div>
            <div class="pull-left info">
              <p><?= $this->account->name ?></p>
              <a><?= $this->account->user ?></a>
            </div>
          </div>

          <!-- Sidebar Menu -->
          <ul class="sidebar-menu">
            <li class="header">NAVIGATION</li>
            <!-- Optionally, you can add icons to the links -->
            <li class="<?php if (isset($mainMenu) && $mainMenu == 'home'): ?>active<?php endif ?>"><a href="/"><i class="fa fa-home"></i> <span>Home</span></a></li>
            <li class="<?php if (isset($mainMenu) && $mainMenu == 'myCca'): ?>active<?php endif ?>"><a href="/cca"><i class="fa fa-soccer-ball-o"></i> <span>My CCA</span></a></li>
            <li class="treeview <?php if (isset($mainMenu) && $mainMenu == 'account'): ?>active<?php endif ?>">
              <a href="#"><i class="fa fa-user"></i> <span>My Account</span> <i class="fa fa-angle-left pull-right"></i></a>
              <ul class="treeview-menu">
                <li class="<?php if (isset($subMenu) && $subMenu == 'profile'): ?>active<?php endif ?>"><a href="/account/profile"><i class="fa fa-circle-o"></i> View Profile</a></li>
                <li class="<?php if (isset($subMenu) && $subMenu == 'changePassword'): ?>active<?php endif ?>"><a href="/changepassword"><i class="fa fa-circle-o"></i> Change Password</a></li>
              </ul>
            </li>
            <?php if ($this->account->is_admin): ?>
              <li class="treeview <?php //if (isset($mainMenu) && $mainMenu == 'admin'): ?>active<?php //endif ?>">
                <a href="#"><i class="fa fa-gear"></i> <span>Admin</span> <i class="fa fa-angle-left pull-right"></i></a>
                <ul class="treeview-menu">
                  <li class="<?php if (isset($subMenu) && $subMenu == 'account'): ?>active<?php endif ?>">
                    <a href="#"><i class="fa fa-circle-o"></i> <span>Account</span> <i class="fa fa-angle-left pull-right"></i></a>
                    <ul class="treeview-menu">
                      <li class="<?php if (isset($subSubMenu) && $subSubMenu == 'viewAccount'): ?>active<?php endif ?>"><a href="/account/view"><i class="fa fa-square-o"></i> View all Accounts</a></li>
                    </ul>
                  </li>
                  <li class="<?php if (isset($subMenu) && $subMenu == 'cca'): ?>active<?php endif ?>">
                    <a href="#"><i class="fa fa-circle-o"></i> <span>CCA</span> <i class="fa fa-angle-left pull-right"></i></a>
                    <ul class="treeview-menu">
                      <li class="<?php if (isset($subSubMenu) && $subSubMenu == 'viewCca'): ?>active<?php endif ?>"><a href="/cca/view"><i class="fa fa-square-o"></i> View all CCAs</a></li>
                      <li class="<?php if (isset($subSubMenu) && $subSubMenu == 'viewCcatype'): ?>active<?php endif ?>"><a href="/ccatype/view"><i class="fa fa-square-o"></i> View all CCA Types</a></li>
                      <li class="<?php if (isset($subSubMenu) && $subSubMenu == 'viewCcaclassification'): ?>active<?php endif ?>"><a href="/ccaclassification/view"><i class="fa fa-square-o"></i> View all CCA Classifications</a></li>
                    </ul>
                  </li>
                </ul>
              </li>
            <?php endif ?>

          </ul><!-- /.sidebar-menu -->
        </section>
        <!-- /.sidebar -->
      </aside>