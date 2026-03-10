# Reglas de Desarrollo del Proyecto

## Principios Fundamentales

### 1. Principios SOLID

**Aplicar estrictamente en todo el código:**

- **S**ingle Responsibility Principle - Una clase, un propósito
- **O**pen/Closed Principle - Abierto a extensión, cerrado a modificación
- **L**iskov Substitution Principle - Subtypes deben ser sustituibles
- **I**nterface Segregation Principle - Interfaces específicas, no genéricas
- **D**ependency Inversion Principle - Depender de abstracciones

### 2. No Inventar Datos

- No crear datos fake o de ejemplo no solicitados
- Solo implementar lo específicamente requerido
- No agregar funcionalidades "por si acaso"
- Ceñirse estrictamente a los requisitos

### 3. No Testing Automático

- No ejecutar tests después de cada instrucción
- Solo testear cuando sea explícitamente solicitado
- Confiar en el código escrito con buenas prácticas

### 4. Arquitectura Modular

**CONVENCIÓN OBLIGATORIA:** Respetar estructura de directorios de Laravel.

**Los módulos se organizan DENTRO de la estructura del framework:**

```
app/
├── Http/
│   ├── Controllers/
│   │   └── [Modulo]/          # Controladores del módulo
│   ├── Requests/
│   │   └── [Modulo]/          # Form Requests del módulo
│   └── Resources/
│       └── [Modulo]/          # API Resources del módulo
├── Models/
│   └── [Modulo]/              # Modelos del módulo
├── Services/
│   └── [Modulo]/              # Services del módulo
├── Repositories/
│   └── [Modulo]/              # Repositories del módulo
├── Traits/
│   └── [Modulo]/              # Traits del módulo
├── Policies/
│   └── [Modulo]/              # Policies del módulo
├── Jobs/
│   └── [Modulo]/              # Jobs del módulo

routes/
└── [modulo].php               # Rutas del módulo

docs/
└── modules/
    └── [modulo]/              # Documentación del módulo
```

**Ejemplo: Módulo Admin**
- Controllers: `app/Http/Controllers/Admin/`
- Models: `app/Models/Admin/`
- Services: `app/Services/Admin/`
- Rutas: `routes/admin.php`
- Docs: `docs/modules/admin/`

**Registro de rutas:**
```php
// En routes/api.php
Route::prefix('admin')->group(base_path('routes/admin.php'));
```

Cada módulo es autocontenido con sus propias responsabilidades.

### 5. Documentación Concisa

**Para desarrolladores experimentados:**

- Sin exceso de ejemplos de código
- Documentación técnica directa
- Solo lo esencial y necesario
- Ejemplos mínimos pero claros
- Sin explicaciones obvias

### 6. Commits Frecuentes

- Hacer commit después de cada paso completado
- Mensajes descriptivos y claros
- Un commit por funcionalidad/cambio lógico
- No acumular múltiples cambios sin commit

### 6.1. Procedimiento de Cierre de Sesión

**CRÍTICO:** Al finalizar cada sesión de trabajo:

1. **Commitear todos los cambios pendientes**
   - No dejar código sin commitear
   - Verificar con `git status` que esté limpio

2. **Crear resumen de sesión**
   - Ubicación: `docs/sesiones/YYYY-MM-DD_HH-MM.md`
   - Contenido: Resumen breve y descriptivo
   - Sin ejemplos de código, solo descripción
   - Incluir: funcionalidades, archivos, commits, pendientes

3. **Formato del resumen:**
   - Funcionalidades implementadas
   - Archivos principales modificados
   - Commits realizados
   - Pendiente para próxima sesión (si aplica)

### 7. Uso de Herramientas del Framework

**Aprovechar todas las herramientas Laravel:**

- **Traits** - Para funcionalidad reutilizable
- **Services** - Lógica de negocio compleja
- **Repositories** - Abstracción de acceso a datos
- **Form Requests** - Validación
- **API Resources** - Transformación de respuestas
- **Events/Listeners** - Eventos del sistema
- **Jobs** - Tareas asíncronas
- **Middleware** - Filtros de request
- **Policies** - Autorización
- **Observers** - Eventos de modelo

### 8. Rendimiento y Performance

**Optimización prioritaria:**

- Uso eficiente de queries (N+1, eager loading)
- Cache estratégico
- Colas para procesos pesados
- Índices de base de datos apropiados
- Paginación en colecciones grandes
- Lazy loading cuando aplique
- Minimizar dependencias innecesarias

## Reglas Adicionales

### Nomenclatura

- **Clases**: PascalCase
- **Métodos**: camelCase
- **Variables**: camelCase
- **Constantes**: UPPER_SNAKE_CASE
- **Rutas**: kebab-case
- **Archivos**: Coincidir con nombre de clase

### Estructura de Código

- Inyección de dependencias en constructores
- Type hinting siempre que sea posible
- Return types declarados
- Métodos pequeños y específicos
- Evitar código duplicado (DRY)
- Comentarios solo cuando sea necesario explicar "por qué", no "qué"

### Separación de Responsabilidades

**Controllers:**
- Solo orquestación
- Delegar a Services
- No lógica de negocio

**Services:**
- Lógica de negocio
- Operaciones complejas
- Transacciones

**Repositories:**
- Acceso a datos
- Queries complejas
- Abstracción de Eloquent

**Models:**
- Definición de estructura
- Relaciones
- Scopes
- Mutators/Accessors

### Base de Datos

- Migraciones para todos los cambios de schema
- Seeders para datos iniciales requeridos
- Factories solo para testing
- Índices en columnas de búsqueda frecuente
- Foreign keys con cascadas apropiadas
- Soft deletes cuando sea necesario

---

### ⚠️ CRÍTICO — DB Compartida: Tablas Preexistentes

**Este proyecto se conecta a una base de datos que contiene tablas de otros sistemas.**

**PROHIBIDO sin instrucción explícita del usuario:**
- Modificar tablas no creadas por este proyecto
- Ejecutar `migrate:fresh`, `migrate:rollback` sin especificar `--step` controlado
- Crear migraciones que alteren (`ALTER`, `DROP`, `RENAME`) tablas ajenas
- Crear seeders que escriban en tablas ajenas
- Modificar, eliminar o actualizar modelos existentes del proyecto

**Permitido únicamente:**
- Crear nuevas tablas propias del proyecto mediante migraciones nuevas
- Leer tablas ajenas con modelos de solo lectura si el usuario lo solicita
- Modificar modelos/tablas propias **solo bajo instrucción explícita**

**Comandos de migración seguros en este contexto:**
```bash
php artisan migrate                        # solo corre migraciones pendientes
php artisan migrate --step=1               # una migración a la vez
php artisan migrate:status                 # revisar estado sin ejecutar
```

**Estado actual del proyecto (tablas propias):**
- `users` — autenticación base
- `cache`, `jobs` — framework Laravel
- `personal_access_tokens` — Sanctum

---

**CRÍTICO - Normalización de Campos Booleanos:**
- **TODOS** los campos de estado (activo/inactivo, habilitado/deshabilitado, etc.) DEBEN almacenarse como **1 y 0**
- Usar `->boolean()` en migraciones
- Usar trait `NormalizesBooleans` en modelos para asegurar conversión automática
- Evitar inconsistencias entre true/false y 1/0 en diferentes tablas
- En casting de modelos: `'campo' => 'boolean'` pero el trait maneja la normalización

### Seguridad

- Validación en Form Requests
- Autorización con Policies/Gates
- Sanitización de inputs
- Protección CSRF (automática en Laravel)
- Rate limiting en rutas críticas
- Never trust user input

### API REST

- Verbos HTTP correctos (GET, POST, PUT/PATCH, DELETE)
- Códigos de estado apropiados (200, 201, 404, 422, etc.)
- Respuestas JSON consistentes
- Versionado cuando sea necesario
- Documentación de endpoints

### Git

**Mensajes de Commit:**
```
tipo: descripción breve

- Detalle adicional si es necesario
- Otro detalle

Tipos: feat, fix, refactor, docs, style, test, chore
```

**Ejemplo:**
```
feat: agregar autenticación con Sanctum

- Crear AuthController con login/register
- Agregar rutas API de autenticación
- Configurar middleware auth:sanctum
```

### Testing (Cuando se Requiera)

- Tests unitarios para Services
- Tests de integración para APIs
- Factories para datos de prueba
- Arrange-Act-Assert pattern
- Nombres descriptivos de tests

## Workflow de Desarrollo

### Al Implementar una Nueva Funcionalidad:

1. **Analizar** requisitos y arquitectura
2. **Diseñar** estructura modular (SOLID)
3. **Crear** migraciones si es necesario
4. **Implementar** en orden:
   - Models
   - Repositories (si aplica)
   - Services
   - Requests (validación)
   - Resources (transformación)
   - Controllers
   - Routes
5. **Documentar** de forma concisa en docs del módulo
6. **Commit** con mensaje descriptivo

### Al Refactorizar:

1. **Identificar** problema/oportunidad
2. **Planear** mejora respetando SOLID
3. **Refactorizar** manteniendo funcionalidad
4. **Verificar** que no se rompe nada
5. **Commit** con tipo "refactor"

## Antipatrones a Evitar

- God Classes (clases que hacen demasiado)
- Fat Controllers (controllers con lógica de negocio)
- Queries en vistas
- Lógica en migraciones
- Hardcodear valores que deberían ser configurables
- Código comentado (eliminar, usar git)
- Variables no descriptivas ($data, $temp, $x)
- Métodos de más de 20 líneas
- Clases de más de 200 líneas
- Dependencias circulares

## Checklist Pre-Commit

- [ ] Código sigue principios SOLID
- [ ] No hay datos fake/inventados
- [ ] Uso apropiado de herramientas Laravel
- [ ] Nomenclatura consistente
- [ ] Sin código duplicado
- [ ] Performance considerado
- [ ] **Campos booleanos normalizados con trait NormalizesBooleans**
- [ ] Documentación actualizada (si aplica)
- [ ] Mensaje de commit descriptivo

## Sobre el Autor

- **Codebini & Vidi** — Arquitectura, diseño y dirección del proyecto
- **Claude Sonnet 4.6 (Anthropic)** — Co-autor de implementación