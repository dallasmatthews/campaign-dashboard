
<div class="row"><!-- Page Row-->
    <div class="col-xs-12"><!-- Containing Column -->

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

        <div class="row">
            <div class="col-lg-8 col-md-10">
                <?= $this->messages->show(); ?>   
            </div>
        </div>

        <!-- Main table-->
        <div class="row">
            <div class="col">
                <div class="row">

                    <div class="well well-lg">
                        <p class="lead"><?= ucwords($p->smile_title()); ?></p>
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8 col-sm-12">
                                <div class="center-block">
                                    <?= $p->smile_url(); ?>    
                                </div>

                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 col-sm-12 margin_top_30">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">What's the fuss about...?</h3>
                                    </div>
                                    <div class="panel-body">
                                        <p><?= $p->smile_comment(); ?></p>
                                        <?= $p->attribution(); ?>
                                        
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>
            </div>
        </div>



        <!-- /Main table-->


    </div><!-- /Containing column-->
</div><!-- /Page Row-->

<?= partial('_debug_footer'); ?>