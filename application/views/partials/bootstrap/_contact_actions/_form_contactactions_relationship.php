
<div class="form-group col-lg-12 col-md-12 col-sm-12  col-xs-12">
	<label class="" for="action_title">Who is related?</label>
	<input type="text" class="form-control input-lg" name="action_title" id="action_title" placeholder="E.g. Follow up on last phone call"  value="<?= $contact_action->action_title(); ?>">
</div>

<div class="form-group col-lg-12 col-md-12 col-sm-12  col-xs-12">
	<label class="" for="action_enddate">Completion date:</label>
	<input type="date" class="form-control input-lg" name="action_enddate" id="action_enddate"  value="<?= $contact_action->action_enddate(); ?>">
</div>

<div class="form-group col-lg-12 col-md-12 col-sm-12  col-xs-12">
	<label class="" for="action_description">Any notes?</label>
	<textarea class="form-control" id="action_description" name="action_description" placeholder="E.g. We discussed another repeat order last time we spoke." rows="6"><?= $contact_action->action_description(); ?></textarea>
</div>

<div class="form-group col-lg-6 col-md-6 col-sm-12  col-xs-12">
	<label class="" for="action_enddate">Completion date:</label>
	<input type="date" class="form-control input-lg" name="action_enddate" id="action_enddate"  value="<?= $contact_action->action_enddate(); ?>">
</div>


<div class="form-group col-lg-6 col-md-6 col-sm-12  col-xs-12">
	<label class="" for="user_id">Assigned to:</label>
	<input type="text" class="form-control input-lg" name="user_id" id="user_id" placeholder="E..g Susan"  value="<?= $contact_action->user_id(); ?>">
</div>
