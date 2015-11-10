{{Form::open(['url'=>'jugadores/nuevo', 'id'=>'nuevojugador'])}} 
<div id='div-jugador'>
    <hr>
    <h4>Datos del Jugador</h4>
    {{Form::hidden('jugador_id', Input::old('jugador_id'), ['id'=>'jugador_id'])}}
    <div class="row">
        {{Form::btInput($jugador,'tipo_nacionalidad_id',5)}}
        {{Form::btInput($jugador,'documento_identidad',5)}}    
    </div>
    <div class="row">
        {{Form::btInput($jugador,'nombre',5)}}
        {{Form::btInput($jugador,'apellido',5)}}          
    </div>
    <div class="row">
        <div class="col-lg-12">
            <button type="button" class="btn btn-primary salvar-persona" style="display: none;"><i class="glyphicon glyphicon-floppy-disk"></i> Guardar</button>
        </div>
    </div> 
</div>
<hr>
<h4>Datos del Representante</h4>
<div id='div-representante'>
    {{Form::hidden('representante_id', Input::old('representante_id'), ['id'=>'representante_id'])}}
    <div class="row">
        <div id="div-menor">
            {{Form::btInput($representante,'tipo_nacionalidad_id',5)}}
            {{Form::btInput($representante,'documento_identidad',5)}}    
        </div>
    </div>
    <div class="row">
        {{Form::btInput($representante,'nombre',5)}}
        {{Form::btInput($representante,'apellido',5)}}           
    </div>
    <div class="row">
        <div class="col-lg-12">
            <button type="button" class="btn btn-primary salvar-persona" style="display: none;"><i class="glyphicon glyphicon-floppy-disk"></i> Guardar</button>
        </div>
    </div>
    <div id="lista-relacionados"></div>     
</div>
<hr>
@include('templates.bootstrap.submit',['nombreSubmit'=>'Siguiente','nomostrar'=>true,'icon'=>'forward', 'clase'=>'siguiente'])
@section('javascript')
{{HTML::script('js/personas/nuevojugador.js')}}
@stop
{{Form::close()}}