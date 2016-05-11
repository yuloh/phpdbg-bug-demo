<?php

namespace Fidry\PhpdbgDemo;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Whoops\Handler\JsonResponseHandler;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;
use Whoops\RunInterface;

final class WhoopsDebugRenderer implements DebugRendererInterface
{
    /**
     * @var Run
     */
    private $run;

    public function __construct(RunInterface $run = null)
    {
        $this->run = ($run === null) ? new Run() : $run;
    }

    /**
     * @inheritDoc
     */
    public function render(Request $request, \Exception $exception)
    {
        $this->run->pushHandler(new PrettyPageHandler());

        if ($request->isJson()) {
            // Since this handler is first in the stack, it will be executed before the error page handler, and will
            // have a chance to decide if anything needs to be done.
            $this->run->pushHandler(new JsonResponseHandler());
        }

        return new Response(
            $this->run->handleException($exception)
        );
    }
}
