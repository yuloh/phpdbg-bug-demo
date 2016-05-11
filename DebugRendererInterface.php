<?php

namespace Fidry\PhpdbgDemo;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

interface DebugRendererInterface
{
    /**
     * Renders an exception in debug mode, so include all the necessary stuff for debugging. Must not be used
     * production.
     *
     * @param Request    $request
     * @param \Exception $exception
     *
     * @return Response
     */
    public function render(Request $request, \Exception $exception);
}
