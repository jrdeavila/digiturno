<?php

namespace App\Http\Controllers\UI\Screen;

use App\Http\Controllers\Controller;
use App\Models\Room;

class IndexController extends Controller
{
  public function __invoke(Room $room)
  {
    return view('attention.screen.index', [
      'currentRoom' => $room
    ]);
  }
}
