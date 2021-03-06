<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="All your marketing in one place!">
    <meta name="author" content="Dallas Matthews Ltd">
    <link rel="shortcut icon" href="<? echo site_url('assets/bootstrap-3/ico/favicon.png'); ?>">

    <title>CampaignDashboard.co.uk</title>

    <? 
      /* Get local copies of libraries if its a local version... */ 
      if (ENVIRONMENT === 'development') :
    ?>
      <!-- Bootstrap core CSS -->
      <link href="<? echo site_url('assets/bootstrap-3/css/bootstrap.min.css'); ?>" rel="stylesheet">

      <!-- Include Font-awesome -->
      <!--<link href="<? //echo site_url('assets/bootstrap-3/font-awesome3.2.1/css/font-awesome.css'); ?>" rel="stylesheet">-->
      <link href="<? echo site_url('assets/bootstrap-3/font-awesome4.0.0/css/font-awesome.css'); ?>" rel="stylesheet">

    <? else: ?>
      <!-- Bootstrap core CSS -->
      <link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap.min.css" rel="stylesheet">

      <!-- Include Font-awesome Upgraded from 3.2.1-->
      <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.0/css/font-awesome.css" rel="stylesheet">

    <? endif; ?>

    <!-- CSS for the datepickers -->
    <link href="<? echo site_url('assets/bootstrap-3/css/datepicker.css'); ?>" rel="stylesheet">
    <link href="<? echo site_url('assets/bootstrap-3/css/daterangepicker-bs3.css'); ?>" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="<? echo site_url('assets/bootstrap-3/css/custom.css'); ?>" rel="stylesheet">
    
    <!-- Styles for Datatables -->
    <link href="<? echo site_url('assets/bootstrap-3/css/dataTables.editor.css'); ?>" rel="stylesheet">  

    <!-- Styles for Typeahead -->
    <link href="<? echo site_url('assets/bootstrap-3/css/typeahead.css'); ?>" rel="stylesheet">
    
    <!-- Styles for Tagsinput -->
    <link href="<? echo site_url('assets/bootstrap-3/css/bootstrap-tagsinput.css'); ?>" rel="stylesheet"> 

    <!-- Styles for wysihtml5 -->
    <link href="<? echo site_url('assets/bootstrap-3/css/bootstrap3-wysihtml5.css'); ?>" rel="stylesheet">

    <!-- Include any custom style sheets/scripts/meta etc for this client-->
    <?
      if ($element = config('include_in_header', 'extras'))
      {
        foreach ($element as $type => $path)
        {
          if($type == 'script')
          {
            echo '<script src="' . site_url($path) . '"></script>';
          }
          elseif($type == 'stylesheet')
          {
            echo '<link href="' . site_url($path) . '" rel="stylesheet">';    
          }
        }
      }; 
    ?>


    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="<? echo site_url('assets/bootstrap-3/js/bootstrap/html5shiv.js'); ?>"></script>
      <script src="<? echo site_url('assets/bootstrap-3/js/respond.min.js'); ?>"></script>
    <![endif]-->
  </head>

  <body>