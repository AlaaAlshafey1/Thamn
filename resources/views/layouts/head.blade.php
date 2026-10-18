<!-- Title -->
<title>@yield('title', 'ثمن')</title>
<!-- Favicon -->
<link rel="icon" type="image/png" href="{{URL::asset('assets/img/Logo.png')}}"/>
<link rel="icon" type="image/png" sizes="32x32" href="{{URL::asset('assets/img/Logo.png')}}"/>
<link rel="icon" type="image/png" sizes="16x16" href="{{URL::asset('assets/img/Logo.png')}}"/>
<link rel="shortcut icon" type="image/png" href="{{URL::asset('assets/img/Logo.png')}}"/>
<link rel="apple-touch-icon" href="{{URL::asset('assets/img/Logo.png')}}"/>
<!-- Icons css -->
<link href="{{URL::asset('assets/css/icons.css')}}" rel="stylesheet">
<!--  Custom Scroll bar-->
<link href="{{URL::asset('assets/plugins/mscrollbar/jquery.mCustomScrollbar.css')}}" rel="stylesheet"/>
<!--  Sidebar css -->
<link href="{{URL::asset('assets/plugins/sidebar/sidebar.css')}}" rel="stylesheet">
<!-- Sidemenu css -->
<link rel="stylesheet" href="{{URL::asset('assets/css-rtl/sidemenu.css')}}">
<!--- Style css -->
<link href="{{URL::asset('assets/css-rtl/style.css')}}" rel="stylesheet">
<!--- Dark-mode css -->
<link href="{{URL::asset('assets/css-rtl/style-dark.css')}}" rel="stylesheet">
<!---Skinmodes css-->
<link href="{{URL::asset('assets/css-rtl/skin-modes.css')}}" rel="stylesheet">
<!-- Boxicons CSS -->
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<!-- IBM Plex Sans Arabic Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
  body, button, input, select, textarea, .btn, .badge, .alert, table, th, td, h1, h2, h3, h4, h5, h6, p, a, label, div, span:not([class*="bx"]):not([class*="fa"]):not([class*="fe"]):not([class*="la"]):not([class*="ion"]):not([class*="typcn"]):not([class*="mdi"]) {
    font-family: 'IBM Plex Sans Arabic', sans-serif !important;
  }

  /* Protect Boxicons and all icon fonts from font override */
  .bx, [class^="bx-"], [class*=" bx-"],
  .bx::before, [class^="bx-"]::before, [class*=" bx-"]::before,
  .bx::after, [class^="bx-"]::after, [class*=" bx-"]::after {
    font-family: 'boxicons' !important;
    font-style: normal;
    font-weight: normal;
    text-transform: none;
    line-height: 1;
    display: inline-block;
  }

  .fa, .fas, .far, .fab, .fal, .fad,
  .fa::before, .fas::before, .far::before, .fab::before,
  [class^="fa-"]::before, [class*=" fa-"]::before {
    font-family: "Font Awesome 5 Free", "FontAwesome" !important;
    font-style: normal;
  }
  .fab, .fab::before {
    font-family: "Font Awesome 5 Brands" !important;
  }

  .la, .las, .lar, .lab,
  .la::before, .las::before, .lar::before, .lab::before,
  [class^="la-"]::before, [class*=" la-"]::before {
    font-family: "Line Awesome Free", "LineAwesome" !important;
    font-style: normal;
  }
  .lab, .lab::before {
    font-family: "Line Awesome Brands" !important;
  }

  .fe, [class^="fe-"], [class*=" fe-"],
  .fe::before, [class^="fe-"]::before, [class*=" fe-"]::before {
    font-family: 'feather' !important;
    font-style: normal;
  }

  .typcn, [class^="typcn-"], [class*=" typcn-"],
  .typcn::before, [class^="typcn-"]::before, [class*=" typcn-"]::before {
    font-family: 'typicons' !important;
    font-style: normal;
  }

  .ion, [class^="ion-"], [class*=" ion-"],
  .ion::before, [class^="ion-"]::before, [class*=" ion-"]::before {
    font-family: "Ionicons" !important;
    font-style: normal;
  }

  .mdi, [class^="mdi-"], [class*=" mdi-"],
  .mdi::before, [class^="mdi-"]::before, [class*=" mdi-"]::before {
    font-family: "Material Design Icons" !important;
    font-style: normal;
  }

  i[class*="bx"], i[class*="fa"], i[class*="fe"], i[class*="la"], i[class*="ion"], i[class*="typcn"], i[class*="mdi"] {
    font-style: normal;
  }
</style>
@yield('css')
