<?php

// Import all command files

foreach (glob(__DIR__ . '/commands/*.php') as $file) {
  require_once $file;
}
