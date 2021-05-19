<?php
# -------------------------- #
#  Name: chaz6chez           #
#  Email: admin@chaz6chez.cn #
#  Date: 2018/9/19           #
# -------------------------- #
namespace Kernel\Utils;

class Service extends Instance {

    /**
     * 载入配置内容
     */
    protected function _initConfig(){

    }

    /**
     * 响应
     * @param array $response
     * @return Response
     */
    protected function response(array $response = []) : Response{
        /** @var Response $res */
        $res = make(Response::class, $response);
        return $res;
    }
}