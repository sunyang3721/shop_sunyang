<?php include template('header','admin');?>
        <div class="fixed-nav layout">
            <ul>
                <li class="first">通知系统设置 - <?php echo $notify['name']?></li>
                <!--<li class="spacer-gray"></li>
                <li><a class="current" href="javascript:;">邮件设置</a></li>
                <li><a href="email_test.html">发送测试</a></li>-->
            </ul>
            <div class="hr-gray"></div>
        </div>
        <div class="content padding-big have-fixed-nav">
            <form method="post">
            <p class="padding-small-top padding-small-bottom">请设置您的通知模板，选择左侧通知类型并进行编辑</p>
            <div class="notify-model">
                <div class="left border border-sub">
                    <?php foreach($hooks as $k=>$v):?>
                        <a href="javascript:;" data-type="<?php echo $k?>">
                            <?php echo $v?>
                            <?php if(in_array($k, $template['enabled']) && is_array($template)):?>
                                <!--开启-->
                                <div class="switch checked"><span class="control"></span><input type="checkbox"  name="enabled[]" value="<?php echo $k?>"  checked="checked"/></div>
                            <?php else:?>
                                <!--关闭-->
                                <div class="switch"><span class="control"></span><input type="checkbox" name="enabled[]" value="<?php echo $k?>"/></div>

                            <?php endif;?>
                        </a>
                    <?php endforeach;?>
                </div>
                <div class="right">
                    <div class="wrap border border-sub bg-white clearfix">
                        <?php include(MODULE_PATH.'library/driver/'.$_GET['code'].'/template.inc.php');?>
                    </div>
                </div>
            </div>
            <div class="margin-big-top">
                <input type="submit" class="button bg-main" value="保存" />
                <input type="button" class="button margin-left bg-gray" value="返回" />
            </div>
            <input type="hidden" name="id" value="<?php echo $_GET['code']?>" />
            <input type="hidden" name="select_wrap" value="" />
            </form>
        </div>
        <script>
            $(function(){
                $(".notify-model").find('edui-container').css("z-index",1);
                $(".notify-model .left a").eq(0).trigger('click');
                $("#editor").css({minHeight:"436px"});
                $(".notify-model .left a").live('click',function(e){
                    e.preventDefault();
                    $(this).addClass("current").siblings().removeClass("current");
                    $("#content-label").html($(this).text());
                    $("div[id^='edit_']").hide();
                    $("#edit_"+$(this).attr('data-type')).show();
                    $("input[name=select_wrap]").val($(this).attr('data-type'));

                });
                $(".notify-model .left a").eq(0).trigger("click");
                $(".switch input").live('click',function(e){
                    e.stopPropagation();
                    if($(this).is(":checked")){
                        $(this).parent().addClass('checked');
                    }else{
                        $(this).parent().removeClass('checked');
                    }
                });
                $(".edui-btn-fullscreen").click(function(){
                    if($(this).hasClass("edui-active")){
                        $(".edui-container").css({zIndex:"999"})
                    }else{
                        $(".edui-container").css({zIndex:"8"})
                    }
                })
                $(window).scroll(function(){
                    $(".edui-container").css({zIndex:"8"});
                    var gp = $(".edui-container").offset().top-37;
                    var sp = $(document).scrollTop();
                    if(sp>gp){
                        $(".edui-toolbar").css({top:"37px",width:$(".edui-container").width(),position:"fixed"});
                    }
                })


            });

        </script>
    <?php include template('footer','admin');?>
