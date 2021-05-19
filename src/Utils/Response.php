<?php
declare(strict_types=1);

namespace Kernel\Utils;

class Response {
    private $status;
    private $code;
    private $message;
    private $data;
    private $ext;

    /**
     * Response constructor.
     * @param array $data
     */
    public function __construct(array $data = []) {
        if($data){
            $this->_init($data);
        }
    }

    /**
     * @param array $data
     */
    public function __invoke(array $data)
    {
        $this->_init($data);
    }

    /**
     * @param array $data
     */
    protected function _init(array $data){
        $this->ext     = isset($data['ext']) ? $data['ext'] : null;
        $this->data    = isset($data['data']) ? $data['data'] : null;
        $this->message = isset($data['message']) ? $data['message'] : 'success';
        $this->status  = isset($data['status']) ? $data['status'] : 1;
        $this->code    = isset($data['code']) ? $data['code'] : '0';
    }

    /**
     * @param string $msg
     * @param string|null $code
     * @param null $data
     * @param null $ext
     * @return $this
     */
    public function error(string $msg, ?string $code = '500', $data = null, $ext = null) : Response
    {
        $this->status = 0;
        $this->code = $code;
        $this->message = $msg;
        $this->data = $data ?? $this->data;
        $this->ext = $ext ?? $this->ext;
        $err = explode('|', $msg);
        if (is_array($err) && count($err) > 1) {
            $this->code = $err[0];
            $this->message = $err[1];
        }
        return $this;
    }

    /**
     * @param null $data
     * @param string|null $code
     * @param null $ext
     * @param string $msg
     * @return Response
     */
    public function success($data = null, ?string $code = '0', $ext = null, string $msg = 'success') : Response{
        $this->status = 1;
        $this->data = $data;
        $this->message = $msg;
        $this->ext = $ext ?? $this->ext;
        if($code){
            $this->code = $code;
            $code = explode('|', $code);
            if (is_array($code) && count($code) > 1) {
                $this->code = $code[0];
                $this->message = $code[1];
            }
        }

        return $this;
    }

    /**
     * @return Response
     */
    public function throwError() : Response {
        return $this;
    }

    /**
     * 反射获取对象属性
     * @param bool $object
     * @return \ReflectionProperty[]
     */
    public function getFields($object = false) : array{
        try{
            $class = new \ReflectionClass($this);
            $private = $class->getProperties(\ReflectionProperty::IS_PRIVATE);
            if($object){
                return $private;
            }
            $res = [];
            foreach ($private as $item){
                $name = $item->getName();
                $res[$name] = $this->$name;
            }
            return $res;
        }catch (\Exception $exception){
            return [];
        }
    }

    /**
     * 有错误信息
     * @return $this|bool
     */
    public function hasError() : bool {
        return boolval($this->status !== 1);
    }

    /**
     * @return int
     */
    public function getStatus() : int{
        return $this->status;
    }

    /**
     * @return string
     */
    public function getMessage() : string {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getCode() : string{
        return $this->code;
    }

    /**
     * @param null $key
     * @return null
     */
    public function getData($key = null){
        if($key){
            return isset($this->data[$key]) ? $this->data[$key] : null;
        }
        return $this->data;
    }

    /**
     * @return null
     */
    public function getExt(){
        return $this->ext;
    }
}