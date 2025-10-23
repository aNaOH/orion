<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title><?= $title ?? "Orion | Admin Panel" ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Gabarito:wght@400..900&family=Lexend:wght@100..900&display=swap"
    rel="stylesheet">

  <!-- Tailwind -->
  <script src="/assets/vendor/tailwind-3.4.15.js"></script>
  <script src="/assets/js/tailwindConfig.js"></script>

  <!-- JQuery -->
  <script src="/assets/vendor/jquery/jquery-3.7.1.min.js"></script>

  <!-- Orion CSS -->
  <link rel="stylesheet" href="/assets/css/main.css">
  <link rel="stylesheet" href="/assets/css/orion-panel.css">

  <!-- Vendor CSS -->
  <link rel="stylesheet" href="/assets/vendor/bootstrap-icons/bootstrap-icons.min.css">

</head>

<body class="bg-brand-900 text-gray-100 font-base h-screen overflow-hidden">
  <!-- CONTENEDOR PRINCIPAL -->
  <div class="flex h-full w-full overflow-hidden">
