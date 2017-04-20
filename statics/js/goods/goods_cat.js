/**
 *显示分类 @lcl 2014-11-11 11:47:09
 */

//显示分类
function nb_category(pid, e){
    $(e).parent().nextAll('.child').empty();
    if($(e).hasClass('focus')){
        if($(e).parent().next('.child').children('a').length<=0){
            $(this).removeClass('focus');
        }else{
            var flog = true;
            $(e).parent().nextAll('div a').each(function(){
            if($(this).hasClass('focus')){
                flog = false;
            }
            });
            if(flog){
                _this.removeClass('focus');
            }
        }
    }else{
        $(e).addClass('focus').siblings().removeClass('focus');
    }   
    var strHTML = "";
    $.each(jsoncategory, function(InfoIndex, Info){
    if (pid == Info.parent_id)
        strHTML += " <a href = 'javascript:void(0)' onclick = 'nb_category(" + Info.id + ",this)' id = 'cat" + Info.id + "' data-id = " + Info.id + " > " + Info.name + " </a>";
    });
    if (pid == 0){
    	$(".root").html(strHTML);
    } else{
    	$(e).parent().next('div').css('background', '#FFF');
        $(e).parent().next('div').html(strHTML);
    }
}

//显示分类
function nb_p_category(pid, e){
    if($(e).hasClass('disable')){
        return false;
    }
    $(e).parent().nextAll('.child').empty();
    if($(e).hasClass('focus')){
        if($(e).parent().next('.child').children('a').length<=0){
            $(this).removeClass('focus');
        }else{
            var flog = true;
            $(e).parent().nextAll('div a').each(function(){
            if($(this).hasClass('focus')){
                flog = false;
            }
            });
            if(flog){
                _this.removeClass('focus');
            }
        }
    }else{
        $(e).addClass('focus').siblings().removeClass('focus');
    }   
   var strHTML = "";
    $.each(jsoncategory, function(InfoIndex, Info){
    if (pid == Info.parent_id)
        strHTML += " <a href = 'javascript:void(0)' onclick = 'nb_p_category(" + Info.id + ",this)' id = 'cat" + Info.id + "' data-id = " + Info.id + " > " + Info.name + " </a>";
    });
    if (pid == -1){
        $(".root").html(strHTML);
    } else{
        $(e).parent().next('div').css('background', '#FFF');
        $(e).parent().next('div').html(strHTML);
    }
}