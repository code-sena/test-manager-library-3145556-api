<?php

arch('Domain does not depend on Application or Adapters')
    ->expect('App\Domain')
    ->not->toUse(['App\Application', 'App\Adapters', 'App\Bootstrap']);

arch('Application does not depend on Adapters')
    ->expect('App\Application')
    ->not->toUse(['App\Adapters', 'App\Bootstrap']);

arch('Domain does not use Eloquent or Illuminate')
    ->expect('App\Domain')
    ->not->toUse(['Illuminate', 'Eloquent']);
