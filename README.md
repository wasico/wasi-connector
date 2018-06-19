# Wasi Connector #
**Contributors:** jalamprea  
**Tags:** comments, spam  
**Requires at least:** 4.0  
**Tested up to:** 4.9.6  
**License:** CC BY-ND 4.0  
**License URI:** https://creativecommons.org/licenses/by-nd/4.0/  

Plugin que permite conectar y mostrar un listado de propiedades inmobiliarias desde Wasi.co

## Descripción ##

Este plugin permite a un sitio web en WordPress conectarse a la API de https://wasi.co y convertirlo en un sitio web especializado para ofertar inmuebles.

Para esto necesita tener una cuenta en Wasi y obtener las “Credenciales de API“ en el menú “Configuración > Ajustes Generales“,  sí no encuentra cómo generarlas por favor contáctenos al correo soporte@wasi.co


## Instalación ##

1. Subir el plugin `wasi-connector` a la carpeta `/wp-content/plugins/`
2. Activar el plugin a través del menú lateral 'Plugins' en WordPress
3. **Crear las páginas para:** Listado completo de propiedades y para una Propiedad única  
4. En dichas páginas agregar los shortcodes necesarios (Ver sección de shortcodes)
5. En el menú de Ajustes (Settings), ir a las configuración de Wasi y configurar todos los valores.


## Configuración del API Key (Llave de API) ##

En el menú de “Ajustes (Settings)“, ir al submenú de “API Wasi.co” y guardar allí los dos valores principales: **ID Company y Wasi Token.**
Ambos deben ser generados desde tu propia cuenta de Wasi en el menú “Configuración > Ajustes Generales“.

Puedes aprender más sobre la API aqui: https://api.wasi.co/guide/es/first_steps/access.html


## Ajustes principales del plugin ##

En el menú de WP de Ajustes, submenú de “API Wasi.co”, además de la API, se encuentran las siguientes configuraciones:

- El valor numérico del total de propiedades a mostrar por cada página cuando hayan resultados de búsquedas.

- Una lista desplegable que permite definir cuál será la página donde se mostrará todo el listado completo de propiedades y los resultados de las búsquedas. Esta página debe estar creada con anterioridad y debe tener en su contenido el shortcode [wasi-properties]  (Ver configuraciones de shortcodes).

- Otra lista de páginas para seleccionar la página encargada de mostrar la información de cada propiedad de manera independiente. El contenido de esta página será ignorado por completo y se mostrará la información de la propiedad que sea pasada desde el listado de propiedades.

- Un checkbox para permitir seleccionar si se quiere cargar la librería gráfica “Bootstrap 3.6” la cual contiene las clases gráficas necesarias para mostrar el layout del plugin de manera correcta. Activar esta opción solo si su plantilla/theme no tiene ya por defecto activo esta librería gráfica.

- La duración (en días) de la memoria cache temporal del plugin. Por defecto viene de 1 días, pero puede cambiar a un valor entre 1 y 365. Esto tendrá efecto solo si después de guardar el valor, se limpia la cache con el botón "Clear Plugin Cache" para eliminar los datos actuales y refrescar toda la cache.



## Configuraciones de Shortcodes y Widgets ##

El plugin cuenta con varios shortcodes y widgets importantes para su funcionamiento. Los más importantes son el shortcode para mostrar las propiedades y el shortcode o widget para mostrar el buscador de propiedades.
A continuación los detalles de cada uno.


### Shortcode de propiedades ###

El más importante es el shortcode [wasi-properties] el cual es el encargado de mostrar el listado completo de propiedades de wasi.co.  Este shortcode tiene varios atributos de personalización que permiten modificar su agregar filtros y modificar su layout.
Ejemplo:
`[wasi-properties layout="grid" featured="true" limit="9" btn-class="search-btn" tags-bg-color="#db2723"]`

Estos atributos son:

**- layout:** que puede ser "list" o "grid"  (el grid siempre es de 3 columnas) Por defecto es tipo “list”  
**- featured:** que puede ser "true" o "false". Sirve para mostrar solo las propiedades marcadas como destacadas. Por defecto es false.  
**- limit:** Es opcional, define cuantas propiedades va a mostrar. El valor por defecto aplicar es el que esté definido en las configuraciones iniciales del plugin (Inicialmente 10)  
**- btn-class:**  Define la clase CSS que se le va a aplicar al botón de más información. Por defecto es "btn btn-primary" (las cuales son las clases compatibles por defecto con Bootstrap) Si algún theme quiere cambiarlo como en el ejemplo, lo puede hacer.  
**- tags-bg-color:** Define el color de fondo de los tags encima de la imagen cuando se usa el layout tipo grid. Si no se pone nada, por defecto va el azul de wasi: #194C9A  


### Shortcode y Widget para el Buscador ###

El buscador de propiedades puede ser mostrado a través de un Widget o a través de un shortcode en alguna página aparte.
Tanto el Widget como el shortcode contienen los mismos atributos:

**- formClass:** Clase CSS que se aplica al formulario principal para controlar su layout. Por defecto: “row”  
**- submitClass:** Clase CSS que se aplica al botón de búsqueda. Por defecto: “btn btn-primary”  
La forma más recomendada de mostrar el buscador es a través de un widget en algún sidebar del theme.

### Widget para el formulario de Contacto ###

Cada propiedad tiene un formulario de contacto propio. El cual permite que los visitantes diligencien un formulario y sus datos sean asignados al Agente Inmobiliario a cargo de la propiedad, además de que le llegará un e-mail con la respectiva notificación.
Este widget cuenta con los mismos atributos del widget del buscador:

**- formClass:** Clase CSS que se aplica al formulario principal para controlar su layout. Por defecto: “row”  
**- submitClass:** Clase CSS que se aplica al botón de búsqueda. Por defecto: “btn btn-primary”  

## Traducción del plugin ##

Por defecto el plugin viene en idioma inglés, pero cuenta con la plantilla de cadenas y todo el soporte para agregar cualquier idioma, ya sea a través de algún plugin o con un programa de edición de cadenas.

El método más recomendado para traducir este plugin es utilizar un plugin de traducción. El más recomendado es Loco Translate (https://wordpress.org/plugins/loco-translate/).
Al instalar ese plugin, en la sección de plugins estará activo el plugin de Wasi para ser traducido. Allí se podrá ver todas las cadenas de texto y agregar las traducciones de cada uno de las que sean necesarias.


## Preguntas Frecuentes ##

### Puedo personalizar la interfaz gráfica del plugin  ###

Si claro, desde tu plantilla/theme de wordpress puedes sobrescribir todos los estilos CSS necesarios.

### Si hago cambios en mis propiedades de wasi, ¿porque no se ven en mi sitio? ###

El plugin tiene una memoria temporal que se refresca cada 1 día, si quieres refrescarla  manualmente, puedes hacerlo dentro de las configuraciones del plugin usando el botón de "Clear plugin Cache"

### Mi pregunta no se ha respondido aquí ###

Puedes contactarnos desde nuestra página de soporte: https://wasi.co/app-contactenos.htm
O puedes dejar tu soliticud directo en la página de desarrollo del plugin: https://github.com/wasico/wasi-connector

Ver más información en https://api.wasi.co/guide/index.html



## Changelog ##

### 1.0 ###
* Versión inicial del plugin

