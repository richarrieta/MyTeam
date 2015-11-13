<?php

namespace App\Http\Controllers;

class FichasController extends Controller {

    private $reporte;

    public function __construct(\ayudantes\Reporte $rep) {
        $this->reporte = $rep;
        parent::__construct();
    }

    public function getVer($id) {
        $data['ficha'] = \App\Ficha::findOrFail($id);
        $data['jugador'] = $data['ficha']->jugador;
        $data['representante'] = $data['ficha']->representante;
        $data['club'] = $data['ficha']->club;
        $data['posicion'] = $data['ficha']->posicion;
        return View::make('fichas.planilla', $data);
    }

    public function getIndex() {
        $data['fichas'] = Ficha::eagerLoad()
                ->aplicarFiltro(Input::except(['asignar', 'solo_asignadas', 'page', 'cerrar', 'anulando', '']))
                ->ordenar();
        if (Input::has('asignar')) {
            $data['campo'] = Input::get('asignar');
            $data['solicitud'] = new Solicitud();
            if ($data['campo'] == 'usuario') {
                $usuario = Usuario::getLogged();
                $data['solicitudes']->whereDepartamentoId($usuario->departamento_id);
                $data['analistas'] = $usuario->getCompaneros();
            }
        } else if (Input::has('anulando')) {
            $data['anulando'] = true;
        } else if (Input::has('cerrar')) {
            $data['cerrar'] = true;
        } else if (Input::has('solo_asignadas')) {
            $data['solo_asignadas'] = true;
        }
        $data['solicitudes'] = $data['solicitudes']->paginate(5);
        //se usa para el helper de busqueda
        $data['persona'] = new Persona();
        $data['solicitud'] = new Solicitud();
        $data['presupuesto'] = new Presupuesto();
        $data['requerimiento'] = new Requerimiento();
        return View::make('solicitudes.index', $data);
    }

    public function postModificar() {
        Session::forget('ficha');
        $ficha = Ficha::findOrNew(Input::get('id'));
        $ficha->fill(Input::all());
        if (Input::has('informe')) {
            $ficha->reglasInforme();
        }
        if ($ficha->save()) {
            $data['solicitud'] = $ficha;
            $data['mensaje'] = "Datos guardados correctamente";
            if (Request::ajax()) {
                return Response::json($data);
            }
            return Redirect::to('solicitudes/modificar/' . $ficha->id);
        } else {
            if (Request::ajax()) {
                return Response::json(['errores' => $ficha->getErrors()], 400);
            }
            return Redirect::back()->withInput()->withErrors($ficha->getErrors());
        }
    }

    public function getModificar($id = null) {
        if (is_null($id) && !Session::has('solicitud')) {
            $data['nuevo'] = true;
        } else {
            $data['nuevo'] = false;
        }
        if (Session::has('solicitud') && is_null($id)) {
            $data['solicitud'] = new Solicitud(Session::get('solicitud'));
        } else {
            $data['solicitud'] = Solicitud::findOrFail($id);
        }
        if (!$data['solicitud']->puedeModificar()) {
            return Redirect::to('solicitudes')->with('error', 'Solo se pueden editar solicitudes en Elaboración');
        }
        $data['beneficiario'] = Persona::findOrFail($data['solicitud']->persona_beneficiario_id);
        $data['solicitante'] = Persona::findOrNew($data['solicitud']->persona_solicitante_id);
        $data['familiares'] = $data['beneficiario']->getFamiliares();
        $data['familiar'] = new Persona();
        $data['recaudo'] = new RecaudoSolicitud();
        $data['recaudos'] = $data['solicitud']->recaudosSolicitud;
        $data['presupuesto'] = new Presupuesto();
        $data['presupuestos'] = $data['solicitud']->presupuestos;
        $data['bitacora'] = new Bitacora();
        $data['bitacoras'] = $data['solicitud']->bitacoras;
        $data['parentesco'] = $data['beneficiario']->getParentesco($data['solicitante']->id);
        $data['foto'] = new FotoSolicitud();
        $data['fotos'] = $data['solicitud']->fotos;
        $data['beneficiario_kerux'] = new Oracle\Beneficiario();
        if (isset($data['solicitud']->area_id)) {
            $requerimientos = Requerimiento::select('id', 'nombre')->whereTipoAyudaId($data['solicitud']->area->tipo_ayuda_id)->get();
            foreach ($requerimientos as $requerimiento) {
                $requerimientof[$requerimiento->id] = $requerimiento->nombre;
                $data['requerimientos'] = $requerimientof;
            }
        }
        if (Request::ajax()) {
            return Response::json($data);
        }
        return View::make("solicitudes.plantilla", $data);
    }

    public function getNuevo() {
        Session::forget('ficha');
        $data['nuevo'] = true;
        $data['ficha'] = new \App\Ficha();
        $data['jugador'] = new Persona();
        $data['representante'] = new Persona();
        return View::make("fichas.plantilla", $data);
    }

    public function postNuevo() {
        $ficha = \App\Ficha::crear(Input::all());
        if (!$ficha->hasErrors()) {
            Session::set('ficha', $ficha->toArray());
            return Redirect::to('fichas/modificar');
        } else {
            return Redirect::back()->withInput()->withErrors($ficha->getErrors());
        }
    }

    public function postAsignar() {
        $resultado = Solicitud::asignar(Input::all());
        if ($resultado->hasErrors()) {
            return Response::json(['errores' => $resultado->getErrors()], 400);
        }
        return Response::json(['mensaje' => 'Se asignaron las solicitudes correctamente']);
    }

    /* -------------------------------------------------------------------- */

    public function getPlanilla($id, $store = false) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        $data['beneficiario'] = $data['solicitud']->getBeneficiario();
        $data['solicitante'] = $data['solicitud']->getSolicitante();
        return $this->reporte->generar('solicitudes.imprimir', $data);
    }

    public function getVermemo($id) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        $data['personaBeneficiario'] = $data['solicitud']->personaBeneficiario;
        return View::make('memorandun.memorandun', $data);
    }

    public function getMemo($id, $store = false) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        $data['personaBeneficiario'] = $data['solicitud']->personaBeneficiario;
        return $this->reporte->generar('memorandum.imprimir', $data);
    }

    public function getAceptarasignacion($id) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        return View::make('solicitudes.aceptarasignacion', $data);
    }

    public function postAceptarasignacion() {
        $ficha = Solicitud::findOrFail(Input::get('id'));
        if ($ficha->aceptarAsignacion()) {
            return Redirect::to('solicitudes/modificar/' . $ficha->id)->with('mensaje', 'Se aceptó la asignación de la solicitud: ' . $ficha->id . ', correctamente');
        }
        return Redirect::to('solicitudes?solo_asignadas=true')->with('error', $ficha->getErrors()->first());
    }

    public function getDevolverasignacion($id) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        return View::make('solicitudes.devolverasignacion', $data);
    }

    public function postDevolverasignacion() {
        $ficha = Solicitud::findOrFail(Input::get('id'));
        if ($ficha->devolverAsignacion()) {
            return Redirect::to('solicitudes?solo_asignadas=true')->with('mensaje', 'Se devolvió la asignación de la solicitud: ' . $ficha->id . ', correctamente');
        }
        return Redirect::to('solicitudes?solo_asignadas=true')->with('error', $ficha->getErrors()->first());
    }

    public function getSolicitaraprobacion($id) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        $data['solicitud']->configurarPresupuesto("", false);
        $data['informe'] = $data['solicitud']->informe_social;
        $data['manual'] = Configuracion::get('ind_secuencia_automatica') == "No";
        $data['recaudos'] = RecaudoSolicitud::whereSolicitudId($id)
                ->where('ind_recibido', '=', true)
                ->where('ind_obligatorio', '=', true)
                ->leftJoin('recaudos', 'recaudo_solicitud.recaudo_id', '=', 'recaudos.id')
                ->get();
        return View::make('solicitudes.solicitaraprobacion', $data);
    }

    public function postSolicitaraprobacion() {
        $ficha = Solicitud::findOrFail(Input::get('id'));
        $num_proc = Input::get('num_proc');
        $proc_documento = new ayudantes\ProcesarDocumento();
        $data = $proc_documento->buscarDefEvento($ficha);
//        $id_usuario = Sentry::getUser()->id;
        if (!$ficha->validarAprobacion(Input::get('usuario_autorizacion_id'))) {
            if (!empty($data['eventos'])) {
                $mensaje = $proc_documento->insertarDocumentos($data);
                if (!empty($mensaje)) {
                    $this->cancelarTransaccion();
                    return Response::json($mensaje, 400);
                } else {
                    $ficha->configurarPresupuesto($num_proc);
                    $proc_documento->atualizarEstatus($data, Input::get('usuario_autorizacion_id'));
                }
            } else {
                return Response::json(['errores' => 'No se puede aprobar la solicitud, defina al menos un tipo de documento'], 400);
            }
        } else {
            return Response::json(['errores' => $ficha->getErrors()], 400);
        }
        Bitacora::registrar('Se solicitó la aprobación de la solicitud correctamente', $ficha->id);
        return Response::json(['mensaje' => 'Se solicito la aprobacion de la solicitud: ' . $ficha->id . ', correctamente', 'url' => Redirect::back()->getTargetUrl()], 200);
    }

    public function cancelarTransaccion() {
        \DB::rollBack();
        \DB::connection('oracle')->rollBack();
    }

    public function getAnular($id) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        $data['bitacora'] = new Bitacora();
        return View::make('solicitudes.anular', $data);
    }

    public function postAnular() {
        $ficha = Solicitud::findOrFail(Input::get('id'));
        if ($ficha->anular(Input::get('nota'))) {
            return Redirect::to('solicitudes?estatus[]=ELA&estatus[]=ART&estatus[]=ELD&estatus[]=ACA&estatus[]=DEV&estatus[]=EAA&anulando=true')
                            ->with('mensaje', 'Se anuló la solicitud: ' . $ficha->id . ', correctamente');
        }
        return Redirect::to('solicitudes?estatus[]=ELA&estatus[]=ART&estatus[]=ELD&estatus[]=ACA&estatus[]=DEV&estatus[]=EAA&anulando=true')->with('error', $ficha->getErrors()->first());
    }

    public function getCerrar($id) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        $data['bitacora'] = new Bitacora();
        return View::make('solicitudes.cerrar', $data);
    }

    public function postCerrar() {
        $ficha = Solicitud::findOrFail(Input::get('id'));
        if ($ficha->cerrar()) {
            return Redirect::to('solicitudes?estatus[]=APR&cerrar=true')->with('mensaje', 'Se cerro la solicitud: ' . $ficha->id . ', correctamente');
        }
        return Redirect::to('solicitudes?estatus[]=APR&cerrar=true')->with('error', $ficha->getErrors()->first());
    }

    public function getBitacora($id, $store = false) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        return $this->reporte->generar('solicitudes.imprimirbitacora', $data);
    }

    public function getInforme($id, $store = false) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        return $this->reporte->generar('solicitudes.imprimirinforme', $data);
    }

    public function getHistorial($id, $store = false) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        return View::make('solicitudes.historial', $data);
    }

    public function getRequerimientos($id, $store = false) {
        $data['solicitud'] = Solicitud::findOrFail($id);
        return View::make('solicitudes.verrequerimientos', $data);
    }

    /* -------------------------------------- */
}
