<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/x-icon" href="favicon.ico">

    <!-- Bootstrap CSS -->
    {{-- <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/style.css"> --}}
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/owl.theme.default.min.css') }}">
    <link rel="stylesheet" href="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/css/style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.1/css/boxicons.min.css' rel='stylesheet'>
   

    <title>{{ $site_title }}</title>

    {{-- <style>
        .slide1 {
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)), url('http://127.0.0.1:8080/assets/landing_page_assets/img/bg_banner2.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style> --}}

    <style>
        .float{
         position:fixed;
         width:60px;
         height:60px;
         bottom:40px;
         right:40px;
         background-color:#25d366;
         color:#FFF;
         border-radius:50px;
         text-align:center;
         font-size:30px;
         box-shadow: 2px 2px 3px #999;
         z-index:100;
         }

         .my-float{
         margin-top:16px;
         }
   </style>


</head>

<body data-bs-spy="scroll" data-bs-target=".navbar" data-bs-offset="70">


    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    {{-- &text=Hola%21%20Quisiera%20m%C3%A1s%20informaci%C3%B3n%20sobre%20Varela%202. --}}
    <a href="https://api.whatsapp.com/send?phone={{  $support_whatsapp_number  }}&text=Hello,%20Please%20I%20need%20help" class="float" target="_blank">
    <i class="fa fa-whatsapp my-float"></i>
    </a>       


    <!-- TOP NAV -->
    <div class="top-nav" id="home">
        <div class="container">
            <div class="row justify-content-between">
                <div class="col-auto">
                    <p> <i class='bx bxs-envelope'></i> {{ $topnav_email  }}</p>
                    <p>  <a style="text-decoration: none; color:white" href="tel:08168509044"><i class='bx bxs-phone-call'></i>{{ $topnav_phone }}</a> </p>
                </div>
                <div class="col-auto social-icons">
                    <a href="{{ $facebook_link }}"><i class='bx bxl-facebook'></i></a>
                    <a href="{{ $twitter_link }}"><i class='bx bxl-twitter'></i></a>
                    <a href="{{ $instagram_link }}"><i class='bx bxl-instagram'></i></a>
                    <a href="https://api.whatsapp.com/send?phone={{  $support_whatsapp_number  }}"><i class='bx bxl-whatsapp'></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- BOTTOM NAV -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">{{ $site_logo_alt }}<span class="dot">.</span></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">Services</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#portfolio">Portfolio</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#team">Team</a>
                    </li> --}}
                    <li class="nav-item">
                        <a class="nav-link" href="#reviews">Testimonials</a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" href="#blog">Blog</a>
                    </li> --}}
                </ul>
                {{-- data-bs-toggle="modal" data-bs-target="#exampleModal" --}}
                <a href="{{ url('/register') }}" 
                    class="btn btn-brand ms-lg-3">Signup</a>

                <a href="{{ url('/login') }}" 
                class="btn btn-brand ms-lg-3">Login</a>
            </div>
        </div>
    </nav>

    <!-- SLIDER -->
    <div class="owl-carousel owl-theme hero-slider">
        {{-- style=" background-image: 'url( {{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/bg_banner2.jpg')  }} )' " --}}
        <div  class="slide slide1">
            <div class="container">
                <div class="row">
                    <div class="col-12 text-center text-white">
                        <h6 class="text-white text-uppercase">{{ $sub_hero1 }}</h6>
                        <h1 class="display-3 my-4">{{ $hero1_part1 }}<br />{{ $hero1_part2 }}</h1>
                        <a href="{{ url('/register') }}" class="btn btn-brand">Get Started</a>
                        <a href="{{ url('/login') }}" class="btn btn-outline-light ms-3">Login</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="slide slide2">
            <div class="container">
                <div class="row">
                    <div class="col-12 col-lg-10 offset-lg-1 text-white">
                        <h6 class="text-white text-uppercase">{{ $sub_hero2 }}</h6>
                        <h1 class="display-3 my-4">{{ $hero2_part1 }}<br />{{ $hero2_part2 }}</h1>
                        <a href="{{ url('/register') }}" class="btn btn-brand">Get Started</a>
                        <a href="{{ url('/login') }}" class="btn btn-outline-light ms-3">Login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ABOUT -->
    {{-- <section id="about">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 py-5">
                    <div class="row">

                        <div class="col-12">
                            <div class="info-box">
                                <img src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/img/icon6.png') }}" alt="">
                                <div class="ms-4">
                                    <h5>Digital Marketing</h5>
                                    <p>It is a long established fact that a reader will be distracted by the readable
                                        content of a page </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="info-box">
                                <img src="img/icon4.png" alt="">
                                <div class="ms-4">
                                    <h5>E-mail Marketing</h5>
                                    <p>It is a long established fact that a reader will be distracted by the readable
                                        content of a page </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 mt-4">
                            <div class="info-box">
                                <img src="img/icon5.png" alt="">
                                <div class="ms-4">
                                    <h5>Buisness Marketing</h5>
                                    <p>It is a long established fact that a reader will be distracted by the readable
                                        content of a page </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <img src="img/about.png" alt="">
                </div>
            </div>
        </div>
    </section> --}}
    <section class="bg-light" id="about">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="intro">
                        <h6>about us</h6>
                        <h1>Who we are</h1>
                        <p class="mx-auto">{{ $aboutus_introduction }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- this was hidden - examine later --}}
        <div id="projects-slider" class="owl-theme owl-carousel">
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project1.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project2.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project3.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project4.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project5.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
        </div>
    </section>

    <!-- MILESTONE -->
    <section id="milestone">
        <div class="container">
            <div class="row text-center justify-content-center gy-4">
                <div class="col-lg-2 col-sm-6">
                    <h1 class="display-4">{{ $value_analytics1 }}</h1>
                    <p class="mb-0">{{ $title_analytics1 }}</p>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <h1 class="display-4">{{ $value_analytics2 }}</h1>
                    <p class="mb-0">{{ $title_analytics2 }}</p>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <h1 class="display-4">{{ $value_analytics3 }}</h1>
                    <p class="mb-0">{{ $title_analytics3 }}</p>
                </div>
                <div class="col-lg-2 col-sm-6">
                    <h1 class="display-4">{{ $value_analytics4 }}</h1>
                    <p class="mb-0">{{ $title_analytics4 }}</p>
                </div>
            </div>
        </div>
    </section>

    <section id="services" class="text-center">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="intro">
                        <h6>Get to know us more</h6>
                        <h1>Our Features and Services</h1>
                        <p class="mx-auto">{{ $service_intro }}</p>
                    </div>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-lg-4 col-md-6">
                    <div class="service">
                        <img src="img/icon1.png" alt="">
                        <h5>{{  $data_title }}</h5>
                        <p>{{ $data_description}}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service">
                        <img src="img/icon2.png" alt="">
                        <h5>{{  $airtime_title }}</h5>
                        <p>{{ $airtime_description}}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service">
                        <img src="img/icon3.png" alt="">
                        <h5>{{  $bills_title }}</h5>
                        <p>{{ $bills_description}}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service">
                        <img src="img/icon4.png" alt="">
                        <h5>{{  $cable_tv_title }}</h5>
                        <p>{{ $cable_tv_description}}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service">
                        <img src="img/icon5.png" alt="">
                        <h5>{{  $epins_title }}</h5>
                        <p>{{ $epins_description}}</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6">
                    <div class="service">
                        <img src="img/icon6.png" alt="">
                        <h5>{{  $result_checker_title }}</h5>
                        <p>{{ $result_checker_description}}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>


<!-- Client 1 - HCF Bootstrap 5 Component -->
<section class="bg-light pb-7 pt-6 py-xl-6">
  <div class="container mb-1 mb-md-6">
    <div class="row justify-content-md-center">
      {{-- <div class="col-12 col-md-10 col-lg-8 col-xl-7 col-xxl-6 text-center">
        <h2 class="mb-4 display-5">Clients</h2>
        <p class="text-secondary mb-4 mb-md-5">Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque et neque id ligula mattis commodo.</p>
        <hr class="w-50 mx-auto mb-0 text-secondary">
      </div> --}}
      <div class="row">
        <div class="col-12">
            <div class="intro">
                {{-- <h6>partners</h6> --}}
                <h1>Our Partners</h1>
                {{-- <p class="mx-auto">Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Pellentesque et neque id ligula mattis commodo.</p> --}}
            </div>
        </div>
     </div>

    </div>
  </div>
  <div class="container overflow-hidden">
    <div class="row gy-5 gy-md-6">
      <div class="col-6 col-md-3 align-self-center text-center">
        <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/landing/partners/mtn22.png') }}" height="200"  alt="">  
      </div>
      <div class="col-6 col-md-3 align-self-center text-center">
        <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/landing/partners/glo.png') }}" height="160" alt="">  
    </div>
      <div class="col-6 col-md-3 align-self-center text-center">
        <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/landing/partners/_9mobile.jpg') }}" width="" alt="">  
    </div>
      <div class="col-6 col-md-3 align-self-center text-center">
        <img src="{{ asset(env('APP_ASSETS_BASE_URL').'img/landing/partners/airtel.jpg') }}" width="" alt="">  
     </div>
      {{-- <div class="col-6 col-md-3 align-self-center text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="125" height="65" viewBox="0 0 125 65" fill="currentColor" class="text-dark"><path d="M122.61,13.9c-1.32,0-2.4,1.13-2.4,2.52s1.07,2.52,2.4,2.52c1.32,0,2.39-1.13,2.39-2.52h0c0-1.4-1.07-2.52-2.39-2.52Zm0,4.74c-1.16,0-2.11-.99-2.11-2.22h0c0-1.23,.94-2.22,2.11-2.22h0c1.16,0,2.1,.99,2.1,2.21,0,1.22-.93,2.22-2.09,2.22h0Zm1.23-2.88c0-.24-.1-.49-.3-.61-.2-.13-.43-.14-.65-.14h-1.17v2.82h.33v-1.3h.6l.77,1.3h.39l-.81-1.3c.48-.01,.85-.22,.85-.77h0Zm-1.29,.5h-.5v-1h.77c.33,0,.7,.05,.7,.49,0,.57-.58,.51-.96,.51ZM42.33,58.01l-15.62-8.02c-.34-.19-.52-.66-.51-.98l.04-4.43c0-.31,.06-.63,.45-.93,1.67-1.12,6.84-4.13,12.19-12.33,7.12-10.93,0-20.1-11.12-17.61-9.88,2.22-19.94,7.85-24.3,10.9C-.43,27.34,.11,30.64,0,33.59c.22,1.25,.65,1.14,1.62-.11,7.24-7.04,18.25-12.38,20.3-13.29s6.91-3.98,12.64-2.61c5.5,1.31,4.16,9.17,2.48,12.49-1.84,3.63-7.07,8.92-9.45,10.22-.9,.5-1.24,0-1.24-.8l.38-17.1c0-1.14-.49-1.65-1.57-1.25l-1.73,.91c-1.19,.74-1.13,1.02-1.24,1.93l-.26,19.71c0,.43-.2,.77-.42,.87l-5.64,3.33c-1.3,.68-.97,1.7,0,2.05l4.54,2.27c1.08,.45,1.4,.79,1.4,1.93l-.16,9.54c4.05-.85,4.32-2.9,4.43-4.26v-2.9c.11-.68,.49-.91,1.03-.79l13.18,6.7c1.19,.57,1.84,.34,2.7-1.25,.86-1.59,.97-2.38-.65-3.18h0Zm23.02-24.07c-1.83,1.11-2.68,3.19-1.96,4.51,.72,1.31,2.9,1.53,4.73,.42,1.83-1.11,2.62-3.14,1.9-4.45-.72-1.31-2.84-1.59-4.68-.48Zm-6.76,1.41c-.5,.64-1.46,2.58-2,3.78-.54,1.19-1.67,3.89-2.81,3.89-1.2,0-1.35-3.12-1.35-4.26s-.65-1.31-.97-1.25-1.24,.4-1.51,1.59c-.27,1.19-1.73,6.36-3.35,6.36-1.03,0-1.78-3.12-1.84-4.09-.05-.96-.49-1.76-1.4-1.76-.65,0-1.46,.34-1.78,.74s-.38,1.02-2.11,.62c-1.57-.06-3.08,.8-4.91,4.03-1.11,1.95-2,5.58-.32,7.04,1.57,1.36,4.32-1.08,5.13-2.1,.81-1.02,1.51-1.76,1.84-2.33,.32-.57,.65-.51,.81,.06,.16,.57,1.08,2.78,2.27,2.73,1.19-.06,2.7-1.02,4.48-4.32,.32-.68,.81-.62,.92-.06,.11,.57,.81,2.44,2,2.44s2.4-1.89,3.02-3.01c.43-.77,1.08-.4,1.08,.34s-.32,18.17-.32,18.17c0,.79,.3,1.02,.84,1.02h2.35c.43,0,.81-.4,.81-1.02l.49-28.26c0-.68-.73-1.16-1.35-.37h0Zm-18.36,11.13c-.49,.85-1.78,3.12-2.92,2.73-.59-.34,.2-2.61,.59-3.41,.59-1.19,2.34-3.09,3.13-2.61,.76,.45-.32,2.44-.81,3.29h0ZM122.52,29.06c-.22,.2-.57,.54-1.35,.17-1.35-.57-1.46-3.24-2.16-5.68-.63-2.19-2.15-2.06-2.7-1.05l-1.65,2.78c-.3,.57-.94,1.87-1.59,1.87-.76,0-.42-1.14-.38-2.27,.03-.77-.43-1.73-1.19-1.73-.86,0-1.78,1.39-2.48,2.98-.49,1.11-1.76,5.37-3.35,5.37-1.03,0-1.13-3.12-1.19-4.09s-.38-2.16-1.3-2.16c-.62,0-1.03,.34-1.35,.74-.32,.4-.38,.74-1.51,.57-1.57-.06-4.16,1.48-5.99,4.71-1.11,1.95-2.44,6.04-.76,7.5,1.57,1.36,4.37-1.82,5.18-2.84,.81-1.02,1.51-1.76,1.84-2.33,.32-.57,.65-.51,.81,.06,.16,.57,1.08,2.78,2.27,2.73,1.19-.06,2.16-.34,3.94-3.64,.32-.68,.92-.77,1.03-.2s.22,2.07,1.35,2.07c1.94,0,4.15-4.97,4.7-6.13,.49-1.02,1.27-.74,1.54,.03,.39,1.1,.73,4.63,2.83,4.63s3.54-2.36,3.97-3.1c.49-.74,.23-1.68-.51-.99h0Zm-22.89,3.41c-.49,.85-1.57,2.73-2.7,2.33-.59-.34,.14-1.99,.54-2.78,.59-1.19,2.33-3.12,3.13-2.64,.75,.45-.49,2.24-.97,3.1h0Zm-10.43-2.98s-4.59-3.52-5.89-5.11c-.87-1.07-.27-1.87,.27-2.33,3.24-2.27,8.31-5.79,10.31-11.25,1.92-5.26-1.08-9.83-3.24-10.56-2.96-1.01-9.23,1.02-12.37,7.16-2.03,3.97-2.97,5.28-3.24,16.07-.05,2.04-.11,4.37-.11,6.93s-.27,9.48-.54,10.79c-1.03,3.07,2.05,1.25,3.46,.06,1.4-1.19,1.3-2.98,1.3-3.75,0-.97,.05-18,.54-21.81,.3-2.37,.65-6.76,3.02-10.56,2.38-3.81,4.81-2.27,5.45-1.65,.65,.62,1.57,2.22,1.08,5.45-.75,5.02-6.26,10.51-7.67,12.21-1.4,1.7-1.08,3.18-.27,4.37,.58,.85,2,2.5,3.46,4.03,1.46,1.53,1.3,2.9,1.08,3.92-.76,3.07-8.64,11.36-8.64,11.36-1.67,1.87,.05,4.94,.65,5.96,.6,1.02,1.35,.68,1.84-.17,1.94-3.8,10.31-13.01,10.31-13.01,2.43-3.24,1.41-6.36-.81-8.12h0Z" fill-rule="evenodd"/></svg>
      </div>
      <div class="col-6 col-md-3 align-self-center text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="125" height="65" viewBox="0 0 125 65" fill="currentColor" class="text-dark"><path d="M67.37,.18c-.41,.05-.8,1.32-1.59,1.98-.58,.47-1.3,.45-1.68,1.03-.15,.22-.1,.59-.26,.95-.32,.72-1.46,.78-1.46,1.57,0,.85,.95,1.01,1.77,1.61,.64,.48,.7,.82,1.48,1.05,.66,.19,1.65-.43,2.54-.21,.73,.18,1.43,.31,1.6,.94,.14,.58-.01,1.48-.9,1.38-.3-.03-1.6-.39-3.19-.25-1.92,.18-4.11,.71-4.33,2.51-.12,1,1.36,2.19,2.79,1.95,.99-.16,.52-1.14,1.06-1.62,.7-.61,4.7,2.13,8.42,2.13,1.56,0,2.73-.33,3.89-1.35,.11-.08,.24-.24,.41-.25,.16,.01,.43,.14,.52,.2,2.99,2.02,5.26,6.08,16.26,6.12,1.55,0,3.31,.63,4.75,1.73,1.28,1,2.04,2.55,2.77,4.12,1.1,2.36,3.07,4.66,6.07,7.22,.16,.14,2.63,1.75,2.82,1.87,.03,.02,.21,.38,.15,.58-.07,1.53-.34,5.99,3.62,6.19,.96,.04,.71-.53,.71-.92,0-.78-.17-1.55,.32-2.34,.67-1.09-1.41-1.6-1.35-3.96,.05-1.76-1.72-1.46-2.61-2.8-.52-.78-.97-1.19-.94-2.14,.2-5.35-1.36-8.87-2.13-9.74-.61-.65-1.11-.91-.55-1.21,3.3-1.83,4.05-3.54,4.05-3.54,1.76-3.47,3.33-6.63,5.51-8.03,.44-.29,1.56-.99,2.25-1.27,2.03-.8,3.1-1.29,3.69-1.77,.93-.76,1.67-2.36,.78-3.32-1.11-1.19-3.04-.25-3.89,.18-6.07,3.03-6.96,8.36-9.07,11.43-1.68,2.45-4.4,4.25-6.84,4.4-1.83,.11-3.8-.2-5.76-.92-4.77-1.76-7.38-4.03-8-4.43-1.28-.83-11.23-9.04-19.29-9.37,0,0-1-1.68-1.25-1.71-.59-.06-1.19,1-1.62,1.13-.41,.11-1.09-1.16-1.5-1.1h0ZM31.88,49.16c-.76-.02-1.4-.54-1.4-1.19V26.85h-8.29v23.29c0,1.14,1.1,2.07,2.45,2.07h14.49c1.36,0,2.45-.93,2.45-2.07V26.85h-8.28v21.13c0,.65-.65,1.17-1.41,1.19Zm27.75-22.31h-12.54c-1.49,0-2.7,1.02-2.69,2.27h0v23.09h8.3V31.03c0-.65,.63-1.17,1.4-1.16h0c.77,0,1.4,.5,1.41,1.15v21.2h8.26V31.01c.01-.64,.63-1.15,1.41-1.15s1.4,.51,1.41,1.16v21.18h8.3V29.12c0-1.25-1.2-2.27-2.69-2.27h-12.55ZM16.66,45.84H8.28v6.37H0V26.85H16.71c1.5,0,2.66,1.02,2.66,2.28v14.42c0,1.26-1.21,2.28-2.71,2.29h-.01Zm-5.56-14.76c.01-.66-.62-1.2-1.4-1.21h-1.41v12.83h1.41c.73,0,1.41-.49,1.4-1.19v-10.43h0Zm95.93,18.13c0,1.72-1.64,3.12-3.68,3.12-2.04,0-3.7-1.39-3.71-3.11,0,0,0-.01,0-.02,0-1.7,1.65-3.12,3.65-3.12,2.09,0,3.74,1.38,3.74,3.12Zm-3.73-2.67c-1.67,0-3.06,1.21-3.06,2.67s1.4,2.67,3.11,2.67,3.09-1.2,3.09-2.67-1.38-2.67-3.09-2.67h-.05Zm1.79,4.47h-.99l-1.27-1.42v1.42h-.81v-3.6h.85c1.16,0,1.77,.37,1.77,1.07,0,.47-.36,.88-.87,1.01l-.06,.02,1.38,1.5h0Zm-2.12-2.01c.57,0,.88-.17,.88-.51,0-.31-.3-.46-.86-.46h-.17v.98h.14Zm-14.19,3.2v-6.37h-2.82v6.37h-8.28V29.12c0-1.26,1.21-2.27,2.69-2.27h13.99c1.49,0,2.7,1.02,2.7,2.27h0v23.09h-8.28Zm0-9.51v-11.68c-.02-.65-.64-1.14-1.41-1.14-.77,0-1.39,.52-1.41,1.16v11.66h2.82ZM.23,65v-6.97h.92v.65c.22-.26,.46-.45,.74-.58,.27-.13,.61-.19,1-.19,.51,0,.96,.11,1.35,.33,.39,.22,.69,.54,.89,.94,.2,.4,.3,.85,.3,1.33,0,.51-.11,.98-.33,1.39-.22,.41-.54,.73-.96,.95-.4,.21-.85,.33-1.32,.33-.34,0-.64-.06-.91-.18-.26-.11-.48-.27-.66-.46v2.45H.23Zm.92-4.42c0,.65,.16,1.13,.47,1.44,.31,.31,.69,.46,1.13,.46s.84-.16,1.16-.48c.32-.32,.48-.82,.48-1.49s-.16-1.12-.47-1.44c-.31-.32-.69-.48-1.13-.48s-.82,.17-1.15,.51c-.33,.34-.5,.84-.5,1.48h0Zm15.22,2.49v-.74c-.47,.57-1.1,.85-1.9,.85-.35,0-.68-.06-.99-.17-.31-.11-.53-.26-.68-.43-.16-.19-.27-.41-.31-.63-.04-.17-.06-.43-.06-.8v-3.12h1.02v2.79c0,.45,.02,.75,.06,.9,.07,.22,.2,.4,.41,.53,.21,.13,.46,.19,.77,.19s.59-.07,.86-.2c.27-.13,.46-.31,.57-.54,.11-.23,.17-.56,.17-.98v-2.7h1.02v5.04h-.91Zm8.28,0v-5.04h.91v.71c.19-.25,.45-.45,.75-.59,.31-.15,.67-.23,1.07-.23,.44,0,.81,.08,1.09,.23,.29,.16,.49,.37,.6,.65,.48-.59,1.09-.88,1.85-.88,.6,0,1.05,.14,1.37,.42,.32,.28,.48,.7,.48,1.28v3.46h-1.01v-3.17c0-.34-.03-.59-.1-.74-.07-.15-.19-.28-.36-.36-.18-.09-.4-.14-.61-.14-.42,0-.77,.12-1.05,.35-.28,.24-.42,.61-.42,1.13v2.93h-1.02v-3.27c0-.38-.08-.67-.25-.85-.17-.19-.44-.28-.81-.28-.29,0-.55,.06-.79,.19-.24,.13-.42,.31-.53,.55-.11,.24-.16,.59-.16,1.05v2.61h-1.02Zm19.32-.62c-.38,.27-.74,.46-1.09,.57-.36,.11-.74,.17-1.12,.17-.66,0-1.16-.13-1.52-.4-.35-.27-.53-.62-.53-1.04,0-.25,.07-.47,.2-.68,.13-.2,.31-.36,.52-.49,.22-.12,.46-.22,.73-.28,.3-.06,.6-.1,.9-.13,.82-.08,1.43-.18,1.81-.3,0-.12,0-.19,0-.22,0-.35-.1-.59-.29-.73-.26-.19-.65-.29-1.16-.29-.48,0-.83,.07-1.06,.21-.23,.14-.39,.39-.5,.75l-.99-.12c.09-.35,.24-.64,.45-.86,.21-.22,.51-.39,.9-.51,.39-.12,.84-.18,1.36-.18s.93,.05,1.25,.15c.32,.1,.56,.23,.71,.38,.15,.15,.25,.35,.32,.58,.03,.14,.05,.41,.05,.79v1.14c0,.79,.02,1.3,.07,1.51,.04,.21,.13,.41,.26,.61h-1.06c-.11-.19-.18-.41-.2-.62h0Zm-.09-1.91c-.37,.13-.92,.23-1.66,.32-.42,.05-.71,.11-.89,.17-.16,.06-.3,.15-.4,.28-.09,.12-.14,.25-.14,.4,0,.23,.1,.42,.31,.57,.2,.15,.5,.23,.9,.23s.74-.07,1.04-.21c.29-.13,.53-.34,.67-.59,.11-.19,.16-.48,.16-.85v-.31h0Zm8.67,2.53v-.97h1.16v.97h-1.16Zm12.62-1.85l1,.11c-.11,.58-.39,1.03-.84,1.36-.45,.33-1,.49-1.66,.49-.82,0-1.48-.22-1.98-.67-.5-.45-.75-1.1-.75-1.94,0-.54,.11-1.02,.32-1.43,.22-.41,.54-.71,.98-.92,.44-.2,.93-.31,1.43-.31,.65,0,1.19,.14,1.6,.42,.41,.28,.68,.67,.8,1.18l-.99,.13c-.1-.34-.26-.59-.5-.76-.24-.17-.53-.26-.87-.26-.51,0-.93,.15-1.25,.46-.32,.31-.48,.8-.48,1.46s.16,1.17,.46,1.48c.31,.31,.71,.46,1.21,.46,.4,0,.73-.1,1-.31,.27-.2,.44-.52,.51-.95h0Zm7.27-.67c0-.93,.31-1.62,.93-2.07,.52-.37,1.14-.56,1.88-.56,.83,0,1.5,.23,2.02,.68,.52,.45,.78,1.08,.78,1.88,0,.65-.12,1.16-.35,1.53-.23,.37-.58,.67-1.01,.87-.44,.21-.94,.31-1.45,.31-.84,0-1.52-.22-2.04-.68-.52-.45-.78-1.1-.78-1.95h0Zm1.05,0c0,.64,.17,1.13,.5,1.45,.33,.32,.76,.48,1.26,.48s.92-.16,1.26-.48c.33-.32,.5-.81,.5-1.48,0-.62-.17-1.1-.5-1.42-.34-.32-.76-.48-1.26-.48s-.93,.16-1.26,.48c-.34,.32-.5,.8-.5,1.45Zm11.54,2.52v-5.04h.91v.71c.19-.25,.45-.45,.75-.59,.31-.15,.67-.23,1.07-.23,.44,0,.81,.08,1.09,.23,.29,.16,.49,.37,.6,.65,.48-.59,1.09-.88,1.85-.88,.59,0,1.05,.14,1.37,.42,.32,.28,.48,.7,.48,1.28v3.46h-1.01v-3.17c0-.34-.03-.59-.1-.74-.07-.15-.19-.27-.36-.36-.18-.09-.39-.14-.61-.14-.42,0-.77,.12-1.05,.35-.28,.24-.42,.61-.42,1.13v2.93h-1.02v-3.27c0-.38-.08-.67-.25-.85-.17-.19-.44-.28-.81-.28-.29,0-.55,.06-.79,.19-.24,.13-.42,.31-.53,.55-.11,.24-.16,.59-.16,1.05v2.61h-1.02Z"/></svg>
      </div>
      <div class="col-6 col-md-3 align-self-center text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="65" height="65" viewBox="0 0 65 65" fill="currentColor" class="text-dark"><path d="M32.5,0c17.9,0,32.5,14.6,32.5,32.5s-14.6,32.5-32.5,32.5S0,50.4,0,32.5,14.6,0,32.5,0h0Zm0,5.86c14.67,0,26.64,11.97,26.64,26.64s-11.97,26.64-26.64,26.64S5.86,47.17,5.86,32.5,17.83,5.86,32.5,5.86h0Zm15.61,44.09h-12.99V24.44h6.21v20.32h6.78v5.2Zm-15.48-16.37c0,4.65-3.1,7.18-7.53,7.18h-.91c-4.4,0-7.59-2.26-7.59-6.98V14.85h6.21v19.26c0,1.35,.54,2.02,1.62,2.02h.34c1.38,0,1.65-1.32,1.65-2.43V14.85h6.21v18.73h0Zm-11.67,12.39c0,2,1.63,3.62,3.62,3.62s3.62-1.63,3.62-3.62-1.61-3.62-3.62-3.62c-2,0-3.62,1.63-3.62,3.62h0Zm.68,0c0-1.71,1.33-3.07,2.94-3.07s2.94,1.37,2.94,3.07-1.3,3.07-2.94,3.07c-1.62,0-2.94-1.38-2.94-3.07Zm1.62,1.78h.67v-1.5h.55c.77,0,.79,.34,.8,.67,.02,.6,.01,.68,.13,.83h.69c-.14-.2-.11-.3-.14-.86-.02-.36-.05-.78-.52-.87v-.02c.54-.22,.61-.58,.61-.83,0-.94-.81-1.05-1.19-1.05h-1.61v3.62h0Zm.67-3.13h.7c.43,0,.74,.11,.74,.55,0,.26-.1,.58-.66,.58h-.78v-1.13h0Z" fill-rule="evenodd"/></svg>
      </div>
      <div class="col-6 col-md-3 align-self-center text-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="150" height="65" viewBox="0 0 150 65" fill="currentColor" class="text-dark"><path d="M76.42,3.33c-3.06,3.64-11.77,15.4-12.77,16.92-1.91,2.92-2.08,4.33-2.14,5.27-.06,.92-.06,2.59,1.5,3.95,2.61,2.25,6.26,1.88,9.51,1.88h26.72c1.14,0,1.48-.33,2.1-1.12,2.14-3.15,3.09-6.58,3.09-9.64,0-6.56-4.41-14.82-15.56-18.55l-1.26,1.74c8.85,4.06,12.51,9.37,12.51,16.79,.06,1.99-.49,4.15-1.84,6.14h-25.99c-1.56,0-1.78-1.7-1.22-2.66,2.23-3.83,4.71-7.52,7.44-11.03,2.63-3.39,5.21-6.56,7.93-9.87,.73-.92,.24-1.81-1.22-2.1-3.04-.62-6.21-1.05-10.05-1.05-21.32,0-29.12,11.83-29.12,20.6,0,9.51,7.86,20.54,29.18,20.54,11.83,0,19.58-3.68,24.06-8.41h-4.52c-3.92,3.5-10.65,5.98-19.54,5.98-18.06,0-24.71-9.8-24.71-18.24,.13-7.54,7.27-17.93,25.16-18.28,.68-.02,1.2,.58,.73,1.14h0Zm64.13,54.29c1.07,.15,1.89,.96,1.89,1.92,0,1.09-1.01,1.96-2.25,1.96h-22.91v-14.47h-6.75v14.51h-12.41c-3.36,0-4.05-1.97-4.05-3.79v-10.81h-11.19c-1.11,.72-2.18,1.52-3.32,2.19l-6.64,3.95-8.62-6.09h-13.43v3.64h4.86v-1.99l11.42,7.92-7.8,4.73h-8.48v3.64h9.81c1.05-.69,2.04-1.41,3.13-2.05l6.82-4,8.66,5.98h9.86l-13.03-9.47,6.69-4.22c1.43-.91,2.93-1.67,4.39-2.5v9.53c0,4.02,1.07,6.81,9.45,6.81l47.34-.11c3.66,.05,6-2.39,6-5.34s-2.7-5.35-6-5.35h-17.1c.23-2.01,2.02-3.69,3.84-3.69h17.31l-.05-3.5h-18.36c-5.81,0-10.46,4.66-10.59,10.07-.02,.38,.24,.62,.68,.62l20.83-.08h0Zm-89.68-.05v-3.71h3.34v3.71h-3.34Zm0-10.58h-12.68c-3.43,0-6.08,.87-8.02,2.35-3.51,2.68-4.63,5.51-4.59,6.78h4.57c-2.01,1.45-3.36,3.19-4.07,5.2H11.08c-2.7,0-4.44-1.85-4.44-3.64v-10.81H0v11.23c0,4.04,3.98,6.83,10.35,6.83H50.87v-3.64h-16.09c.08-1.85,2.12-3.71,3.97-3.71h12.11v-3.71h-16.28c-.22-1.09,1.88-3.22,3.49-3.22h12.79v-3.64h0Z" fill-rule="evenodd"/></svg>
      </div> --}}
    </div>
  </div>
</section>



    <section class="bg-white" id="portfolio">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="intro">
                        <h6>Contact</h6>
                        <h1>Need something else?</h1>
                        <p class="mx-auto">We’re here to help! Reach out to our customer support team through the following channels:</p>
                        <span>Email:  <a href="#">{{ $topnav_email }}</a>  </span> <br>
                        <span>Reach us on whatsapp by <a href="{{ $twitter_link }}">clicking this link</a>  </span>
                    </div>
                </div>
            </div>
        </div>
        <div id="projects-slider" class="owl-theme owl-carousel">
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project1.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project2.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project3.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project4.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project5.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
        </div>
    </section>

    {{-- <section class="bg-light" id="portfolio">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="intro">
                        <h6>Work</h6>
                        <h1>Successful projects</h1>
                        <p class="mx-auto">Contrary to popular belief, Lorem Ipsum is not simply random text. It has
                            roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old</p>
                    </div>
                </div>
            </div>
        </div>
        <div id="projects-slider" class="owl-theme owl-carousel">
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project1.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project2.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project3.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project4.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
            <div class="project">
                <div class="overlay"></div>
                <img src="img/project5.jpg" alt="">
                <div class="content">
                    <h2>Consulting Marketing</h2>
                    <h6>Website Design</h6>
                </div>
            </div>
        </div>
    </section> --}}

    {{-- <section id="team">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="intro">
                        <h6>Team</h6>
                        <h1>Team Members</h1>
                        <p class="mx-auto">Contrary to popular belief, Lorem Ipsum is not simply random text. It has
                            roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old</p>
                    </div>
                </div>
            </div>
            <div class="row justify-content-center">
                <div class="col-lg-4 col-md-8">
                    <div class="team-member">
                        <div class="image">
                            <img src="img/team_1.jpg" alt="">
                            <div class="social-icons">
                                <a href="#"><i class='bx bxl-facebook'></i></a>
                                <a href="#"><i class='bx bxl-twitter'></i></a>
                                <a href="#"><i class='bx bxl-instagram'></i></a>
                                <a href="#"><i class='bx bxl-pinterest'></i></a>
                            </div>
                            <div class="overlay"></div>
                        </div>

                        <h5>Marvin McKinney</h5>
                        <p>Marketing Coordinator</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8">
                    <div class="team-member">
                        <div class="image">
                            <img src="img/team_2.jpg" alt="">
                            <div class="social-icons">
                                <a href="#"><i class='bx bxl-facebook'></i></a>
                                <a href="#"><i class='bx bxl-twitter'></i></a>
                                <a href="#"><i class='bx bxl-instagram'></i></a>
                                <a href="#"><i class='bx bxl-pinterest'></i></a>
                            </div>
                            <div class="overlay"></div>
                        </div>

                        <h5>Kathryn Murphy</h5>
                        <p>Ethical Hacker</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-8">
                    <div class="team-member">
                        <div class="image">
                            <img src="img/team_3.jpg" alt="">
                            <div class="social-icons">
                                <a href="#"><i class='bx bxl-facebook'></i></a>
                                <a href="#"><i class='bx bxl-twitter'></i></a>
                                <a href="#"><i class='bx bxl-instagram'></i></a>
                                <a href="#"><i class='bx bxl-pinterest'></i></a>
                            </div>
                            <div class="overlay"></div>
                        </div>

                        <h5>Darrell Steward</h5>
                        <p>Software Developer</p>
                    </div>
                </div>
            </div>
        </div>
    </section> --}}

    <section class="bg-light" id="reviews">

        <div class="owl-theme owl-carousel reviews-slider container">
            <div class="review">
                <div class="person">
                    <img src="img/team_1.jpg" alt="">
                    <h5>{{ $reviewer_name1  }}</h5>
                    <small>{{ $reviewer_position1  }}</small>
                </div>
                <h3>{{ $review1 }}</h3>
                <div class="stars">
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class="bx bxs-star-half"></i>
                </div>
                <i class='bx bxs-quote-alt-left'></i>
            </div>
            <div class="review">
                <div class="person">
                    <img src="img/team_1.jpg" alt="">
                    <h5>{{ $reviewer_name2  }}</h5>
                    <small>{{ $reviewer_position2  }}</small>
                </div>
                <h3>{{ $review2 }}</h3>
                <div class="stars">
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class="bx bxs-star-half"></i>
                </div>
                <i class='bx bxs-quote-alt-left'></i>
            </div>
            <div class="review">
                <div class="person">
                    <img src="img/team_1.jpg" alt="">
                    <h5>{{ $reviewer_name3  }}</h5>
                    <small>{{ $reviewer_position3  }}</small>
                </div>
                <h3>{{ $review3 }}</h3>
                <div class="stars">
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class='bx bxs-star'></i>
                    <i class="bx bxs-star-half"></i>
                </div>
                <i class='bx bxs-quote-alt-left'></i>
            </div>
        </div>
    </section>

    {{-- <section id="blog">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="intro">
                        <h6>Blog</h6>
                        <h1>Blog Posts</h1>
                        <p class="mx-auto">Contrary to popular belief, Lorem Ipsum is not simply random text. It has
                            roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old</p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <article class="blog-post">
                        <img src="img/project5.jpg" alt="">
                        <a href="#" class="tag">Web Design</a>
                        <div class="content">
                            <small>01 Dec, 2022</small>
                            <h5>Web Design trends in 2022</h5>
                            <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a
                                piece of classical Latin literature from</p>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="blog-post">
                        <img src="img/project4.jpg" alt="">
                        <a href="#" class="tag">Programming</a>
                        <div class="content">
                            <small>01 Dec, 2022</small>
                            <h5>Web Design trends in 2022</h5>
                            <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a
                                piece of classical Latin literature from</p>
                        </div>
                    </article>
                </div>
                <div class="col-md-4">
                    <article class="blog-post">
                        <img src="img/project2.jpg" alt="">
                        <a href="#" class="tag">Marketing</a>
                        <div class="content">
                            <small>01 Dec, 2022</small>
                            <h5>Web Design trends in 2022</h5>
                            <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a
                                piece of classical Latin literature from</p>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section> --}}

    <footer>
        {{-- <div class="footer-top text-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-6 text-center">
                        <h4 class="navbar-brand">Prixima<span class="dot">.</span></h4>
                        <p>Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of
                            classical Latin literature from</p>
                        <div class="col-auto social-icons">
                            <a href="#"><i class='bx bxl-facebook'></i></a>
                            <a href="#"><i class='bx bxl-twitter'></i></a>
                            <a href="#"><i class='bx bxl-instagram'></i></a>
                            <a href="#"><i class='bx bxl-pinterest'></i></a>
                        </div>
                        <div class="col-auto conditions-section">
                            <a href="#">privacy</a>
                            <a href="#">terms</a>
                            <a href="#">disclaimer</i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
        <div class="footer-bottom text-center">
            {{-- <p class="mb-0" style="color: white">Copyright Crystalpay 2024. All rights Reserved</p> --}}
            <p class="mb-0" style="color: white">Developed with ❤️ by Subutility 	&#169; {{ date('Y') }} . All rights Reserved</p>
        </div>
    </footer>


    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-body p-0">
                    <div class="container-fluid">
                        <div class="row gy-4">
                            <div class="col-lg-4 col-sm-12 bg-cover"
                                style="background-image: url(img/c2.jpg); min-height:300px;">
                                <div>

                                </div>
                            </div>
                            <div class="col-lg-8">
                                <form class="p-lg-5 col-12 row g-3">
                                    <div>
                                        <h1>Get in touch</h1>
                                        <p>Fell free to contact us and we will get back to you as soon as possible</p>
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="userName" class="form-label">First name</label>
                                        <input type="text" class="form-control" placeholder="Jon" id="userName"
                                            aria-describedby="emailHelp">
                                    </div>
                                    <div class="col-lg-6">
                                        <label for="userName" class="form-label">Last name</label>
                                        <input type="text" class="form-control" placeholder="Doe" id="userName"
                                            aria-describedby="emailHelp">
                                    </div>
                                    <div class="col-12">
                                        <label for="userName" class="form-label">Email address</label>
                                        <input type="email" class="form-control" placeholder="Johndoe@example.com"
                                            id="userName" aria-describedby="emailHelp">
                                    </div>
                                    <div class="col-12">
                                        <label for="exampleInputEmail1" class="form-label">Enter Message</label>
                                        <textarea name="" placeholder="This is looking great and nice."
                                            class="form-control" id="" rows="4"></textarea>
                                    </div>

                                    <div class="col-12">
                                        <button type="submit" class="btn btn-brand">Send Message</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>







   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
   <script src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/js/main.js') }}"></script>
   <script src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/js/bootstrap.bundle.min.js') }}"></script>
   <script src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/js/owl.carousel.min.js') }}"></script>
   <script src="{{ asset(env('APP_ASSETS_BASE_URL').'landing_page_assets/js/app.js') }}"></script>
</body>

</html>