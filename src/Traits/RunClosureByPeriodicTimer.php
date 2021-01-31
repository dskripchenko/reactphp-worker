<?php

namespace Dskripchenko\ReactphpWorker\Traits;

use \React\EventLoop\Factory;

trait RunClosureByPeriodicTimer
{
    /**
     * @param float $interval
     * @param callable $closure
     */
    public function runClosureByPeriodicTimer(float $interval, callable $closure): void
    {
        $loop = Factory::create();

        $loop->addPeriodicTimer($interval, function ($timer) use ($loop, $closure){
            $closure($timer, $loop);
        });

        $loop->addSignal($this->getTimerStopSignal(), function () use ($loop) {
            $loop->futureTick(function () use ($loop) {
                $loop->stop();
            });
        });

        $loop->run();
    }

    /**
     * @return int
     */
    public function getTimerStopSignal() : int
    {
        return defined('SIGINT') ? SIGINT : 2;
    }
}
