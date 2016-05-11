<?php

namespace Fidry\PhpdbgDemo;

use Illuminate\Http\Request;
use Prophecy\Argument;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\RunInterface;

/**
 * @covers App\Infrastructure\Debug\Renderer\WhoopsDebugRenderer
 */
class WhoopsDebugRendererTest extends \PHPUnit_Framework_TestCase
{
    public function testIsARenderer()
    {
        $this->assertTrue(is_a(WhoopsDebugRenderer::class, DebugRendererInterface::class, true));
    }

    public function testHandleException()
    {
        $exception = new \Exception();

        $requestProphecy = $this->prophesize(Request::class);
        $requestProphecy->isJson()->willReturn(false);
        /* @var Request $request */
        $request = $requestProphecy->reveal();

        $runProphecy = $this->prophesize(RunInterface::class);
        $runProphecy->pushHandler(Argument::type(PrettyPageHandler::class))->shouldBeCalled();
        $runProphecy->handleException($exception)->willReturn('content');
        /* @var RunInterface $run */
        $run = $runProphecy->reveal();

        $handler = new WhoopsDebugRenderer($run);
        $response = $handler->render($request, $exception);

        $this->assertEquals('content', $response->getContent());

        $requestProphecy->isJson()->shouldHaveBeenCalledTimes(1);
        $runProphecy->pushHandler(Argument::any())->shouldHaveBeenCalledTimes(1);
        $runProphecy->handleException(Argument::any())->shouldHaveBeenCalledTimes(1);
    }

    public function testHandleExceptionForJsonRequest()
    {
        $exception = new \Exception();

        $requestProphecy = $this->prophesize(Request::class);
        $requestProphecy->isJson()->willReturn(true);
        /* @var Request $request */
        $request = $requestProphecy->reveal();

        $runProphecy = $this->prophesize(RunInterface::class);
        $runProphecy->pushHandler(Argument::type(PrettyPageHandler::class))->shouldBeCalled();
        $runProphecy->pushHandler(Argument::type(JsonResponseHandler::class))->shouldBeCalled();
        $runProphecy->handleException($exception)->willReturn('content');
        /* @var RunInterface $run */
        $run = $runProphecy->reveal();

        $handler = new WhoopsDebugRenderer($run);
        $response = $handler->render($request, $exception);

        $this->assertEquals('content', $response->getContent());

        $requestProphecy->isJson()->shouldHaveBeenCalledTimes(1);
        $runProphecy->pushHandler(Argument::type(PrettyPageHandler::class))->shouldHaveBeenCalledTimes(1);
        $runProphecy->pushHandler(Argument::type(JsonResponseHandler::class))->shouldHaveBeenCalledTimes(1);
        $runProphecy->handleException(Argument::any())->shouldHaveBeenCalledTimes(1);
    }
}
