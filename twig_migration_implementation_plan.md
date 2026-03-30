# Migración de Vistas PHP a Twig — Plan Incremental

## Contexto

El proyecto Orion actualmente renderiza vistas con un patrón PHP propio:
- Cada vista define `$title` y una función `showPage()` con HTML embebido
- Un template (`main.php` / `nomain.php`) llama a `showPage()` e incluye `header.php`, `navbar.php` y `footer.php`
- Los datos se pasan vía `$GLOBALS`, variables locales o `$_SESSION`
- Los controladores y rutas usan `include "views/..."` directamente

Ya se tiene Twig 3.24 instalado y un `ViewController::render()` básico.

## Estrategia General

> [!IMPORTANT]
> **Coexistencia PHP + Twig**: Las vistas ya migradas usarán `ViewController::render()`. Las que aún no se migren seguirán funcionando con `include`. Ambos sistemas coexistirán durante toda la migración.

### Convención de nombres
- Archivos Twig: `views/<area>/<nombre>.twig` (extensión `.twig` limpia)
- Layouts Twig: `views/layouts/base.twig`, `views/layouts/base_nomain.twig`

---

## Fase 0 — Infraestructura Base (PRIMERA)

Establecer la base sobre la que se construirán todas las vistas Twig.

### [NEW] ViewHelpers class
#### [NEW] [ViewHelpers.php](file:///d:/orion2025/orion/helpers/ViewHelpers.php)
- Crear clase `ViewHelpers` con método `getBaseData()` que devuelva datos comunes:
  - `session_user` → `$_SESSION['user'] ?? null`
  - `user` (objeto User si hay sesión)
  - `has_order` → `OrderHelper::getOrder()` (si aplica)
  - `cart_items`, `cart_total` (si hay carrito)
  - `is_admin`, `is_developer` (flags de rol)
  - Cualquier otro dato global necesario para el navbar/footer

### [MODIFY] ViewController
#### [MODIFY] [ViewController.php](file:///d:/orion2025/orion/controllers/ViewController.php)
- Convertir la instancia de Twig en singleton/estática para evitar crear el loader en cada render
- Cambiar extensión de `.php.twig` a `.twig` en la llamada a `render()`
- Mantener caché activado (`'cache' => './cache/twig'`)
- Registrar extensiones/funciones Twig personalizadas:
  - `asset()` → genera rutas `/assets/...`
  - `cdn_url()` → genera URLs del CDN de Orion
  - `user_profile_pic(user)` → `getProfilePicURL()`
  - `user_handle(user)` → `getHandle()`
- Añadir `ViewController::renderFromController($view, $data)` que hace `echo` y `exit()`

### [NEW] Layout base Twig
#### [NEW] [base.twig](file:///d:/orion2025/orion/views/layouts/base.twig)
- Replica `header.php` + `<main>` + `footer.php` en un layout Twig
- Define bloques: `{% block title %}`, `{% block content %}`, `{% block scripts %}`, `{% block styles %}`
- Incluye navbar como `{% include 'layouts/navbar.twig' %}`

#### [NEW] [base_nomain.twig](file:///d:/orion2025/orion/views/layouts/base_nomain.twig)
- Similar a `base.twig` pero SIN el wrapper `<main class="container">` (para hero sections, etc.)

#### [NEW] [navbar.twig](file:///d:/orion2025/orion/views/layouts/navbar.twig)
- Traduce `navbar.php` y `NavbarHelper::getUserNavbar()` a Twig puro
- Usa las variables de `ViewHelpers::getBaseData()` directamente

#### [NEW] [footer.twig](file:///d:/orion2025/orion/views/layouts/footer.twig)
- Traduce `footer.php` a Twig

### [MODIFY] Autoload
#### [MODIFY] [composer.json](file:///d:/orion2025/orion/composer.json)
- Añadir `controllers/` al classmap de autoload

---

## Fase 1 — Páginas Legales (las más simples, ~4 vistas)

> [!TIP]
> Empezar por aquí porque son vistas estáticas, sin lógica ni datos dinámicos. Perfectas para probar que la infraestructura funciona.

| Vista PHP | Vista Twig |
|---|---|
| `views/legal/cookies.php` | `views/legal/cookies.php.twig` |
| `views/legal/privacy.php` | `views/legal/privacy.php.twig` |
| `views/legal/terms.php` | `views/legal/terms.php.twig` |
| `views/legal/refund.php` | `views/legal/refund.php.twig` |

**Cambios en rutas** ([routes/legal.php](file:///d:/orion2025/orion/routes/legal.php)):
```php
// Antes:
include "views/legal/cookies.php";

// Después:
ViewController::render('legal/cookies', ['title' => 'Política de cookies']);
```

---

## Fase 2 — Auth: Login y Register (~2 vistas)

| Vista PHP | Vista Twig |
|---|---|
| `views/auth/login.php` | `views/auth/login.php.twig` |
| `views/auth/register.php` | `views/auth/register.php.twig` |

**Cambios en rutas** ([routes/auth.php](file:///d:/orion2025/orion/routes/auth.php)):
- Cambiar `include` por `ViewController::render()`

---

## Fase 3 — Auth: Perfil, Edición de Perfil, Librería, Amigos (~4 vistas)

| Vista PHP | Vista Twig |
|---|---|
| `views/auth/profile.php` | `views/auth/profile.php.twig` |
| `views/auth/profileEdit.php` | `views/auth/profileEdit.php.twig` |
| `views/auth/library.php` | `views/auth/library.php.twig` |
| `views/auth/friends.php` | `views/auth/friends.php.twig` |

**Cambios necesarios**:
- Eliminar uso de `$GLOBALS` en rutas; pasar datos como parámetros a `ViewController::render()`
- Actualizar `FriendController::friendsList()` para usar `ViewController::render()` en lugar de `include`

---

## Fase 4 — Community (~10 vistas)

| Vista PHP | Vista Twig |
|---|---|
| `views/community/index.php` | `views/community/index.php.twig` |
| `views/community/hub.php` | `views/community/hub.php.twig` |
| `views/community/posts/index.php` | `views/community/posts/index.php.twig` |
| `views/community/posts/post.php` | `views/community/posts/post.php.twig` |
| `views/community/posts/create.php` | `views/community/posts/create.php.twig` |
| `views/community/news/index.php` | `views/community/news/index.php.twig` |
| `views/community/news/post.php` | `views/community/news/post.php.twig` |
| `views/community/gallery/index.php` | `views/community/gallery/index.php.twig` |
| `views/community/gallery/post.php` | `views/community/gallery/post.php.twig` |
| `views/community/gallery/create.php` | `views/community/gallery/create.php.twig` |
| `views/community/guides/index.php` | `views/community/guides/index.php.twig` |
| `views/community/guides/post.php` | `views/community/guides/post.php.twig` |
| `views/community/guides/create.php` | `views/community/guides/create.php.twig` |

**Reto principal**: Migrar los componentes PHP reutilizables (`OrionComponents`) a macros/includes de Twig.

---

## Fase 5 — Store (~4 vistas)

| Vista PHP | Vista Twig |
|---|---|
| `views/store/index.php` | `views/store/index.php.twig` |
| `views/store/hub.php` | `views/store/hub.php.twig` |
| `views/store/search.php` | `views/store/search.php.twig` |
| `views/store/cart.php` | `views/store/cart.php.twig` |

---

## Fase 6 — Home, 404, Stripe, Dev (~10+ vistas)

| Vista PHP | Vista Twig |
|---|---|
| `views/index.php` | `views/index.php.twig` |
| `views/404.php` | `views/404.php.twig` |
| `views/stripe/success.php` | `views/stripe/success.php.twig` |
| `views/dev/index.php` | `views/dev/index.php.twig` |
| `views/dev/profile.php` | `views/dev/profile.php.twig` |
| `views/dev/panel/*` | `views/dev/panel/*.php.twig` |

---

## Fase 7 — Admin (~5+ vistas) y Limpieza Final

| Vista PHP | Vista Twig |
|---|---|
| `views/admin/home.php` | `views/admin/home.php.twig` |
| `views/admin/tools.php` | `views/admin/tools.php.twig` |
| `views/admin/gamefeatures/index.php` | etc. |
| Templates panel admin/dev | Layouts Twig dedicados |

**Limpieza**:
- Eliminar archivos PHP de vistas ya migradas
- Eliminar `views/templates/main.php`, `nomain.php`, `header.php`, `navbar.php`, `footer.php`
- Eliminar `NavbarHelper` PHP
- Eliminar archivos `-bs5` legacy

---

## Decisiones Confirmadas

- ✅ Extensión `.twig` (no `.php.twig`)
- ✅ Caché de Twig activado (`'cache' => './cache/twig'`)
- ✅ Empezar por Fase 0 (infra) + Fase 1 (legal)
- ✅ Eliminar archivos legacy `-bs5`

---

## Plan de Verificación

### Por cada fase:
1. Crear la vista `.php.twig`
2. Actualizar la ruta/controlador para usar `ViewController::render()`
3. Verificar visualmente en el navegador que la vista se renderiza correctamente
4. Confirmar que las vistas PHP no migradas siguen funcionando

### Al final de la migración:
- Eliminar todos los archivos PHP de vista obsoletos
- Validar que no queden referencias a `include "views/..."` para vistas ya migradas
- Limpiar la caché de Twig
