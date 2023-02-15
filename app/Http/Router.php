<?php

namespace App\Http;

use \Closure;
use \Exception;
use \ReflectionFunction;
use App\Http\Middleware\Queue;


class Router
{

    private $url = ''; // ur raiz
    private $prefix = ''; // prefixo
    private $routes = []; // todas as rotas
    private $request;

    public function __construct($url)
    {
        $this->request = new Request($this);
        $this->url = $url;
        $this->setPrefix();
    }

    /**
     * Altera o content type para altera retorno entre site e API
     *
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Retorna a mesagem conforme o content type
     *
     * @param string $message
     * @return mixed
     */
    public function getErrorMessage($message)
    {
        switch ($this->contentType) {
            case 'application/json':
                return ['error' => $message];
                break;
            default:
                return $message;
                break;
        }
    }

    /**
     * padrão
     *
     * @var string
     */
    private $contentType = 'text/html';

    public function setPrefix()
    {
        //INFORMAÇÕES DA URL ATUAL
        $parseUrl = parse_url($this->url);

        $this->prefix = $parseUrl['path'] ?? '';
    }


    public function get($router, $params = [])
    {
        return $this->addRoute('GET', $router, $params);
    }

    public function post($router, $params = [])
    {
        return $this->addRoute('POST', $router, $params);
    }

    public function put($router, $params = [])
    {
        return $this->addRoute('PUT', $router, $params);
    }
    public function delete($router, $params = [])
    {
        return $this->addRoute('DELETE', $router, $params);
    }

    public function addRoute($method, $route, $params = [])
    {
        //VALIDAÇÃO DOS PARAMENTROS;
        foreach ($params as $key => $value) {

            if ($value instanceof Closure) {
                $params['controller'] = $value;
                unset($params[$key]);
                continue;
            }
        }

        //MIDDLEWARES DA ROTA
        $params['middlewares']  = $params['middlewares'] ?? [];


        $params['variables'] = [];

        $patternValiable = '/{(.*?)}/';

        if (preg_match_all($patternValiable, $route, $matches)) {
            $route = preg_replace($patternValiable, '(.*?)', $route);
            $params['variables'] = $matches[1];
        }


        //REMOVE BARRA NO FINAL DA ROTA
        $route = rtrim($route, '/');

        //PADRÃO DE VALIDAÇÃO DA URL
        $patternRouter = '/^' . str_replace('/', '\/', $route) . '$/';

        $this->routes[$patternRouter][$method] = $params;;
    }

    //RETORNA A URI DO PREFIXO
    public function getUri()
    {
        $uri = $this->request->getUri(); #URI da request


        //FATIA A URI COM PREFIXO
        $xUri = strlen($this->prefix) ? explode($this->prefix, $uri) : [$uri];



        // return end($xUri);
        return rtrim(end($xUri), '/'); //ultimo indice do array

    }

    //RETORNA OS DADOS DA ROTA ATUAL
    private function getRoute()
    {
        //URI
        $uri = $this->getUri();

        //METHOD
        $httpMethod = $this->request->getHttpMethod();

        //VALIDA AS ROTAS
        foreach ($this->routes as $patternRoute => $methods) {

            //VERIFICA SE A URI bate com o padrão
            if (preg_match($patternRoute, $uri, $matches)) {
                //verifica o método
                if (isset($methods[$httpMethod])) {

                    unset($matches[0]);

                    $keys = $methods[$httpMethod]['variables'];
                    $methods[$httpMethod]['variables'] = array_combine($keys, $matches);
                    $methods[$httpMethod]['variables']['request'] = $this->request;
                    return $methods[$httpMethod];
                }

                throw new Exception('Método não permitido', 405);
            }
        }

        throw new Exception('URL não encontrada', 404);
    }


    /**
     * @return Response
     */
    #executa a rota atual;
    public function run()
    {
        try {


            $route = $this->getRoute(); //rota atual 

            // var_dump($route);

            //VERIFICA O CONTROLADOR

            if (!isset($route['controller'])) {
                throw new Exception('A URL não pode ser processada', 500);
            }

            $args = [];

            $reflection = new ReflectionFunction($route['controller']);
            foreach ($reflection->getParameters() as $parameter) {

                $name = $parameter->getName();
                $args[$name] = $route['variables'][$name] ?? '';
            }

            //RETORNA A EXECUÇÃO DA FILA DE MIDDLEWARES
            return (new Queue($route['middlewares'], $route['controller'], $args))->next($this->request);
            //return call_user_func_array($route['controller'], $args);

        } catch (Exception $e) {
            return new Response($e->getCode(), $this->getErrorMessage($e->getMessage()), $this->contentType);
            //CRIA A INSTACIA DE RESPONSE QUE PODE SER CHAMA DIRETA DE ROUTER PELO RUM
        }
    }

    //Retorna a URL atual
    public function getCurrentUrl()
    {
        return  $this->url . $this->getUri();
    }
    //Retorna a URL
    public function getUrl()
    {
        return  $this->url;
    }



    public function redirect($route)
    {
        //monta o redirect depois da URI Base    
        $url = $this->url . $route;

        //executa o redirect
        header('location: ' . $url);
        exit;
    }
}
