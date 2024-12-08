<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::whereDate('start', '>=', $request->start)
            ->whereDate('end', '<=', $request->end)
            ->get();

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $event = Event::create([
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
        ]);

        return response()->json($event, 201);
    }

    public function update(Request $request, $id)
    {
        $event = Event::findOrFail($id);
        $event->update([
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
        ]);
        return response()->json($event);
    }

    public function destroy($id)
    {
        $event = Event::findOrFail($id);
        $event->delete();
        return response()->json(null, 204);
    }

    public function getEvent(Request $request)
    {
        $eventId = $request->id;
        $event = Event::findOrFail($eventId);
        $start = Carbon::parse($event->start)->format('H:i');
        $end = Carbon::parse($event->end)->format('H:i');
        $date = Carbon::parse($event->start)->format('Y-m-d');;
        $result = [
            'title' => $event->title,
            'start' => $start,
            'end' => $end,
            'date' => $date
        ];
        return response()->json($result);
    }
}
