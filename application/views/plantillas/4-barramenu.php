
<div class="col-md-3" style="background-color: #95a5a6; padding: 10px;">
    <div id='menu-sigma'>
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
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-briefcase" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Correctivo</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>correctivoplanificado'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-calendar" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Correctivo Planificado</label>
                        
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>fallas'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-exclamation-triangle" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Fallas</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>plantilla'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-sticky-note" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Plantilla</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>preventivo'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-bullseye" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Preventivo</label>
                        </div>
                    </div>
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
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-wrench" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Ajustes</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>bienes'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-cube" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Bienes</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>cambiosestatus'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-certificate" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Cambio de Estatus</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>compatibilidad'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-object-group" style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Compatibilidad</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>herramientas'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-compass " style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Herramientas</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>piezas'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-cubes " style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Piezas</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>tipopieza'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-bandcamp " style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Tipo de Pieza</label>
                        </div>
                    </div>
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
                    <a href='<?=base_url()?>reportes/obreros'>
                    <span class="fa fa-address-card" style="width:20px;"></span> 
                    <label>Obreros</label>
                    </a>
                </li>
                <li class="submenu submenu<?=$posMenu?>">
                    <a href='<?=base_url()?>reportes/partidas'>
                    <span class="fa fa-bookmark" style="width:20px;"></span> 
                    <label>Partidas</label>
                    </a>
                </li>
                <li class="submenu submenu<?=$posMenu?>">
                    <a href='<?=base_url()?>reportes/patrimonio'>
                    <span class="fa fa-product-hunt" style="width:20px;" ></span>
                    <label>Patrimonio</label>
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
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-list " style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Listas Desplegables</label>
                        </div>
                    </div>
                </a>
            </li>
            <li class="submenu submenu<?=$posMenu?>">
                <a href='<?=base_url()?>usuarios'>
                    <div class="divContenedorSubmenu">
                        <div class="divSpanSubmenu">
                            <span class="fa fa-users " style="width:20px;" ></span>
                        </div>
                        <div class="divNombreSubmenu">
                            <label>Usuarios</label>
                        </div>
                    </div>
                </a>
            </li>
            <?php
                }
            ?>
        </ul>
    </div>
</div>