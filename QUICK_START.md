# ⚡ Quick Start - Sistema de Roles

## 🚀 Instalación en 3 Pasos

### 1️⃣ Ejecutar Script de Instalación

**Linux/Mac**:
```bash
chmod +x setup-roles.sh
./setup-roles.sh
```

**Windows (PowerShell)**:
```powershell
.\setup-roles.ps1
```

**O manualmente**:
```bash
php artisan migrate
php artisan db:seed --class=UserSeeder
```

---

### 2️⃣ Iniciar Servidor

```bash
php artisan serve
```

---

### 3️⃣ Probar

Acceder a: **http://127.0.0.1:8000/login**

**Usuarios de prueba**:
- Admin: `admin@example.com` / `password`
- User: `user@example.com` / `password`

---

## 🎯 Diferencias entre Roles

| Acción | Admin | User |
|--------|:-----:|:----:|
| Ver productos | ✅ | ✅ |
| Crear productos | ✅ | ✅ |
| Editar productos | ✅ | ✅ |
| **Eliminar productos** | ✅ | ❌ |

---

## 📚 Documentación

| Archivo | Descripción |
|---------|-------------|
| **SECURITY_GUIDE.md** | 📖 Guía completa de seguridad (LEER PRIMERO) |
| **INSTALLATION_INSTRUCTIONS.md** | 🔧 Instrucciones detalladas de instalación |
| **CHANGES_SUMMARY.md** | 📋 Resumen de todos los cambios |
| **README_ROLES.md** | 📘 README completo del sistema |
| **CHECKLIST.md** | ✅ Checklist de verificación |
| **QUICK_START.md** | ⚡ Este archivo (inicio rápido) |

---

## 🧪 Prueba Rápida

### Como Admin
1. Login: `admin@example.com` / `password`
2. Ir a: `/products`
3. **Resultado**: Ves botón "🗑 Eliminar"

### Como User
1. Login: `user@example.com` / `password`
2. Ir a: `/products`
3. **Resultado**: NO ves botón "🗑 Eliminar"

---

## 🔒 Seguridad Implementada

✅ CSRF Protection  
✅ Hash de contraseñas  
✅ Blade escaping (XSS)  
✅ Middleware de roles  
✅ Validación de datos  
✅ Múltiples capas de seguridad  

---

## 🆘 Problemas Comunes

### Error: "Column 'role' not found"
```bash
php artisan migrate
```

### Error: "Class 'UserSeeder' not found"
```bash
composer dump-autoload
php artisan db:seed --class=UserSeeder
```

### Los cambios no se reflejan
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## 📞 Siguiente Paso

**Lee el SECURITY_GUIDE.md** para entender:
- Cómo funciona cada componente
- Conceptos de autenticación y autorización
- Mejores prácticas de seguridad
- Explicaciones paso a paso

---

**¡Listo en 3 minutos!** ⏱️
