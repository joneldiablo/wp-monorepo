# 🏗️ WP Monorepo

Monorepo de proyectos WordPress: temas, plugins y sitios completos.

Agrupación de repositorios WordPress de [@joneldiablo](https://github.com/joneldiablo) en un solo lugar para facilitar su mantenimiento y visibilidad.

---

## 📋 Índice

### 🎨 Temas

| Proyecto | Descripción | Tech |
|----------|-------------|------|
| [llanteradelvalle-wp-theme](./themes/llanteradelvalle-wp-theme) | Chromium child theme para Llantera del Valle | PHP, Pug, SCSS |
| [wp-bs4-child-theme](./themes/wp-bs4-child-theme) | Wp-bootstrap-4 child theme | PHP, CSS |
| [bs-themes](./themes/bs-themes) | Variantes Bootswatch (cerulean, cosmo, darkly, etc.) | SCSS, CSS |

### 🔌 Plugins

| Proyecto | Descripción | Tech |
|----------|-------------|------|
| [wc-gateway-multipagos-express](./plugins/wc-gateway-multipagos-express) | WooCommerce gateway — Multipagos Express | PHP |
| [wp-shortcode-search](./plugins/wp-shortcode-search) | Shortcode search para productos/posts con React | PHP, React |
| [wp-plugin-price-calculator](./plugins/wp-plugin-price-calculator) | Calculadora de precios vía shortcode | PHP, React |
| [wp-plugins](./plugins/wp-plugins) | Colección de plugins basados en REST + shortcodes | PHP |

### 🌐 Sitios WordPress Completos

| Proyecto | Descripción | Tech | Stack |
|----------|-------------|------|-------|
| [llanteradelvalle](./sites/llanteradelvalle) | Tienda WooCommerce — Llantera del Valle | PHP | WP + WooCommerce |
| [etic-a-fem](./sites/etic-a-fem) | WordPress completo | PHP | WP Stock |
| [form-agent-register](./sites/form-agent-register) | WordPress completo | PHP | WP Stock |
| [psicologofernando](./sites/psicologofernando) | WordPress v4.8.1 via Composer + wpackagist (Heroku-ready) | PHP | WP + Composer |

---

## 🚀 Cómo usar

Cada proyecto funciona de forma independiente dentro de su carpeta.

### Para desarrollo local

```bash
# Tema
cd themes/llanteradelvalle-wp-theme
npm install  # si aplica

# Plugin
cd plugins/wp-shortcode-search
npm install && npm run build

# Sitio
cd sites/llanteradelvalle
# Copiar wp-config-sample.php a wp-config.php y configurar DB
```

### Para contribuir

PRs y sugerencias bienvenidas. Cada proyecto mantiene su propia estructura.

---

## 🧹 Créditos

Creado y mantenido por [@joneldiablo](https://github.com/joneldiablo) 😈
