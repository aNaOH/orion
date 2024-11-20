<!DOCTYPE html>
<html lang="es" class="dark h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$title?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
            extend: {
              colors: {
                brand: {
                  DEFAULT: '#1B2A49',
                  50: '#F0F3F9',
                  100: '#D1D9E4',
                  200: '#A3B0C9',
                  300: '#7487AD',
                  400: '#4C6B91',
                  500: '#1B2A49', // El color principal
                  600: '#15213a',
                  700: '#111a2d',
                  800: '#0D1320',
                  900: '#091023',
                  950: '#070B1C'
                },
                branddark: {
                  DEFAULT: '#15213a',
                  50: '#E2E6F1',
                  100: '#B5BCC8',
                  200: '#8891A0',
                  300: '#5C6680',
                  400: '#3F4A60',
                  500: '#15213a', // El color más oscuro
                  600: '#11172D',
                  700: '#0C1220',
                  800: '#080C15',
                  900: '#040608',
                  950: '#020305'
                },
                alt: {
                  DEFAULT: '#DEAB18',
                  50: '#FFF8E1',
                  100: '#FFE680',
                  200: '#FFDC4D',
                  300: '#FFD118',
                  400: '#FFBB00',
                  500: '#DEAB18', // El color principal
                  600: '#C88A15',
                  700: '#9E7412',
                  800: '#7B5E0F',
                  900: '#5A480C',
                  950: '#41370A'
                }
              }
            }
          }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        .animate-fadeIn {
            animation: fadeIn 0.3s ease-out;
        }
        .hover-pulse:hover {
            animation: pulse 0.3s ease-in-out;
        }
        .link-underline {
            position: relative;
        }
        .link-underline::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: -2px;
            left: 0;
            background-color: white;
            transition: width 0.3s ease;
        }
        .link-underline:hover::after {
            width: 100%;
        }
        .account-dropdown:hover .dropdown-icon,
        .account-dropdown[aria-expanded="true"] .dropdown-icon {
            transform: rotate(90deg);
        }

        .menu-open {
            animation: fadeIn 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-brand text-white h-full">
  <?php include('navbar.php') ?>