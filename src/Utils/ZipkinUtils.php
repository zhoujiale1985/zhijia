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
    public static function requestWithZipkin($spanName='child_span_name', $reqType='POST', $serviceName='servicename.com')
    {
        /* Creates the span  */
        $request = request();
        $span = $request['zipkin_span_obj'];
        $tracer = $request['zipkin_tracer_obj'];
        $tracing = $request['zipkin_tracing_obj'];
        $childSpan = $tracer->newChild($span->getContext());
        $childSpan->start();
        $childSpan->setKind(Kind\CLIENT);
        $childSpan->setName($spanName);
        $headers = [];
        /* Injects the context into the wire */
        $injector = $tracing->getPropagation()->getInjector(new Map());
        $injector($childSpan->getContext(), $headers);
        /* HTTP Request to the backend */
        $httpClient = new Client();
        $request = new \GuzzleHttp\Psr7\Request($reqType, $serviceName, $headers);
        $childSpan->annotate('request_started', Timestamp\now());
        $response = $httpClient->send($request);
        $childSpan->annotate('request_finished', Timestamp\now());
        $childSpan->finish();

        return $response;
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
        $childSpan = $tracer->newChild($span->getContext());
        $childSpan->start();
        $childSpan->setKind(Kind\CLIENT);
        $childSpan->setName($spanName);
//        $exceptionTraceString = CommonUtils::getExceptionTraceAsString($exception);
        $errMsg['Msg'] = $exception->getMessage();
        $lineInfo = $exception->getFile() . '(' . $exception->getLine() . ')';
        $errMsg['Trace'] = $lineInfo . PHP_EOL . $exception->getTraceAsString();
        $childSpan->tag('stack', 'Exception:'.json_encode($errMsg));
        $childSpan->finish();

        return true;
    }

    public static function createChildSpanForMysql()
    {
        // todo...
    }

    public static function createChildSpanForRedis()
    {
        // todo...
    }
}