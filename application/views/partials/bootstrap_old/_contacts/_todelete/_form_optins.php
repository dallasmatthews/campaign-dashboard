
  <div class="form-inline"><!-- .form-inline-->
    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <label for="optin_email">1. Opt into emails?</label>
      <div>
        <label class="radio-inline">
          <input type="radio" name="optin_email" id="optin_email_yes" value="1"  <?= set_radio('optin_email', 1, TRUE); ?> > <i class="fa fa-thumbs-o-up "></i> Yes<br>
        </label>
        <label class="radio-inline">
          <input type="radio" name="optin_email" id="optin_email_no" value="0"  <?= set_radio('optin_email', 0); ?> > <i class="fa fa-thumbs-o-down "></i> No<br>
        </label>  
      </div>
    </div>
    <br><br>
    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <label for="optin_sms">1. Opt into SMS texts?</label>
      <div>
        <label class="radio-inline">
          <input type="radio" name="optin_sms" id="optin_sms_no" value="0"  <?= set_radio('optin_sms', 1, TRUE); ?> > <i class="fa fa-thumbs-o-up "></i> Yes<br>
        </label>
        <label class="radio-inline">
          <input type="radio" name="optin_sms" id="optin_sms_no" value="0"  <?= set_radio('optin_sms', 0); ?> > <i class="fa fa-thumbs-o-down "></i> No<br>
        </label>  
      </div>
    </div>
    <br><br>
    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
      <label for="optin_post">1. Opt into post?</label>
      <div>
        <label class="radio-inline">
          <input type="radio" name="optin_post" id="optin_post_no" value="0"  <?= set_radio('optin_post', 1, TRUE); ?> > <i class="fa fa-thumbs-o-up "></i> Yes<br>
        </label>
        <label class="radio-inline">
          <input type="radio" name="optin_post" id="optin_post_no" value="0"  <?= set_radio('optin_post', 0); ?> > <i class="fa fa-thumbs-o-down "></i> No<br>
        </label>  
      </div>
    </div>

    <div class="form-group col-lg-12 col-md-12 col-sm-12">
      <button type="submit" class="btn btn-success pull-right"><i class="fa fa-check"></i> Save Changes</button>
    </div>

  </div><!-- /.form-inline-->


  