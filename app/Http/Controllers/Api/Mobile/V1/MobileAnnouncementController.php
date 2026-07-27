<?php

namespace App\Http\Controllers\Api\Mobile\V1;

use App\Http\Controllers\Api\Mobile\V1\Concerns\RespondsToMobileApi;
use App\Http\Controllers\Controller;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;

class MobileAnnouncementController extends Controller
{
    use RespondsToMobileApi;

    public function __invoke(): JsonResponse
    {
        $announcements = Announcement::query()
            ->where('status', '1')
            ->latest()
            ->limit(10)
            ->get(['id', 'title', 'description', 'position', 'created_at'])
            ->map(fn (Announcement $announcement) => [
                'id' => $announcement->id,
                'title' => $announcement->title,
                'description' => trim(preg_replace(
                    '/\s+/',
                    ' ',
                    html_entity_decode(strip_tags((string) $announcement->description), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                )),
                'position' => $announcement->position,
                'created_at' => $announcement->created_at,
            ]);

        return $this->successResponse('Announcements fetched successfully.', $announcements);
    }
}
