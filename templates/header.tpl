<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v3.8.6">
    <title>Shop CMS</title>


    <!-- Bootstrap core CSS -->
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<meta name="theme-color" content="#563d7c">

{literal}
    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
    </style>
{/literal}
    <!-- Custom styles for this template -->
{*     <link href="album.css" rel="stylesheet"> *}
  </head>
  <body>
    <header>
  <div class="collapse bg-dark" id="navbarHeader">
    <div class="container">
      <div class="row">
        <div class="col-sm-8 col-md-7 py-4">
          <h4 class="text-white">Shop v0.1</h4>         
        </div>
        <div class="col-sm-4 offset-md-1 py-4">
          <ul class="list-unstyled">
            <li><a href="/folder/" class="text-white">Категории</a></li>
            <li><a href="/vendor/" class="text-white">Производители</a></li>
          </ul>
        </div>
      </div>
    </div>
  </div>
  <div class="navbar navbar-dark bg-dark shadow-sm">
    <div class="container d-flex justify-content-between">
      <a href="/" class="navbar-brand d-flex align-items-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" aria-hidden="true" class="mr-2" viewBox="0 0 24 24" focusable="false"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
        <strong>Shop Main</strong>
      </a>
      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
    </div>
  </div>

      <div class="navbar navbar-dark bg-dark shadow-sm">
        <div class="container d-flex justify-content-between">

          {if $user->getId()}
            <span>Вы зашли как: <a href="/user/edit.php">{$user->getName()}</a></span>
            <a href="/user/logout.php">Выход</a>

          {else}

          <form class="form-inline" method="post" action="/user/login.php">
            <label class="sr-only" for="inlineFormInputName2">Login</label>
            <input type="text" name="login" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="login">

            <label class="sr-only" for="inlineFormInputName2">Name</label>
            <input type="password" name="password" class="form-control mb-2 mr-sm-2" id="inlineFormInputName2" placeholder="pwd">

{*            <div class="form-check mb-2 mr-sm-2">*}
{*              <input class="form-check-input" type="checkbox" id="inlineFormCheck">*}
{*              <label class="form-check-label" for="inlineFormCheck">*}
{*                Remember me*}
{*              </label>*}
{*            </div>*}

            <button type="submit" class="btn btn-primary mb-2">Submit</button>
          </form>
            <a href="/user/edit.php">Регистрация</a>
          {/if}

        </div>
      </div>
</header>

<main role="main">


  <div class="album py-5 bg-light">
    <div class="container">
