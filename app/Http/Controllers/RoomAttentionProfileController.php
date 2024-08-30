<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RoomAttentionProfileController extends Controller
{


    public function store(
        \App\Models\AttentionProfile $attentionProfile,
        \App\Http\Requests\RoomAttentionProfileRequest $request
    ) {
        $attentionProfile =  $request->roomAttentionProfile($attentionProfile);
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function update(
        \App\Http\Requests\RoomAttentionProfileRequest $request,
        \App\Models\AttentionProfile $attentionProfile
    ) {
        $attentionProfile  = $request->updateRoomAttentionProfile($attentionProfile);
        return new \App\Http\Resources\AttentionProfileResource($attentionProfile);
    }

    public function destroy(
        \App\Models\AttentionProfile $attentionProfile
    ) {
        $attentionProfile->rooms()->detach();
        return response()->json(null, 204);
    }
}
