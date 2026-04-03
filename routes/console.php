<?php

use App\Jobs\ActivateScheduledAuctionsJob;
use App\Jobs\CloseExpiredAuctionsJob;
use Illuminate\Support\Facades\Schedule;

Schedule::job(new ActivateScheduledAuctionsJob)->everyMinute();
Schedule::job(new CloseExpiredAuctionsJob)->everyMinute();
