<?php
/* $timerId = \Swoole\Timer::after(3000, function () {
    echo "Laravel 也很棒\n";
});
 */

$count = 0;
\Swoole\Timer::tick(1000, function ($timerId, $count) {
    global $count;
    echo "Swoole 很棒\n";
    $count++;
    if ($count == 3) {
        \Swoole\Timer::clear($timerId);
    }
}, $count);
