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
            $this->beforeCall();
            $closure($timer, $loop);
            $this->afterCall();
        });

        $loop->addSignal($this->getTimerStopSignal(), function () use ($loop) {
            $loop->futureTick(function () use ($loop) {
                $this->beforeStop();
                $loop->stop();
                $this->afterStop();
            });
        });

        $this->beforeStart();
        $loop->run();
    }

    /**
     * @return int
     */
    public function getTimerStopSignal() : int
    {
        return defined('SIGINT') ? SIGINT : 2;
    }

    public function beforeStart(): void { }

    public function beforeStop(): void { }

    public function afterStop() { }

    public function beforeCall(): void { }

    public function afterCall(): void { }
}
