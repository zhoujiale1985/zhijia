<?php

namespace ZhijiaCommon\Utils;

use Closure;
use GuzzleHttp\Client;
use Zipkin\Endpoint;
use Zipkin\Samplers\BinarySampler;
use Zipkin\TracingBuilder;
use Zipkin\Propagation\DefaultSamplingFlags;
use Zipkin\Propagation\Map;
use Zipkin\Timestamp;
use Zipkin\Kind;
use Zipkin\Reporters;
use Zipkin\Tags;

class ZipkinUtils
{
    /**
     * create_tracing function is a handy function that allows you to create a tracing
     * component by just passing the local service information. If you need to pass a
     * custom zipkin server URL use the HTTP_REPORTER_URL env var.
     */
    public static function createTracing($localServiceName, $localServiceIPv4, $localServicePort = null)
    {
        $httpReporterURL = getenv('HTTP_REPORTER_URL');
        if ($httpReporterURL === false) {
            throw new \Exception('please set HTTP_REPORTER_URL env var');
        }

        $endpoint = Endpoint::create($localServiceName, $localServiceIPv4, null, $localServicePort);
        $reporter = new Reporters\Http(
            Reporters\Http\CurlFactory::create(),
            ['endpoint_url' => $httpReporterURL]
        );
        $sampler = BinarySampler::createAsAlwaysSample();
        $tracing = TracingBuilder::create()
            ->havingLocalEndpoint($endpoint)
            ->havingSampler($sampler)
            ->havingReporter($reporter)
            ->build();

        return $tracing;
    }

    /**
     * 发起一次service的http请求，并生成zipkin child span
     * @param string $spanName
     * @param string $reqType
     * @param string $serviceName
     * @return \Psr\Http\Message\ResponseInterface
     */
    public static function requestWithZipkin($spanName='child_span_name', $reqType='POST', $serviceName='servicename.com', $token='', $filter=[])
    {
        /* Creates the span  */
        $request = request();
        $span = $request['zipkin_span_obj'];
        $tracer = $request['zipkin_tracer_obj'];
        $tracing = $request['zipkin_tracing_obj'];
        if(!$tracer){
            return ;
        }
        $childSpan = $tracer->newChild($span->getContext());
        $childSpan->start();
        $childSpan->setKind(Kind\CLIENT);
        $childSpan->setName($spanName);
        $headers = [];
        /* Injects the context into the wire */
        $injector = $tracing->getPropagation()->getInjector(new Map());
        $injector($childSpan->getContext(), $headers);
        if($reqType){
            $headers['newadmin-token'] = $token;
        }
        /* HTTP Request to the backend */
        $httpClient = new Client();
        $request = new \GuzzleHttp\Psr7\Request($reqType, $serviceName, $headers, json_encode($filter));
        $childSpan->annotate('request_started', Timestamp\now());
        $response = $httpClient->send($request);
        $code = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $childSpan->annotate('request_finished', Timestamp\now());
        $childSpan->finish();
        //显示获得的数据
        if ($code == 200) {
            $data = json_decode($body, true);
            return $data;
        } else {
            return;
        }
    }

    /**
     * 创建捕获异常的zipkin span
     * @param string $spanName
     * @param Exception $exception
     * @return bool
     */
    public static function createChildSpanForException($spanName='exception_handle', $exception)
    {
        /* Creates the child span */
        $request = request();
        $span = $request['zipkin_span_obj'];
        $tracer = $request['zipkin_tracer_obj'];
        $tracing = $request['zipkin_tracing_obj'];
        if($tracer) {
            $childSpan = $tracer->newChild($span->getContext());
            $childSpan->start();
            $childSpan->setKind(Kind\CLIENT);
            $childSpan->setName($spanName);
//        $exceptionTraceString = CommonUtils::getExceptionTraceAsString($exception);
            $errMsg['Msg'] = $exception->getMessage();
            $lineInfo = $exception->getFile() . '(' . $exception->getLine() . ')';
            $errMsg['Trace'] = $lineInfo . PHP_EOL . $exception->getTraceAsString();
            $childSpan->tag(Tags\ERROR, json_encode($errMsg));
            $childSpan->finish();
        }

        return true;
    }

    /**
     * 创建sql child span
     * @param string $spanName
     * @param array $querySql
     * @return bool
     */
    public static function createChildSpanForMysql($spanName='query_mysql', $querySql='', $dbType='sql', $dbInstance='')
    {
        /* Creates the child span */
        $request = request();
        $span = $request['zipkin_span_obj'];
        $tracer = $request['zipkin_tracer_obj'];
        $tracing = $request['zipkin_tracing_obj'];
        if($tracer) {
            $childSpan = $tracer->newChild($span->getContext());
            $childSpan->start();
            $childSpan->setKind(Kind\CLIENT);
            $childSpan->setName($spanName);
            $childSpan->tag('db.statement', $querySql);
            $childSpan->tag('db.type', $dbType);
            $childSpan->tag('db.instance', $dbInstance);
            $childSpan->finish();
        }

        return true;
    }

    /**
     * 生成mongo child span
     * @param string $spanName
     * @param array $queryMongo
     * @return bool
     */
    public static function createChildSpanForMongo($spanName='query_mongo', $queryMongo=[], $dbType='mongo', $dbInstance='')
    {
        self::createChildSpanForMysql($spanName, $queryMongo, $dbType, $dbInstance);

        return true;
    }

    /**
     * 生成redis child span
     * @param string $spanName
     * @param array $querySql
     * @return bool
     */
    public static function createChildSpanForRedis($spanName='query_redis', $queryRedis=[], $dbType='redis', $dbInstance='')
    {
        self::createChildSpanForMysql($spanName, $queryRedis, $dbType, $dbInstance);

        return true;
    }
}