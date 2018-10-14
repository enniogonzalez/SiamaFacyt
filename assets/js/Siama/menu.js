$(function(){
    
    $('#menu-siama ul li').on('click',function(){
        if(!$(this).hasClass('submenu')){
                
            if($(".oMenu").index($('li.menu-activo')) == $(".oMenu").index($(this))){

                indice = $(".oMenu").index($('li.menu-activo'));
                $('.submenu'+indice).hide();

                $('li.menu-activo').find('span').each(function(){
                    if($(this).hasClass('fa-caret-up')){
                        $(this).removeClass('fa-caret-up');
                        $(this).addClass('fa-caret-down');
                    }
                });

                $('li.menu-activo').removeClass('menu-activo');

            }else{
                
                indice = $(".oMenu").index($('li.menu-activo'));
                $('.submenu'+indice).hide();
        
                $('li.menu-activo').find('span').each(function(){
                    if($(this).hasClass('fa-caret-up')){
                        $(this).removeClass('fa-caret-up');
                        $(this).addClass('fa-caret-down');
                    }
                });
                $('li.menu-activo').removeClass('menu-activo');
        
                
                $(this).find('span').each(function(){
                    if($(this).hasClass('fa-caret-down')){
                        $(this).removeClass('fa-caret-down');
                        $(this).addClass('fa-caret-up');
                    }
                });
                
        
                $(this).addClass('menu-activo');
                indice = $(".oMenu").index(this);
                $('.submenu'+indice).show();
            }
        }

    });


});