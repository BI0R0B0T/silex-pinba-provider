<?php
/**
 * @author Mikhail Dolgov <dolgov@bk.ru>
 * @date   04.03.2016 17:54
 */

/**
 * @return \Psr\Log\LoggerInterface
 * @throws Exception
 */
function get_pinba_logger()
{
    /**
     * @var $app \Silex\Application
     */
    global $app;
    static $logger = null;
    if(is_null($logger)) {
        $logger = $app['pinba_logger'];
    }
    return $logger;
}

function pinba_script_name_set ($name){
    get_pinba_logger()->debug(__FUNCTION__, func_get_args());
}
function pinba_timer_start ($tags){
    get_pinba_logger()->debug(__FUNCTION__, func_get_args());
    return rand(100,999);
}
function pinba_timer_stop ($tags){
    get_pinba_logger()->debug(__FUNCTION__, func_get_args());
}
function pinba_timer_add ($tags, $time){
    get_pinba_logger()->debug(__FUNCTION__, func_get_args());
}
function pinba_get_info (){
    get_pinba_logger()->debug(__FUNCTION__);
    return array(
        'hostname'    => 'emulate hostname',
        "server_name" => 'emulate server name',
    );
 }