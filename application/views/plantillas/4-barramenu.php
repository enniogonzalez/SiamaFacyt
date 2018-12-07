
<div class="col-md-3" style="background-color: #95a5a6; padding: 10px;">
    <div id='menu-siama'>
        <ul id="menu">
            <?php
            $posMenu = -1;
            if($this->session->userdata("Permisos")['Localizacion']){
                $posMenu++;
            ?>
            <li class="oMenu">
                <a href='<?=site_url('localizaciones')?>'>
                    <span class="fa fa-map-marker" style="width:20px;"></span> 
                    <label>Localizaci&oacute;n</label>
                </a>
            </li>
            <?php
            }

            if($this->session->userdata("Permisos")['Mantenimiento']){
                $posMenu++;
            ?>
            <li class="oMenu">
                <a>
                    <span class="fa fa-sliders" style="width:20px;"></span> 
                    <label>Mantenimiento</label>
                    <span style="float:right;" class="fa fa-caret-down"></span>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>correctivo'>
                    <span class="fa fa-briefcase" style="width:20px;" ></span>
                    <label>Correctivo</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>fallas'>
                    <span class="fa fa-exclamation-triangle" style="width:20px;" ></span>
                    <label>Fallas</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>plantilla'>
                    <span class="fa fa-sticky-note" style="width:20px;" ></span>
                    <label>Plantilla</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>preventivo'>
                    <span class="fa fa-bullseye" style="width:20px;" ></span>
                    <label>Preventivo</label>
                </a>
            </li>
            <?php
            }

            if($this->session->userdata("Permisos")['Marcas']){
                $posMenu++;
            ?>
            <li class="oMenu">
                <a href='<?=site_url('marcas')?>'>
                    <span class="fa fa-meetup" style="width:20px;"></span> 
                    <label>Marcas</label>
                </a>
            </li>
            <?php
            }

            if($this->session->userdata("Permisos")['Obreros']){
                $posMenu++;
            ?>
            <li class="oMenu">
                <a href='<?=site_url('obreros')?>'>
                    <span class="fa fa-address-card" style="width:20px;"></span> 
                    <label>Obreros</label>
                </a>
            </li>
            <?php
            }

            if($this->session->userdata("Permisos")['Partidas']){
                $posMenu++;
            ?>
            <li class="oMenu">
                <a href='<?=site_url('partidas')?>'>
                    <span class="fa fa-bookmark" style="width:20px;"></span> 
                    <label>Partidas</label>
                </a>
            </li>
            <?php
            }

            if($this->session->userdata("Permisos")['Patrimonio']){
                $posMenu++;
            ?>
            
            <li class="oMenu">
                <a>
                    <span class="fa fa-product-hunt" style="width:20px;" ></span>
                    <label>Patrimonio</label>
                    <span style="float:right;" class="fa fa-caret-down"></span>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>ajustes'>
                    <span class="fa fa-wrench" style="width:20px;" ></span>
                    <label>Ajustes</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>bienes'>
                    <span class="fa fa-cube" style="width:20px;" ></span>
                    <label>Bienes</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>cambiosestatus'>
                    <span class="fa fa-certificate" style="width:20px;" ></span>
                    <label>Cambio de Estatus</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>compatibilidad'>
                    <span class="fa fa-object-group" style="width:20px;" ></span>
                    <label>Compatibilidad</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>herramientas'>
                    <span class="fa fa-compass " style="width:20px;" ></span>
                    <label>Herramientas</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>piezas'>
                    <span class="fa fa-cubes " style="width:20px;" ></span>
                    <label>Piezas</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>tipopieza'>
                    <span class="fa fa-bandcamp " style="width:20px;" ></span>
                    <label>Tipo de Pieza</label>
                </a>
            </li>
            <?php
            }

            if($this->session->userdata("Permisos")['Proveedores']){
                $posMenu++;
            ?>
            <li class="oMenu">
                <a href='<?=site_url('proveedores')?>'>
                    <span class="fa fa-user" style="width:20px;"></span> 
                    <label>Proveedores</label>
                </a>
            </li>
            <?php
            }

            if($this->session->userdata("Permisos")['Reportes']){
                $posMenu++;
            ?>
                <li class="oMenu">
                    <a>
                        <span class="fa fa-file-text" style="width:20px;"></span> 
                        <label>Reportes</label>
                        <span style="float:right;" class="fa fa-caret-down"></span>
                    </a>
                </li>
                <li class="submenu submenu<?=$posMenu?>">
                    <a href='<?=base_url()?>reportes/localizaciones'>
                        <span class="fa fa-map-marker" style="width:20px;" ></span>
                        <label>Localizaci&oacute;n</label>
                    </a>
                </li>
                <li class="submenu submenu<?=$posMenu?>">
                    <a href='<?=base_url()?>reportes/mantenimiento'>
                        <span class="fa fa-sliders" style="width:20px;" ></span>
                        <label>Mantenimiento</label>
                    </a>
                </li>
                <li class="submenu submenu<?=$posMenu?>">
                    <a href='<?=base_url()?>reportes/marcas'>
                        <span class="fa fa-meetup" style="width:20px;" ></span>
                        <label>Marcas</label>
                    </a>
                </li>
                <li class="submenu submenu<?=$posMenu?>">
                    <a href='<?=base_url()?>reportes/proveedores'>
                        <span class="fa fa-user" style="width:20px;" ></span>
                        <label>Proveedores</label>
                    </a>
                </li>
            <?php
            }



            if($this->session->userdata("Permisos")['Sistema']){
                $posMenu++;
            ?>
            <li class="oMenu">
                <a>
                    <span class="fa fa-cogs" style="width:20px;" ></span>
                    <label>Sistema</label>
                    <span style="float:right;" class="fa fa-caret-down"></span>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>listasdesplegables'>
                    <span class="fa fa-list " style="width:20px;" ></span>
                    <label>Listas Desplegables</label>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>usuarios'>
                    <span class="fa fa-users " style="width:20px;" ></span>
                    <label>Usuarios</label>
                </a>
            </li>
            <?php
                }
            ?>
        </ul>
    </div>
</div>