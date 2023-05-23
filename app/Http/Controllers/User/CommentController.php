<?php

namespace App\Http\Controllers\User;

use App\Exceptions\DeletionException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Repositories\CommentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $page = $request->input('page', null);
        $search = $request->input('search', null);
        $perPage = $request->input('perPage', null);
        $orderBy = $request->input('orderBy', 'created_at');
        $orderDirection = $request->input('orderDirection', 'desc');

        $chef = auth()->id();

        $comments = CommentRepository::paginate($page, $perPage, $search, $orderBy, $orderDirection, $chef);

        return response()->json(['status' => 'success', 'data' => $comments], 200);
    }

    /**
     * @param int $commentId
     *
     * @return JsonResponse
     */
    public function show($commentId)
    {
        $chef = auth()->id();

        $comment = Comment::whereHas('alert', function ($alert) use ($chef) {
            $alert->where('chef_id', '=', $chef);
        })
            ->with(['alert', 'alert.chef', 'alert.zone'])
            ->findOrFail($commentId);

        return response()->json(['status' => 'success', 'data' => $comment], 200);
    }


    /**
     * @param $commentId
     * @param CommentRequest $request
     *
     * @return JsonResponse
     */
    public function update($commentId, CommentRequest $request)
    {
        $chef = auth()->id();

        $comment = Comment::whereHas('alert', function ($alert) use ($chef) {
            $alert->where('chef_id', '=', $chef);
        })->findOrFail($commentId);

        $message = $request->input('comment');

        $commentRepository = new CommentRepository($comment);

        $comment = $commentRepository->update($message, $chef);

        return response()->json(['status' => 'success', 'data' => $comment], 200);
    }

    /**
     * @param int $commentId
     *
     * @return JsonResponse
     */
    public function delete($commentId)
    {
        $chef = auth()->id();

        $comment = Comment::whereHas('alert', function ($alert) use ($chef) {
            $alert->where('chef_id', '=', $chef);
        })->findOrFail($commentId);

        $commentRepository = new CommentRepository($comment);

        try {
            $commentRepository->delete();
        } catch (DeletionException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 422);
        }

        return response()->json(['status' => 'success'], 200);
    }

}
