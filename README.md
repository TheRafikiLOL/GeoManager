# GeoManager

## Índice

- [Descripción del proyecto](#descripción-del-proyecto)
- [Versiones utilizadas](#descripción-del-proyecto)
- [Puesta en marcha](#puesta-en-marcha)
  - [Descargar el repositorio](#descargar-el-repositorio)
  - [Preparar el proyecto](#preparar-el-proyecto)
  - [Base de datos](#base-de-datos)
- [Librerías utilizadas](#librerías-utilizadas)

## Descripción del proyecto

GeoManager es una aplicación diseñada para cubrir la necesidad de gestionar un CRUD de países. El proyecto permite sincronizar los datos obtenidos de la API "REST Countries", asegurando que los registros estén actualizados con la información más reciente.

El proyecto está compuesto por cuatro vistas principales:

1. **Paises (index)**: Un listado de todos los países disponibles, presentado en formato de tabla. Desde aquí se pueden ejecutar diversas acciones como sincronizar los países con la API, crear un nuevo país, ver el detalle de un país, editarlo o eliminarlo.
2. **Crear**: Esta vista ofrece un formulario para la creación de un nuevo registro de país.
3. **Detalle de un país**: Muestra de forma detallada toda la información de un país seleccionado.
4. **Editar**: Un formulario que permite modificar los datos existentes de un país.

Además de la entidad _"País"_, el sistema incluye dos entidades más: _**"Idiomas"**_ y _**"Monedas"**_. Estas entidades no se pueden crear directamente desde las vistas de la aplicación, ya que se generan automáticamente durante la sincronización de los países. Sin embargo, es posible relacionarlas con los países a través de los campos correspondientes en el formulario de edición de países.

### Versiones utilizadas

| Herramienta   | Versión    |
|---------------|------------|
| PHP           | 8.2        |
| Base de datos | MySQL 9.1.0|
| Symfony       | 7.1        |


## Puesta en marcha

### Descargar el repositorio

Para iniciar el proyecto en un entorno local, abre un terminal en el directorio donde desees clonar el proyecto y ejecuta:

```bash
git clone https://github.com/TheRafikiLOL/GeoManager.git
```

### Preparar el proyecto
Una vez clonado el repositorio, accede al directorio del proyecto con:
```bash
cd GeoManager
```

### Base de datos
El siguiente paso es configurar la base de datos donde se almacenará la información de los países. Para este proyecto, se utilizó una base de datos con el nombre _"app_geomanager"_ y el cotejamiento _"utf8mb4_unicode_ci"_.

Después de crear la base de datos, edita el archivo ".env" ubicado en la raíz del proyecto:
```bash
GeoManager/.env
```

Modifica la línea que define la conexión a la base de datos (`DATABASE_URL`), asegurándote de que contenga el nombre correcto de tu base de datos. Ejemplo:
```php
DATABASE_URL="mysql://root@127.0.0.1:3306/app_geomanager"
```
Si tu base de datos tiene un usuario con contraseña, actualiza la línea de la siguiente manera:
```php
DATABASE_URL="mysql://user:password@127.0.0.1:3306/app_geomanager"
```

Una vez que el archivo _".env"_ esté configurado, prepara la estructura de la base de datos ejecutando el siguiente comando:
```bash
php bin/console doctrine:migrations:migrate
```
Con la base de datos lista, inicia el servidor de Symfony:
```bash
symfony serve:start
```

Cuando el servidor esté activo, podrás acceder a la aplicación a través de la URL: [http://127.0.0.1:8000](http://127.0.0.1:8000).

## Librerías utilizadas

- [Bootstrap 5](https://getbootstrap.com/docs/5.0/getting-started/introduction/).
- [FontAwesome](https://fontawesome.com/).
- [DataTables](https://datatables.net/).
- [Select2](https://select2.org/).
