<?php

namespace App\Repositories;

use App\Models\Comment;
use App\Exceptions\DeletionException;
use App\Helpers\Helpers;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CommentRepository
{
    /**
     * @var Comment
     */
    private $comment;

    /**
     * CommentRepository constructor.
     *
     * @param Comment $comment
     */
    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    /**
     * Paginates comments
     *
     * @param int|null $page
     * @param int|null $perPage
     * @param string|null $search
     * @param string|null $orderBy
     * @param string|null $orderDirection
     * @param int|null $chef
     *
     * @return LengthAwarePaginator
     */
    public static function paginate($page = 1, $perPage = 20, $search = null, $orderBy = null, $orderDirection = 'desc', $chef = null)
    {
        $comments = Comment::with(['alert', 'alert.chef', 'alert.zone']);

        if ($chef) {
            $comments->whereHas('alert', function ($alert) use ($chef) {
                $alert->where('chef_id', '=', $chef);
            });
        }

        if (isset($search)) {
            $comments->where(Helpers::fieldsLike($search, 'comment'));
        }

        if (isset($orderBy) && isset($orderDirection)) {
            $comments->orderBy($orderBy, $orderDirection);
        }

        return $comments->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Updates a comment
     *
     * @param $message
     *
     * @return Comment
     */
    public function update($message)
    {

        $this->comment->comment = $message;

        $this->comment->save();

        return $this->comment;
    }

    /**
     * Deletes a comment
     *
     * @throws DeletionException
     */
    public function delete()
    {
        try {
            $this->comment->delete();
        } catch (\Exception $e) {
            throw new DeletionException($e->getMessage(), $e->getCode(), $e);
        }
    }

}
