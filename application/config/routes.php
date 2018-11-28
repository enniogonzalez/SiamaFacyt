<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/


$route['ajustes/aprobar'] = 'Patrimonio/ajustes/aprobar';
$route['ajustes/obtener'] = 'Patrimonio/ajustes/obtener';
$route['ajustes/busqueda'] = 'Patrimonio/ajustes/busqueda';
$route['ajustes/eliminar'] = 'Patrimonio/ajustes/eliminar';
$route['ajustes/guardar'] = 'Patrimonio/ajustes/guardar';
$route['ajustes/imprimir/(:any)'] = 'Patrimonio/ajustes/imprimir/$1';
$route['ajustes'] = 'Patrimonio/ajustes/view/';

$route['alertas/CantidadAlertas'] = 'alertas/CantidadAlertas';
$route['alertas'] = 'alertas/view';

$route['bienes/obtener'] = 'Patrimonio/bienes/obtener';
$route['bienes/busqueda'] = 'Patrimonio/bienes/busqueda';
$route['bienes/eliminar'] = 'Patrimonio/bienes/eliminar';
$route['bienes/guardar'] = 'Patrimonio/bienes/guardar';
$route['bienes/imprimir/(:any)'] = 'Patrimonio/bienes/imprimir/$1';
$route['bienes'] = 'Patrimonio/bienes/view/';

$route['cambiosestatus/aprobar'] = 'Patrimonio/cambiosestatus/aprobar';
$route['cambiosestatus/obtener'] = 'Patrimonio/cambiosestatus/obtener';
$route['cambiosestatus/busqueda'] = 'Patrimonio/cambiosestatus/busqueda';
$route['cambiosestatus/eliminar'] = 'Patrimonio/cambiosestatus/eliminar';
$route['cambiosestatus/guardar'] = 'Patrimonio/cambiosestatus/guardar';
$route['cambiosestatus/imprimir/(:any)'] = 'Patrimonio/cambiosestatus/imprimir/$1';
$route['cambiosestatus'] = 'Patrimonio/cambiosestatus/view/';

$route['configurar/guardar'] = 'Sistema/configurarusuario/guardar';
$route['configurar'] = 'Sistema/configurarusuario/view';

$route['correctivo/aprobar'] = 'Mantenimiento/correctivo/aprobar';
$route['correctivo/reversar'] = 'Mantenimiento/correctivo/reversar';
$route['correctivo/obtener'] = 'Mantenimiento/correctivo/obtener';
$route['correctivo/busqueda'] = 'Mantenimiento/correctivo/busqueda';
$route['correctivo/eliminar'] = 'Mantenimiento/correctivo/eliminar';
$route['correctivo/guardar'] = 'Mantenimiento/correctivo/guardar';
$route['correctivo/imprimir/(:any)'] = 'Mantenimiento/correctivo/imprimir/$1';
$route['correctivo'] = 'Mantenimiento/correctivo/view/';

$route['fallas/busqueda'] = 'Mantenimiento/fallas/busqueda';
$route['fallas/eliminar'] = 'Mantenimiento/fallas/eliminar';
$route['fallas/guardar'] = 'Mantenimiento/fallas/guardar';
$route['fallas/imprimir/(:any)'] = 'Mantenimiento/fallas/imprimir/$1';
$route['fallas'] = 'Mantenimiento/fallas/view';

$route['listasdesplegables/ObtenerLista'] = 'Sistema/listasdesplegables/ObtenerLista';
$route['listasdesplegables/busqueda'] = 'Sistema/listasdesplegables/busqueda';
$route['listasdesplegables/eliminar'] = 'Sistema/listasdesplegables/eliminar';
$route['listasdesplegables/guardar'] = 'Sistema/listasdesplegables/guardar';
$route['listasdesplegables/imprimir/(:any)'] = 'Sistema/listasdesplegables/imprimir/$1';
$route['listasdesplegables'] = 'Sistema/listasdesplegables/view/';

$route['localizaciones/busqueda'] = 'localizaciones/busqueda';
$route['localizaciones/eliminar'] = 'localizaciones/eliminar';
$route['localizaciones/guardar'] = 'localizaciones/guardar';
$route['localizaciones/imprimir/(:any)'] = 'localizaciones/imprimir/$1';
$route['localizaciones'] = 'localizaciones/view';

$route['marcas/busqueda'] = 'marcas/busqueda';
$route['marcas/eliminar'] = 'marcas/eliminar';
$route['marcas/guardar'] = 'marcas/guardar';
$route['marcas/imprimir/(:any)'] = 'marcas/imprimir/$1';
$route['marcas'] = 'marcas/view';

$route['reportes/mantenimiento/RepManLoc'] = 'Reportes/mantenimientorep/RepManLoc';
$route['reportes/mantenimiento/RepManPro'] = 'Reportes/mantenimientorep/RepManPro';
$route['reportes/mantenimiento/RepManBie'] = 'Reportes/mantenimientorep/RepManBie';
$route['reportes/mantenimiento/RepManUsu'] = 'Reportes/mantenimientorep/RepManUsu';
$route['reportes/mantenimiento'] = 'Reportes/mantenimientorep/view';

$route['partidas/busqueda'] = 'partidas/busqueda';
$route['partidas/eliminar'] = 'partidas/eliminar';
$route['partidas/guardar'] = 'partidas/guardar';
$route['partidas/imprimir/(:any)'] = 'partidas/imprimir/$1';
$route['partidas'] = 'partidas/view';

$route['piezas/obtener'] = 'Patrimonio/piezas/obtener';
$route['piezas/busqueda'] = 'Patrimonio/piezas/busqueda';
$route['piezas/busquedaDisponibles'] = 'Patrimonio/piezas/busquedaDisponibles';
$route['piezas/eliminar'] = 'Patrimonio/piezas/eliminar';
$route['piezas/guardar'] = 'Patrimonio/piezas/guardar';
$route['piezas/imprimir/(:any)'] = 'Patrimonio/piezas/imprimir/$1';
$route['piezas'] = 'Patrimonio/piezas/view/';

$route['plantilla/aprobar'] = 'Mantenimiento/plantilla/aprobar';
$route['plantilla/reversar'] = 'Mantenimiento/plantilla/reversar';
$route['plantilla/obtener'] = 'Mantenimiento/plantilla/obtener';
$route['plantilla/obtenerMantenimiento'] = 'Mantenimiento/plantilla/obtenerMantenimiento';
$route['plantilla/busqueda'] = 'Mantenimiento/plantilla/busqueda';
$route['plantilla/eliminar'] = 'Mantenimiento/plantilla/eliminar';
$route['plantilla/guardar'] = 'Mantenimiento/plantilla/guardar';
$route['plantilla/imprimir/(:any)'] = 'Mantenimiento/plantilla/imprimir/$1';
$route['plantilla'] = 'Mantenimiento/plantilla/view/';

$route['preventivo/aprobar'] = 'Mantenimiento/preventivo/aprobar';
$route['preventivo/reversar'] = 'Mantenimiento/preventivo/reversar';
$route['preventivo/obtener'] = 'Mantenimiento/preventivo/obtener';
$route['preventivo/busqueda'] = 'Mantenimiento/preventivo/busqueda';
$route['preventivo/eliminar'] = 'Mantenimiento/preventivo/eliminar';
$route['preventivo/guardar'] = 'Mantenimiento/preventivo/guardar';
$route['preventivo/imprimir/(:any)'] = 'Mantenimiento/preventivo/imprimir/$1';
$route['preventivo'] = 'Mantenimiento/preventivo/view/';

$route['proveedores/busqueda'] = 'proveedores/busqueda';
$route['proveedores/eliminar'] = 'proveedores/eliminar';
$route['proveedores/guardar'] = 'proveedores/guardar';
$route['proveedores/imprimir/(:any)'] = 'proveedores/imprimir/$1';
$route['proveedores'] = 'proveedores/view';

$route['restablecer/reset'] = 'resetpassword/reset/';
$route['restablecer/guardar'] = 'resetpassword/guardar/';
$route['restablecer/(:any)'] = 'resetpassword/view/$1';

$route['tipopieza/busqueda'] = 'Patrimonio/tipopieza/busqueda';
$route['tipopieza/eliminar'] = 'Patrimonio/tipopieza/eliminar';
$route['tipopieza/guardar'] = 'Patrimonio/tipopieza/guardar';
$route['tipopieza/imprimir/(:any)'] = 'Patrimonio/tipopieza/imprimir/$1';
$route['tipopieza'] = 'Patrimonio/tipopieza/view';

$route['usuarios/busqueda'] = 'Sistema/usuarios/busqueda';
$route['usuarios/eliminar'] = 'Sistema/usuarios/eliminar';
$route['usuarios/guardar'] = 'Sistema/usuarios/guardar';
$route['usuarios/obtener'] = 'Sistema/usuarios/obtener';
$route['usuarios/imprimir/(:any)'] = 'Sistema/usuarios/imprimir/$1';
$route['usuarios'] = 'Sistema/usuarios/view/';


$route['home'] = 'home/view';
$route['login/validar'] = 'login/validar';
$route['default_controller'] = 'login/view';
$route['(:any)'] = 'login/view/$1';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
