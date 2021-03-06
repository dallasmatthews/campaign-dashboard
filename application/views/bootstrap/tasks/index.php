
<div class="row"><!-- Page Row-->
  <div class="col-xs-12"><!-- Containing Column -->

    <div class="row">
      <div class="col-xs-12 hidden-lg hidden-md hidden-sm visible-xs">
        <a href="<?= site_url(controller() . '/create'); ?>" class="btn btn-primary btn-lg pull-right">Create New Contact</a>
      </div>
    </div>

    <div class="row"><!-- Top line -->
      <div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
        <div class="page-header">
          <?= partial('_pageheader'); ?>
        </div>
      </div> 
      <div class="col-lg-4 col-md-4 col-sm-4 hidden-xs">
        <div class="page-header border-none">
          <div class="btn-group pull-right">
            <div class="btn-group btn-block">
              <button type="button" class="btn btn-lg btn-default btn-block2 btn-justified dropdown-toggle " data-toggle="dropdown">
                Extra Actions... <span class="caret"></span>
              </button>
              <ul class="dropdown-menu" role="menu">
                <?= partial('_button_extraactions_dropdown'); ?>
              </ul>
            </div>
          </div>  
        </div>
      </div>
    </div><!-- /Top line -->

    <div class="form-success alert alert-success alert-task hide margin_top_30">
        Woo Hoo! Saved your changes!
    </div>
    <div id="container-task-table">
        <?= $this->table->gen_table('task_table', $p->get_tasks()); ?>
    </div>


  </div><!-- /Containing column-->
</div><!-- /Page Row-->

<?= partial('_debug_footer'); ?>