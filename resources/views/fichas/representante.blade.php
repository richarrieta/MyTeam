{{Form::hidden('id',$solicitud->persona_solicitante_id)}}
<div class="row">
    <div class="col-xs-12 col-sm-12 col-md-8">
        <h4><span id='span-solicitante-documento'>{{$solicitante->documento}}</span></h4>
    </div>
</div>
<div class="row">
    {{Form::btInput($solicitante,'nombre',4)}}
    {{Form::btInput($solicitante,'apellido',4)}}
    {{Form::btInput($beneficiario,'sexo',4,"select",[], BaseModel::$cmbsexo)}}     
</div>
<div class="row">
    {{Form::btInput($solicitante,'lugar_nacimiento',8)}}
    {{Form::btInput($solicitante,'fecha_nacimiento',4)}}
</div>
<div class="row">
    {{Form::btInput($solicitante,'edad',4,'text',['disabled'=>'disabled'])}}
    {{Form::btInput($solicitante,'estado_civil_id',4)}}
    {{Form::btSelect('parentesco_id', Parentesco::getCombo("Parentesco"), @$parentesco->id, 4)}}
</div>
<div class="row">
    {{Form::btInput($solicitante,'nivel_academico_id',8)}}
    {{Form::btInput($solicitante,'ind_trabaja',4)}}
</div>
<div id='div-trabaja'>
    <hr>
    <h4>Datos Empleo</h4> 
    <div class="row">
        {{Form::btInput($solicitante,'ocupacion',6)}}
        {{Form::btInput($solicitante,'ingreso_mensual',4)}}

        <div class="col-xs-12 col-sm-12 col-md-2">
            <div class="form-group">
                <a href="http://www.ivss.gov.ve/" class="btn btn-primary" target="_black"><i class="glyphicon glyphicon-search"></i> 
                    IVSS
                </a>                
            </div>
        </div>         
    </div> 
</div>
@include('solicitudes/direccion-solicitante')
<div class="row">
    {{Form::btInput($solicitante,'email',6)}}
    {{Form::btInput($solicitante,'twitter',6)}}
</div>  
<div class="row">
    {{Form::btInput($solicitante,'observaciones')}}
</div>  
@include('templates.bootstrap.submit',['nomostrar'=>true])