  <div class="page-header">
    <h1 id="navbar"><?= $contact->get_full_name(); ?></h1>
  </div>

  <div class="row">
    <div class="col-lg-6">
      <div class="well">
        
       <div class="tabbable"><!-- Start of pills -->
          <ul class="nav nav-pills">
            <li class="active"><a href="#overview" data-toggle="tab">Overview</a></li>
            <li><a href="#indepth" data-toggle="tab">In-Depth</a></li>
            <li><a href="#notes" data-toggle="tab">Notes</a></li>
            <li><a href="#relationships" data-toggle="tab">Relationships</a></li>
            <li><a href="#optins" data-toggle="tab">Opt-ins</a></li>
          </ul>

          <div class="tab-content">

            <div class="tab-pane active" id="overview">
              <br/><p class="lead">More of <?= $contact->get_name_owned(); ?> data under each blue tab</p>
                <?= form_open('contacts/edit/' . $contact->id(), 'class="form-horizontal" role="form"'); ?>
                <?php include (APPPATH. 'views/partials/' . $this->config->item('layout_folder') . '/_form_contact_overview.php'); ?>
                <div class="form-group">
                  <div class="col-lg-10 col-lg-offset-2">
                    <button type="submit" class="btn btn-success pull-right"><i class="icon-ok"></i> Save Changes</button> 
                  </div>
                </div>
            </div>

            <div class="tab-pane" id="indepth">
              <br/><p class="lead">All of <?= $contact->get_name_owned(); ?> secrets...</p>
                <?php include (APPPATH. 'views/partials/' . $this->config->item('layout_folder') . '/_form_contact_indepth.php'); ?>
                <div class="form-group">
                  <div class="col-lg-10 col-lg-offset-2">
                    <button type="submit" class="btn btn-success pull-right"><i class="icon-ok"></i> Save Changes</button> 
                  </div>
                </div>
              </div>

            <div class="tab-pane" id="optins">
              <br/><p class="lead">Manage <?= $contact->get_name_owned(); ?>
               communication preferences...</p>
                <?php include (APPPATH. 'views/partials/' . $this->config->item('layout_folder') . '/_form_contact_optin.php'); ?>
                <div class="form-group">
                  <div class="col-lg-10 col-lg-offset-2">
                    <button type="submit" class="btn btn-success pull-right"><i class="icon-ok"></i> Save Changes</button> 
                  </div>
                </div>
                <?= form_close(); ?>
            </div>

            <div class="tab-pane" id="notes">
              <br/><p class="lead">All the stuff we've said about <?= $contact->get_first_name(); ?>...</p>
                <? foreach ($contact->get_actions($actions, 'note') as $note): ?>
                  <? include (APPPATH. 'views/partials/' . $this->config->item('layout_folder') . '/_form_contact_notes.php');?>
                <? endforeach; ?>

                <?php echo form_open('contact_actions/edit', 'class="ajax"'); ?>
                <?php echo form_input('action_title'); ?>
                <?php echo form_input('action_description'); ?>
                <?php echo form_hidden('contact_id', $contact->id()); ?>
                <?php echo form_hidden('action_type', 'note'); ?>
                <?php echo form_submit('', 'Save', 'id="submit"'); ?>
                <?php echo form_close();?>

                <div class="form-group">
                  <div class="col-lg-10 col-lg-offset-2">
                    <button type="" class="btn btn-success pull-right"><i class="icon-ok"></i> Add New Relationship</button> 
                  </div>
                </div>
              </div>

            <div class="tab-pane" id="relationships">
              <br/><p class="lead">Who does <?= $contact->get_first_name(); ?> know?</p>
                <? foreach ($contact->get_actions($actions, 'relationship') as $note): ?>
                    <? include (APPPATH. 'views/partials/' . $this->config->item('layout_folder') . '/_form_contact_relationships.php');?>
                <? endforeach; ?>
                <div class="form-group">
                  <div class="col-lg-10 col-lg-offset-2">
                    <button type="" class="btn btn-success pull-right"><i class="icon-ok"></i> Add XXX Changes</button> 
                  </div>
                </div>
            </div>

            

            <a href="<?php echo site_url('contacts/delete/' . $contact->id()); ?>">
              <button class="btn btn-danger btn-xs pull-left" onclick="return deletechecked();">Delete <?= $contact->get_first_name()?></button>
            </a>
          </div>
          
        </div><!-- End of pills -->
      </div>
    </div>
    <div class="col-lg-5 col-lg-offset-1">

      <!-- Start of pills -->
      <div class="tabbable">
          <ul class="nav nav-pills">
            <li class="active"><a href="#orders" data-toggle="tab">Orders</a></li>
            <li><a href="#tasks" data-toggle="tab">Tasks</a></li>
            <li><a href="#roles" data-toggle="tab">Roles</a></li>
          </ul>

          <div class="tab-content">
            <div class="tab-pane active" id="orders">
              <br><p class="lead">See what <?= $contact->get_first_name(); ?> has been buying</p>
               <? 
                $table_header = array('id', 'action_type', 'action_title');
                $table_style = array('table_open' => '<table class="table table-striped table-bordered table-hover">');
                $this->table->set_heading($table_header);
                $this->table->set_template($table_style);
                echo $this->table->generate($contact->get_actions($actions, 'tweet', $table_header));
              ?>

                <a class="btn btn-lg btn-primary pull-right" href="#contactaction-modal" data-toggle="modal">Create New Order &raquo;</a>
            </div>
            <div class="tab-pane" id="tasks">
              <br><p class="lead">"Todo's for <?= $contact->get_first_name(); ?></p>
                <table class="table DataTable" table-id="task-table" id="task-table" data-source="<?php echo site_url('ajax/contact_actions/get_table/id/action_type/action_title/contact_id?action_type=task&contact_id=' . $contact->id()); ?>" data-link="#" html-source="<?php echo site_url('contact_actions/create'); ?>" table-type="modal">
        <thead>
          <tr>
            <th>Id</th>
            <th>Action type</th>
            <th>Action title</th>
            <th>Contct Id</th>
          </tr>
        </thead>
      </table>

                <? 
                /*$table_header = array('id', 'action_type', 'action_title');
                $table_style = array('table_open' => '<table class="table table-striped table-bordered table-hover">');
                $this->table->set_heading($table_header);
                $this->table->set_template($table_style);
                echo $this->table->generate($contact->get_actions($actions, 'task', $table_header));*/
              ?>
               <a class="btn btn-lg btn-primary pull-right" data-target="#contactaction-modal" href="" data-toggle="modal">Create New Task &raquo;</a>
            </div>
            <div class="tab-pane" id="roles">
              <br><p class="lead">"Remember when " <?= $contact->get_first_name(); ?> did that thing..?</p>
                <? 
                $table_header = array('id', 'action_type', 'action_title');
                $table_style = array('table_open' => '<table class="table table-striped table-bordered table-hover">');
                $this->table->set_heading($table_header);
                $this->table->set_template($table_style);
                echo $this->table->generate($contact->get_actions($actions, 'role', $table_header));
              ?>
              
            </div>
          </div>
        </div>
      <!-- End of pills -->
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="contactaction-modal" tabindex="-1" role="dialog" aria-labelledby="contactaction-modal-label" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">Modal title</h4>
        </div>

        <div class="modal-body">
          ...
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->  

  



  <? dump($contact); ?>
  <? dump($actions); ?>
  <? dump($contact->get_actions($actions, 'note')); ?>
  <? foreach ($contact->get_actions($actions, 'note') as $note)
                    echo '<br>', $note->action_title;